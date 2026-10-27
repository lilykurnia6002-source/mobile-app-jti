<?php

// 1. Interface: Menjamin setiap pengguna punya kemampuan login
interface IAutentikasi {
    public function login($username, $password);
}

// 2. Abstract Class: Template dasar untuk semua kategori user
abstract class Pengguna implements IAutentikasi {
    protected $nama;
    protected $username;
    protected $role;

    public function __construct($nama, $username, $role) {
        if (empty($nama)) {
            throw new Exception("Error: Nama tidak boleh kosong.");
        }
        $this->nama = $nama;
        $this->username = $username;
        $this->role = $role;
    }

    // Abstract method: Wajib diimplementasikan oleh class anak
    abstract public function aksesFitur();

    public function login($username, $password) {
        // Simulasi sederhana: password dianggap benar jika sama dengan username
        if ($this->username === $username && $password === $username) {
            return true;
        }
        throw new Exception("Login Gagal: Username atau Password salah.");
    }

    public function getNama() { return $this->nama; }
    public function getRole() { return $this->role; }
}

// 3. Implementasi Class Admin
class Admin extends Pengguna {
    public function __construct($nama, $username) {
        parent::__construct($nama, $username, "Admin");
    }

    public function aksesFitur() {
        return "Mengelola data kelas, pendaftaran user, dan konfigurasi sistem.";
    }
}

// 4. Implementasi Class Guru
class Guru extends Pengguna {
    public function __construct($nama, $username) {
        parent::__construct($nama, $username, "Guru");
    }

    public function aksesFitur() {
        return "Memberikan tugas dan menginput nilai siswa.";
    }

    // Fitur Guru: Memberi Tugas & Nilai
    public function beriTugas($namaTugas) {
        return "Guru {$this->nama} telah mempublikasikan tugas: $namaTugas.";
    }

    public function inputNilai($namaSiswa, $nilai) {
        if ($nilai < 0 || $nilai > 100) {
            throw new Exception("Error: Nilai untuk $namaSiswa harus di antara 0 - 100 (Input: $nilai).");
        }
        return "Berhasil menginput nilai $nilai untuk siswa: $namaSiswa.";
    }
}

// 5. Implementasi Class Siswa
class Siswa extends Pengguna {
    private $nilai = [];

    public function __construct($nama, $username, $nilai = []) {
        parent::__construct($nama, $username, "Siswa");
        $this->nilai = $nilai;
    }

    public function aksesFitur() {
        return "Melihat daftar nilai dan rata-rata akademik.";
    }

    // Fitur Siswa: Melihat Nilai & Rata-rata
    public function lihatDaftarNilai() {
        if (empty($this->nilai)) {
            throw new Exception("Error: Belum ada data nilai untuk siswa {$this->nama}.");
        }
        return "Daftar Nilai: " . implode(", ", $this->nilai);
    }

    public function hitungRataRata() {
        if (empty($this->nilai)) {
            throw new Exception("Error: Tidak bisa menghitung rata-rata karena data nilai kosong.");
        }
        $rataRata = array_sum($this->nilai) / count($this->nilai);
        return number_format($rataRata, 2);
    }
}

// --- SIMULASI SISTEM (MAIN PROGRAM) ---

try {
    // A. Pendaftaran (Simulasi Database)
    $db_user = [
        new Admin("Lily", "lily_admin"),
        new Guru("Pak Rafli", "rafli_guru"),
        new Siswa("Budi", "budi_siswa", [85, 90, 78])
    ];

    echo "=== SIMULASI SISTEM MANAJEMEN SEKOLAH ===\n\n";

    // B. Simulasi Login Guru (Pak Rafli)
    $input_user = "rafli_guru";
    $input_pass = "rafli_guru";

    $user_aktif = null;
    foreach ($db_user as $u) {
        if ($u->login($input_user, $input_pass)) {
            $user_aktif = $u;
            break;
        }
    }

    if ($user_aktif) {
        echo "Login Berhasil!\n";
        echo "Selamat Datang, " . $user_aktif->getNama() . " (" . $user_aktif->getRole() . ")\n";
        echo "Hak Akses: " . $user_aktif->aksesFitur() . "\n\n";

        // C. Implementasi Fitur Guru: Memberi Tugas & Nilai
        if ($user_aktif instanceof Guru) {
            echo "--- AKTIVITAS GURU ---\n";
            echo $user_aktif->beriTugas("Pemrograman Web OOP") . "\n";
            echo $user_aktif->inputNilai("Budi", 88) . "\n";
            // echo $user_aktif->inputNilai("Andi", 150); // Hapus komentar untuk tes Error
        }
    }

    echo "\n------------------------------------------\n\n";

    // D. Implementasi Fitur Siswa: Melihat Nilai & Rata-rata
    // Kita ambil objek Budi dari database ($db_user[2])
    $siswa_budi = $db_user[2];
    if ($siswa_budi instanceof Siswa) {
        echo "--- AKTIVITAS SISWA ---\n";
        echo "Nama Siswa: " . $siswa_budi->getNama() . "\n";
        echo $siswa_budi->lihatDaftarNilai() . "\n";
        echo "Rata-rata Nilai: " . $siswa_budi->hitungRataRata() . "\n";
    }

} catch (Exception $e) {
    // Menangkap semua jenis error (Login gagal, Nilai tidak valid, Nilai kosong, dll)
    echo "\n[SISTEM ERROR]: " . $e->getMessage() . "\n";
    echo "Terjadi pada file: " . $e->getFile() . " (Baris: " . $e->getLine() . ")\n";

} finally {
    echo "\n=== PROSES SELESAI: Sesi Ditutup Secara Aman ===\n";
}

?>