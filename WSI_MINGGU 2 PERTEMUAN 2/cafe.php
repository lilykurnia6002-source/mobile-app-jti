<?php

// =======================
// Class MenuCafe
// =======================
class MenuCafe {
    // Property
    public $namaMenu;
    public $harga;

    // Constructor
    public function __construct($namaMenu, $harga) {
        $this->namaMenu = $namaMenu;
        $this->harga = $harga;
    }

    // Method
    public function infoMenu() {
        return $this->namaMenu . " - Rp" . $this->harga;
    }
}


// =======================
// Class Pesanan
// =======================
class Pesanan {
    // Property
    public $daftarPesanan = [];

    // Method: tambah pesanan
    public function tambahPesanan(MenuCafe $menu) {
        $this->daftarPesanan[] = $menu;
    }

    // Method: hitung total
    public function hitungTotal() {
        $total = 0;
        foreach ($this->daftarPesanan as $menu) {
            $total += $menu->harga;
        }
        return $total;
    }

    // Method: tampilkan struk
    public function tampilkanStruk() {
        echo "<h2>Struk Pesanan Kafe</h2>";
        echo "<ul>";
        foreach ($this->daftarPesanan as $menu) {
            echo "<li>" . $menu->infoMenu() . "</li>";
        }
        echo "</ul>";
        echo "<strong>Total Bayar: Rp" . $this->hitungTotal() . "</strong>";
    }
}


// =======================
// Object & Eksekusi Program
// =======================

// Object menu
$kopi = new MenuCafe("Kopi Latte", 25000);
$teh  = new MenuCafe("Teh Manis", 15000);
$roti = new MenuCafe("Roti Bakar", 20000);

// Object pesanan
$pesanan = new Pesanan();
$pesanan->tambahPesanan($kopi);
$pesanan->tambahPesanan($roti);
$pesanan->tambahPesanan($teh);

// Tampilkan hasil
$pesanan->tampilkanStruk();

?>
