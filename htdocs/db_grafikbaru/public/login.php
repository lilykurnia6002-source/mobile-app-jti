<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';

$db = new Database();
$auth = new Auth($db->getConnection());

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->login($_POST['email'], $_POST['password'])) {
        header("Location: ../backend/dashboard.php");
        exit;
    } else {
        $error = "Login gagal! Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PawShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <!-- Login Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-lg-5">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                                <i class="fas fa-paw fa-2x text-warning"></i>
                            </div>
                            <h2 class="fw-bold mb-1">PawShop</h2>
                            <p class="text-secondary mb-0">Masuk ke akun Anda</p>
                        </div>

                        <!-- Error Alert -->
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="password" class="form-label fw-semibold mb-0">Password</label>
                                    <a href="#" class="text-decoration-none small">Lupa password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password Anda" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="text-secondary mb-0">Belum punya akun? 
                                    <a href="register.php" class="text-decoration-none fw-semibold">
                                        Register di sini
                                    </a>
                                </p>
                            </div>
                        </form>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Social Login -->
                        <div class="text-center">
                            <p class="text-secondary small mb-3">atau masuk dengan</p>
                            <div class="row g-2 justify-content-center">
                                <div class="col-4">
                                    <a href="#" class="btn btn-outline-secondary w-100">
                                        <i class="fab fa-google"></i>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="#" class="btn btn-outline-secondary w-100">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="#" class="btn btn-outline-secondary w-100">
                                        <i class="fab fa-apple"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-4">
                    <a href="../index.php" class="text-decoration-none text-secondary small">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Password Visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

</body>
</html>

