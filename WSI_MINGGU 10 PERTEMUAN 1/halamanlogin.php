<?php
require_once '../../config/Database.php';
require_once '../../classes/Auth.php';

$db = new Database();
$coon = $db->getConnection();
$auth = new Auth($coon);

// Proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = $_POST['email'];
    $password = $_POST['password'] ?? '';

    // Panggil method login
    if($auth->login($email, $password)) {
        // Redirect jika berhasil
        header("Location: ../backend/dashboard.php");
        exit;
    } else {
        $eror = "Username atau Password salah!";
    }
}
?>