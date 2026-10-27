<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\PelangganResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Autentikasi mobile (konsep_api.md § 2).
 *
 * Hanya akun ber-role `pelanggan` yang boleh memakai API ini; admin dan
 * pemilik memakai session pada website.
 */
class AuthController extends Controller
{
    /**
     * POST /api/v1/auth/register — buat akun + profil pelanggan sekaligus.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::ROLE_PELANGGAN,
                'no_hp' => $data['no_hp'],
            ]);

            $user->pelanggan()->create([
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'] ?? null,
            ]);

            return $user;
        });

        return ApiResponse::success([
            'token' => $user->createToken('mobile')->plainTextToken,
            'pelanggan' => new PelangganResource($user->pelanggan()->with('user')->firstOrFail()),
        ], 'Registrasi berhasil', 201);
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        // Password disimpan apa adanya (tanpa hash) untuk keperluan demo,
        // jadi dibandingkan langsung — lihat App\Models\User::casts().
        // Pada aplikasi produksi gunakan Hash::check() di sini.
        if ($user === null || $user->password !== $data['password']) {
            // Pesan sengaja tidak membedakan email salah vs password salah.
            return ApiResponse::error('Email atau password salah', 401, ['kode' => 'UNAUTHENTICATED']);
        }

        if (! $user->isPelanggan()) {
            return ApiResponse::error(
                'Akun ini tidak terdaftar sebagai pelanggan',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        }

        $pelanggan = $user->pelanggan()->with('user')->first();

        if ($pelanggan === null) {
            return ApiResponse::error(
                'Profil pelanggan tidak ditemukan. Hubungi bengkel.',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        }

        return ApiResponse::success([
            'token' => $user->createToken('mobile')->plainTextToken,
            'pelanggan' => new PelangganResource($pelanggan),
        ], 'Login berhasil');
    }

    /**
     * POST /api/v1/auth/logout — cabut token yang sedang dipakai.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logout berhasil');
    }
}
