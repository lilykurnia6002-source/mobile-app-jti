<?php
$conn = new mysqli("localhost", "root", "", "app_db");

if($conn->connect_error){
    die("DB gagal");
}
?>