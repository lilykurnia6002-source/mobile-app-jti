<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

include('../layout/header.php');
include('../layout/sidebar.php');
?>

<div id="content-wrapper">
<div id="content">

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="card p-3">
        Selamat datang, <?= $_SESSION['user']; ?>
    </div>

</div>

</div>
</div>

<?php include('../layout/footer.php'); ?>