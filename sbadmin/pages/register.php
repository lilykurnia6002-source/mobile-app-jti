<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container mt-5">

<div class="card">
<div class="card-body">

<h3>Register</h3>

<form action="../auth/register_process.php" method="POST">

    <input type="text" name="username" class="form-control mb-2" placeholder="Username">
    <input type="password" name="password" class="form-control mb-2" placeholder="Password">

    <button class="btn btn-success">Register</button>

</form>

</div>
</div>

</div>

</body>
</html>