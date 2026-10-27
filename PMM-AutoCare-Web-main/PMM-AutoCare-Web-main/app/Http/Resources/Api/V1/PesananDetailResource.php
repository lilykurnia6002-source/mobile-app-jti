<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Detail lengkap satu pesanan (konsep_api.md § 6.3).
 *
 * `pembayaran` menyertakan data bila sudah ada barisnya, dan bernilai null
 * bila belum — modul pembayaran (unggah bukti & verifikasi) belum dibangun,
 * jadi untuk sementara nilai ini biasanya null.
 */
class PesananDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pesanan' => $this->id_pesanan,
            'kode_pesanan' => $this->kode_pesanan,
            'status' => $this->status,
            'status_terakhir_diubah' => $this->riwayatStatus
                ->last()?->waktu?->format('Y-m-d H:i:s'),
            'keluhan' => $this->keluhan,
            'mobil' => $this->mobil ? new MobilResource($this->mobil) : null,
            'slot' => $this->slot ? new SlotRingkasResource($this->slot) : null,
            'detail_layanan' => DetailLayananResource::collection($this->detailLayanan),
            'detail_sparepart' => DetailSparepartResource::collection($this->detailSparepart),
            'total_biaya' => (float) $this->total_biaya,
            'pembayaran' => $this->pembayaran ? [
                'id_pembayaran' => $this->pembayaran->id_pembayaran,
                'status_bayar' => $this->pembayaran->status_bayar,
                'jumlah' => (float) $this->pembayaran->jumlah,
                'metode' => $this->pembayaran->metode?->nama_metode,
            ] : null,
            'penugasan' => PengerjaanResource::collection($this->pengerjaan),
            'dapat_dibatalkan' => $this->dapatDibatalkan(),
        ];
    }
}
