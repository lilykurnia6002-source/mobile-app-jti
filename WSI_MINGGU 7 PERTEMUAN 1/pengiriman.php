<?php
// Interface
interface Pengiriman {
    public function hitungBiaya();
}

// Abstract Class
abstract class LayananPengiriman implements Pengiriman {
    protected $berat;

    public function __construct($berat) {
        $this->berat = $berat;
    }
    abstract public function hitungBiaya();
}

// Class Reguler
class Reguler extends LayananPengiriman {

public function hitungBiaya() {
    return $this->berat * 10000;
   }
}

// Class Express
class Express extends LayananPengiriman {

public function hitungBiaya() {
    return $this->berat * 20000;
}
}

// Class Cargo
class Cargo extends LayananPengiriman {

public function hitungBiaya() {
    return $this->berat * 5000;
}
}

//Polymorphism
$pengiriman = [
    new Reguler(2),
    new Express(2),
    new Cargo(2)
];

foreach($pengiriman as $layanan) {
    echo "Biaya pengiriman: Rp " . $layanan->hitungBiaya() . "<br>";
}
?>