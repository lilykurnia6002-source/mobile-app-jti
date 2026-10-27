<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Histori perubahan status pesanan (konsep_api.md § 6.4).
 *
 * `mekanik` hanya diisi bila perubahan status berkaitan dengan penugasan
 * mekanik (mis. saat pesanan mulai dikerjakan).
 */
class RiwayatStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'status' => $this->status,
            'waktu' => $this->waktu?->format('Y-m-d H:i:s'),
        ];

        if ($this->keterangan !== null) {
            $data['keterangan'] = $this->keterangan;
        }

        return $data;
    }
}
