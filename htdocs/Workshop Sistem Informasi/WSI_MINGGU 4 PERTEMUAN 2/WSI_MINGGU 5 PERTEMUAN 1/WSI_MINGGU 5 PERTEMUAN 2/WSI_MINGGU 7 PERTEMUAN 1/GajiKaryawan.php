<?php
// 1. Interface: Menentukan aturan dasar
interface Penggajian {
    public function hitungGaji();
}

// 2. Abstract Class: Menyimpan data umum
abstract class Karyawan implements Penggajian {
    protected $nama;
    protected $gajiPokok;

    public function __construct($nama, $gajiPokok) {
        $this->nama = $nama;
        $this->gajiPokok = $gajiPokok;
    }

    // Metode abstrak yang harus diisi oleh kelas anak
    abstract public function hitungGaji();
    
    public function getNama() {
        return $this->nama;
    }
}

// 3. Concrete Class: Karyawan Tetap (Mendapat Bonus)
class KaryawanTetap extends Karyawan {
    private $bonus = 500000;

    public function hitungGaji() {
        return $this->gajiPokok + $this->bonus;
    }
}

// 4. Concrete Class: Karyawan Kontrak (Gaji sesuai standar)
class KaryawanKontrak extends Karyawan {
    public function hitungGaji() {
        return $this->gajiPokok;
    }
}

// 5. Concrete Class: Freelance (Gaji berdasarkan jam kerja)
class Freelance extends Karyawan {
    private $jamKerja;

    public function __construct($nama, $tarifPerJam, $jamKerja) {
        parent::__construct($nama, $tarifPerJam);
        $this->jamKerja = $jamKerja;
    }

    public function hitungGaji() {
        return $this->gajiPokok * $this->jamKerja;
    }
}

// --- IMPLEMENTASI POLIMORFISME ---

$daftarKaryawan = [
    new KaryawanTetap("Andi", 4000000),
    new KaryawanKontrak("Budi", 3000000),
    new Freelance("Cici", 50000, 100) // 50rb per jam, kerja 100 jam
];

echo "<h3>Laporan Gaji Karyawan</h3>";
foreach ($daftarKaryawan as $k) {
    echo "Nama: " . $k->getNama() . " | Total Gaji: Rp " . number_format($k->hitungGaji(), 0, ',', '.') . "<br>";
}
?>