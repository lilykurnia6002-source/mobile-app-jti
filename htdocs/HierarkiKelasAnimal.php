<?php
class Animal {
    // Properti
    protected $name;
    protected $age;
    protected $species;

    // Constructor
    public function __construct($name, $age, $species) {
        $this->name = $name;
        $this->age = $age;
        $this->species = $species;
    }

    // Metode umum
    public function eat() {
        return $this->name . " the " . $this->species . " is eating.";
    }

    public function sleep() {
        return $this->name . " is sleeping.";
    }

    public function getInfo() {
        return "Name: " . $this->name . ", Age: " . $this->age . " years, Species: " . $this->species;
    }
    public function makeSound() {
        return "Some generic animal sound.";
    }
}

class Mammal extends Animal {
    // Properti tambahan khusus mammal
    protected $furColor;

    public function __construct($name, $age, $species, $furColor) {
        parent::__construct($name, $age, $species);
        $this->furColor = $furColor;
    }

    // Metode khusus mammal
    public function giveBirth() {
        return $this->name . " is giving birth to live young.";
    }

    public function nurse() {
        return $this->name . " is nursing its young.";
    }
}

class Bird extends Animal {
    // Properti tambahan khusus bird
    protected $wingspan;
    protected $canFly;

    public function __construct($name, $age, $species, $wingspan, $canFly = true) {
        parent::__construct($name, $age, $species);
        $this->wingspan = $wingspan;
        $this->canFly = $canFly;
    }

    // Metode khusus bird
    public function fly() {
        if ($this->canFly) {
            return $this->name . " is flying!";
        } else {
            return $this->name . " cannot fly.";
        }
    }

    public function layEggs() {
        return $this->name . " is laying eggs.";
    }
}

class Fish extends Animal {
    // Properti tambahan khusus fish
    protected $waterType; // fresh, salt, or brackish
    protected $maxDepth;

    public function __construct($name, $age, $species, $waterType, $maxDepth) {
        parent::__construct($name, $age, $species);
        $this->waterType = $waterType;
        $this->maxDepth = $maxDepth;
    }

    // Metode khusus fish
    public function swim() {
        return $this->name . " is swimming in " . $this->waterType . " water.";
    }

    public function breathe() {
        return $this->name . " is breathing through its gills.";
    }
}

class Dog extends Mammal {
    protected $breed;

    public function __construct($name, $age, $furColor, $breed) {
        parent::__construct($name, $age, "Dog", $furColor);
        $this->breed = $breed;
    }

    public function bark() {
        return $this->name . " the " . $this->breed . " is barking: Woof! Woof!";
    }

    public function wagTail() {
        return $this->name . " is wagging its tail happily.";
    }
     public function makeSound() {
        return "Woof! Woof!";
    }
      public function eat() {
        // Panggil metode eat dari kelas induk
        $parentResult = parent::eat();

        // Tambahkan perilaku khusus anjing
        return $parentResult. " It's goobling its food quickly!";
    }
}

class Cat extends Mammal {
    protected $indoor;

    public function __construct($name, $age, $furColor, $indoor = true) {
        parent::__construct($name, $age, "Cat", $furColor);
        $this->indoor = $indoor;
    }

    public function meow() {
        return $this->name . " says: Meow!";
    }

    public function scratch() {
        return $this->name . " is sharpening its claws.";
    }
        public function makeSound() {
        return "Meow!";
    }
}

class Eagle extends Bird {
    protected $visionRange;

    public function __construct($name, $age, $wingspan, $visionRange) {
        parent::__construct($name, $age, "Eagle", $wingspan, true);
        $this->visionRange = $visionRange;
    }

    public function hunt() {
        return $this->name . " is hunting prey from high above.";
    }

    public function soar() {
        return $this->name . " is soaring gracefully with a wingspan of " . $this->wingspan . " meters.";
    }
}

class Shark extends Fish {
    protected $teethCount;

    public function __construct($name, $age, $maxDepth, $teethCount) {
        parent::__construct($name, $age, "Shark", "salt", $maxDepth);
        $this->teethCount = $teethCount;
    }

    public function attack() {
        return $this->name . " attacks with " . $this->teethCount . " sharp teeth!";
    }

    public function detectBlood() {
        return $this->name . " detects blood from miles away.";
    }
}   

// --- Contoh Penggunaan ---

$dog = new Dog("Cookies", 3, "Golden", "Golden Retriever");
echo $dog->getInfo() . "<br>";
echo $dog->eat() . "<br>"; // Output akan mencakup perilaku khusus gobbling
echo $dog->makeSound() . "<br><br>";

$cat = new Cat("Choco", 2, "Black", true);
echo $cat->getInfo() . "<br>";
echo $cat->makeSound() . "<br>";

// Membuat objek dari kelas Dog
$dog = new Dog("Cookies", 3, "Golden", "Golden Retriever");
echo $dog->getInfo() . "<br>";
echo $dog->eat() . "<br>";
echo $dog->makeSound() . "<br>";
echo $dog->bark() . "<br>";
echo $dog->wagTail() . "<br><br>";

// Membuat objek dari kelas Cat
$cat = new Cat("Choco", 2, "Black", true);
echo $cat->getInfo() . "<br>";
echo $cat->eat() . "<br>";
echo $cat->makeSound() . "<br>";
echo $cat->meow() . "<br>";
echo $cat->scratch() . "<br><br>";

// Membuat objek dari kelas Eagle
$eagle = new Eagle("Freedom", 5, 2.5, 8);
echo $eagle->getInfo() . "<br>";
echo $eagle->eat() . "<br>";
echo $eagle->fly() . "<br>";
echo $eagle->hunt() . "<br>";
echo $eagle->soar() . "<br><br>";

// Membuat objek dari kelas Shark
$shark = new Shark("Jaws", 10, 1000, 300);
echo $shark->getInfo() . "<br>";
echo $shark->eat() . "<br>";
echo $shark->swim() . "<br>";
echo $shark->breathe() . "<br>";
echo $shark->attack() . "<br>";
