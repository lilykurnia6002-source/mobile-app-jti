<?php
class ParkiranMobil {
    // Property (private)
    private $namaParkiran;
    private $kapasitas;
    private $jumlahMobil;

    // Constructor
    public function __construct($namaParkiran, $kapasitas) {
        $this->namaParkiran = $namaParkiran;
        $this->kapasitas = $kapasitas;
        $this->jumlahMobil = 0;
    }

    // Method mobil masuk
    public function mobilMasuk() {
        if ($this->jumlahMobil < $this->kapasitas) {
            $this->jumlahMobil++;
            return "Mobil masuk ke parkiran";
        } else {
            return "Parkiran penuh";
        }
    }

    // Method mobil keluar
    public function mobilKeluar() {
        if ($this->jumlahMobil > 0) {
            $this->jumlahMobil--;
            return "Mobil keluar dari parkiran";
        } else {
            return "Parkiran masih kosong";
        }
    }

    // Method tampilkan status parkiran
    public function tampilkanStatus() {
        return "Nama Parkiran: " . $this->namaParkiran .
               "<br>Kapasitas: " . $this->kapasitas .
               "<br>Jumlah Mobil: " . $this->jumlahMobil;
    }
}

// Object
$parkiran = new ParkiranMobil("Parkiran Mall Kota", 5);

// Proses
echo $parkiran->mobilMasuk() . "<br>";
echo $parkiran->mobilMasuk() . "<br>";
echo $parkiran->mobilMasuk() . "<br>";
echo $parkiran->mobilKeluar() . "<br>";

// Output
echo "<br>" . $parkiran->tampilkanStatus();
?>
