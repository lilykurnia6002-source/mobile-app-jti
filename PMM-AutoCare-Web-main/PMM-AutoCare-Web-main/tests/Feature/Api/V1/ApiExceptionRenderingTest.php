<?php

namespace Tests\Feature\Api\V1;

use App\Exceptions\ApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Memastikan exception domain dirender menjadi envelope JSON yang benar
 * ketika lolos ke handler global (bukan ditangkap controller).
 */
class ApiExceptionRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_exception_dirender_dengan_kode_dan_status(): void
    {
        Route::get('/api/v1/_uji/domain', function () {
            throw ApiException::slotTidakTersedia('kuota_penuh');
        });

        $this->getJson('/api/v1/_uji/domain')
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.kode', 'SLOT_TIDAK_TERSEDIA')
            ->assertJsonPath('data.alasan', 'kuota_penuh');
    }

    public function test_mobil_tidak_tersedia_membawa_errors_per_field(): void
    {
        Route::get('/api/v1/_uji/mobil', function () {
            throw ApiException::mobilTidakTersedia();
        });

        $this->getJson('/api/v1/_uji/mobil')
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'MOBIL_TIDAK_TERSEDIA')
            ->assertJsonPath('data.errors.id_mobil.0', 'Mobil tidak ditemukan pada akun Anda.');
    }

    public function test_pembayaran_lunas_memakai_status_409(): void
    {
        Route::get('/api/v1/_uji/lunas', function () {
            throw ApiException::pembayaranLunas();
        });

        $this->getJson('/api/v1/_uji/lunas')
            ->assertStatus(409)
            ->assertJsonPath('data.kode', 'PEMBAYARAN_LUNAS');
    }
}
