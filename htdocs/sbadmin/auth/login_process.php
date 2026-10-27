<?php
session_start();
include('db.php');

$username = $_POST['username'];
$password = $_POST['password'];

$query = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");

if($query->num_rows > 0){
    $_SESSION['user'] = $username;
    header("Location: ../pages/dashboard.php");
} else {
    echo "Login gagal";
}
?>