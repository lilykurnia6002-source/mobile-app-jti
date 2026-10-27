<?php

class Ponsel {
    public $merk;
    public $model;
    public $apps = [];

    // Konstruktor mengisi merk dan model, serta menambahkan apps bawaan
    public function __construct($merk, $model, $appsBawaan = ['Telepon', 'SMS', 'Jam']) {
        $this->merk = $merk;
        $this->model = $model;
        $this->apps = $appsBawaan;
    }

    // Metode untuk menampilkan info ponsel
    public function info() {
        echo "Ponsel: {$this->merk} {$this->model}<br>";
        echo "Aplikasi bawaan: " . implode(', ', $this->apps) . "<br>";
    }

    // Metode untuk menambah aplikasi
    public function pasangApp($namaApp) {
        $this->apps[] = $namaApp;
    }
}

// Fungsi global (helper) untuk menampilkan garis pemisah
function garisPembatas() {
    echo "<hr>";
}

// Penggunaan
$p = new Ponsel('iPhone', '14 Pro', ['Telepon', 'SMS', 'Music']);
$p->info();
garisPembatas();

$p->pasangApp('Maps');
$p->info();

?>