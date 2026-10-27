<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan penting: data master dulu (user → pelanggan → mobil → slot →
     * layanan → sparepart), baru pesanan yang merujuk ke semuanya.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MekanikSeeder::class,
            MobilSeeder::class,
            JenisLayananSeeder::class,
            SparepartSeeder::class,
            SlotJadwalSeeder::class,
            MetodeBayarSeeder::class,
            PesananSeeder::class,
        ]);
    }
}
