<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use Illuminate\Database\Seeder;

class SparepartSeeder extends Seeder
{
    /**
     * Data persediaan awal bengkel.
     *
     * Sengaja disertakan satu item yang stoknya habis dan satu yang menipis
     * agar ketiga status (Tersedia / Menipis / Habis) terlihat saat demo.
     *
     * Catatan: stok di sini adalah stok AWAL. PesananSeeder akan mengurangi
     * sebagiannya karena pemakaian pada pesanan contoh.
     */
    public function run(): void
    {
        $sparepart = [
            ['Oli Mesin 5W-30', 'liter', 85000, 40, 10],
            ['Filter Oli', 'pcs', 45000, 25, 5],
            ['Filter Udara', 'pcs', 95000, 18, 5],
            ['Kampas Rem Depan', 'set', 275000, 12, 4],
            ['Busi Iridium', 'pcs', 75000, 30, 8],
            ['Aki 12V 45Ah', 'unit', 850000, 6, 2],
            // Contoh stok menipis (stok <= stok_minimal).
            ['Minyak Rem DOT 4', 'botol', 55000, 3, 5],
            // Contoh stok habis.
            ['Kabel Busi', 'set', 120000, 0, 3],
        ];

        foreach ($sparepart as [$nama, $satuan, $harga, $stok, $minimal]) {
            Sparepart::create([
                'nama' => $nama,
                'satuan' => $satuan,
                'harga' => $harga,
                'stok' => $stok,
                'stok_minimal' => $minimal,
            ]);
        }
    }
}
