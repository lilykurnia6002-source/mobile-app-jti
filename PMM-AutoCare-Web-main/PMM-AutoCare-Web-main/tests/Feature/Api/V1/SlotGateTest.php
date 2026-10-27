<?php

namespace Tests\Feature\Api\V1;

use App\Models\SlotJadwal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Aturan gate ketersediaan slot (konsep_api.md § 1.4).
 *
 * Sebuah slot hanya boleh muncul/dipakai bila: status 'dibuka',
 * tanggal >= hari ini, dan terisi < kapasitas.
 */
class SlotGateTest extends TestCase
{
    use RefreshDatabase;

    private function buatSlot(array $override = []): SlotJadwal
    {
        return SlotJadwal::create([
            'tanggal' => today()->addDay()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kapasitas' => 3,
            'terisi' => 0,
            'status' => SlotJadwal::STATUS_DIBUKA,
            ...$override,
        ]);
    }

    public function test_daftar_slot_hanya_menampilkan_yang_lolos_gate(): void
    {
        $tersedia = $this->buatSlot(['jam_mulai' => '08:00']);
        $this->buatSlot(['jam_mulai' => '10:00', 'status' => SlotJadwal::STATUS_DITUTUP]);
        $this->buatSlot(['jam_mulai' => '13:00', 'terisi' => 3]);
        $this->buatSlot(['jam_mulai' => '15:00', 'tanggal' => today()->subDay()->toDateString()]);

        $response = $this->getJson('/api/v1/slot?tanggal='.today()->addDay()->format('Y-m-d'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id_slot', $tersedia->id_slot)
            ->assertJsonPath('data.0.sisa_kuota', 3);
    }

    public function test_sisa_kuota_dihitung_server(): void
    {
        $this->buatSlot(['kapasitas' => 5, 'terisi' => 2]);

        $this->getJson('/api/v1/slot?tanggal='.today()->addDay()->format('Y-m-d'))
            ->assertOk()
            ->assertJsonPath('data.0.kapasitas', 5)
            ->assertJsonPath('data.0.terisi', 2)
            ->assertJsonPath('data.0.sisa_kuota', 3);
    }

    public function test_tanggal_tanpa_slot_mengembalikan_array_kosong_dengan_pesan_informatif(): void
    {
        $tanggal = today()->addDays(10)->format('Y-m-d');

        $this->getJson("/api/v1/slot?tanggal={$tanggal}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', "Tidak ada slot tersedia pada {$tanggal}")
            ->assertJsonCount(0, 'data');
    }

    public function test_tanggal_wajib_diisi(): void
    {
        $this->getJson('/api/v1/slot')
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'UNPROCESSABLE_CONTENT')
            ->assertJsonPath('data.errors.tanggal.0', 'Kolom tanggal wajib diisi.');
    }

    public function test_tanggal_tersedia_hanya_memuat_tanggal_yang_punya_slot_lolos_gate(): void
    {
        $besok = today()->addDay();
        $lusa = today()->addDays(2);

        $this->buatSlot(['tanggal' => $besok->toDateString()]);

        // Penuh dan ditutup — tanggalnya tidak boleh muncul.
        $this->buatSlot(['tanggal' => $lusa->toDateString(), 'jam_mulai' => '08:00', 'terisi' => 3]);
        $this->buatSlot(['tanggal' => $lusa->toDateString(), 'jam_mulai' => '10:00', 'status' => SlotJadwal::STATUS_DITUTUP]);

        $this->getJson('/api/v1/slot/tanggal-tersedia')
            ->assertOk()
            ->assertJsonPath('data.dari', today()->format('Y-m-d'))
            ->assertJsonPath('data.tanggal_tersedia', [$besok->format('Y-m-d')]);
    }

    public function test_tanggal_tersedia_kosong_memberi_pesan_khusus(): void
    {
        $this->getJson('/api/v1/slot/tanggal-tersedia')
            ->assertOk()
            ->assertJsonPath('message', 'Belum ada jadwal yang tersedia')
            ->assertJsonPath('data.tanggal_tersedia', []);
    }

    public function test_detail_slot_yang_tidak_ada_menghasilkan_not_found(): void
    {
        $this->getJson('/api/v1/slot/99999')
            ->assertStatus(404)
            ->assertJsonPath('data.kode', 'NOT_FOUND');
    }

    public function test_detail_slot_yang_ditutup_ditolak_dengan_slot_tidak_tersedia(): void
    {
        $slot = $this->buatSlot(['status' => SlotJadwal::STATUS_DITUTUP]);

        $this->getJson("/api/v1/slot/{$slot->id_slot}")
            ->assertStatus(422)
            ->assertJsonPath('data.kode', 'SLOT_TIDAK_TERSEDIA')
            ->assertJsonPath('data.alasan', SlotJadwal::ALASAN_DITUTUP);
    }

    public function test_detail_slot_yang_penuh_ditolak_dengan_alasan_kuota_penuh(): void
    {
        $slot = $this->buatSlot(['terisi' => 3]);

        $this->getJson("/api/v1/slot/{$slot->id_slot}")
            ->assertStatus(422)
            ->assertJsonPath('data.alasan', SlotJadwal::ALASAN_PENUH);
    }

    public function test_detail_slot_yang_sudah_lewat_ditolak_dengan_alasan_tanggal_lewat(): void
    {
        $slot = $this->buatSlot(['tanggal' => today()->subDay()->toDateString()]);

        $this->getJson("/api/v1/slot/{$slot->id_slot}")
            ->assertStatus(422)
            ->assertJsonPath('data.alasan', SlotJadwal::ALASAN_LEWAT);
    }

    public function test_jam_diformat_hh_mm(): void
    {
        $this->buatSlot(['jam_mulai' => '08:00', 'jam_selesai' => '10:00']);

        $this->getJson('/api/v1/slot?tanggal='.today()->addDay()->format('Y-m-d'))
            ->assertOk()
            ->assertJsonPath('data.0.jam_mulai', '08:00')
            ->assertJsonPath('data.0.jam_selesai', '10:00');
    }
}
