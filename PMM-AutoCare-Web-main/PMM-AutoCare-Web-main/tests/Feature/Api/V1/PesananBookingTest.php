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
 * Booking servis (konsep_api.md § 6.1) — endpoint pusat sistem.
 */
class PesananBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Pelanggan $pelanggan;

    private Mobil $mobil;

    private SlotJadwal $slot;

    private JenisLayanan $oli;

    private JenisLayanan $rem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->pelanggan()->create(['email' => 'budi@example.com']);
        $this->pelanggan = $this->user->pelanggan;

        $this->mobil = Mobil::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'plat_nomor' => 'N 1234 AB',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2018,
        ]);

        $this->slot = SlotJadwal::create([
            'tanggal' => today()->addDays(3)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kapasitas' => 1,
            'terisi' => 0,
            'status' => SlotJadwal::STATUS_DIBUKA,
        ]);

        $this->oli = JenisLayanan::create([
            'nama_layanan' => 'Ganti Oli Mesin',
            'harga_jasa' => 150000,
            'estimasi_menit' => 45,
        ]);

        $this->rem = JenisLayanan::create([
            'nama_layanan' => 'Servis Rem',
            'harga_jasa' => 120000,
            'estimasi_menit' => 60,
        ]);
    }

    private function payload(array $override = []): array
    {
        return [
            'id_mobil' => $this->mobil->id_mobil,
            'id_slot' => $this->slot->id_slot,
            'keluhan' => 'Mesin bergetar saat idling, oli sudah 6000 km',
            'detail_layanan' => [
                ['id_layanan' => $this->oli->id_layanan, 'jumlah' => 1],
                ['id_layanan' => $this->rem->id_layanan, 'jumlah' => 1],
            ],
            ...$override,
        ];
    }

    public function test_pelanggan_dapat_mengajukan_pesanan(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload());

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Pesanan berhasil diajukan')
            ->assertJsonPath('data.status', Pesanan::STATUS_MENUNGGU)
            // total 150000 + 120000
            ->assertJsonPath('data.total_biaya', 270000)
            ->assertJsonCount(2, 'data.detail_layanan')
            ->assertJsonStructure(['data' => [
                'id_pesanan', 'kode_pesanan', 'status', 'keluhan', 'waktu_pesan',
                'slot' => ['id_slot', 'tanggal', 'jam_mulai', 'jam_selesai'],
                'detail_layanan' => [['id_layanan', 'nama_layanan', 'jumlah', 'harga_jasa', 'subtotal']],
                'total_biaya',
            ]]);
    }

    public function test_booking_menaikkan_kuota_terpakai_dan_mencatat_riwayat(): void
    {
        $this->actingAs($this->user)->postJson('/api/v1/pesanan', $this->payload())->assertCreated();

        $this->assertSame(1, $this->slot->fresh()->terisi);

        $pesanan = Pesanan::firstOrFail();
        $this->assertSame(Pesanan::KANAL_MOBILE, $pesanan->kanal_pesan);
        $this->assertSame(Pesanan::STATUS_MENUNGGU, $pesanan->status);

        // Riwayat status pertama wajib tercatat.
        $this->assertDatabaseHas('tb_riwayat_status', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status' => Pesanan::STATUS_MENUNGGU,
        ]);
    }

    public function test_kode_pesanan_mengikuti_format_pmm_tanggal_urut(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload());

        $response->assertCreated();

        $diharapkan = 'PMM-'.$this->slot->tanggal->format('Ymd').'-001';
        $this->assertSame($diharapkan, $response->json('data.kode_pesanan'));

        // Pesanan kedua pada tanggal sama melanjutkan urutannya.
        $slot2 = SlotJadwal::create([
            'tanggal' => $this->slot->tanggal->toDateString(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'kapasitas' => 1,
            'terisi' => 0,
            'status' => SlotJadwal::STATUS_DIBUKA,
        ]);

        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload(['id_slot' => $slot2->id_slot]))
            ->assertCreated()
            ->assertJsonPath('data.kode_pesanan', 'PMM-'.$this->slot->tanggal->format('Ymd').'-002');
    }

    public function test_booking_ulang_slot_terakhir_ditolak_kuota_penuh(): void
    {
        // Kapasitas slot ini 1 — booking pertama menghabiskannya.
        $this->actingAs($this->user)->postJson('/api/v1/pesanan', $this->payload())->assertCreated();

        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload())
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Slot tidak tersedia. Silakan pilih slot lain.')
            ->assertJsonPath('data.kode', 'SLOT_TIDAK_TERSEDIA')
            ->assertJsonPath('data.alasan', SlotJadwal::ALASAN_PENUH);

        // Tidak ada pesanan kedua yang tercipta.
        $this->assertDatabaseCount('tb_pesanan', 1);
    }

    public function test_booking_slot_ditutup_ditolak(): void
    {
        $this->slot->update(['status' => SlotJadwal::STATUS_DITUTUP]);

        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload())
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'SLOT_TIDAK_TERSEDIA')
            ->assertJsonPath('data.alasan', SlotJadwal::ALASAN_DITUTUP);
    }

    public function test_booking_slot_yang_sudah_lewat_ditolak(): void
    {
        $this->slot->update(['tanggal' => today()->subDay()->toDateString()]);

        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload())
            ->assertStatus(422)
            ->assertJsonPath('data.alasan', SlotJadwal::ALASAN_LEWAT);
    }

    public function test_mobil_milik_pelanggan_lain_ditolak(): void
    {
        $lain = User::factory()->pelanggan()->create();
        $mobilLain = Mobil::create([
            'id_pelanggan' => $lain->pelanggan->id_pelanggan,
            'plat_nomor' => 'N 9999 ZZ',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2020,
        ]);

        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload(['id_mobil' => $mobilLain->id_mobil]))
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'MOBIL_TIDAK_TERSEDIA')
            ->assertJsonPath('data.errors.id_mobil.0', 'Mobil tidak ditemukan pada akun Anda.');

        // Kuota slot tidak boleh terpakai saat booking gagal.
        $this->assertSame(0, $this->slot->fresh()->terisi);
    }

    public function test_detail_layanan_wajib_berisi_minimal_satu_item(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload(['detail_layanan' => []]))
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'UNPROCESSABLE_CONTENT')
            ->assertJsonPath('data.errors.detail_layanan.0', 'Kolom rincian layanan wajib diisi.');
    }

    public function test_kuota_tidak_terpakai_bila_transaksi_gagal(): void
    {
        // id_layanan tidak ada → validasi gagal sebelum transaksi.
        $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload([
                'detail_layanan' => [['id_layanan' => 99999, 'jumlah' => 1]],
            ]))
            ->assertStatus(422);

        $this->assertSame(0, $this->slot->fresh()->terisi);
        $this->assertDatabaseCount('tb_pesanan', 0);
    }

    public function test_riwayat_pesanan_terbaru_dulu_dan_berhalaman(): void
    {
        $this->actingAs($this->user)->postJson('/api/v1/pesanan', $this->payload())->assertCreated();

        $response = $this->actingAs($this->user)->getJson('/api/v1/pesanan');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Riwayat pesanan')
            ->assertJsonStructure([
                'data' => [['id_pesanan', 'kode_pesanan', 'tanggal_slot', 'jam_mulai', 'jam_selesai', 'status', 'total_biaya', 'waktu_pesan']],
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
            ]);
    }

    public function test_riwayat_dapat_disaring_berdasarkan_status(): void
    {
        $this->actingAs($this->user)->postJson('/api/v1/pesanan', $this->payload())->assertCreated();

        $this->actingAs($this->user)
            ->getJson('/api/v1/pesanan?status=selesai')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->actingAs($this->user)
            ->getJson('/api/v1/pesanan?status=menunggu')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_pelanggan_tidak_bisa_melihat_pesanan_pelanggan_lain(): void
    {
        $lain = User::factory()->pelanggan()->create();
        $pesananLain = Pesanan::create([
            'kode_pesanan' => 'PMM-20260101-001',
            'id_pelanggan' => $lain->pelanggan->id_pelanggan,
            'id_mobil' => Mobil::create([
                'id_pelanggan' => $lain->pelanggan->id_pelanggan,
                'plat_nomor' => 'N 7777 XX',
                'merk' => 'Daihatsu',
                'tipe' => 'Xenia',
                'tahun' => 2015,
            ])->id_mobil,
            'keluhan' => 'Keluhan orang lain',
            'status' => Pesanan::STATUS_MENUNGGU,
            'total_biaya' => 0,
            'waktu_pesan' => now(),
        ]);

        // 403 FORBIDDEN, bukan 404 — sesuai kontrak.
        $this->actingAs($this->user)
            ->getJson("/api/v1/pesanan/{$pesananLain->id_pesanan}")
            ->assertStatus(403)
            ->assertJsonPath('data.kode', 'FORBIDDEN');
    }

    public function test_detail_pesanan_memuat_relasi_lengkap(): void
    {
        $id = $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload())
            ->json('data.id_pesanan');

        $this->actingAs($this->user)
            ->getJson("/api/v1/pesanan/{$id}")
            ->assertOk()
            ->assertJsonPath('message', 'Detail pesanan')
            ->assertJsonPath('data.dapat_dibatalkan', true)
            ->assertJsonStructure(['data' => [
                'id_pesanan', 'kode_pesanan', 'status', 'status_terakhir_diubah', 'keluhan',
                'mobil' => ['id', 'plat_nomor', 'merk', 'tipe', 'tahun'],
                'slot' => ['id_slot', 'tanggal', 'jam_mulai', 'jam_selesai'],
                'detail_layanan', 'detail_sparepart', 'total_biaya', 'penugasan',
            ]]);
    }

    public function test_riwayat_status_pesanan_urut_menaik(): void
    {
        $id = $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload())
            ->json('data.id_pesanan');

        $this->actingAs($this->user)
            ->getJson("/api/v1/pesanan/{$id}/riwayat-status")
            ->assertOk()
            ->assertJsonPath('message', 'Riwayat status pesanan')
            ->assertJsonPath('data.riwayat.0.status', Pesanan::STATUS_MENUNGGU)
            ->assertJsonStructure(['data' => ['id_pesanan', 'kode_pesanan', 'riwayat' => [['status', 'waktu']]]]);
    }

    public function test_nota_menampilkan_rincian_jasa_dan_total(): void
    {
        $id = $this->actingAs($this->user)
            ->postJson('/api/v1/pesanan', $this->payload())
            ->json('data.id_pesanan');

        $this->actingAs($this->user)
            ->getJson("/api/v1/pesanan/{$id}/nota")
            ->assertOk()
            ->assertJsonPath('message', 'Nota pesanan')
            ->assertJsonPath('data.subtotal_jasa', 270000)
            ->assertJsonPath('data.subtotal_sparepart', 0)
            ->assertJsonPath('data.total_biaya', 270000)
            ->assertJsonStructure(['data' => ['id_pesanan', 'kode_pesanan', 'mobil', 'detail_layanan', 'detail_sparepart']]);
    }
}
