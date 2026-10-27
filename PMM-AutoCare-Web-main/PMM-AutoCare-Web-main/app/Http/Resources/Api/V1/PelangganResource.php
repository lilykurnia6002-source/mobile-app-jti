<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Profil pelanggan. Bentuknya dipakai oleh register, login, GET /profil,
 * dan PUT /profil (konsep_api.md § 2.1, 2.2, 2.4, 2.5).
 *
 * `id` di sini adalah id_pelanggan, sesuai contoh dokumen.
 */
class PelangganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_pelanggan,
            'nama' => $this->nama,
            'email' => $this->user?->email,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
        ];
    }
}
