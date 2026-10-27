<?php
// nama file: Nama_NIM_Golongan

class Student {
    private int $id;
    private string $name;
    private array $grades = [];

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function addGrade(string $subject, float $score): void {
        // Validasi score harus antara 0-100
        if ($score < 0 || $score > 100) {
            throw new InvalidArgumentException("Score harus berada di rentang 0 hingga 100.");
        }
        // Simpan dalam array associative subject => score
        $this->grades[$subject] = $score;
    }

    public function getGrade(string $subject): ?float {
        // Mengembalikan nilai berdasarkan mata pelajaran, atau null jika tidak ada
        return $this->grades[$subject] ?? null;
    }

    public function getAverage(): float {
        if (empty($this->grades)) {
            return 0.0;
        }
        // Menghitung rata-rata nilai
        return array_sum($this->grades) / count($this->grades);
    }

    public function hasPassed(float $passMark = 60.0): bool {
        // Cek apakah rata-rata nilai memenuhi passMark
        return $this->getAverage() >= $passMark;
    }
}

// --- Test Output ---
try {
    $student = new Student(1, "Budi Santoso");

    // Tambah beberapa nilai
    $student->addGrade("Matematika", 85.0);
    $student->addGrade("IPA", 70.5);
    $student->addGrade("Bahasa Inggris", 55.0);

    echo "Nama Student: Budi Santoso\n";
    echo "Rata-rata Nilai: " . $student->getAverage() . "\n";
    echo "Status Lulus: " . ($student->hasPassed() ? "Lulus" : "Tidak Lulus") . "\n";

    // Tes InvalidArgumentException
    echo "\nMencoba menambah nilai -10...\n";
    $student->addGrade("Fisika", -10);

} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}