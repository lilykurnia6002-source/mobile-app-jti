<?php

namespace Tests\Feature\Api\V1;

use App\Models\Mobil;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Notifikasi pelanggan (konsep_api.md § 7).
 *
 * Kepemilikan diturunkan dari pesanan induk, karena tb_notifikasi tidak
 * menyimpan id_user.
 */
class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Pesanan $pesanan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->pelanggan()->create();
        $this->pesanan = $this->buatPesananUntuk($this->user, 'N 1234 AB', 'PMM-20260920-001');
    }

    private function buatPesananUntuk(User $user, string $plat, string $kode): Pesanan
    {
        $mobil = Mobil::create([
            'id_pelanggan' => $user->pelanggan->id_pelanggan,
            'plat_nomor' => $plat,
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2018,
        ]);

        return Pesanan::create([
            'kode_pesanan' => $kode,
            'id_pelanggan' => $user->pelanggan->id_pelanggan,
            'id_mobil' => $mobil->id_mobil,
            'keluhan' => 'Servis rutin.',
            'status' => Pesanan::STATUS_MENUNGGU,
            'total_biaya' => 150000,
            'waktu_pesan' => now(),
        ]);
    }

    private function buatNotifikasi(Pesanan $pesanan, bool $dibaca = false, string $judul = 'Pesanan Diterima'): Notifikasi
    {
        return $pesanan->notifikasi()->create([
            'judul' => $judul,
            'pesan' => 'Pesanan '.$pesanan->kode_pesanan.' telah diterima.',
            'waktu_kirim' => now(),
            'dibaca' => $dibaca,
        ]);
    }

    public function test_pelanggan_melihat_notifikasi_miliknya(): void
    {
        $this->buatNotifikasi($this->pesanan);

        $this->actingAs($this->user)
            ->getJson('/api/v1/notifikasi')
            ->assertOk()
            ->assertJsonPath('message', 'Daftar notifikasi')
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [['id_notifikasi', 'judul', 'pesan', 'waktu_kirim', 'dibaca', 'id_pesanan']],
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
            ]);
    }

    public function test_notifikasi_pelanggan_lain_tidak_muncul(): void
    {
        $lain = User::factory()->pelanggan()->create();
        $pesananLain = $this->buatPesananUntuk($lain, 'N 9999 ZZ', 'PMM-20260920-002');
        $this->buatNotifikasi($pesananLain, judul: 'Punya Orang Lain');

        $this->actingAs($this->user)
            ->getJson('/api/v1/notifikasi')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_filter_belum_baca(): void
    {
        $this->buatNotifikasi($this->pesanan, dibaca: false, judul: 'Belum Dibaca');
        $this->buatNotifikasi($this->pesanan, dibaca: true, judul: 'Sudah Dibaca');

        $this->actingAs($this->user)
            ->getJson('/api/v1/notifikasi')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->actingAs($this->user)
            ->getJson('/api/v1/notifikasi?belum_baca=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.judul', 'Belum Dibaca');
    }

    public function test_pelanggan_dapat_menandai_notifikasi_dibaca(): void
    {
        $notifikasi = $this->buatNotifikasi($this->pesanan);

        $this->actingAs($this->user)
            ->patchJson("/api/v1/notifikasi/{$notifikasi->id_notifikasi}/baca")
            ->assertOk()
            ->assertJsonPath('message', 'Notifikasi ditandai dibaca')
            ->assertJsonPath('data.dibaca', true);

        $this->assertTrue($notifikasi->fresh()->dibaca);
    }

    public function test_menandai_dibaca_bersifat_idempoten(): void
    {
        $notifikasi = $this->buatNotifikasi($this->pesanan, dibaca: true);

        $this->actingAs($this->user)
            ->patchJson("/api/v1/notifikasi/{$notifikasi->id_notifikasi}/baca")
            ->assertOk()
            ->assertJsonPath('data.dibaca', true);
    }

    public function test_notifikasi_tidak_ada_menghasilkan_not_found(): void
    {
        $this->actingAs($this->user)
            ->patchJson('/api/v1/notifikasi/99999/baca')
            ->assertStatus(404)
            ->assertJsonPath('data.kode', 'NOT_FOUND');
    }

    public function test_notifikasi_milik_pelanggan_lain_menghasilkan_forbidden(): void
    {
        $lain = User::factory()->pelanggan()->create();
        $pesananLain = $this->buatPesananUntuk($lain, 'N 8888 YY', 'PMM-20260920-003');
        $notifikasiLain = $this->buatNotifikasi($pesananLain);

        $this->actingAs($this->user)
            ->patchJson("/api/v1/notifikasi/{$notifikasiLain->id_notifikasi}/baca")
            ->assertStatus(403)
            ->assertJsonPath('data.kode', 'FORBIDDEN');
    }
}
