<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Baris daftar riwayat pesanan (konsep_api.md § 6.2).
 *
 * Data slot diratakan ke tingkat atas (tanggal_slot, jam_mulai, jam_selesai)
 * sesuai contoh dokumen, bukan sebagai objek bersarang.
 */
class PesananRingkasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pesanan' => $this->id_pesanan,
            'kode_pesanan' => $this->kode_pesanan,
            'tanggal_slot' => $this->slot?->tanggal?->format('Y-m-d'),
            'jam_mulai' => $this->slot ? substr((string) $this->slot->jam_mulai, 0, 5) : null,
            'jam_selesai' => $this->slot ? substr((string) $this->slot->jam_selesai, 0, 5) : null,
            'status' => $this->status,
            'total_biaya' => (float) $this->total_biaya,
            'waktu_pesan' => $this->waktu_pesan?->format('Y-m-d H:i:s'),
        ];
    }
}
