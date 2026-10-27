<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelanggan_dapat_mendaftar_dan_menerima_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Melati No. 12, Malang',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Registrasi berhasil')
            ->assertJsonPath('data.pelanggan.nama', 'Budi Santoso')
            ->assertJsonPath('data.pelanggan.email', 'budi@example.com')
            ->assertJsonStructure(['data' => ['token', 'pelanggan' => ['id', 'nama', 'email', 'no_hp', 'alamat']]]);

        // Satu akun tb_user (role pelanggan) + satu tb_pelanggan.
        $this->assertDatabaseHas('tb_user', [
            'email' => 'budi@example.com',
            'role' => User::ROLE_PELANGGAN,
        ]);
        $this->assertDatabaseCount('tb_pelanggan', 1);
    }

    public function test_email_yang_sudah_terdaftar_ditolak(): void
    {
        User::factory()->create(['email' => 'budi@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Budi Lain',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'no_hp' => '081234567890',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.kode', 'UNPROCESSABLE_CONTENT');

        // data.errors berupa OBJEK {field: [pesan]}, bukan array.
        $this->assertIsArray($response->json('data.errors'));
        $this->assertArrayHasKey('email', $response->json('data.errors'));
    }

    public function test_pelanggan_dapat_login(): void
    {
        $user = User::factory()->pelanggan()->create([
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login berhasil')
            ->assertJsonStructure(['data' => ['token', 'pelanggan' => ['id', 'nama', 'email']]]);
    }

    public function test_login_dengan_password_salah_ditolak(): void
    {
        User::factory()->pelanggan()->create([
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'password-salah',
        ])->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Email atau password salah')
            ->assertJsonPath('data.kode', 'UNAUTHENTICATED');
    }

    public function test_admin_tidak_boleh_login_lewat_api_mobile(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@pmm-auto.test',
            'password' => 'rahasia123',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@pmm-auto.test',
            'password' => 'rahasia123',
        ])->assertStatus(403)
            ->assertJsonPath('data.kode', 'FORBIDDEN');
    }

    public function test_pelanggan_dapat_logout_dan_token_dicabut(): void
    {
        $user = User::factory()->pelanggan()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout berhasil');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_endpoint_terlindungi_menolak_tanpa_token(): void
    {
        $this->getJson('/api/v1/profil')
            ->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.kode', 'UNAUTHENTICATED');
    }
}
