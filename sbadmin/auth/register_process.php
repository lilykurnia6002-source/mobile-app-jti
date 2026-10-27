<?php
include('db.php');

$username = $_POST['username'];
$password = $_POST['password'];

$conn->query("INSERT INTO users(username,password) VALUES('$username','$password')");

header("Location: ../pages/login.php");
?>