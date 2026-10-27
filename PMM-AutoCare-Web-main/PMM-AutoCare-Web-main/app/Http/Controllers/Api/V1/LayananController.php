<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LayananResource;
use App\Http\Responses\ApiResponse;
use App\Models\JenisLayanan;
use Illuminate\Http\JsonResponse;

/**
 * Daftar layanan bengkel (konsep_api.md § 4) — dapat dibaca tanpa token,
 * karena dibutuhkan saat pelanggan menjelajah sebelum mendaftar.
 */
class LayananController extends Controller
{
    /**
     * GET /api/v1/layanan
     */
    public function index(): JsonResponse
    {
        $layanan = JenisLayanan::where('aktif', true)
            ->orderBy('id_layanan')
            ->get();

        return ApiResponse::success(LayananResource::collection($layanan), 'Daftar layanan');
    }

    /**
     * GET /api/v1/layanan/{id}
     */
    public function show(int $id): JsonResponse
    {
        $layanan = JenisLayanan::find($id);

        if ($layanan === null) {
            throw new ApiException('Layanan tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
        }

        return ApiResponse::success(new LayananResource($layanan), 'Detail layanan');
    }
}
