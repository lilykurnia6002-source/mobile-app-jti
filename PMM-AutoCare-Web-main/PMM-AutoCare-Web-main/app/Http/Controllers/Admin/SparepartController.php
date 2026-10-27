<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSparepartRequest;
use App\Http\Requests\Admin\UpdateSparepartRequest;
use App\Models\Sparepart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Master data sparepart bengkel: harga & persediaan.
 *
 * Pemakaian sparepart pada pesanan dicatat terpisah di halaman detail
 * pesanan (lihat PesananController::catatSparepart), karena di sanalah
 * stok berkurang.
 */
class SparepartController extends Controller
{
    public function index(): View
    {
        $sparepart = Sparepart::withCount('detailSparepart as total_pemakaian')
            ->orderBy('nama')
            ->get();

        return view('sparepart.index', [
            'sparepart' => $sparepart,
            'ringkasan' => [
                'total_item' => $sparepart->count(),
                'nilai_persediaan' => (float) $sparepart->sum(
                    fn (Sparepart $s) => (float) $s->harga * $s->stok
                ),
                'stok_menipis' => $sparepart->filter(
                    fn (Sparepart $s) => $s->stok > 0 && $s->stok <= $s->stok_minimal
                )->count(),
                'stok_habis' => $sparepart->where('stok', 0)->count(),
            ],
            // Saran satuan untuk <datalist>: tetap teks bebas.
            'saranSatuan' => $this->saranSatuan(),
        ]);
    }

    public function store(StoreSparepartRequest $request): JsonResponse
    {
        $data = $request->validated();

        $sparepart = Sparepart::create([
            'nama' => $data['nama'],
            'satuan' => $data['satuan'],
            'harga' => $data['harga'],
            'stok' => $data['stok'],
            'stok_minimal' => $data['stok_minimal'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Sparepart {$sparepart->nama} berhasil ditambahkan.",
        ]);
    }

    public function update(UpdateSparepartRequest $request, Sparepart $sparepart): JsonResponse
    {
        $data = $request->validated();

        $sparepart->update([
            'nama' => $data['nama'],
            'satuan' => $data['satuan'],
            'harga' => $data['harga'],
            'stok' => $data['stok'],
            'stok_minimal' => $data['stok_minimal'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Data sparepart {$sparepart->nama} berhasil diperbarui.",
        ]);
    }

    /**
     * Hapus sparepart dari master data.
     *
     * Hanya diizinkan bila sparepart belum pernah dipakai pada pesanan mana
     * pun, karena tb_detail_sparepart memakai FK restrictOnDelete demi menjaga
     * riwayat nota pelanggan tetap utuh.
     */
    public function destroy(Sparepart $sparepart): JsonResponse
    {
        $totalPemakaian = $sparepart->detailSparepart()->count();

        if ($totalPemakaian > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Sparepart {$sparepart->nama} sudah dipakai pada {$totalPemakaian} pesanan "
                    .'dan tidak dapat dihapus. Riwayat nota pelanggan harus tetap utuh.',
            ], 422);
        }

        $nama = $sparepart->nama;
        $sparepart->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Sparepart {$nama} berhasil dihapus.",
        ]);
    }

    /**
     * Daftar satuan yang sudah pernah dipakai, untuk saran input.
     *
     * @return Collection<int, string>
     */
    private function saranSatuan()
    {
        return Sparepart::select('satuan')
            ->distinct()
            ->orderBy('satuan')
            ->pluck('satuan')
            ->filter()
            ->values();
    }
}
