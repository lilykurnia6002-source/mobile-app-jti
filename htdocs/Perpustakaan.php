<?php

// Nama_NIM_Golongan

class Book {
    private string $isbn;
    private string $title;
    private string $author;
    private bool $isBorrowed = false;
    private ?string $borrowedBy = null;

    public function __construct(string $isbn, string $title, string $author) {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->author = $author;
    }

    public function borrow(string $memberId): void {
        if ($this->isBorrowed) {
            throw new Exception("Buku '{$this->title}' sedang dipinjam dan tidak tersedia.");
        }
        
        $this->isBorrowed = true;
        $this->borrowedBy = $memberId;
        echo "Buku berhasil dipinjam oleh member: $memberId\n";
    }

    public function returnBook(): void {
        $this->isBorrowed = false;
        $this->borrowedBy = null;
        echo "Buku telah dikembalikan.\n";
    }

    // Getters
    public function getIsbn(): string {
        return $this->isbn;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function isBorrowed(): bool {
        return $this->isBorrowed;
    }
}

// --- Contoh Pengujian Sesuai Test Output ---
try {
    $book = new Book("978-602", "Pemrograman PHP", "Budi");

    // Pinjam pertama kali
    $book->borrow("MEMBER001");

    // Pinjam kedua kali berturut-turut (Akan melempar Exception)
    $book->borrow("MEMBER002");

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Kembalikan buku
$book->returnBook();

// Pinjam lagi setelah dikembalikan (Berhasil)
$book->borrow("MEMBER002");