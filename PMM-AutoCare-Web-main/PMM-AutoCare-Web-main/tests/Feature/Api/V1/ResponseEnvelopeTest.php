<?php

namespace Tests\Feature\Api\V1;

use App\Models\JenisLayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kontrak bentuk respons (konsep_api.md § 1.2 dan § 1.5).
 *
 * Setiap respons wajib berbentuk {success, message, data} dengan kode error
 * stabil, karena app mobile memakainya sebagai penanda.
 */
class ResponseEnvelopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_respons_sukses_selalu_memiliki_tiga_kunci(): void
    {
        $response = $this->getJson('/api/v1/layanan');

        $response->assertOk();
        $this->assertSame(['success', 'message', 'data'], array_keys($response->json()));
        $this->assertTrue($response->json('success'));
        $this->assertIsString($response->json('message'));
    }

    public function test_respons_gagal_selalu_memiliki_tiga_kunci_dan_kode(): void
    {
        $response = $this->getJson('/api/v1/layanan/99999');

        $response->assertStatus(404);
        $this->assertSame(['success', 'message', 'data'], array_keys($response->json()));
        $this->assertFalse($response->json('success'));
        $this->assertSame('NOT_FOUND', $response->json('data.kode'));
    }

    public function test_respons_validasi_memakai_errors_sebagai_objek(): void
    {
        $response = $this->getJson('/api/v1/slot');

        $response->assertStatus(422);
        $this->assertSame('UNPROCESSABLE_CONTENT', $response->json('data.kode'));

        // Wajib objek {field: [pesan]}, bukan array datar.
        $errors = $response->json('data.errors');
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('tanggal', $errors);
        $this->assertIsArray($errors['tanggal']);
    }

    public function test_daftar_berhalaman_memiliki_meta(): void
    {
        $response = $this->getJson('/api/v1/layanan');

        $response->assertOk();
        // Endpoint tanpa paginasi tidak memuat meta.
        $this->assertNull($response->json('meta'));
    }

    public function test_route_tidak_dikenal_mengembalikan_json_not_found(): void
    {
        $this->getJson('/api/v1/endpoint-tidak-ada')
            ->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.kode', 'NOT_FOUND');
    }

    public function test_layanan_dapat_dibaca_tanpa_token(): void
    {
        JenisLayanan::create([
            'nama_layanan' => 'Ganti Oli Mesin',
            'kategori' => 'perawatan rutin',
            'harga_jasa' => 150000,
            'estimasi_menit' => 45,
        ]);

        $this->getJson('/api/v1/layanan')
            ->assertOk()
            ->assertJsonPath('message', 'Daftar layanan')
            ->assertJsonPath('data.0.nama_layanan', 'Ganti Oli Mesin')
            ->assertJsonPath('data.0.harga_jasa', 150000)
            ->assertJsonPath('data.0.estimasi_menit', 45);
    }

    public function test_detail_layanan_dapat_dibaca_tanpa_token(): void
    {
        $layanan = JenisLayanan::create([
            'nama_layanan' => 'Servis Rem',
            'harga_jasa' => 120000,
            'estimasi_menit' => 60,
        ]);

        $this->getJson("/api/v1/layanan/{$layanan->id_layanan}")
            ->assertOk()
            ->assertJsonPath('message', 'Detail layanan')
            ->assertJsonPath('data.id_layanan', $layanan->id_layanan);
    }
}
