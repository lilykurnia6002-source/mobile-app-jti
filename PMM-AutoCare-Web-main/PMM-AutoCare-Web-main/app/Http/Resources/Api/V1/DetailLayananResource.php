<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Rincian layanan pada pesanan (konsep_api.md § 6.1, § 6.3).
 *
 * `harga_jasa` dibaca dari snapshot di tb_detail_layanan, bukan dari master
 * jenis_layanan, agar nilai pesanan lama tidak ikut berubah bila tarif naik.
 */
class DetailLayananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_layanan' => $this->id_layanan,
            'nama_layanan' => $this->layanan?->nama_layanan,
            'jumlah' => (int) $this->jumlah,
            'harga_jasa' => (float) $this->harga_jasa,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}
