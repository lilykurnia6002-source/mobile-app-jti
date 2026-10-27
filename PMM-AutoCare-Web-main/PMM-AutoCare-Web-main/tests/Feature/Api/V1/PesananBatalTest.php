<?php

namespace Tests\Feature\Api\V1;

use App\Models\JenisLayanan;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\SlotJadwal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pembatalan pesanan oleh pelanggan (PATCH /pesanan/{id}/batal).
 *
 * Boleh dilakukan selama status masih `menunggu`; kuota slot dikembalikan
 * dan pembatalan tercatat pada riwayat status. Bersifat idempoten.
 */
class PesananBatalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Pelanggan $pelanggan;

    private Mobil $mobil;

    private SlotJadwal $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->pelanggan()->create();
        $this->pelanggan = $this->user->pelanggan;

        $this->mobil = Mobil::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'plat_nomor' => 'N 1234 AB',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2018,
        ]);

        $this->slot = SlotJadwal::create([
            'tanggal' => today()->addDays(2)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kapasitas' => 3,
            'terisi' => 0,
            'status' => SlotJadwal::STATUS_DIBUKA,
        ]);

        JenisLayanan::create([
            'nama_layanan' => 'Ganti Oli Mesin',
            'harga_jasa' => 150000,
            'estimasi_menit' => 45,
        ]);
    }

    private function buatPesanan(): Pesanan
    {
        $id = $this->actingAs($this->user)->postJson('/api/v1/pesanan', [
            'id_mobil' => $this->mobil->id_mobil,
            'id_slot' => $this->slot->id_slot,
            'keluhan' => 'Servis rutin.',
            'detail_layanan' => [['id_layanan' => 1, 'jumlah' => 1]],
        ])->json('data.id_pesanan');

        return Pesanan::findOrFail($id);
    }

    public function test_pelanggan_dapat_membatalkan_pesanan_yang_masih_menunggu(): void
    {
        $pesanan = $this->buatPesanan();
        $this->assertSame(1, $this->slot->fresh()->terisi);

        $this->actingAs($this->user)
            ->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Pesanan berhasil dibatalkan')
            ->assertJsonPath('data.status', Pesanan::STATUS_DIBATALKAN);

        $this->assertSame(Pesanan::STATUS_DIBATALKAN, $pesanan->fresh()->status);
    }

    public function test_pembatalan_mengembalikan_kuota_slot(): void
    {
        $pesanan = $this->buatPesanan();
        $this->assertSame(1, $this->slot->fresh()->terisi);

        $this->actingAs($this->user)->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")->assertOk();

        // Kuota kembali seperti semula.
        $this->assertSame(0, $this->slot->fresh()->terisi);
    }

    public function test_pembatalan_mencatat_riwayat_status(): void
    {
        $pesanan = $this->buatPesanan();

        $this->actingAs($this->user)->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")->assertOk();

        $this->assertDatabaseHas('tb_riwayat_status', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status' => Pesanan::STATUS_DIBATALKAN,
        ]);
    }

    public function test_pembatalan_bersifat_idempoten(): void
    {
        $pesanan = $this->buatPesanan();

        $this->actingAs($this->user)->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")->assertOk();

        // Panggilan kedua tetap sukses dan tidak mengembalikan kuota dua kali.
        $this->actingAs($this->user)
            ->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")
            ->assertOk()
            ->assertJsonPath('data.status', Pesanan::STATUS_DIBATALKAN);

        $this->assertSame(0, $this->slot->fresh()->terisi);
        $this->assertDatabaseCount('tb_riwayat_status', 2); // menunggu + dibatalkan
    }

    public function test_pesanan_yang_sudah_diterima_tidak_dapat_dibatalkan(): void
    {
        $pesanan = $this->buatPesanan();
        $pesanan->update(['status' => Pesanan::STATUS_DITERIMA]);

        $this->actingAs($this->user)
            ->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'PESANAN_TIDAK_DAPAT_DIBATALKAN')
            ->assertJsonPath('data.status_saat_ini', Pesanan::STATUS_DITERIMA);

        $this->assertSame(Pesanan::STATUS_DITERIMA, $pesanan->fresh()->status);
        // Kuota tidak dikembalikan karena pembatalan gagal.
        $this->assertSame(1, $this->slot->fresh()->terisi);
    }

    public function test_pelanggan_tidak_dapat_membatalkan_pesanan_orang_lain(): void
    {
        $pesanan = $this->buatPesanan();

        $lain = User::factory()->pelanggan()->create();

        $this->actingAs($lain)
            ->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")
            ->assertStatus(403)
            ->assertJsonPath('data.kode', 'FORBIDDEN');

        $this->assertSame(Pesanan::STATUS_MENUNGGU, $pesanan->fresh()->status);
    }

    public function test_kuota_tidak_pernah_negatif(): void
    {
        $pesanan = $this->buatPesanan();

        // Simulasi data tidak konsisten: terisi sudah 0.
        $this->slot->update(['terisi' => 0]);

        $this->actingAs($this->user)->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")->assertOk();

        $this->assertSame(0, $this->slot->fresh()->terisi);
    }

    public function test_slot_hasil_pembatalan_dapat_dipesan_kembali(): void
    {
        // Kapasitas 1 → setelah dibatalkan, slot harus tersedia lagi.
        $this->slot->update(['kapasitas' => 1]);

        $pesanan = $this->buatPesanan();
        $this->actingAs($this->user)->patchJson("/api/v1/pesanan/{$pesanan->id_pesanan}/batal")->assertOk();

        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', [
                'id_mobil' => $this->mobil->id_mobil,
                'id_slot' => $this->slot->id_slot,
                'keluhan' => 'Booking ulang setelah batal.',
                'detail_layanan' => [['id_layanan' => 1, 'jumlah' => 1]],
            ])
            ->assertCreated();
    }
}
