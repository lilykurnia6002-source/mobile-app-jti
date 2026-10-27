<?php

class Bike {
    private int $id;
    private string $model;
    private float $pricePerHour;
    private int $stock;

    // Constructor dengan validasi
    public function __construct(int $id, string $model, float $pricePerHour, int $stock) {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID harus > 0");
        }
        if ($pricePerHour < 0) {
            throw new InvalidArgumentException("Harga per jam tidak boleh negatif");
        }
        if ($stock < 0) {
            throw new InvalidArgumentException("Stock tidak boleh negatif");
        }

        $this->id = $id;
        $this->model = $model;
        $this->pricePerHour = $pricePerHour;
        $this->stock = $stock;
    }

    // Getter public
    public function getId(): int {
        return $this->id;
    }

    public function getModel(): string {
        return $this->model;
    }

    public function getPricePerHour(): float {
        return $this->pricePerHour;
    }

    public function getStock(): int {
        return $this->stock;
    }

    // Method SETTER untuk mengubah harga per jam
    public function setPricePerHour(float $pricePerHour): void {
        if ($pricePerHour < 0) {
            throw new InvalidArgumentException("Harga per jam tidak boleh negatif");
        }
        $this->pricePerHour = $pricePerHour;
    }

    // Operasi bisnis - sewa sepeda
    public function rent(int $quantity = 1): void {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Quantity harus > 0");
        }

        if ($this->stock < $quantity) {
            throw new RuntimeException("Stock tidak mencukupi");
        }

        $this->stock -= $quantity;
    }

    // Kembalikan sepeda
    public function returnBike(int $quantity = 1): void {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Quantity harus > 0");
        }

        $this->stock += $quantity;
    }

    // Hitung biaya sewa
    public function calculateFee(int $hours, int $quantity = 1): float {
        if ($hours < 0) {
            throw new InvalidArgumentException("Jam sewa tidak boleh negatif");
        }

        return $this->pricePerHour * $hours * $quantity;
    }

    public function getInfo(): string {
        return sprintf(
            "ID: %d | Model: %s | Harga/jam: Rp %.2f | Stock: %d",
            $this->id,
            $this->model,
            $this->pricePerHour,
            $this->stock
        );
    }
}


// ======================
// MAIN PROGRAM
// ======================

try {

    $bike = new Bike(1, "Polygon Heist", 50000.0, 5);

    echo "Informasi awal: " . $bike->getInfo() . "\n";

    // Ubah harga per jam
    $bike->setPricePerHour(60000.0);
    echo "Setelah harga diubah: " . $bike->getInfo() . "\n";

    // Sewa 2 unit
    $bike->rent(2);
    echo "Setelah sewa 2 unit: " . $bike->getInfo() . "\n";

    // Hitung biaya sewa 3 jam untuk 2 unit
    $fee = $bike->calculateFee(3, 2);
    echo "Biaya sewa 3 jam untuk 2 unit: Rp " . number_format($fee, 2, ',', '.') . "\n";

    // Kembalikan 1 unit
    $bike->returnBike(1);
    echo "Setelah kembalikan 1 unit: " . $bike->getInfo() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}