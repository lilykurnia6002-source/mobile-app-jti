<?php

namespace Database\Seeders;

use App\Models\Mobil;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class MobilSeeder extends Seeder
{
    public function run(): void
    {
        $mobil = [
            'budi@example.com' => [
                ['N 1234 AB', 'Toyota', 'Avanza', 2018],
                ['N 5678 CD', 'Honda', 'Brio', 2020],
            ],
            'siti@example.com' => [
                ['N 4321 EF', 'Daihatsu', 'Xenia', 2016],
            ],
            'rian@example.com' => [
                ['N 8899 GH', 'Mitsubishi', 'Xpander', 2021],
                ['N 2211 IJ', 'Suzuki', 'Ertiga', 2019],
            ],
        ];

        foreach ($mobil as $email => $daftar) {
            $pelanggan = Pelanggan::whereHas('user', fn ($q) => $q->where('email', $email))->first();

            if ($pelanggan === null) {
                continue;
            }

            foreach ($daftar as [$plat, $merk, $tipe, $tahun]) {
                Mobil::create([
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'plat_nomor' => $plat,
                    'merk' => $merk,
                    'tipe' => $tipe,
                    'tahun' => $tahun,
                ]);
            }
        }
    }
}
