<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexNotifikasiRequest;
use App\Http\Resources\Api\V1\NotifikasiResource;
use App\Http\Responses\ApiResponse;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Notifikasi pelanggan (konsep_api.md § 7).
 *
 * tb_notifikasi tidak menyimpan id_user, jadi kepemilikan diturunkan dari
 * pesanan induk — lihat scopeUntukPelanggan() pada model Notifikasi.
 */
class NotifikasiController extends Controller
{
    /**
     * GET /api/v1/notifikasi
     */
    public function index(IndexNotifikasiRequest $request): JsonResponse
    {
        $query = Notifikasi::untukPelanggan($request->user()->pelanggan->id_pelanggan)
            ->orderByDesc('waktu_kirim');

        if ($request->boolean('belum_baca')) {
            $query->belumDibaca();
        }

        $paginator = $query->paginate($request->validated('per_page') ?? 15);
        $paginator->appends($request->query());

        return ApiResponse::paginated(
            $paginator,
            NotifikasiResource::collection($paginator->getCollection())->resolve(),
            'Daftar notifikasi',
        );
    }

    /**
     * PATCH /api/v1/notifikasi/{id}/baca — idempoten.
     */
    public function baca(Request $request, int $id): JsonResponse
    {
        $notifikasi = Notifikasi::find($id);

        if ($notifikasi === null) {
            throw new ApiException('Notifikasi tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
        }

        if ($notifikasi->pesanan?->id_pelanggan !== $request->user()->pelanggan->id_pelanggan) {
            throw new ApiException(
                'Anda tidak berhak mengakses notifikasi ini.',
                'FORBIDDEN',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        }

        // Memanggil ulang tidak error (idempoten).
        if (! $notifikasi->dibaca) {
            $notifikasi->update(['dibaca' => true]);
        }

        return ApiResponse::success([
            'id_notifikasi' => $notifikasi->id_notifikasi,
            'dibaca' => true,
        ], 'Notifikasi ditandai dibaca');
    }
}
