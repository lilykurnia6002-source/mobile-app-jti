<?php
/**
 * Tugas: Sistem Manajemen Sekolah (OOP PHP)
 * Nama File: sistem_sekolah_463.php
 * Konsep: Abstract Class & Interface
 */

// 1. INTERFACE: Kontrak untuk sistem login
interface Autentikasi {
    public function login($username, $password);
}

// 2. ABSTRACT CLASS: Cetak biru dasar untuk semua pengguna
abstract class Pengguna_463 implements Autentikasi {
    protected $nama;
    protected $peran;

    public function __construct($nama, $peran) {
        $this->nama = $nama;
        $this->peran = $peran;
    }

    // Abstract method: Wajib diimplementasikan secara berbeda oleh tiap child class
    abstract public function tampilkanMenu();

    // Implementasi method dari interface
    public function login($username, $password) {
        echo "--------------------------------------------------<br>";
        echo "<b>[SIMULASI LOGIN]</b><br>";
        echo "Mencoba masuk sebagai: " . $this->peran . "<br>";
        echo "Berhasil! Selamat datang, <b>" . $this->nama . "</b>.<br>";
        echo "--------------------------------------------------<br>";
    }
}

// 3. CHILD CLASS: ADMIN
class Admin_463 extends Pengguna_463 {
    public function tampilkanMenu() {
        echo "MENU ADMIN:<br>";
        echo "1. Pendaftaran Guru, Siswa, dan Admin baru.<br>";
        echo "2. Mengelola data kelas.<br>";
    }

    public function daftarkanUser($namaBaru, $peranBaru) {
        echo "Sistem: Berhasil mendaftarkan " . $namaBaru . " sebagai " . $peranBaru . ".<br>";
    }
}

// 4. CHILD CLASS: GURU
class Guru_463 extends Pengguna_463 {
    public function tampilkanMenu() {
        echo "MENU GURU:<br>";
        echo "1. Input Tugas Siswa.<br>";
        echo "2. Memberikan Nilai Akademik.<br>";
    }

    public function beriNilai($namaSiswa, $nilai) {
        echo "Guru: Memberikan nilai " . $nilai . " kepada siswa bernama " . $namaSiswa . ".<br>";
    }
}

// 5. CHILD CLASS: SISWA
class Siswa_463 extends Pengguna_463 {
    private $kumpulanNilai = [85, 90, 78, 92]; // Simulasi data

    public function tampilkanMenu() {
        echo "MENU SISWA:<br>";
        echo "1. Lihat daftar tugas terbaru.<br>";
        echo "2. Lihat rekap nilai dan rata-rata.<br>";
    }

    public function hitungRataRata() {
        $total = array_sum($this->kumpulanNilai);
        $rataRata = $total / count($this->kumpulanNilai);
        echo "Siswa: Rata-rata nilai Anda saat ini adalah <b>" . $rataRata . "</b>.<br>";
    }
}

// --- MAIN PROGRAM (SIMULASI PENGGUNAAN) ---

echo "<h2>SISTEM MANAJEMEN SEKOLAH - POLIJE</h2>";

// Simulasi Aksi Admin
$adminObj = new Admin_463("Budi Santoso", "Admin");
$adminObj->login("admin_463", "password123");
$adminObj->tampilkanMenu();
$adminObj->daftarkanUser("Andi", "Siswa");
echo "<br>";

// Simulasi Aksi Guru
$guruObj = new Guru_463("Ibu Ratna", "Guru");
$guruObj->login("ratna_guru", "guru463");
$guruObj->tampilkanMenu();
$guruObj->beriNilai("Andi", 95);
echo "<br>";

// Simulasi Aksi Siswa
$siswaObj = new Siswa_463("Andi", "Siswa");
$siswaObj->login("andi_siswa", "siswa463");
$siswaObj->tampilkanMenu();
$siswaObj->hitungRataRata();

?>