<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Data mobil pelanggan (konsep_api.md § 3).
 *
 * `id` di sini adalah id_mobil.
 */
class MobilResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_mobil,
            'plat_nomor' => $this->plat_nomor,
            'merk' => $this->merk,
            'tipe' => $this->tipe,
            'tahun' => (int) $this->tahun,
        ];
    }
}
