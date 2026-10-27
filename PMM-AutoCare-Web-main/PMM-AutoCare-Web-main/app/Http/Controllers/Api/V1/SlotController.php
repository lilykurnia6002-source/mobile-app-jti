<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexSlotRequest;
use App\Http\Requests\Api\V1\TanggalTersediaRequest;
use App\Http\Resources\Api\V1\SlotResource;
use App\Http\Responses\ApiResponse;
use App\Models\SlotJadwal;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * Slot jadwal servis (konsep_api.md § 5) — hanya baca.
 *
 * Slot dibuat dan diatur Admin lewat website; API mobile tidak pernah
 * membuat atau mengubah slot. Seluruh endpoint di sini memakai scope
 * tersedia() sehingga aturan gate § 1.4 konsisten dengan validasi booking.
 */
class SlotController extends Controller
{
    /**
     * GET /api/v1/slot/tanggal-tersedia — tanggal yang masih punya slot lolos gate.
     */
    public function tanggalTersedia(TanggalTersediaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $dari = isset($data['dari']) ? Carbon::parse($data['dari']) : today();
        $sampai = isset($data['sampai']) ? Carbon::parse($data['sampai']) : today()->addDays(30);

        $tanggal = SlotJadwal::tersedia()
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->map(fn ($t) => Carbon::parse($t)->format('Y-m-d'))
            ->unique()
            ->values();

        return ApiResponse::success([
            'dari' => $dari->format('Y-m-d'),
            'sampai' => $sampai->format('Y-m-d'),
            'tanggal_tersedia' => $tanggal,
        ], $tanggal->isEmpty() ? 'Belum ada jadwal yang tersedia' : 'Tanggal tersedia');
    }

    /**
     * GET /api/v1/slot?tanggal=YYYY-MM-DD
     */
    public function index(IndexSlotRequest $request): JsonResponse
    {
        $tanggal = $request->validated('tanggal');

        $slot = SlotJadwal::tersedia()
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get();

        return ApiResponse::success(
            SlotResource::collection($slot),
            $slot->isEmpty()
                ? "Tidak ada slot tersedia pada {$tanggal}"
                : "Slot tersedia pada {$tanggal}",
        );
    }

    /**
     * GET /api/v1/slot/{id}
     *
     * Slot yang ada tetapi tidak lolos gate (ditutup/lewat/penuh) tetap
     * ditolak dengan SLOT_TIDAK_TERSEDIA, bukan dikembalikan.
     */
    public function show(int $id): JsonResponse
    {
        $slot = SlotJadwal::find($id);

        if ($slot === null) {
            throw new ApiException('Slot tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
        }

        $alasan = $slot->alasanTidakTersedia();

        if ($alasan !== null) {
            throw ApiException::slotTidakTersedia($alasan);
        }

        return ApiResponse::success(new SlotResource($slot), 'Detail slot');
    }
}
