<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tefa_canning_db"; // Sesuaikan dengan nama database di PHPMyAdmin kamu

try {
    // Membuat koneksi PDO
    $koneksi = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Set mode error untuk mempermudah debugging
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Hapus baris di bawah ini jika sudah berhasil terkoneksi
    echo "Koneksi Berhasil!"; 
} catch (PDOException $e) {
    // Menampilkan pesan jika koneksi gagal
    die("Koneksi Gagal: " . $e->getMessage());
}
?>