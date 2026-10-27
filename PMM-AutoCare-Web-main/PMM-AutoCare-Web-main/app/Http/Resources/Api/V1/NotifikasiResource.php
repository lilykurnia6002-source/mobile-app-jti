<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Satu baris notifikasi pelanggan (konsep_api.md § 7.1). */
class NotifikasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_notifikasi' => $this->id_notifikasi,
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'waktu_kirim' => $this->waktu_kirim?->format('Y-m-d H:i:s'),
            'dibaca' => (bool) $this->dibaca,
            'id_pesanan' => $this->id_pesanan,
        ];
    }
}
