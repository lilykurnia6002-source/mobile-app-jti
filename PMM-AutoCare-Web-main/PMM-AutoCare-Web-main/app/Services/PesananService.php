<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

/**
 * Perubahan status pesanan yang dipicu pelanggan dari aplikasi mobile.
 *
 * Perubahan status oleh admin (terima/tolak/tugaskan mekanik/selesai)
 * akan ditambahkan pada fase website dan tetap memakai BookingService::catatRiwayat().
 */
class PesananService
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Batalkan pesanan milik pelanggan.
     *
     * Hanya diizinkan selama status masih `menunggu` (sebelum admin menerima).
     * Bersifat idempoten: memanggil ulang pesanan yang sudah `dibatalkan`
     * tetap mengembalikan sukses, sehingga klik ganda di app tidak error.
     *
     * @throws ApiException
     */
    public function batalkan(Pesanan $pesanan, ?int $idUser = null): Pesanan
    {
        return DB::transaction(function () use ($pesanan, $idUser) {
            // Kunci baris agar dua permintaan batal bersamaan tidak
            // sama-sama mengembalikan kuota slot.
            $segar = Pesanan::whereKey($pesanan->id_pesanan)->lockForUpdate()->firstOrFail();

            if ($segar->status === Pesanan::STATUS_DIBATALKAN) {
                return $segar;
            }

            if (! $segar->dapatDibatalkan()) {
                throw ApiException::pesananTidakDapatDibatalkan($segar->status);
            }

            $segar->update(['status' => Pesanan::STATUS_DIBATALKAN]);

            // Kembalikan kuota slot bila pesanan ini memakai slot.
            if ($segar->id_slot !== null) {
                $slot = $segar->slot()->lockForUpdate()->first();

                if ($slot !== null && $slot->terisi > 0) {
                    $slot->decrement('terisi');
                }
            }

            $this->bookingService->catatRiwayat(
                $segar,
                Pesanan::STATUS_DIBATALKAN,
                $idUser,
                'Pesanan dibatalkan oleh pelanggan.',
            );

            return $segar->load(['slot', 'detailLayanan.layanan']);
        });
    }
}
