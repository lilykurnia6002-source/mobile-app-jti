<?php

namespace Database\Seeders;

use App\Models\JenisLayanan;
use App\Models\Mekanik;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pengerjaan;
use App\Models\Pesanan;
use App\Models\SlotJadwal;
use App\Models\Sparepart;
use App\Models\User;
use App\Services\SparepartService;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    public function __construct(private SparepartService $sparepartService) {}

    /**
     * Pesanan contoh lintas status, lengkap dengan rincian layanan,
     * riwayat status, penugasan mekanik, dan notifikasi — agar setiap
     * layar aplikasi mobile punya data untuk ditampilkan.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $budi = Pelanggan::whereHas('user', fn ($q) => $q->where('email', 'budi@example.com'))->first();
        $siti = Pelanggan::whereHas('user', fn ($q) => $q->where('email', 'siti@example.com'))->first();

        if ($admin === null || $budi === null || $siti === null) {
            return;
        }

        $mobilBudi = Mobil::where('id_pelanggan', $budi->id_pelanggan)->orderBy('id_mobil')->get();
        $mobilSiti = Mobil::where('id_pelanggan', $siti->id_pelanggan)->orderBy('id_mobil')->get();

        // 1. Pesanan menunggu verifikasi admin (bisa dibatalkan pelanggan).
        $this->buatPesanan(
            pelanggan: $budi,
            mobil: $mobilBudi->first(),
            tanggal: today()->addDays(1)->toDateString(),
            jam: '08:00',
            keluhan: 'Mesin bergetar saat idling, oli sudah 6000 km.',
            layanan: [['Ganti Oli Mesin', 1], ['Servis Rem', 1]],
            status: Pesanan::STATUS_MENUNGGU,
            admin: $admin,
        );

        // 2. Pesanan diterima + mekanik ditetapkan.
        $diterima = $this->buatPesanan(
            pelanggan: $budi,
            mobil: $mobilBudi->skip(1)->first() ?? $mobilBudi->first(),
            tanggal: today()->addDays(2)->toDateString(),
            jam: '10:00',
            keluhan: 'Rem depan terasa berdecit saat mengerem.',
            layanan: [['Servis Rem', 1], ['Balancing & Spooring', 1]],
            status: Pesanan::STATUS_DITERIMA,
            admin: $admin,
        );

        Pengerjaan::create([
            'id_pesanan' => $diterima->id_pesanan,
            'id_mekanik' => Mekanik::where('spesialis', 'kaki-kaki')->value('id_mekanik'),
            'dicatat_oleh' => $admin->id_user,
            'catatan' => 'Mekanik ditetapkan, menunggu jadwal pengerjaan.',
        ]);

        // 3. Pesanan sedang dikerjakan + sparepart dipakai.
        $dikerjakan = $this->buatPesanan(
            pelanggan: $siti,
            mobil: $mobilSiti->first(),
            tanggal: today()->toDateString(),
            jam: '08:00',
            keluhan: 'Tarikan mesin lemah dan bahan bakar boros.',
            layanan: [['Tune Up Mesin', 1], ['Ganti Filter Udara', 1]],
            status: Pesanan::STATUS_DIKERJAKAN,
            admin: $admin,
        );

        Pengerjaan::create([
            'id_pesanan' => $dikerjakan->id_pesanan,
            'id_mekanik' => Mekanik::where('spesialis', 'mesin')->value('id_mekanik'),
            'dicatat_oleh' => $admin->id_user,
            'waktu_mulai' => now()->subHours(2),
            'catatan' => 'Pemeriksaan menyeluruh sistem pembakaran.',
        ]);

        $this->tambahSparepart($dikerjakan, 'Filter Udara', 1);
        $this->tambahSparepart($dikerjakan, 'Busi Iridium', 4);

        // 4. Pesanan selesai (nota lengkap dengan jasa + sparepart).
        $selesai = $this->buatPesanan(
            pelanggan: $budi,
            mobil: $mobilBudi->first(),
            tanggal: today()->subDays(2)->toDateString(),
            jam: '10:00',
            keluhan: 'Servis rutin 10.000 km.',
            layanan: [['Ganti Oli Mesin', 1], ['Ganti Filter Udara', 1]],
            status: Pesanan::STATUS_SELESAI,
            admin: $admin,
        );

        Pengerjaan::create([
            'id_pesanan' => $selesai->id_pesanan,
            'id_mekanik' => Mekanik::where('spesialis', 'mesin')->value('id_mekanik'),
            'dicatat_oleh' => $admin->id_user,
            'waktu_mulai' => now()->subDays(2)->setTime(10, 5),
            'waktu_selesai' => now()->subDays(2)->setTime(11, 30),
            'catatan' => 'Servis selesai, kendaraan siap diambil.',
        ]);

        $this->tambahSparepart($selesai, 'Oli Mesin 5W-30', 2);
        $this->tambahSparepart($selesai, 'Filter Oli', 1);

        // 5. Pesanan ditolak admin.
        $this->buatPesanan(
            pelanggan: $siti,
            mobil: $mobilSiti->first(),
            tanggal: today()->addDays(3)->toDateString(),
            jam: '13:00',
            keluhan: 'Ingin cat ulang seluruh body.',
            layanan: [['Cuci Mobil & Salon', 1]],
            status: Pesanan::STATUS_DITOLAK,
            admin: $admin,
            alasanTolak: 'Jadwal bengkel penuh untuk pengerjaan body & cat. Silakan pilih tanggal lain.',
        );

        // 6. Pesanan dibatalkan pelanggan (kuota sudah dikembalikan).
        $this->buatPesanan(
            pelanggan: $budi,
            mobil: $mobilBudi->first(),
            tanggal: today()->addDays(4)->toDateString(),
            jam: '15:00',
            keluhan: 'Rencana servis AC.',
            layanan: [['Servis Kelistrikan', 1]],
            status: Pesanan::STATUS_DIBATALKAN,
            admin: $admin,
        );
    }

    /**
     * Buat satu pesanan beserta rincian layanan, riwayat status, notifikasi,
     * dan penyesuaian kuota slot.
     *
     * @param  array<int, array{0:string, 1:int}>  $layanan  [nama layanan, jumlah]
     */
    private function buatPesanan(
        Pelanggan $pelanggan,
        Mobil $mobil,
        string $tanggal,
        string $jam,
        string $keluhan,
        array $layanan,
        string $status,
        User $admin,
        ?string $alasanTolak = null,
    ): Pesanan {
        $slot = SlotJadwal::whereDate('tanggal', $tanggal)
            ->where('jam_mulai', $jam)
            ->first();

        $rincian = [];
        $total = 0.0;

        foreach ($layanan as [$namaLayanan, $jumlah]) {
            $master = JenisLayanan::where('nama_layanan', $namaLayanan)->first();

            if ($master === null) {
                continue;
            }

            $subtotal = (float) $master->harga_jasa * $jumlah;
            $total += $subtotal;

            $rincian[] = [
                'id_layanan' => $master->id_layanan,
                'jumlah' => $jumlah,
                'harga_jasa' => $master->harga_jasa,
                'subtotal' => $subtotal,
            ];
        }

        $pesanan = Pesanan::create([
            'kode_pesanan' => $this->kodePesanan($tanggal),
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_mobil' => $mobil->id_mobil,
            'id_slot' => $slot?->id_slot,
            'keluhan' => $keluhan,
            'status' => $status,
            'kanal_pesan' => Pesanan::KANAL_MOBILE,
            'total_biaya' => $total,
            'waktu_pesan' => now()->subDays(random_int(0, 5)),
            'alasan_tolak' => $alasanTolak,
        ]);

        $pesanan->detailLayanan()->createMany($rincian);

        // Kuota terpakai untuk semua status kecuali yang sudah dilepas.
        if ($slot !== null && ! in_array($status, [Pesanan::STATUS_DIBATALKAN, Pesanan::STATUS_DITOLAK], true)) {
            $slot->increment('terisi');
        }

        $this->catatRiwayat($pesanan, Pesanan::STATUS_MENUNGGU, $pelanggan->user->id_user, 'Pesanan diajukan melalui aplikasi mobile.');
        $this->kirimNotifikasi($pesanan, 'Pesanan Diterima Bengkel', 'Pesanan '.$pesanan->kode_pesanan.' telah kami terima dan sedang menunggu verifikasi admin.');

        if ($status !== Pesanan::STATUS_MENUNGGU) {
            $this->catatRiwayat($pesanan, $status, $admin->id_user, $this->keteranganStatus($status, $alasanTolak));
            $this->kirimNotifikasi($pesanan, $this->judulNotifikasi($status), $this->pesanNotifikasi($pesanan, $status, $tanggal, $jam));
        }

        return $pesanan;
    }

    private function tambahSparepart(Pesanan $pesanan, string $namaSparepart, int $jumlah): void
    {
        $sparepart = Sparepart::where('nama', $namaSparepart)->first();

        if ($sparepart === null) {
            return;
        }

        // Memakai service yang sama dengan aplikasi, supaya data contoh
        // konsisten: stok berkurang dan total_biaya dihitung ulang.
        $this->sparepartService->catatPemakaian($pesanan, $sparepart->id_sparepart, $jumlah);
    }

    private function kodePesanan(string $tanggal): string
    {
        $prefix = 'PMM-'.date('Ymd', strtotime($tanggal)).'-';
        $urutan = Pesanan::where('kode_pesanan', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
    }

    private function catatRiwayat(Pesanan $pesanan, string $status, ?int $idUser, string $keterangan): void
    {
        $pesanan->riwayatStatus()->create([
            'status' => $status,
            'keterangan' => $keterangan,
            'diubah_oleh' => $idUser,
            'waktu' => now()->subDays(random_int(0, 4)),
        ]);
    }

    private function kirimNotifikasi(Pesanan $pesanan, string $judul, string $pesan): void
    {
        $pesanan->notifikasi()->create([
            'judul' => $judul,
            'pesan' => $pesan,
            'waktu_kirim' => now()->subDays(random_int(0, 4)),
            'dibaca' => (bool) random_int(0, 1),
        ]);
    }

    private function keteranganStatus(string $status, ?string $alasanTolak): string
    {
        return match ($status) {
            Pesanan::STATUS_DITERIMA => 'Pesanan disetujui admin dan dijadwalkan.',
            Pesanan::STATUS_DIKERJAKAN => 'Kendaraan sedang dikerjakan mekanik.',
            Pesanan::STATUS_SELESAI => 'Pengerjaan selesai, kendaraan siap diambil.',
            Pesanan::STATUS_DITOLAK => $alasanTolak ?? 'Pesanan ditolak admin.',
            Pesanan::STATUS_DIBATALKAN => 'Pesanan dibatalkan oleh pelanggan.',
            default => 'Perubahan status pesanan.',
        };
    }

    private function judulNotifikasi(string $status): string
    {
        return match ($status) {
            Pesanan::STATUS_DITERIMA => 'Pesanan Diterima',
            Pesanan::STATUS_DIKERJAKAN => 'Servis Sedang Dikerjakan',
            Pesanan::STATUS_SELESAI => 'Servis Selesai',
            Pesanan::STATUS_DITOLAK => 'Pesanan Ditolak',
            Pesanan::STATUS_DIBATALKAN => 'Pesanan Dibatalkan',
            default => 'Pembaruan Pesanan',
        };
    }

    private function pesanNotifikasi(Pesanan $pesanan, string $status, string $tanggal, string $jam): string
    {
        $jadwal = date('d F Y', strtotime($tanggal)).' pukul '.$jam;

        return match ($status) {
            Pesanan::STATUS_DITERIMA => "Pesanan {$pesanan->kode_pesanan} Anda telah diterima dan dijadwalkan pada {$jadwal}.",
            Pesanan::STATUS_DIKERJAKAN => "Pesanan {$pesanan->kode_pesanan} sedang dikerjakan oleh mekanik kami.",
            Pesanan::STATUS_SELESAI => "Pesanan {$pesanan->kode_pesanan} telah selesai dikerjakan. Silakan cek nota pembayaran.",
            Pesanan::STATUS_DITOLAK => "Mohon maaf, pesanan {$pesanan->kode_pesanan} tidak dapat kami proses. {$pesanan->alasan_tolak}",
            Pesanan::STATUS_DIBATALKAN => "Pesanan {$pesanan->kode_pesanan} telah dibatalkan.",
            default => "Ada pembaruan pada pesanan {$pesanan->kode_pesanan}.",
        };
    }
}
