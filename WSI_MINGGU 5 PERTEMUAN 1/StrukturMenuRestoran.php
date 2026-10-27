<?php
class Menu {
    // Properti (Sama seperti Animal: name, age, species)
    protected $nama;
    protected $harga;
    protected $kategori;

    // Constructor
    public function __construct($nama, $harga, $kategori) {
        $this->nama = $nama;
        $this->harga = $harga;
        $this->kategori = $kategori;
    }

    // Metode umum
    public function hitungPajak() {
        return "Pajak untuk " . $this->nama . " adalah 10%.";
    }

    public function getInfo() {
        return "Menu: " . $this->nama . " | Harga: Rp" . number_format($this->harga, 0, ',', '.') . " | Kategori: " . $this->kategori;
    }

    public function sajikan() {
        return "Menu standar sedang disiapkan.";
    }
}

class Makanan extends Menu {
    protected $isPedas;

    public function __construct($nama, $harga, $isPedas = false) {
        parent::__construct($nama, $harga, "Makanan");
        $this->isPedas = $isPedas;
    }

    public function tambahNasi() {
        $this->harga += 5000;
        return "Nasi ditambahkan ke " . $this->nama . ". Harga bertambah.";
    }

    public function sajikan() {
        return $this->nama . " disajikan hangat di atas piring.";
    }
}

class Minuman extends Menu {
    protected $suhu; // Panas atau Dingin

    public function __construct($nama, $harga, $suhu) {
        parent::__construct($nama, $harga, "Minuman");
        $this->suhu = $suhu;
    }

    public function tambahEs() {
        return $this->nama . " sekarang diberikan es batu ekstra.";
    }

    public function sajikan() {
        return $this->nama . " dituangkan ke dalam gelas dalam kondisi " . $this->suhu . ".";
    }
}

class Kopi extends Minuman {
    protected $jenisBiji;

    public function __construct($nama, $harga, $suhu, $jenisBiji) {
        parent::__construct($nama, $harga, $suhu);
        $this->jenisBiji = $jenisBiji;
    }

    public function seduh() {
        return "Menyeduh kopi " . $this->nama . " menggunakan biji " . $this->jenisBiji . ".";
    }

    public function sajikan() {
        $parentSajikan = parent::sajikan();
        return $parentSajikan . " Aromanya sangat harum!";
    }
}

// --- Contoh Penggunaan ---

echo "<h3>Daftar Menu Restoran</h3>";

// Membuat objek Makanan
$nasiGoreng = new Makanan("Nasi Goreng Spesial", 25000, true);
echo $nasiGoreng->getInfo() . "<br>";
echo $nasiGoreng->tambahNasi() . "<br>";
echo $nasiGoreng->sajikan() . "<br><br>";

// Membuat objek Kopi
$latte = new Kopi("Cafe Latte", 30000, "Panas", "Arabica");
echo $latte->getInfo() . "<br>";
echo $latte->seduh() . "<br>";
echo $latte->sajikan() . "<br>";
?>