<?php

// Class dasar untuk semua orang di RS
class Person {
    public $id;
    public $nama;
    public $peran;

    public function __construct($id, $nama, $peran) {
        $this->id = $id;
        $this->nama = $nama;
        $this->peran = $peran;
    }
}

// Class Tenaga Medis (Inheritance dari Person)
class TenagaMedis extends Person {
    public $spesialisasi;

    public function __construct($id, $nama, $peran, $spesialisasi) {
        parent::__construct($id, $nama, $peran);
        $this->spesialisasi = $spesialisasi;
    }
}

// Class Pasien (Inheritance dari Person)
class Pasien extends Person {
    public $riwayat_medis = [];
    public $status_inap = "Rawat Jalan";

    public function __construct($id, $nama) {
        parent::__construct($id, $nama, "Pasien");
    }

    public function tambahRekam($diagnosa, $obat) {
        $this->riwayat_medis[] = ["diagnosa" => $diagnosa, "obat" => $obat];
    }
}

// Class Utama Sistem Rumah Sakit
class RumahSakit {
    private $daftarMedis = [];
    private $daftarPasien = [];

    // 1. Menambahkan Dokter/Perawat/Pasien
    public function tambahTenagaMedis($id, $nama, $peran, $spesialisasi) {
        $tm = new TenagaMedis($id, $nama, $peran, $spesialisasi);
        $this->daftarMedis[] = $tm;
        echo "✓ $peran $nama berhasil ditambahkan.<br>";
    }

    public function tambahPasien($id, $nama) {
        $p = new Pasien($id, $nama);
        $this->daftarPasien[] = $p;
        echo "✓ Pasien $nama berhasil terdaftar.<br>";
    }

    // 2 & 3. Pemeriksaan dan Riwayat Medis
    public function periksaPasien($idPasien, $diagnosa, $obat) {
        foreach ($this->daftarPasien as $p) {
            if ($p->id == $idPasien) {
                $p->tambahRekam($diagnosa, $obat);
                echo "✓ Pemeriksaan selesai untuk $p->nama. Diagnosa: $diagnosa.<br>";
                return;
            }
        }
        echo "! Pasien tidak ditemukan.<br>";
    }

    // 5. Simulasi Rawat Inap (Admit/Discharge)
    public function updateStatusInap($idPasien, $status) {
        foreach ($this->daftarPasien as $p) {
            if ($p->id == $idPasien) {
                $p->status_inap = (strtolower($status) == "admit") ? "Rawat Inap" : "Rawat Jalan";
                $aksi = (strtolower($status) == "admit") ? "Masuk Rawat Inap" : "Keluar (Discharge)";
                echo "✓ Update: $p->nama sekarang $aksi.<br>";
                return;
            }
        }
    }

    // 4. Menampilkan Daftar
    public function tampilkanData() {
        echo "<h3>DAFTAR TENAGA MEDIS</h3>";
        foreach ($this->daftarMedis as $tm) {
            echo "[$tm->id] $tm->nama - $tm->peran ($tm->spesialisasi)<br>";
        }

        echo "<h3>DAFTAR PASIEN & RIWAYAT</h3>";
        foreach ($this->daftarPasien as $p) {
            echo "<strong>[$p->id] $p->nama | Status: $p->status_inap</strong><br>";
            foreach ($p->riwayat_medis as $r) {
                echo "- Diagnosa: {$r['diagnosa']} | Obat: {$r['obat']}<br>";
            }
            echo "<br>";
        }
    }
}

// --- EKSEKUSI ---
$rs = new RumahSakit();

$rs->tambahTenagaMedis("D01", "Dr. Andi", "Dokter", "Bedah");
$rs->tambahTenagaMedis("P01", "Suster Maya", "Perawat", "IGD");
$rs->tambahPasien("PS01", "Budi");

$rs->periksaPasien("PS01", "Flu Berat", "Paracetamol");
$rs->updateStatusInap("PS01", "admit"); // Simulasi Admit

$rs->tampilkanData();
?>