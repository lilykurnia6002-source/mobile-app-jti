<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MekanikController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\SparepartController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Website — Panel Admin & Pemilik Bengkel
|--------------------------------------------------------------------------
|
| Panel ini dipakai role admin dan pemilik. Pelanggan tidak punya akses ke
| website — satu-satunya jembatan mereka ke sistem adalah REST API /api/v1
| (lihat routes/api.php).
|
| Data yang ditampilkan di sini berasal dari basis data yang sama dengan
| aplikasi mobile, sehingga antrean servis selalu sinkron.
|
*/

Route::get('/', fn () => redirect()->route('admin.dashboard'));

// --- Autentikasi ------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login.panel');
});

// --- Panel Admin ------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    // Logout cukup butuh auth, tidak butuh role admin (mis. sesi pelanggan
    // yang tersangkut tetap bisa keluar dengan bersih).
    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Manajemen slot & jadwal servis — prasyarat booking dari mobile.
        Route::get('/slot', [SlotController::class, 'index'])->name('slot.index');
        Route::post('/slot', [SlotController::class, 'store'])->name('slot.store');
        Route::put('/slot/{slot}', [SlotController::class, 'update'])->name('slot.update');
        Route::patch('/slot/{slot}/toggle', [SlotController::class, 'toggle'])->name('slot.toggle');
        // Hapus jadwal slot. Membuka kembali slot dilakukan lewat modal edit.
        Route::delete('/slot/{slot}', [SlotController::class, 'destroy'])->name('slot.destroy');

        // Verifikasi pesanan & penugasan mekanik.
        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::post('/pesanan/{pesanan}/terima', [PesananController::class, 'terima'])->name('pesanan.terima');
        Route::post('/pesanan/{pesanan}/tolak', [PesananController::class, 'tolak'])->name('pesanan.tolak');
        // Alur berurutan: diterima → dikerjakan → selesai.
        Route::patch('/pesanan/{pesanan}/kerjakan', [PesananController::class, 'kerjakan'])->name('pesanan.kerjakan');
        Route::patch('/pesanan/{pesanan}/selesai', [PesananController::class, 'selesai'])->name('pesanan.selesai');

        // Pemakaian sparepart pada satu pesanan — di sinilah stok berkurang.
        Route::post('/pesanan/{pesanan}/sparepart', [PesananController::class, 'catatSparepart'])->name('pesanan.sparepart.store');
        Route::delete('/pesanan/{pesanan}/sparepart/{detail}', [PesananController::class, 'hapusSparepart'])->name('pesanan.sparepart.destroy');

        // Master data sparepart — harga & persediaan bengkel.
        Route::get('/sparepart', [SparepartController::class, 'index'])->name('sparepart.index');
        Route::post('/sparepart', [SparepartController::class, 'store'])->name('sparepart.store');
        Route::put('/sparepart/{sparepart}', [SparepartController::class, 'update'])->name('sparepart.update');
        // Hapus permanen: FK restrictOnDelete menjamin hanya yang belum pernah
        // dipakai yang bisa sampai ke baris ini.
        Route::delete('/sparepart/{sparepart}', [SparepartController::class, 'destroy'])->name('sparepart.destroy');

        // Data mekanik (tanpa akun login) — master data, dikelola admin.
        Route::get('/mekanik', [MekanikController::class, 'index'])->name('mekanik.index');
        Route::post('/mekanik', [MekanikController::class, 'store'])->name('mekanik.store');
        Route::put('/mekanik/{mekanik}', [MekanikController::class, 'update'])->name('mekanik.update');
        Route::patch('/mekanik/{mekanik}/toggle', [MekanikController::class, 'toggle'])->name('mekanik.toggle');
        // Soft delete: nama mekanik tetap tersimpan untuk riwayat & nota lama.
        Route::delete('/mekanik/{mekanik}', [MekanikController::class, 'destroy'])->name('mekanik.destroy');

        // Profil admin/pemilik yang sedang login.
        Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    });
});
