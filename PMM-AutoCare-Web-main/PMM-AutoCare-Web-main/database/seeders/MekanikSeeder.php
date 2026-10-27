<?php

namespace Database\Seeders;

use App\Models\Mekanik;
use Illuminate\Database\Seeder;

class MekanikSeeder extends Seeder
{
    public function run(): void
    {
        $mekanik = [
            ['Andi Wijaya', 'mesin', '081255500011'],
            ['Joko Susilo', 'kelistrikan', '081255500022'],
            ['Hendra Gunawan', 'kaki-kaki', '081255500033'],
            ['Bayu Setiawan', 'body & cat', '081255500044'],
        ];

        foreach ($mekanik as [$nama, $spesialis, $noHp]) {
            Mekanik::create([
                'nama' => $nama,
                'spesialis' => $spesialis,
                'no_hp' => $noHp,
                'aktif' => true,
            ]);
        }
    }
}
