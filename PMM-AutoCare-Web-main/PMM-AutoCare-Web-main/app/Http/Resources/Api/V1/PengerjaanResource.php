<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Penugasan mekanik (konsep_api.md § 6.3).
 *
 * Model Pengerjaan dipetakan menjadi `id_penugasan` + data mekanik, karena
 * dokumen menyebutnya "penugasan" pada level API.
 */
class PengerjaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_penugasan' => $this->id_pengerjaan,
            'nama_mekanik' => $this->mekanik?->nama,
            'spesialis' => $this->mekanik?->spesialis,
            'waktu_mulai' => $this->waktu_mulai?->format('Y-m-d H:i:s'),
            'waktu_selesai' => $this->waktu_selesai?->format('Y-m-d H:i:s'),
            'catatan' => $this->catatan,
        ];
    }
}
