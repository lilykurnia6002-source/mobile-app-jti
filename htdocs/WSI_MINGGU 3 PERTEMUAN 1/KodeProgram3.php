<?php

class Product {
    private string $name;
    private float $price; // harga dasar
    private float $taxRate; // mis. 0.1 untuk 10%

    public function __construct(string $name, float $price, float $taxRate = 0.1) {
        $this->name = $name;
        $this->price = $price;
        $this->taxRate = $taxRate;
    }

    public function getName(): string {
        return $this->name;
    }

    // Mengembalikan harga termasuk pajak
    public function getPriceWithTax(): float {
        return $this->price * (1 + $this->taxRate);
    }

    // Set harga baru
    public function setPrice(float $newPrice): void {
        if ($newPrice < 0) {
            throw new InvalidArgumentException("Harga tidak boleh negatif");
        }
        $this->price = $newPrice;
    }
}

// Pemakaian
$product = new Product("Pulpen", 2000.0);
echo $product->getName() . PHP_EOL; // Pulpen
echo "Harga (inc. tax): " . $product->getPriceWithTax() . PHP_EOL;

?>