<?php

namespace Tests\Feature\Api\V1;

use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Data mobil pelanggan (konsep_api.md § 3) — selalu di-scope ke pemiliknya.
 */
class MobilTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->pelanggan()->create();
    }

    private function buatMobil(User $user, string $plat = 'N 1234 AB'): Mobil
    {
        return Mobil::create([
            'id_pelanggan' => $user->pelanggan->id_pelanggan,
            'plat_nomor' => $plat,
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2018,
        ]);
    }

    public function test_pelanggan_melihat_daftar_mobilnya_sendiri(): void
    {
        $this->buatMobil($this->user, 'N 1234 AB');
        $this->buatMobil($this->user, 'N 5678 CD');

        // Mobil pelanggan lain tidak boleh muncul.
        $lain = User::factory()->pelanggan()->create();
        $this->buatMobil($lain, 'N 9999 ZZ');

        $this->actingAs($this->user)
            ->getJson('/api/v1/mobil')
            ->assertOk()
            ->assertJsonPath('message', 'Daftar mobil')
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'plat_nomor', 'merk', 'tipe', 'tahun']]]);
    }

    public function test_pelanggan_dapat_menambah_mobil(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/mobil', [
                'plat_nomor' => 'N 9999 EF',
                'merk' => 'Suzuki',
                'tipe' => 'Ertiga',
                'tahun' => 2021,
            ])
            ->assertCreated()
            ->assertJsonPath('message', 'Mobil berhasil ditambahkan')
            ->assertJsonPath('data.plat_nomor', 'N 9999 EF')
            ->assertJsonPath('data.tahun', 2021);

        $this->assertDatabaseHas('tb_mobil', [
            'plat_nomor' => 'N 9999 EF',
            'id_pelanggan' => $this->user->pelanggan->id_pelanggan,
        ]);
    }

    public function test_plat_nomor_duplikat_ditolak(): void
    {
        $this->buatMobil($this->user, 'N 1234 AB');

        $this->actingAs($this->user)
            ->postJson('/api/v1/mobil', [
                'plat_nomor' => 'N 1234 AB',
                'merk' => 'Honda',
                'tipe' => 'Brio',
                'tahun' => 2020,
            ])
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'UNPROCESSABLE_CONTENT')
            ->assertJsonPath('data.errors.plat_nomor.0', 'Kolom plat nomor sudah terdaftar.');
    }

    public function test_pelanggan_dapat_mengubah_mobilnya(): void
    {
        $mobil = $this->buatMobil($this->user);

        $this->actingAs($this->user)
            ->putJson("/api/v1/mobil/{$mobil->id_mobil}", [
                'plat_nomor' => 'N 1234 AB',
                'merk' => 'Toyota',
                'tipe' => 'Avanza',
                'tahun' => 2022,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Mobil berhasil diperbarui')
            ->assertJsonPath('data.tahun', 2022);
    }

    public function test_mobil_tidak_ada_menghasilkan_not_found(): void
    {
        $this->actingAs($this->user)
            ->putJson('/api/v1/mobil/99999', [
                'plat_nomor' => 'N 1111 AA',
                'merk' => 'Toyota',
                'tipe' => 'Avanza',
                'tahun' => 2020,
            ])
            ->assertStatus(404)
            ->assertJsonPath('data.kode', 'NOT_FOUND');
    }

    public function test_mobil_milik_pelanggan_lain_menghasilkan_forbidden(): void
    {
        $lain = User::factory()->pelanggan()->create();
        $mobilLain = $this->buatMobil($lain, 'N 5555 YY');

        $this->actingAs($this->user)
            ->putJson("/api/v1/mobil/{$mobilLain->id_mobil}", [
                'plat_nomor' => 'N 5555 YY',
                'merk' => 'Honda',
                'tipe' => 'Brio',
                'tahun' => 2021,
            ])
            ->assertStatus(403)
            ->assertJsonPath('data.kode', 'FORBIDDEN');

        // Data tidak berubah.
        $this->assertSame('Toyota', $mobilLain->fresh()->merk);
    }

    public function test_tahun_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/mobil', [
                'plat_nomor' => 'N 8888 QQ',
                'merk' => 'Suzuki',
                'tipe' => 'Ertiga',
                'tahun' => 1800,
            ])
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'UNPROCESSABLE_CONTENT');
    }
}
