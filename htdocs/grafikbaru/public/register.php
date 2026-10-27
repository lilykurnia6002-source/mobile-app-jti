<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';

$db = new Database();
$auth = new Auth($db->getConnection());

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->register($_POST['name'], $_POST['email'], $_POST['password'])) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Register gagal! Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PawShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <!-- Register Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-lg-5">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                                <i class="fas fa-user-plus fa-2x text-warning"></i>
                            </div>
                            <h2 class="fw-bold mb-1">Buat Akun Baru</h2>
                            <p class="text-secondary mb-0">Daftar dan mulai belanja di PawShop</p>
                        </div>

                        <!-- Error Alert -->
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Register Form -->
                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user text-secondary"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Anda" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Buat password (min. 6 karakter)" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label small text-secondary" for="terms">
                                        Saya setuju dengan <a href="#" class="text-decoration-none">Syarat & Ketentuan</a> PawShop
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="text-secondary mb-0">Sudah punya akun? 
                                    <a href="login.php" class="text-decoration-none fw-semibold">
                                        Login di sini
                                    </a>
                                </p>
                            </div>
                        </form>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Social Register -->
                        <div class="text-center">
                            <p class="text-secondary small mb-3">atau daftar dengan</p>
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

    // Password Strength Indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
    });
</script>

</body>
</html>

