<?php
class Mahasiswa {
    public $nim;
    public $nama;

    public function __construct($nim, $nama) {
        $this->nim = $nim;
        $this->nama = $nama;
    }

    public function tampilkanData(): void {
        echo "NIM : " . $this->nim . "<br>";
        echo "NAMA : " . $this->nama . "<br>";
    }
}

$a = new Mahasiswa("E2000000", "Ani");
$b = new Mahasiswa("E2000001", "Bambang");

$b->tampilkanData();
?>