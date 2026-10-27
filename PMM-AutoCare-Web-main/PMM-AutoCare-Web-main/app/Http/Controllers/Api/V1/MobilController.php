<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMobilRequest;
use App\Http\Requests\Api\V1\UpdateMobilRequest;
use App\Http\Resources\Api\V1\MobilResource;
use App\Http\Responses\ApiResponse;
use App\Models\Mobil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Data mobil pelanggan (konsep_api.md § 3).
 *
 * Seluruh endpoint menscope ke pelanggan yang login: mobil milik pelanggan
 * lain tidak boleh terlihat maupun terubah (dijaga lewat scopePelanggan()).
 */
class MobilController extends Controller
{
    /**
     * GET /api/v1/mobil
     */
    public function index(Request $request): JsonResponse
    {
        $mobil = $this->queryPelanggan($request)->orderBy('id_mobil')->get();

        return ApiResponse::success(MobilResource::collection($mobil), 'Daftar mobil');
    }

    /**
     * POST /api/v1/mobil
     */
    public function store(StoreMobilRequest $request): JsonResponse
    {
        $mobil = Mobil::create([
            ...$request->validated(),
            'id_pelanggan' => $request->user()->pelanggan->id_pelanggan,
        ]);

        return ApiResponse::success(new MobilResource($mobil), 'Mobil berhasil ditambahkan', 201);
    }

    /**
     * PUT /api/v1/mobil/{id}
     */
    public function update(UpdateMobilRequest $request, int $id): JsonResponse
    {
        $mobil = $this->temukanMilikPelanggan($request, $id);
        $mobil->update($request->validated());

        return ApiResponse::success(new MobilResource($mobil), 'Mobil berhasil diperbarui');
    }

    /**
     * Query mobil milik pelanggan yang login.
     */
    private function queryPelanggan(Request $request)
    {
        return Mobil::where('id_pelanggan', $request->user()->pelanggan->id_pelanggan);
    }

    /**
     * Ambil mobil milik pelanggan login, atau lempar FORBIDDEN bila ada
     * tapi bukan miliknya (sesuai konsep_api.md § 3.3).
     */
    private function temukanMilikPelanggan(Request $request, int $id): Mobil
    {
        $mobil = Mobil::find($id);

        if ($mobil === null) {
            throw new ApiException('Mobil tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
        }

        if ($mobil->id_pelanggan !== $request->user()->pelanggan->id_pelanggan) {
            throw new ApiException(
                'Anda tidak berhak mengakses mobil ini.',
                'FORBIDDEN',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        }

        return $mobil;
    }
}
