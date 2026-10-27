<?php
// Kelas Induk
class Payment {
    public $amount;

    public function __construct($amount) {
        $this->amount = $amount;
    }
}

// Kelas Turunan untuk QRIS
class Qris extends Payment {

    public function processPayment() {
        return "Pembayaran sebesar Rp " . number_format($this->amount, 0, ',', '.') . 
        " berhasil menggunakan QRIS.";
    }

}

// Inisialisasi dengan nominal 15000
$pembayaran = new Qris(15000);
echo $pembayaran->processPayment();
?>