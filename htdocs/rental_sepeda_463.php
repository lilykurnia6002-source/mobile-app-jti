<?php

/**
 * Tugas: Rental Sepeda
 * Nama File: rental_sepeda_463.php
 */

class SepedaRental {
    // Properti menggunakan kata kunci 'var' sesuai instruksi
    var $id;
    var $merek;
    var $tipe;
    var $hargaPerJam;
    var $tersedia;
    var $penyewa;
    var $durasiTerpakai;

    // Konstruktor: Harus mengisi semua properti awal
    public function __construct($id, $merek, $tipe, $hargaPerJam, $tersedia = true) {
        $this->id = $id;
        $this->merek = $merek;
        $this->tipe = $tipe;
        $this->hargaPerJam = $hargaPerJam;
        $this->tersedia = $tersedia;
        $this->penyewa = ""; // Set penyewa ke ""
        $this->durasiTerpakai = 0; // Set durasiTerpakai ke 0
    }

    // Metode: Menampilkan semua properti secara rapi
    public function tampilkanInfo() {
        $status = $this->tersedia ? "Tersedia" : "Sedang Disewa oleh " . $this->penyewa;
        
        echo "---------------------------------------\n";
        echo "ID SEPEDA : " . $this->id . "\n";
        echo "MEREK     : " . $this->merek . "\n";
        echo "TIPE      : " . $this->tipe . "\n";
        echo "HARGA/JAM : Rp " . number_format($this->hargaPerJam, 0, ',', '.') . "\n";
        echo "STATUS    : " . $status . "\n";
        echo "---------------------------------------\n\n";
    }

    // Metode: sewa($namaPenyewa, $jam) dengan aturan tertentu
    public function sewa($namaPenyewa, $jam) {
        // Jika $jam <= 0 tampilkan pesan error
        if ($jam <= 0) {
            return "[ERROR] Durasi sewa tidak valid!\n";
        }

        // Jika tersedia === false kembalikan pesan gagal
        if ($this->tersedia === false) {
            return "[GAGAL] Sepeda sedang digunakan oleh $this->penyewa.\n";
        }

        // Jika tersedia dan $jam > 0 set data sewa
        if ($this->tersedia && $jam > 0) {
            $this->tersedia = false;
            $this->penyewa = $namaPenyewa;
            $this->durasiTerpakai = $jam;
            return "[SUKSES] Sepeda $this->merek berhasil disewa oleh $namaPenyewa selama $jam jam.\n";
        }
    }

    // Metode: hitungBiaya() berdasarkan hargaPerJam * durasiTerpakai
    public function hitungBiaya() {
        if ($this->durasiTerpakai == 0) {
            return 0; // Jika tidak sedang disewa kembalikan 0
        }
        return $this->hargaPerJam * $this->durasiTerpakai;
    }

    // Metode: kembalikan() untuk reset status
    public function kembalikan() {
        if ($this->tersedia === true) {
            return "[INFO] Tidak ada yang sedang meminjam (sepeda tidak sedang disewa).\n";
        } else {
            $totalBiaya = $this->hitungBiaya(); // Panggil hitungBiaya()
            $peminjam = $this->penyewa;
            
            // Reset status
            $this->tersedia = true;
            $this->penyewa = "";
            $this->durasiTerpakai = 0;

            return "[SUKSES] Sepeda telah dikembalikan oleh $peminjam. Total Biaya: Rp " . number_format($totalBiaya, 0, ',', '.') . "\n";
        }
    }
}

// --- PENGUJIAN KODE ---

// Membungkus dengan tag <pre> agar output turun ke bawah secara otomatis
echo "<pre style='font-family: monospace;'>";
echo "<h2>Sistem Rental Sepeda - NIM 463</h2>";

// 1. Instansiasi Objek
$sepeda = new SepedaRental("S-001", "Polygon Xtrada", "Gunung", 25000);

// 2. Tampilkan Info Awal
echo "<b>STATUS AWAL:</b>\n";
$sepeda->tampilkanInfo();

// 3. Uji Sewa
echo "<b>PROSES SEWA:</b>\n";
echo $sepeda->sewa("Andi", 3);
$sepeda->tampilkanInfo();

// 4. Uji Sewa saat status sudah false
echo "<b>PROSES SEWA LAGI (SAAT MASIH DISEWA):</b>\n";
echo $sepeda->sewa("Budi", 2);
echo "\n";

// 5. Uji Kembalikan
echo "<b>PROSES PENGEMBALIAN:</b>\n";
echo $sepeda->kembalikan();
$sepeda->tampilkanInfo();

echo "</pre>";
?>