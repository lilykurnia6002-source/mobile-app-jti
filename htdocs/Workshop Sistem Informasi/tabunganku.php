<?php
class TabunganKu {
    // Property
    public $namaPemilik;
    public $saldo;

    // Constructor
    public function __construct($namaPemilik, $saldoAwal) {
        $this->namaPemilik = $namaPemilik;
        $this->saldo = $saldoAwal;
    }

    // Method setor tabungan
    public function setor($jumlah) {
        $this->saldo += $jumlah;
    }

    // Method tarik tabungan
    public function tarik($jumlah) {
        if ($jumlah > $this->saldo) {
            return "Saldo tidak mencukupi";
        } else {
            $this->saldo -= $jumlah;
            return "Penarikan berhasil";
        }
    }

    // Method tampilkan saldo
    public function tampilkanSaldo() {
        return "Nama Pemilik: " . $this->namaPemilik .
               "<br>Saldo Saat Ini: Rp " . $this->saldo;
    }
}

// Object
$tabungan = new TabunganKu("Lily", 500000);

// Proses
$tabungan->setor(200000);
$pesan = $tabungan->tarik(100000);

// Output
echo $tabungan->tampilkanSaldo();
echo "<br>Status Transaksi: " . $pesan;
?>
