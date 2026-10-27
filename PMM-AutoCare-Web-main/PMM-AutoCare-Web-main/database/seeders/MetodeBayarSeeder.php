<?php

namespace Database\Seeders;

use App\Models\MetodeBayar;
use Illuminate\Database\Seeder;

class MetodeBayarSeeder extends Seeder
{
    /**
     * Metode pembayaran manual (transfer bank / tunai di bengkel).
     * Tabelnya sudah disiapkan walau modul pembayaran belum dibangun.
     */
    public function run(): void
    {
        MetodeBayar::create([
            'nama_metode' => 'Transfer Bank BRI',
            'jenis' => 'transfer',
            'no_rekening' => '1234-01-567890-53-1',
            'atas_nama' => 'Bengkel PMM Auto Care',
            'aktif' => true,
        ]);

        MetodeBayar::create([
            'nama_metode' => 'Tunai di Bengkel',
            'jenis' => 'tunai',
            'aktif' => true,
        ]);
    }
}
