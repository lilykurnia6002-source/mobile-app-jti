<?php
class Mahasiswa {
    // Property
    public $nama;
    public $nim;

    // Constructor
    public function __construct($nama, $nim) {
        $this->nama = $nama;
        $this->nim = $nim;
    }

    // Method
    public function tampilkanData() {
        return "Nama Mahasiswa: " . $this->nama . "<br>" .
               "NIM: " . $this->nim;
    }
}

// Object
$mahasiswa1 = new Mahasiswa("Lily", "E41250463");

// Output
echo $mahasiswa1->tampilkanData();
?>
