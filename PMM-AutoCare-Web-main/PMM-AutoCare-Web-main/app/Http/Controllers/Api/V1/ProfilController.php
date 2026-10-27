<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateProfilRequest;
use App\Http\Resources\Api\V1\PelangganResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Profil pelanggan (konsep_api.md § 2.4, § 2.5).
 *
 * Email disimpan di tb_user, data lain di tb_pelanggan — responsnya
 * digabung oleh PelangganResource.
 */
class ProfilController extends Controller
{
    /**
     * GET /api/v1/profil
     */
    public function show(Request $request): JsonResponse
    {
        $pelanggan = $request->user()->pelanggan()->with('user')->firstOrFail();

        $data = (new PelangganResource($pelanggan))->toArray($request);
        $data['jumlah_mobil'] = $pelanggan->mobil()->count();

        return ApiResponse::success($data, 'Profil berhasil dimuat');
    }

    /**
     * PUT /api/v1/profil
     */
    public function update(UpdateProfilRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $pelanggan = $user->pelanggan()->firstOrFail();

        $user->fill([
            'name' => $data['nama'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
        ]);

        // Password hanya diubah bila dikirim (opsional).
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        $pelanggan->update([
            'nama' => $data['nama'],
            'no_hp' => $data['no_hp'],
            'alamat' => $data['alamat'] ?? null,
        ]);

        return ApiResponse::success(
            new PelangganResource($pelanggan->fresh('user')),
            'Profil diperbarui',
        );
    }
}
