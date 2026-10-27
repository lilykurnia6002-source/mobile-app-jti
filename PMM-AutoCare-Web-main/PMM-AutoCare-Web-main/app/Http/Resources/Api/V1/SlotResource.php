<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Slot jadwal (konsep_api.md § 5).
 *
 * `sisa_kuota` dihitung server (kapasitas - terisi), tidak disimpan di basis
 * data — lihat scopeTersedia() pada model SlotJadwal untuk aturan gate.
 */
class SlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_slot' => $this->id_slot,
            'tanggal' => $this->tanggal->format('Y-m-d'),
            'jam_mulai' => substr((string) $this->jam_mulai, 0, 5),
            'jam_selesai' => substr((string) $this->jam_selesai, 0, 5),
            'kapasitas' => (int) $this->kapasitas,
            'terisi' => (int) $this->terisi,
            'sisa_kuota' => max(0, (int) $this->kapasitas - (int) $this->terisi),
        ];
    }
}
