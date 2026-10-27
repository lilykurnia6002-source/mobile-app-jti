<?php

class Driver {
    public $nama;
    public $kendaraan;

    public function __construct($nama, $kendaraan) {
        $this->nama = $nama;
        $this->kendaraan = $kendaraan;
    }

    public function infoDriver(): void {
        echo "Driver : " . $this->nama . "<br>";
        echo "Kendaraan : " . $this->kendaraan . "<br>";
    }
}

class Perjalanan {
    public $driver;
    public $penumpang;
    public $jarak; // dalam km
    public $tarifPerKm;
    private $status;

    public function __construct($driver, $penumpang, $jarak, $tarifPerKm = 5000) {
        $this->driver = $driver;
        $this->penumpang = $penumpang;
        $this->jarak = $jarak;
        $this->tarifPerKm = $tarifPerKm;
        $this->status = "Belum Jalan";
    }

    public function mulaiPerjalanan(): void {
        $this->status = "Sedang Jalan";
        echo "Perjalanan dimulai<br>";
    }

    public function selesaiPerjalanan(): void {
        $this->status = "Selesai";
        echo "Perjalanan selesai<br>";
    }

    public function hitungBiaya(): float|int {
        return $this->jarak * $this->tarifPerKm;
    }

    public function infoPerjalanan(): void {
        echo "Penumpang : " . $this->penumpang . "<br>";
        echo "Driver : " . $this->driver->nama . "<br>";
        echo "Kendaraan : " . $this->driver->kendaraan . "<br>";
        echo "Jarak : " . $this->jarak . " km<br>";
        echo "Status : " . $this->status . "<br>";
        echo "Biaya Perjalanan : Rp " . $this->hitungBiaya() . "<br>";
    }
}

// --- EKSEKUSI PROGRAM ---

// Membuat object Driver
$driver1 = new Driver("Andi", "Motor");

// Membuat object Perjalanan
$perjalanan1 = new Perjalanan($driver1, "Budi", 10);

// Menampilkan info awal
$perjalanan1->infoPerjalanan();
echo "<hr>";

// Memulai perjalanan
$perjalanan1->mulaiPerjalanan();
$perjalanan1->infoPerjalanan();
echo "<hr>";

// Selesai perjalanan
$perjalanan1->selesaiPerjalanan();
$perjalanan1->infoPerjalanan();

?>