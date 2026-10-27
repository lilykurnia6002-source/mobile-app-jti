<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LayananController;
use App\Http\Controllers\Api\V1\MobilController;
use App\Http\Controllers\Api\V1\NotaController;
use App\Http\Controllers\Api\V1\NotifikasiController;
use App\Http\Controllers\Api\V1\PesananController;
use App\Http\Controllers\Api\V1\ProfilController;
use App\Http\Controllers\Api\V1\SlotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Mobile — PMM AutoCare (v1)
|--------------------------------------------------------------------------
|
| Kontrak lengkap: .docs/backend/konsep_api.md
|
| Satu backend dipakai bersama aplikasi mobile (role pelanggan) dan website
| (role admin/pemilik). Prefix versi menjaga kompatibilitas app lama saat
| struktur berubah. Endpoint admin/pemilik ditambahkan pada fase website.
|
| Route publik: auth/register, auth/login, layanan, slot (baca).
| Sisanya wajib menyertakan Bearer token Sanctum.
|
*/

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    // --- Publik -------------------------------------------------------
    // auth dibatasi lebih ketat (anti brute force).
    Route::middleware('throttle:auth')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::post('auth/login', [AuthController::class, 'login']);
    });

    Route::get('layanan', [LayananController::class, 'index']);
    Route::get('layanan/{id}', [LayananController::class, 'show'])->whereNumber('id');

    // Wajib didaftarkan SEBELUM slot/{id} agar tidak tertangkap sebagai {id}.
    Route::get('slot/tanggal-tersedia', [SlotController::class, 'tanggalTersedia']);
    Route::get('slot', [SlotController::class, 'index']);
    Route::get('slot/{id}', [SlotController::class, 'show'])->whereNumber('id');

    // --- Perlu token --------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('profil', [ProfilController::class, 'show']);
        Route::put('profil', [ProfilController::class, 'update']);

        Route::get('mobil', [MobilController::class, 'index']);
        Route::post('mobil', [MobilController::class, 'store']);
        Route::put('mobil/{id}', [MobilController::class, 'update'])->whereNumber('id');

        Route::post('pesanan', [PesananController::class, 'store']);
        Route::get('pesanan', [PesananController::class, 'index']);
        Route::get('pesanan/{id}', [PesananController::class, 'show'])->whereNumber('id');
        Route::get('pesanan/{id}/riwayat-status', [PesananController::class, 'riwayatStatus'])->whereNumber('id');
        Route::patch('pesanan/{id}/batal', [PesananController::class, 'batal'])->whereNumber('id');
        Route::get('pesanan/{id}/nota', [NotaController::class, 'show'])->whereNumber('id');

        Route::get('notifikasi', [NotifikasiController::class, 'index']);
        Route::patch('notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->whereNumber('id');
    });
});
