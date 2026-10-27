<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../public/login.php");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    ini dashboard admin, selamat datang <?php echo htmlspecialchars($user['name']); ?>!
</body>
</html>