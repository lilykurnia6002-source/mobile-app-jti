<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Rincian sparepart pada nota pesanan (konsep_api.md § 8.1). */
class DetailSparepartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'nama' => $this->sparepart?->nama,
            'satuan' => $this->sparepart?->satuan,
            'jumlah' => (int) $this->jumlah,
            'harga' => (float) $this->harga,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}
