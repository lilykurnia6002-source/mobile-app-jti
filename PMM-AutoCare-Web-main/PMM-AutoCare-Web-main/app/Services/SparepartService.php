<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Pesanan;
use App\Models\Sparepart;
use Illuminate\Support\Facades\DB;

/**
 * Pencatatan pemakaian sparepart pada pesanan.
 *
 * Sesuai konsep_pemrograman.md § 4.5: server yang memvalidasi dan mengurangi
 * stok. Seluruh perubahan dibungkus transaksi dan mengunci baris sparepart
 * agar dua pencatatan bersamaan tidak membuat stok menjadi minus.
 */
class SparepartService
{
    /**
     * Catat pemakaian sparepart pada pesanan.
     *
     * Stok berkurang dan total_biaya pesanan ikut dihitung ulang.
     *
     * @throws ApiException bila sparepart tidak ada atau stok tidak cukup
     */
    public function catatPemakaian(Pesanan $pesanan, int $idSparepart, int $jumlah): Sparepart
    {
        return DB::transaction(function () use ($pesanan, $idSparepart, $jumlah) {
            $sparepart = Sparepart::whereKey($idSparepart)->lockForUpdate()->first();

            if ($sparepart === null) {
                throw new ApiException('Sparepart tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
            }

            if ($sparepart->stok < $jumlah) {
                throw new ApiException(
                    "Stok {$sparepart->nama} tidak cukup. Tersedia {$sparepart->stok} {$sparepart->satuan}.",
                    'STOK_TIDAK_CUKUP',
                    422,
                    ['kode' => 'STOK_TIDAK_CUKUP'],
                );
            }

            $pesanan->detailSparepart()->create([
                'id_sparepart' => $sparepart->id_sparepart,
                'jumlah' => $jumlah,
                // Snapshot harga saat dipakai, agar perubahan harga master
                // tidak mengubah nota pesanan yang sudah berjalan.
                'harga' => $sparepart->harga,
                'subtotal' => (float) $sparepart->harga * $jumlah,
            ]);

            $sparepart->decrement('stok', $jumlah);

            $this->hitungUlangTotal($pesanan);

            return $sparepart;
        });
    }

    /**
     * Batalkan pemakaian sparepart — stok dikembalikan ke persediaan.
     *
     * @throws ApiException bila baris pemakaian tidak ditemukan
     */
    public function hapusPemakaian(Pesanan $pesanan, int $idDetail): void
    {
        DB::transaction(function () use ($pesanan, $idDetail) {
            $detail = $pesanan->detailSparepart()
                ->whereKey($idDetail)
                ->lockForUpdate()
                ->first();

            if ($detail === null) {
                throw new ApiException(
                    'Data pemakaian sparepart tidak ditemukan pada pesanan ini.',
                    'NOT_FOUND',
                    404,
                    ['kode' => 'NOT_FOUND'],
                );
            }

            $sparepart = Sparepart::whereKey($detail->id_sparepart)->lockForUpdate()->first();

            if ($sparepart !== null) {
                $sparepart->increment('stok', $detail->jumlah);
            }

            $detail->delete();

            $this->hitungUlangTotal($pesanan);
        });
    }

    /**
     * Hitung ulang total_biaya pesanan dari seluruh rinciannya.
     *
     * Sengaja dihitung ulang dari sumbernya, bukan increment/decrement,
     * supaya nilainya tidak pernah melenceng bila ada operasi yang gagal
     * di tengah jalan.
     */
    public function hitungUlangTotal(Pesanan $pesanan): void
    {
        $total = (float) $pesanan->detailLayanan()->sum('subtotal')
            + (float) $pesanan->detailSparepart()->sum('subtotal');

        $pesanan->update(['total_biaya' => $total]);
    }
}
