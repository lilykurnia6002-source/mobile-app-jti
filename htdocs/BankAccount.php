<?php

class BankAccount {
    // Properti private untuk melindungi data sensitif
    private string $accountNumber;
    private string $accountHolder;
    private float $balance;
    private string $accountType;

    public function __construct(string $accNo, string $name, float $initialDeposit, string $type) {
        $this->accountNumber = $accNo;
        $this->accountHolder = $name;
        $this->accountType = $type;
        
        // Menggunakan setter agar deposit awal tetap tervalidasi
        $this->balance = 0; 
        $this->deposit($initialDeposit);
    }

    // --- GETTER (Akses Data Terkontrol) ---
    
    public function getAccountNumber(): string {
        return $this->accountNumber;
    }

    public function getAccountHolder(): string {
        return $this->accountHolder;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    // --- SETTER (Perubahan Data dengan Validasi) ---

    public function setAccountHolder(string $newName): void {
        if (strlen($newName) < 3) {
            echo "Error: Nama pemilik akun terlalu pendek!\n";
            return;
        }
        $this->accountHolder = $newName;
    }

    // --- METODE OPERASIONAL (Logika Bisnis) ---

    public function deposit(float $amount): void {
        if ($amount <= 0) {
            echo "Error: Jumlah deposit harus lebih dari 0!\n";
            return;
        }
        $this->balance += $amount;
        echo "Deposit Berhasil: Rp " . number_format($amount, 0, ',', '.') . "\n";
    }

    public function withdraw(float $amount): void {
        if ($amount <= 0) {
            echo "Error: Jumlah penarikan tidak valid!\n";
            return;
        }
        if ($amount > $this->balance) {
            echo "Gagal: Saldo tidak mencukupi untuk menarik Rp " . number_format($amount, 0, ',', '.') . "\n";
            return;
        }
        $this->balance -= $amount;
        echo "Penarikan Berhasil: Rp " . number_format($amount, 0, ',', '.') . "\n";
    }

    public function displayStatus(): void {
        echo "\n========== RINGKASAN AKUN ==========\n";
        echo "No. Rekening : " . $this->accountNumber . "\n";
        echo "Pemilik      : " . $this->accountHolder . "\n";
        echo "Tipe Akun    : " . $this->accountType . "\n";
        echo "Total Saldo  : Rp " . number_format($this->balance, 0, ',', '.') . "\n";
        echo "====================================\n\n";
    }
}

// --- EKSEKUSI PROGRAM ---

// 1. Membuat akun baru
$myAcc = new BankAccount("123-456-789", "Budi Santoso", 500000, "Tabungan");

// 2. Menampilkan status awal
$myAcc->displayStatus();

// 3. Melakukan Deposit
$myAcc->deposit(200000);

// 4. Mencoba Penarikan (Berhasil)
$myAcc->withdraw(100000);

// 5. Mencoba Penarikan (Gagal karena saldo tidak cukup)
$myAcc->withdraw(1000000);

// 6. Mengubah nama pemilik akun lewat Setter
$myAcc->setAccountHolder("Budi S. Raharjo");

// 7. Menampilkan status akhir
$myAcc->displayStatus();