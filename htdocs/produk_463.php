<?php

/**
 * Tugas: Sistem Produk Toko
 * Nama File: produk_463.php
 */

class Produk {
    // Properti menggunakan kata kunci 'var' sesuai instruksi spesifikasi
    var $sku;
    var $nama;
    var $harga;
    var $stok;

    // Konstruktor: Harus mengisi semua properti saat objek dibuat
    public function __construct($sku, $nama, $harga, $stok = 0) {
        $this->sku = $sku;
        $this->nama = $nama;
        $this->harga = $harga;
        $this->stok = $stok;
    }

    // Metode: Menampilkan semua properti (cocok untuk CLI)
    public function tampilkanInfo() {
        echo "==============================" . PHP_EOL;
        echo "       DETAIL PRODUK          " . PHP_EOL;
        echo "==============================" . PHP_EOL;
        echo "SKU   : " . $this->sku . PHP_EOL;
        echo "Nama  : " . $this->nama . PHP_EOL;
        echo "Harga : Rp " . number_format($this->harga, 0, ',', '.') . PHP_EOL;
        echo "Stok  : " . $this->stok . " unit" . PHP_EOL;
        echo "==============================" . PHP_EOL . PHP_EOL;
    }

    // Metode: Logika pembelian produk
    public function beli($qty) {
        // Jika qty <= 0, kembalikan pesan error
        if ($qty <= 0) {
            return "[ERROR] Jumlah pembelian tidak valid!" . PHP_EOL;
        }

        // Jika stok cukup, kurangi stok dan beri pesan sukses
        if ($this->stok >= $qty) {
            $this->stok -= $qty;
            return "[SUKSES] Berhasil membeli $qty unit $this->nama." . PHP_EOL;
        } 
        // Jika tidak cukup, kembalikan pesan stok tidak cukup
        else {
            return "[GAGAL] Stok tidak cukup! (Sisa stok: $this->stok)" . PHP_EOL;
        }
    }

    // Metode: Menambah stok produk
    public function restock($qty) {
        if ($qty > 0) {
            $this->stok += $qty;
            echo "[INFO] Berhasil menambah stok. Stok sekarang: $this->stok unit." . PHP_EOL;
        }
    }
}

// --- PENGUJIAN KODE (Dapat dihapus jika hanya ingin kelasnya saja) ---

// 1. Instansiasi Objek
$p1 = new Produk("A001-463", "Monitor Gaming 24'", 2500000, 10);

// 2. Tampilkan Info Awal
$p1->tampilkanInfo();

// 3. Uji Fungsi Beli
echo $p1->beli(5);  // Pembelian berhasil
echo $p1->beli(10); // Stok tidak cukup (sisa 5)
echo $p1->beli(-2); // Error jumlah tidak valid

// 4. Uji Restock
$p1->restock(15);   // Stok bertambah (5 + 15 = 20)

// 5. Tampilkan Info Akhir
$p1->tampilkanInfo();

?>