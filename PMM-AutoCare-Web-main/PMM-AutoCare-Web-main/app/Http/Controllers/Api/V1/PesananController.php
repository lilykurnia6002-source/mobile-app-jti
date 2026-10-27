<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexPesananRequest;
use App\Http\Requests\Api\V1\StorePesananRequest;
use App\Http\Resources\Api\V1\PesananDetailResource;
use App\Http\Resources\Api\V1\PesananDibuatResource;
use App\Http\Resources\Api\V1\PesananRingkasResource;
use App\Http\Resources\Api\V1\RiwayatStatusResource;
use App\Http\Responses\ApiResponse;
use App\Models\Pesanan;
use App\Services\BookingService;
use App\Services\PesananService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Modul pesanan / booking servis (konsep_api.md § 6).
 *
 * Semua akses di-scope ke pesanan milik pelanggan yang login. Baris milik
 * pelanggan lain menghasilkan 403 FORBIDDEN (bukan 404), sesuai kontrak.
 */
class PesananController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private PesananService $pesananService,
    ) {}

    /**
     * POST /api/v1/pesanan — endpoint pusat booking.
     */
    public function store(StorePesananRequest $request): JsonResponse
    {
        $pesanan = $this->bookingService->buatPesanan(
            $request->user()->pelanggan,
            $request->validated(),
        );

        return ApiResponse::success(
            new PesananDibuatResource($pesanan),
            'Pesanan berhasil diajukan',
            201,
        );
    }

    /**
     * GET /api/v1/pesanan — riwayat pesanan, terbaru dulu.
     */
    public function index(IndexPesananRequest $request): JsonResponse
    {
        $query = $this->queryPelanggan($request)
            ->with('slot')
            ->riwayat();

        if ($status = $request->validated('status')) {
            $query->where('status', $status);
        }

        $paginator = $query->paginate($request->validated('per_page') ?? 10);
        $paginator->appends($request->query());

        return ApiResponse::paginated(
            $paginator,
            PesananRingkasResource::collection($paginator->getCollection())->resolve(),
            'Riwayat pesanan',
        );
    }

    /**
     * GET /api/v1/pesanan/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $pesanan = $this->temukanMilikPelanggan($request, $id);

        return ApiResponse::success(new PesananDetailResource($pesanan), 'Detail pesanan');
    }

    /**
     * GET /api/v1/pesanan/{id}/riwayat-status — pelacakan status.
     */
    public function riwayatStatus(Request $request, int $id): JsonResponse
    {
        $pesanan = $this->temukanMilikPelanggan($request, $id);
        $pesanan->load(['riwayatStatus', 'pengerjaan.mekanik']);

        // Nama mekanik disematkan pada entri riwayat yang berkaitan dengan
        // penugasan (konsep_api.md § 6.4 menampilkan "mekanik" pada entri
        // berstatus dikerjakan).
        $namaMekanik = $pesanan->pengerjaan->first()?->mekanik?->nama;

        $riwayat = $pesanan->riwayatStatus->map(function ($r) use ($namaMekanik) {
            $item = (new RiwayatStatusResource($r))->toArray(request());

            if ($namaMekanik !== null && $r->status === Pesanan::STATUS_DIKERJAKAN) {
                $item['mekanik'] = $namaMekanik;
            }

            return $item;
        })->values();

        return ApiResponse::success([
            'id_pesanan' => $pesanan->id_pesanan,
            'kode_pesanan' => $pesanan->kode_pesanan,
            'riwayat' => $riwayat,
        ], 'Riwayat status pesanan');
    }

    /**
     * PATCH /api/v1/pesanan/{id}/batal — pembatalan oleh pelanggan.
     *
     * Hanya berlaku selama status masih `menunggu`; kuota slot dikembalikan
     * dan perubahan dicatat pada riwayat status.
     */
    public function batal(Request $request, int $id): JsonResponse
    {
        $pesanan = $this->temukanMilikPelanggan($request, $id);

        $pesanan = $this->pesananService->batalkan($pesanan, $request->user()->id_user);

        return ApiResponse::success([
            'id_pesanan' => $pesanan->id_pesanan,
            'kode_pesanan' => $pesanan->kode_pesanan,
            'status' => $pesanan->status,
        ], 'Pesanan berhasil dibatalkan');
    }

    private function queryPelanggan(Request $request)
    {
        return Pesanan::untukPelanggan($request->user()->pelanggan->id_pelanggan);
    }

    /**
     * Ambil pesanan milik pelanggan login beserta relasi detailnya.
     *
     * @throws ApiException
     */
    private function temukanMilikPelanggan(Request $request, int $id): Pesanan
    {
        $pesanan = Pesanan::with([
            'mobil', 'slot', 'detailLayanan.layanan',
            'detailSparepart.sparepart', 'pengerjaan.mekanik',
            'riwayatStatus', 'pembayaran.metode',
        ])->find($id);

        if ($pesanan === null) {
            throw new ApiException('Pesanan tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
        }

        if ($pesanan->id_pelanggan !== $request->user()->pelanggan->id_pelanggan) {
            throw new ApiException(
                'Anda tidak berhak mengakses pesanan ini.',
                'FORBIDDEN',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        }

        return $pesanan;
    }
}
