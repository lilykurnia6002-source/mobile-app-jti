<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatatSparepartRequest;
use App\Http\Requests\Admin\TerimaPesananRequest;
use App\Http\Requests\Admin\TolakPesananRequest;
use App\Models\Mekanik;
use App\Models\Pesanan;
use App\Models\Sparepart;
use App\Services\BookingService;
use App\Services\SparepartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pengelolaan pesanan oleh admin: verifikasi, penugasan mekanik,
 * penolakan, dan penyelesaian.
 *
 * Setiap perubahan status menulis tb_riwayat_status (jejak audit) dan
 * tb_notifikasi (agar pelanggan melihatnya di aplikasi mobile) — memakai
 * BookingService agar formatnya identik dengan yang ditulis sisi mobile.
 */
class PesananController extends Controller
{
    /**
     * Status pesanan yang masih boleh diubah rincian sparepartnya.
     */
    private const STATUS_BOLEH_SPAREPART = [
        Pesanan::STATUS_DITERIMA,
        Pesanan::STATUS_DIKERJAKAN,
    ];

    public function __construct(
        private BookingService $bookingService,
        private SparepartService $sparepartService,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $pencarian = $request->query('q');

        $pesanan = Pesanan::with(['pelanggan', 'mobil', 'slot'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($pencarian, function ($q) use ($pencarian) {
                $q->where(function ($sub) use ($pencarian) {
                    $sub->where('kode_pesanan', 'like', "%{$pencarian}%")
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', "%{$pencarian}%"))
                        ->orWhereHas('mobil', fn ($m) => $m->where('plat_nomor', 'like', "%{$pencarian}%"));
                });
            })
            ->orderByDesc('waktu_pesan')
            ->paginate(15)
            ->withQueryString();

        return view('pesanan.index', [
            'pesanan' => $pesanan,
            'status' => $status,
            'pencarian' => $pencarian,
            'jumlahPerStatus' => $this->jumlahPerStatus(),
            // Dibutuhkan modal Proses Pesanan (Terima/Tolak) di halaman daftar.
            'daftarMekanik' => Mekanik::where('aktif', true)->orderBy('nama')->get(),
        ]);
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load([
            'pelanggan.user', 'mobil', 'slot', 'detailLayanan.layanan',
            'detailSparepart.sparepart', 'pengerjaan.mekanik',
            'keluhanTambahan', 'riwayatStatus.diubahOleh', 'pembayaran.metode',
        ]);

        return view('pesanan.show', [
            'pesanan' => $pesanan,
            'daftarMekanik' => Mekanik::where('aktif', true)->orderBy('nama')->get(),
            // Hanya sparepart yang stoknya tersedia yang bisa dipakai.
            'daftarSparepart' => Sparepart::where('stok', '>', 0)->orderBy('nama')->get(),
            'bolehUbahSparepart' => in_array($pesanan->status, self::STATUS_BOLEH_SPAREPART, true),
        ]);
    }

    // Admin dapat membuat pesanan sendiri
    public function buatPesanan(Pesanan $pesanan): View
    {
        
    }

    /**
     * Catat pemakaian sparepart pada pesanan — stok berkurang di sini.
     */
    public function catatSparepart(CatatSparepartRequest $request, Pesanan $pesanan): JsonResponse
    {
        if (! in_array($pesanan->status, self::STATUS_BOLEH_SPAREPART, true)) {
            return $this->tolakAksi(
                'Rincian sparepart hanya dapat diubah saat pesanan sedang diterima atau dikerjakan. '
                ."Pesanan {$pesanan->kode_pesanan} berstatus {$pesanan->status}."
            );
        }

        $sparepart = $this->sparepartService->catatPemakaian(
            $pesanan,
            (int) $request->validated('id_sparepart'),
            (int) $request->validated('jumlah'),
        );

        return response()->json([
            'status' => 'success',
            'message' => "Pemakaian {$sparepart->nama} berhasil dicatat. "
                ."Stok tersisa {$sparepart->fresh()->stok} {$sparepart->satuan}.",
        ]);
    }

    /**
     * Batalkan pemakaian sparepart — stok dikembalikan ke persediaan.
     */
    public function hapusSparepart(Request $request, Pesanan $pesanan, int $detail): JsonResponse
    {
        if (! in_array($pesanan->status, self::STATUS_BOLEH_SPAREPART, true)) {
            return $this->tolakAksi(
                'Rincian sparepart hanya dapat diubah saat pesanan sedang diterima atau dikerjakan. '
                ."Pesanan {$pesanan->kode_pesanan} berstatus {$pesanan->status}."
            );
        }

        $this->sparepartService->hapusPemakaian($pesanan, $detail);

        return response()->json([
            'status' => 'success',
            'message' => 'Pemakaian sparepart dibatalkan dan stok dikembalikan ke persediaan.',
        ]);
    }

    /**
     * Terima pesanan + tugaskan mekanik.
     */
    public function terima(TerimaPesananRequest $request, Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->status !== Pesanan::STATUS_MENUNGGU) {
            return $this->tolakAksi(
                "Pesanan {$pesanan->kode_pesanan} sudah berstatus {$pesanan->status} dan tidak dapat diterima lagi."
            );
        }

        DB::transaction(function () use ($request, $pesanan) {
            $pesanan->update(['status' => Pesanan::STATUS_DITERIMA]);

            // Penugasan mekanik dicatat atas nama admin yang login
            // (mekanik tidak punya akun — konsep_rancanganBasisData.md).
            $pesanan->pengerjaan()->create([
                'id_mekanik' => $request->validated('id_mekanik'),
                'dicatat_oleh' => $request->user()->id_user,
            ]);

            $this->bookingService->catatRiwayat(
                $pesanan,
                Pesanan::STATUS_DITERIMA,
                $request->user()->id_user,
                'Pesanan diverifikasi dan mekanik telah ditugaskan.',
            );

            $this->kirimNotifikasi(
                $pesanan,
                'Pesanan Diterima',
                "Pesanan {$pesanan->kode_pesanan} Anda telah diterima dan mekanik telah ditugaskan.",
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan berhasil diterima dan mekanik telah ditugaskan.',
        ]);
    }

    /**
     * Tolak pesanan + kembalikan kuota slotnya.
     */
    public function tolak(TolakPesananRequest $request, Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->status !== Pesanan::STATUS_MENUNGGU) {
            return $this->tolakAksi(
                "Pesanan {$pesanan->kode_pesanan} sudah berstatus {$pesanan->status} dan tidak dapat ditolak."
            );
        }

        $keterangan = $request->validated('keterangan');

        DB::transaction(function () use ($request, $pesanan, $keterangan) {
            $pesanan->update([
                'status' => Pesanan::STATUS_DITOLAK,
                'alasan_tolak' => $keterangan,
            ]);

            $this->kembalikanKuota($pesanan);

            $this->bookingService->catatRiwayat(
                $pesanan,
                Pesanan::STATUS_DITOLAK,
                $request->user()->id_user,
                $keterangan,
            );

            $this->kirimNotifikasi(
                $pesanan,
                'Pesanan Ditolak',
                "Mohon maaf, pesanan {$pesanan->kode_pesanan} tidak dapat kami proses. {$keterangan}",
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan telah ditolak dan kuota slot dikembalikan.',
        ]);
    }

    /**
     * Tandai pesanan selesai dikerjakan.
     */
    /**
     * Tandai pesanan mulai dikerjakan mekanik.
     *
     * Ini tahap antara setelah pesanan diterima: admin mencatat laporan lisan
     * mekanik bahwa pengerjaan benar-benar dimulai, sehingga waktu_mulai
     * terisi dan pelanggan melihat progres di aplikasi mobile.
     */
    public function kerjakan(Request $request, Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->status !== Pesanan::STATUS_DITERIMA) {
            return $this->tolakAksi(
                "Pesanan {$pesanan->kode_pesanan} sudah berstatus {$pesanan->status}. "
                .'Hanya pesanan yang baru diterima yang dapat mulai dikerjakan.'
            );
        }

        DB::transaction(function () use ($request, $pesanan) {
            $pesanan->update(['status' => Pesanan::STATUS_DIKERJAKAN]);

            // Catat kapan pengerjaan dimulai pada penugasan yang berjalan.
            $pesanan->pengerjaan()
                ->whereNull('waktu_mulai')
                ->update(['waktu_mulai' => now()]);

            $this->bookingService->catatRiwayat(
                $pesanan,
                Pesanan::STATUS_DIKERJAKAN,
                $request->user()->id_user,
                'Mekanik mulai mengerjakan kendaraan.',
            );

            $this->kirimNotifikasi(
                $pesanan,
                'Servis Sedang Dikerjakan',
                "Pesanan {$pesanan->kode_pesanan} sedang dikerjakan oleh mekanik kami.",
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan ditandai sedang dikerjakan.',
        ]);
    }

    /**
     * Selesaikan pengerjaan.
     *
     * Hanya dari status `dikerjakan` — alur wajib berurutan:
     * diterima → dikerjakan → selesai.
     */
    public function selesai(Request $request, Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->status !== Pesanan::STATUS_DIKERJAKAN) {
            return $this->tolakAksi(
                "Pesanan {$pesanan->kode_pesanan} belum mulai dikerjakan. "
                .'Tandai "Mulai Kerjakan" terlebih dahulu sebelum menyelesaikan.'
            );
        }

        DB::transaction(function () use ($request, $pesanan) {
            $pesanan->update(['status' => Pesanan::STATUS_SELESAI]);

            // Tutup penugasan yang masih berjalan.
            $pesanan->pengerjaan()
                ->whereNull('waktu_selesai')
                ->update(['waktu_selesai' => now()]);

            $this->bookingService->catatRiwayat(
                $pesanan,
                Pesanan::STATUS_SELESAI,
                $request->user()->id_user,
                'Pengerjaan selesai, kendaraan siap diambil.',
            );

            $this->kirimNotifikasi(
                $pesanan,
                'Servis Selesai',
                "Pesanan {$pesanan->kode_pesanan} telah selesai dikerjakan. Silakan cek nota pembayaran.",
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan ditandai selesai.',
        ]);
    }

    /**
     * Kembalikan kuota slot bila pesanan memakai slot.
     *
     * Dikunci dan dijaga agar terisi tidak pernah negatif, sama seperti
     * logika pembatalan dari sisi mobile.
     */
    private function kembalikanKuota(Pesanan $pesanan): void
    {
        if ($pesanan->id_slot === null) {
            return;
        }

        $slot = $pesanan->slot()->lockForUpdate()->first();

        if ($slot !== null && $slot->terisi > 0) {
            $slot->decrement('terisi');
        }
    }

    private function kirimNotifikasi(Pesanan $pesanan, string $judul, string $pesan): void
    {
        $pesanan->notifikasi()->create([
            'judul' => $judul,
            'pesan' => $pesan,
            'waktu_kirim' => now(),
            'dibaca' => false,
        ]);
    }

    private function tolakAksi(string $pesan): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $pesan], 422);
    }

    /**
     * Jumlah pesanan per status, untuk badge di sidebar/daftar.
     *
     * @return array<string, int>
     */
    private function jumlahPerStatus(): array
    {
        $hitungan = Pesanan::selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->all();

        $lengkap = [];
        foreach ([
            Pesanan::STATUS_MENUNGGU,
            Pesanan::STATUS_DITERIMA,
            Pesanan::STATUS_DIKERJAKAN,
            Pesanan::STATUS_SELESAI,
            Pesanan::STATUS_DITOLAK,
            Pesanan::STATUS_DIBATALKAN,
        ] as $status) {
            $lengkap[$status] = (int) ($hitungan[$status] ?? 0);
        }

        return $lengkap;
    }
}
