<?php
// Kelas Induk
class Payment {
    public $amount;

    public function __construct($amount) {
        $this->amount = $amount;
    }
}

// Kelas Turunan untuk Virtual Account
class VirtualAccount extends Payment {

    public function processPayment() {
        // Menggunakan number_format agar tampilan nominal lebih rapi (25.000)
        return "Pembayaran sebesar Rp " . number_format($this->amount, 0, ',', '.') . 
        " berhasil menggunakan Virtual Account.";
    }

}

// Inisialisasi dengan nominal 25000
$pembayaran_va = new VirtualAccount(25000);
echo $pembayaran_va->processPayment();
?>