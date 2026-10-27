<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMekanikRequest;
use App\Http\Requests\Admin\UpdateMekanikRequest;
use App\Models\Mekanik;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Master data mekanik bengkel.
 *
 * Mekanik tidak punya akun login — seluruh datanya dikelola admin, dan
 * progresnya dicatat atas nama admin. Karena itu halaman ini murni CRUD
 * data master, bukan manajemen pengguna.
 */
class MekanikController extends Controller
{
    public function index(): View
    {
        $mekanik = Mekanik::withCount([
            'pengerjaan as tugas_berjalan' => function ($query) {
                $query->whereHas('pesanan', function ($q) {
                    $q->whereIn('status', [Pesanan::STATUS_DITERIMA, Pesanan::STATUS_DIKERJAKAN]);
                });
            },
            'pengerjaan as total_penugasan',
        ])
            ->orderBy('nama')
            ->get();

        return view('mekanik.index', [
            'mekanik' => $mekanik,
            'ringkasan' => [
                'total_mekanik' => $mekanik->count(),
                'aktif' => $mekanik->where('aktif', true)->count(),
                'bertugas' => $mekanik->where('tugas_berjalan', '>', 0)->count(),
            ],
            // Saran bidang keahlian untuk <datalist>: tetap teks bebas,
            // tetapi memudahkan admin memilih nilai yang sudah pernah dipakai.
            'saranSpesialis' => $this->saranSpesialis(),
        ]);
    }

    public function store(StoreMekanikRequest $request): JsonResponse
    {
        $data = $request->validated();

        $mekanik = Mekanik::create([
            'nama' => $data['nama'],
            'spesialis' => $data['spesialis'],
            'no_hp' => $data['no_hp'] ?? null,
            'aktif' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Mekanik {$mekanik->nama} berhasil ditambahkan.",
        ]);
    }

    public function update(UpdateMekanikRequest $request, Mekanik $mekanik): JsonResponse
    {
        $data = $request->validated();

        $mekanik->update([
            'nama' => $data['nama'],
            'spesialis' => $data['spesialis'],
            'no_hp' => $data['no_hp'] ?? null,
            'aktif' => $data['aktif'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Data mekanik {$mekanik->nama} berhasil diperbarui.",
        ]);
    }

    /**
     * Aktifkan / nonaktifkan mekanik tanpa menghapusnya.
     *
     * Mekanik nonaktif tidak muncul pada pilihan penugasan, tetapi seluruh
     * riwayat pengerjaannya tetap utuh.
     */
    public function toggle(Mekanik $mekanik): JsonResponse
    {
        $mekanik->update(['aktif' => ! $mekanik->aktif]);

        $label = $mekanik->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'status' => 'success',
            'message' => "Mekanik {$mekanik->nama} berhasil {$label}.",
        ]);
    }

    /**
     * Hapus mekanik (soft delete).
     *
     * Ditolak selama mekanik masih mengerjakan pesanan aktif, karena
     * memindahkan pekerjaan yang sedang berjalan harus jadi keputusan sadar.
     *
     * Menggunakan soft delete supaya nama mekanik tetap terbaca pada riwayat
     * pengerjaan dan nota pelanggan lama setelah ia resign.
     */
    public function destroy(Mekanik $mekanik): JsonResponse
    {
        $tugasBerjalan = $mekanik->jumlahTugasBerjalan();

        if ($tugasBerjalan > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Mekanik {$mekanik->nama} sedang mengerjakan {$tugasBerjalan} pesanan. "
                    .'Selesaikan atau alihkan tugasnya terlebih dahulu sebelum dihapus.',
            ], 422);
        }

        $nama = $mekanik->nama;
        $totalPenugasan = $mekanik->pengerjaan()->count();

        $mekanik->delete();

        $pesan = $totalPenugasan > 0
            ? "Mekanik {$nama} berhasil dihapus. {$totalPenugasan} riwayat pengerjaannya tetap tersimpan."
            : "Mekanik {$nama} berhasil dihapus.";

        return response()->json(['status' => 'success', 'message' => $pesan]);
    }

    /**
     * Daftar bidang keahlian yang sudah pernah dipakai, untuk saran input.
     *
     * @return Collection<int, string>
     */
    private function saranSpesialis()
    {
        return Mekanik::withTrashed()
            ->select('spesialis')
            ->distinct()
            ->orderBy('spesialis')
            ->pluck('spesialis')
            ->filter()
            ->values();
    }
}
