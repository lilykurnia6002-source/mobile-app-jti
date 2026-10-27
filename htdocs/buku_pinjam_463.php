<?php

/**
 * Tugas: Perpustakaan: Buku & Peminjaman
 * Nama File: buku_pinjam_463.php
 */

class Buku {
    // Properti sesuai spesifikasi
    var $kode;
    var $judul;
    var $dipinjam;
    var $pemegang;

    // Konstruktor: Set dipinjam ke false dan pemegang ke string kosong
    public function __construct($kode, $judul) {
        $this->kode = $kode;
        $this->judul = $judul;
        $this->dipinjam = false;
        $this->pemegang = "";
    }

    // Metode: pinjam($namaPeminjam)
    public function pinjam($namaPeminjam) {
        if ($this->dipinjam) {
            // Menggunakan \n untuk baris baru di dalam tag <pre>
            return "[GAGAL] Buku '$this->judul' sedang dipinjam oleh $this->pemegang.\n";
        } else {
            $this->dipinjam = true;
            $this->pemegang = $namaPeminjam;
            return "[SUKSES] Buku '$this->judul' berhasil dipinjam oleh $namaPeminjam.\n";
        }
    }

    // Metode: kembalikan()
    public function kembalikan() {
        if (!$this->dipinjam) {
            return "[INFO] Buku '$this->judul' tidak sedang dipinjam.\n";
        } else {
            $peminjamLama = $this->pemegang;
            $this->dipinjam = false;
            $this->pemegang = "";
            return "[SUKSES] Buku '$this->judul' telah dikembalikan oleh $peminjamLama.\n";
        }
    }

    // Metode: tampilkanStatus()
    public function tampilkanStatus() {
        $status = $this->dipinjam ? "Dipinjam oleh $this->pemegang" : "Tersedia";
        
        echo "---------------------------------------\n";
        echo "KODE   : " . $this->kode . "\n";
        echo "JUDUL  : " . $this->judul . "\n";
        echo "STATUS : " . $status . "\n";
        echo "---------------------------------------\n\n";
    }
}

// --- PENGUJIAN KODE ---

// MENGGUNAKAN TAG <PRE> AGAR OUTPUT TURUN KE BAWAH DAN RAPI
echo "<pre>";
echo "<h2>Sistem Informasi Perpustakaan</h2>";

// 1. Instansiasi Objek
$buku1 = new Buku("B001", "Pemrograman PHP Dasar");

// 2. Tampilkan Status Awal
echo "STATUS AWAL:\n";
$buku1->tampilkanStatus();

// 3. Uji Pinjam
echo "PROSES PINJAM:\n";
echo $buku1->pinjam("Budi");
$buku1->tampilkanStatus();

// 4. Uji Pinjam Lagi
echo "PROSES PINJAM LAGI:\n";
echo $buku1->pinjam("Siti");
echo "\n";

// 5. Uji Kembalikan
echo "PROSES KEMBALIKAN:\n";
echo $buku1->kembalikan();
$buku1->tampilkanStatus();

echo "</pre>";

?>