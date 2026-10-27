<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Slot ringkas untuk disematkan di dalam objek pesanan
 * (konsep_api.md § 6.1 dan § 6.3) — tanpa kapasitas/terisi.
 */
class SlotRingkasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_slot' => $this->id_slot,
            'tanggal' => $this->tanggal->format('Y-m-d'),
            'jam_mulai' => substr((string) $this->jam_mulai, 0, 5),
            'jam_selesai' => substr((string) $this->jam_selesai, 0, 5),
        ];
    }
}
