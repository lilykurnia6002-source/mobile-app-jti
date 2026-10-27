<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\JenisLayanan;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\RiwayatStatus;
use App\Models\SlotJadwal;
use Illuminate\Support\Facades\DB;

/**
 * Intelisensi inti proses booking servis.
 *
 * Dipisahkan dari controller agar aturan gate slot, kuota, dan perhitungan
 * biaya hanya ditulis SEKALI dan dapat dipakai ulang oleh endpoint admin
 * (walk-in / pesanan manual) pada fase website.
 */
class BookingService
{
    /**
     * Buat pesanan baru secara atomik.
     *
     * Seluruh langkah dibungkus satu transaksi: validasi gate slot dengan
     * lockForUpdate(), insert pesanan + rincian layanan, penambahan kuota
     * slot, dan pencatatan riwayat status pertama.
     *
     * @param  array{id_mobil:int, id_slot:int, keluhan:string, detail_layanan:array<int, array{id_layanan:int, jumlah:int}>}  $data
     */
    public function buatPesanan(Pelanggan $pelanggan, array $data): Pesanan
    {
        return DB::transaction(function () use ($pelanggan, $data) {
            // 1. Kunci baris slot agar dua permintaan bersamaan tidak bisa
            //    membaca `terisi` yang sama lalu sama-sama lolos gate.
            $slot = SlotJadwal::whereKey($data['id_slot'])->lockForUpdate()->first();

            if ($slot === null) {
                throw new ApiException('Slot tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
            }

            // 2. Gate slot: status, tanggal, kuota.
            $alasan = $slot->alasanTidakTersedia();

            if ($alasan !== null) {
                throw ApiException::slotTidakTersedia($alasan);
            }

            // 3. Mobil wajib milik pelanggan yang login.
            $mobil = Mobil::where('id_mobil', $data['id_mobil'])
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->first();

            if ($mobil === null) {
                throw ApiException::mobilTidakTersedia();
            }

            // 4. Ambil layanan dari master sekali jalan, lalu susun rincian
            //    beserta snapshot tarifnya.
            $idLayanan = collect($data['detail_layanan'])->pluck('id_layanan')->all();
            $master = JenisLayanan::whereIn('id_layanan', $idLayanan)->get()->keyBy('id_layanan');

            $rincian = [];
            $total = 0.0;

            foreach ($data['detail_layanan'] as $item) {
                $layanan = $master->get($item['id_layanan']);

                // Terjaga oleh validasi exists, ini hanya jaring pengaman.
                if ($layanan === null) {
                    throw new ApiException('Layanan tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
                }

                $subtotal = (float) $layanan->harga_jasa * (int) $item['jumlah'];
                $total += $subtotal;

                $rincian[] = [
                    'id_layanan' => $layanan->id_layanan,
                    'jumlah' => (int) $item['jumlah'],
                    'harga_jasa' => $layanan->harga_jasa,
                    'subtotal' => $subtotal,
                ];
            }

            // 5. Insert pesanan.
            $pesanan = Pesanan::create([
                'kode_pesanan' => $this->generateKodePesanan($slot->tanggal),
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_mobil' => $mobil->id_mobil,
                'id_slot' => $slot->id_slot,
                'keluhan' => $data['keluhan'],
                'status' => Pesanan::STATUS_MENUNGGU,
                'kanal_pesan' => Pesanan::KANAL_MOBILE,
                'total_biaya' => $total,
                'waktu_pesan' => now(),
            ]);

            // 6. Insert rincian layanan + hitung total.
            $pesanan->detailLayanan()->createMany($rincian);

            // 7. Tambah pemakaian kuota slot.
            $slot->increment('terisi');

            // 8. Riwayat status pertama.
            $this->catatRiwayat(
                $pesanan,
                Pesanan::STATUS_MENUNGGU,
                $pelanggan->user?->id_user,
                'Pesanan diajukan melalui aplikasi mobile.',
            );

            return $pesanan->load(['slot', 'detailLayanan.layanan']);
        });
    }

    /**
     * Kode pesanan berformat PMM-YYYYMMDD-NNN, urut per hari.
     *
     * Nomor urut dicari dari pesanan dengan prefix tanggal yang sama.
     * Index unik pada kode_pesanan menjadi pengaman terakhir: bila dua
     * transaksi benar-benar bersamaan, UniqueConstraintViolationException
     * akan menggagalkan salah satunya — pemanggil boleh mencoba lagi.
     */
    public function generateKodePesanan(\DateTimeInterface $tanggal): string
    {
        $prefix = 'PMM-'.$tanggal->format('Ymd').'-';

        $terakhir = Pesanan::where('kode_pesanan', 'like', $prefix.'%')
            ->orderByDesc('kode_pesanan')
            ->value('kode_pesanan');

        $urutan = $terakhir === null ? 1 : ((int) substr($terakhir, -3)) + 1;

        return $prefix.str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Catat satu baris riwayat status.
     */
    public function catatRiwayat(
        Pesanan $pesanan,
        string $status,
        ?int $idUser = null,
        ?string $keterangan = null,
    ): RiwayatStatus {
        return $pesanan->riwayatStatus()->create([
            'status' => $status,
            'keterangan' => $keterangan,
            'diubah_oleh' => $idUser,
            'waktu' => now(),
        ]);
    }
}
