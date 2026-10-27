<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class CheckShopStatus extends Command
{
    /**
     * Nama dan argumen perintah yang dipanggil di terminal.
     */
    protected $signature = 'pos:status {jam?}';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Mengecek status operasional Toko Kelontong POS';

    /**
     * Logika utama perintah.
     */
    public function handle()
    {
        // 1. Minta input nama kasir
        $kasir = $this->ask('Masukkan nama Anda: ');

        // 2. Ambil argumen jam, jika tidak diisi default ke jam 10
        $jam = $this->argument('jam') ?? 10;

        $this->info("=== SISTEM MONITORING TOKO KELONTONG ===");

        // 3. Asumsi toko buka dari jam 08:00 sampai 21:00
        if ($jam >= 8 && $jam <= 21) {
            $this->info("Halo {$kasir}, Status Toko pada jam {$jam}:00 WIB adalah: BUKA");
            $this->comment("Silakan kasir bersiap di meja transaksi.");
        } else {
            $this->error("Halo {$kasir}, Status Toko pada jam {$jam}:00 WIB adalah: TUTUP");
            $this->warn("Akses transaksi kasir dinonaktifkan sementara.");
        }
    }
}