<?php
class Payment {
    public $amount;
    protected $billNumber;

    public function __construct($amount, $billNumber) {
        $this->amount = $amount;
        $this->billNumber = $billNumber;
    }
}

class MbankingBRI extends Payment {
    private $bankName = "Bank BRI";

    public function processPayment() {
        // Logika sederhana untuk simulasi prosedur m-Banking
        return "--- Resi Pembayaran BRIMO ---\n" .
               "Bank           : " . $this->bankName . "\n" .
               "Nomor Tagihan  : " . $this->billNumber . "\n" .
               "Total Bayar    : Rp " . number_format($this->amount, 0, ',', '.') . "\n" .
               "Status         : PEMBAYARAN BERHASIL\n" .
               "------------------------------";
    }
}

// Inisialisasi: Nominal 3.000.000 dengan Nomor Tagihan (NIM) contoh
$pembayaran_mhs = new MbankingBRI(3000000, "2024001463");
echo $pembayaran_mhs->processPayment();
?>