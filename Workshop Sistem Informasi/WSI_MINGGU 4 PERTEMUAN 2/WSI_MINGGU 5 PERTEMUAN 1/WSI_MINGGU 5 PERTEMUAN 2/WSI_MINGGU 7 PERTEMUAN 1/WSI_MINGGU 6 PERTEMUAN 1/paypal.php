<?php
// Kelas Induk
class Payment {
    public $amount;

    public function __construct($amount) {
        $this->amount = $amount;
    }
}

// Kelas Turunan untuk PayPal
class PayPal extends Payment {

    public function processPayment() {
        // Menggunakan number_format agar tampilan nominal rapi (50.000)
        return "Pembayaran sebesar Rp " . number_format($this->amount, 0, ',', '.') . 
        " berhasil menggunakan PayPal.";
    }

}

// Inisialisasi dengan nominal 50000
$pembayaran_paypal = new PayPal(50000);
echo $pembayaran_paypal->processPayment();
?>