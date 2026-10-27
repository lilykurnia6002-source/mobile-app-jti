<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Respons pembuatan pesanan (konsep_api.md § 6.1).
 */
class PesananDibuatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pesanan' => $this->id_pesanan,
            'kode_pesanan' => $this->kode_pesanan,
            'status' => $this->status,
            'keluhan' => $this->keluhan,
            'waktu_pesan' => $this->waktu_pesan?->format('Y-m-d H:i:s'),
            'slot' => $this->slot ? new SlotRingkasResource($this->slot) : null,
            'detail_layanan' => DetailLayananResource::collection($this->detailLayanan),
            'total_biaya' => (float) $this->total_biaya,
        ];
    }
}
