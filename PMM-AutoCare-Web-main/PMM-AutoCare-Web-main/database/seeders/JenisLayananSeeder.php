<?php

namespace Database\Seeders;

use App\Models\JenisLayanan;
use Illuminate\Database\Seeder;

class JenisLayananSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = [
            ['Ganti Oli Mesin', 'perawatan rutin', 150000, 45],
            ['Servis Rem', 'kaki-kaki', 120000, 60],
            ['Tune Up Mesin', 'mesin', 350000, 120],
            ['Ganti Filter Udara', 'perawatan rutin', 75000, 30],
            ['Servis Kelistrikan', 'kelistrikan', 200000, 90],
            ['Balancing & Spooring', 'kaki-kaki', 250000, 75],
            ['Cuci Mobil & Salon', 'perawatan rutin', 90000, 60],
            ['Ganti Kampas Kopling', 'mesin', 450000, 180],
        ];

        foreach ($layanan as [$nama, $kategori, $harga, $estimasi]) {
            JenisLayanan::create([
                'nama_layanan' => $nama,
                'kategori' => $kategori,
                'harga_jasa' => $harga,
                'estimasi_menit' => $estimasi,
                'aktif' => true,
            ]);
        }
    }
}
