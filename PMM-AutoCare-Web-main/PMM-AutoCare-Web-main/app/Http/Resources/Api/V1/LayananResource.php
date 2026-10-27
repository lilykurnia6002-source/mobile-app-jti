<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Daftar layanan bengkel (konsep_api.md § 4). */
class LayananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_layanan' => $this->id_layanan,
            'nama_layanan' => $this->nama_layanan,
            'kategori' => $this->kategori,
            'harga_jasa' => (float) $this->harga_jasa,
            'estimasi_menit' => (int) $this->estimasi_menit,
        ];
    }
}
