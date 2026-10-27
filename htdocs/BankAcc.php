<?php

class BankAccount {
    // Properti (semua private)
    private string $accountNumber;
    private string $owner;
    private float $balance;

    public function __construct(string $accountNumber, string $owner, float $balance = 0.0) {
        // Constructor validasi accountNumber tidak kosong
        if (empty($accountNumber)) {
            throw new InvalidArgumentException("Account number tidak boleh kosong.");
        }
        
        // Validasi balance >= 0
        if ($balance < 0) {
            throw new InvalidArgumentException("Saldo awal tidak boleh negatif.");
        }

        $this->accountNumber = $accountNumber;
        $this->owner = $owner;
        $this->balance = $balance;
    }

    // Getters (Nama_NIM_Golongan)
    public function getAccountNumber(): string {
        return $this->accountNumber;
    }

    public function getOwner(): string {
        return $this->owner;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function deposit(float $amount): void {
        // Validasi amount > 0
        if ($amount <= 0) {
            throw new InvalidArgumentException("Jumlah deposit harus lebih dari 0.");
        }
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void {
        // Validasi amount > 0 dan cek saldo cukup
        if ($amount <= 0) {
            throw new InvalidArgumentException("Jumlah penarikan harus lebih dari 0.");
        }
        
        if ($amount > $this->balance) {
            // Jika tidak cukup, lempar RuntimeException
            throw new RuntimeException("Saldo tidak mencukupi untuk melakukan penarikan.");
        }
        
        $this->balance -= $amount;
    }
}

// --- Test Output Sesuai Spesifikasi ---
try {
    // 1. Buat akun dengan balance 1000
    $myAccount = new BankAccount("ACC001", "Budi Santoso", 1000);
    echo "Saldo awal: " . $myAccount->getBalance() . "\n";

    // 2. Deposit 500 -> balance 1500
    $myAccount->deposit(500);
    echo "Saldo setelah deposit 500: " . $myAccount->getBalance() . "\n";

    // 3. Withdraw 2000 -> RuntimeException
    echo "Mencoba menarik 2000...\n";
    $myAccount->withdraw(2000);

} catch (RuntimeException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}