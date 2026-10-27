<?php
// perpustakaan.php (namafile disesuaikan)

class Buku {
    var $kode;
    var $judul;
    var $pengarang;

    function __construct($kode, $judul, $pengarang) {
        $this->kode = $kode;
        $this->judul = $judul;
        $this->pengarang = $pengarang;
    }

    function tampilkanInfo() {
        echo "Kode: {$this->kode}" . PHP_EOL;
        echo "Judul: {$this->judul}" . PHP_EOL;
        echo "Pengarang: {$this->pengarang}" . PHP_EOL;
    }

    function ubahJudul($judulBaru) {
        $this->judul = $judulBaru;
    }
}

$koleksi = [];

$b1 = new Buku("B001", "Pemrograman PHP Dasar", "Andi");
$b2 = new Buku("B002", "Algoritma & Struktur Data", "Budi");

$koleksi[] = $b1;
$koleksi[] = $b2;

echo "Sebelum perubahan:" . PHP_EOL;
$b1->tampilkanInfo();
echo "----" . PHP_EOL;
$b2->tampilkanInfo();
echo "----" . PHP_EOL;

// ubah judul buku ke-2 lalu tampilkan kembali
$b2->ubahJudul("Algoritma Lanjut");
echo "Sesudah perubahan judul buku B002:" . PHP_EOL;
$b2->tampilkanInfo();
?>