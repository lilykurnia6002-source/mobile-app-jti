<?php

namespace App\Providers;

use App\Models\Mekanik;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureTanggalIndonesia();
        $this->configureDataBersamaSidebar();
    }

    /**
     * Nama hari/bulan berbahasa Indonesia untuk tanggal di panel admin.
     */
    private function configureTanggalIndonesia(): void
    {
        Carbon::setLocale('id');
    }

    /**
     * Angka yang dipakai bersama seluruh halaman panel admin: badge antrean
     * pada blueprint sidebar dan status mekanik pada kotak informasi.
     *
     * Didaftarkan sebagai view composer agar tidak perlu diulang di setiap
     * controller: DashboardController, SlotController, PesananController,
     * MekanikController, dan ProfilController.
     */
    private function configureDataBersamaSidebar(): void
    {
        // Sengaja hanya view halaman (bukan '*') agar partial seperti header,
        // footer, dan alert tidak ikut menjalankan query berulang.
        ViewFacade::composer([
            'index',
            'schedule.index',
            'pesanan.index',
            'pesanan.show',
            'mekanik.index',
            'profil.index',
        ], function (View $view) {
            $view->with([
                'jumlahMenunggu' => Pesanan::where('status', Pesanan::STATUS_MENUNGGU)->count(),
                'jumlahMekanik' => Mekanik::where('aktif', true)->count(),
                'jumlahBertugas' => Mekanik::where('aktif', true)
                    ->whereHas('pengerjaan.pesanan', function ($q) {
                        $q->whereIn('status', [Pesanan::STATUS_DITERIMA, Pesanan::STATUS_DIKERJAKAN]);
                    })
                    ->count(),
            ]);
        });
    }

    /**
     * Batas permintaan API. Melebihi batas ini menghasilkan 429 dengan
     * kode RATE_LIMIT (konsep_api.md § 1.5).
     *
     * Endpoint auth dibatasi lebih ketat karena rawan percobaan berulang.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id_user ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
