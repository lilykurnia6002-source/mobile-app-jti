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
    public $jarak; 
    public $tarifPerKm;
    public $diskonPersen; // Properti baru untuk menyimpan diskon
    private $status;

    public function __construct($driver, $penumpang, $jarak, $tarifPerKm = 5000) {
        $this->driver = $driver;
        $this->penumpang = $penumpang;
        $this->jarak = $jarak;
        $this->tarifPerKm = $tarifPerKm;
        $this->status = "Belum Jalan";
        
        // Diskon otomatis diterapkan saat object dibuat
        $this->diskonPersen = $this->hitungDiskonOtomatis();
    }

    // Fungsi untuk menghitung diskon berdasarkan tabel ketentuan
    private function hitungDiskonOtomatis(): int {
        $jenis = strtolower($this->driver->kendaraan);
        $j = $this->jarak;

        if ($jenis == "motor") {
            if ($j <= 5) return 0;
            if ($j <= 10) return 10;
            return 15;
        } elseif ($jenis == "mobil") {
            if ($j <= 5) return 0;
            if ($j <= 15) return 5;
            return 10;
        }
        return 0;
    }

    // Fungsi ini memungkinkan diskon diganti/dikustomisasi (sesuai instruksi)
    public function setCustomDiskon($persen): void {
        $this->diskonPersen = $persen;
    }

    public function hitungBiayaKotor(): float|int {
        return $this->jarak * $this->tarifPerKm;
    }

    public function hitungPotongan(): float|int {
        return ($this->diskonPersen / 100) * $this->hitungBiayaKotor();
    }

    public function hitungTotalBiaya(): float|int {
        return $this->hitungBiayaKotor() - $this->hitungPotongan();
    }

    public function infoPerjalanan(): void {
        echo "<b>Info Perjalanan</b><br>";
        echo "Penumpang : " . $this->penumpang . "<br>";
        echo "Driver : " . $this->driver->nama . " (" . $this->driver->kendaraan . ")<br>";
        echo "Jarak : " . $this->jarak . " km<br>";
        echo "Biaya Awal : Rp " . number_format($this->hitungBiayaKotor(), 0, ',', '.') . "<br>";
        echo "Diskon : " . $this->diskonPersen . "% (-Rp " . number_format($this->hitungPotongan(), 0, ',', '.') . ")<br>";
        echo "<b>Total Bayar : Rp " . number_format($this->hitungTotalBiaya(), 0, ',', '.') . "</b><br>";
        echo "Status : " . $this->status . "<br><br>";
    }
}

// --- EKSEKUSI DATA SESUAI GAMBAR ---

$driverMotor = new Driver("Andi", "Motor");
$driverMobil = new Driver("Budi", "Mobil");

// 1. Citra - Motor - 8km (Diskon 10%)
$p1 = new Perjalanan($driverMotor, "Citra", 8);
$p1->infoPerjalanan();

// 2. Dewi - Mobil - 15km (Diskon 5%)
$p2 = new Perjalanan($driverMobil, "Dewi", 15);
$p2->infoPerjalanan();

// 3. Eko - Motor - 12km (Diskon 15%)
$p3 = new Perjalanan($driverMotor, "Eko", 12);
$p3->infoPerjalanan();

?>