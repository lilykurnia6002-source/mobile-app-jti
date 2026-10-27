<?php

namespace Tests\Feature\Api\V1;

use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Profil pelanggan (konsep_api.md § 2.4, § 2.5).
 */
class ProfilTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->pelanggan()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        // Factory menyamakan nama pada profil; pastikan sesuai data uji.
        $this->user->pelanggan->update([
            'nama' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Melati No. 12, Malang',
        ]);
    }

    public function test_pelanggan_dapat_memuat_profil_beserta_jumlah_mobil(): void
    {
        Mobil::create([
            'id_pelanggan' => $this->user->pelanggan->id_pelanggan,
            'plat_nomor' => 'N 1234 AB',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2018,
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/profil')
            ->assertOk()
            ->assertJsonPath('message', 'Profil berhasil dimuat')
            ->assertJsonPath('data.id', $this->user->pelanggan->id_pelanggan)
            ->assertJsonPath('data.nama', 'Budi Santoso')
            ->assertJsonPath('data.email', 'budi@example.com')
            ->assertJsonPath('data.no_hp', '081234567890')
            ->assertJsonPath('data.jumlah_mobil', 1);
    }

    public function test_pelanggan_dapat_memperbarui_profil_tanpa_mengubah_password(): void
    {
        $passwordLama = $this->user->password;

        $this->actingAs($this->user)
            ->putJson('/api/v1/profil', [
                'nama' => 'Budi Santosa',
                'email' => 'budi.baru@example.com',
                'no_hp' => '081298765432',
                'alamat' => 'Jl. Anggrek No. 3, Malang',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Profil diperbarui')
            ->assertJsonPath('data.nama', 'Budi Santosa')
            ->assertJsonPath('data.email', 'budi.baru@example.com');

        // Password tidak berubah bila tidak dikirim.
        $this->assertSame($passwordLama, $this->user->fresh()->password);
    }

    public function test_password_dapat_diganti_bila_diisi(): void
    {
        $this->actingAs($this->user)
            ->putJson('/api/v1/profil', [
                'nama' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'no_hp' => '081234567890',
                'password' => 'rahasiaBaru123',
            ])
            ->assertOk();

        $this->assertTrue(Hash::check('rahasiaBaru123', $this->user->fresh()->password));
    }

    public function test_email_milik_akun_lain_ditolak(): void
    {
        User::factory()->pelanggan()->create(['email' => 'siti@example.com']);

        $this->actingAs($this->user)
            ->putJson('/api/v1/profil', [
                'nama' => 'Budi Santoso',
                'email' => 'siti@example.com',
                'no_hp' => '081234567890',
            ])
            ->assertStatus(422)
            ->assertJsonPath('data.errors.email.0', 'Kolom email sudah terdaftar.');
    }

    public function test_validasi_profil_menggunakan_pesan_indonesia(): void
    {
        $this->actingAs($this->user)
            ->putJson('/api/v1/profil', ['nama' => '', 'email' => 'bukan-email'])
            ->assertStatus(422)
            ->assertJsonPath('data.errors.nama.0', 'Kolom nama wajib diisi.')
            ->assertJsonPath('data.errors.email.0', 'Kolom email harus berupa alamat email yang valid.');
    }
}
