<?php

class Produk {
    public $nama;
    public $harga;
    public $stok;
    public $kategori;

    public function __construct($nama, $harga, $stok, $kategori) {
        $this->nama = $nama;
        $this->harga = $harga;
        $this->stok = $stok;
        $this->kategori = $kategori;
    }

    // Fitur: Menerapkan Diskon
    public function terapkanDiskon($persentase) {
        $potongan = ($persentase / 100) * $this->harga;
        $this->harga -= $potongan;
    }

    // Fitur: Memeriksa ketersediaan barang
    public function cekStok() {
        return ($this->stok > 0) ? $this->stok : "Habis";
    }
}

class TokoElektronik {
    private $inventaris = [];

    // Fitur: Menambahkan produk ke toko
    public function tambahProduk($nama, $harga, $stok, $kategori) {
        $produkBaru = new Produk($nama, $harga, $stok, $kategori);
        $this->inventaris[] = $produkBaru;
        echo "✓ Produk '{$nama}' berhasil ditambahkan ke rak.<br>";
    }

    // Fitur: Menampilkan informasi detail per produk
    public function tampilkanKatalog() {
        echo "<h2>KATALOG TOKO ELEKTRONIK</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>
                <tr>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>";
        
        foreach ($this->inventaris as $p) {
            echo "<tr>
                    <td>{$p->nama}</td>
                    <td>{$p->kategori}</td>
                    <td>Rp " . number_format($p->harga, 0, ',', '.') . "</td>
                    <td>{$p->stok}</td>
                    <td>" . $p->cekStok() . "</td>
                  </tr>";
        }
        echo "</table>";
    }

    // Fitur: Menghitung total nilai inventaris
    public function hitungTotalNilai() {
        $total = 0;
        foreach ($this->inventaris as $p) {
            $total += ($p->harga * $p->stok);
        }
        return $total;
    }

    // Fungsi helper untuk mencari produk berdasarkan nama (untuk diskon)
    public function cariProduk($nama) {
        foreach ($this->inventaris as $p) {
            if (strtolower($p->nama) == strtolower($nama)) return $p;
        }
        return null;
    }
}

// --- SIMULASI PENGGUNAAN ---

$toko = new TokoElektronik();

// 1. Menambahkan produk
$toko->tambahProduk("Laptop Gaming", 15000000, 5, "Komputer");
$toko->tambahProduk("Smartphone X", 5000000, 10, "Gadget");
$toko->tambahProduk("Keyboard Mekanik", 800000, 0, "Aksesoris");

echo "<hr>";

// 2. Menerapkan diskon pada Smartphone (misal diskon 10%)
$item = $toko->cariProduk("Smartphone X");
if ($item) {
    $item->terapkanDiskon(10);
    echo "📢 Promo: Smartphone X diskon 10%!<br>";
}

// 3. Menampilkan detail
$toko->tampilkanKatalog();

// 4. Menampilkan total nilai inventaris
echo "<br><strong>Total Nilai Inventaris Toko: Rp " . number_format($toko->hitungTotalNilai(), 0, ',', '.') . "</strong>";

?>