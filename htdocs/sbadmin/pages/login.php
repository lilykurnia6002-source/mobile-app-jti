<?php session_start(); ?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link href="/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container">

<div class="row justify-content-center">
<div class="col-md-6">

<div class="card mt-5">
<div class="card-body">

<h3 class="text-center">Login</h3>

<form action="../auth/login_process.php" method="POST">

    <input type="text" name="username" class="form-control mb-2" placeholder="Username">
    <input type="password" name="password" class="form-control mb-2" placeholder="Password">

    <button class="btn btn-primary w-100">Login</button>

</form>

</div>
</div>

</div>
</div>

</div>

</body>
</html>