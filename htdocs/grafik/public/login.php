<?php
require_once '../../config/Database.php';
require_once '../../classes/Auth.php';

$db = new Database();
$conn = $db->getConnection();
$auth = new Auth($conn);

// Proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Panggil method login
    if ($auth->login($email, $password)) {
        // Redirect jika berhasil
        header("Location: ../backend/dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>