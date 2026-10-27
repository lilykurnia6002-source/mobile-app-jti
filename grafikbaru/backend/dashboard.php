<?php

session_start();
// ... kode dashboard existing

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../public/login.php");
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PawShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --soft-blue: #6c9bcf;
            --soft-blue-dark: #4a7ba7;
            --soft-blue-light: #e8f1f8;
            --soft-bg: #f0f5fa;
            --soft-card: #ffffff;
            --soft-text: #4a5568;
            --soft-text-muted: #718096;
        }
        
        body {
            background-color: var(--soft-bg) !important;
            color: var(--soft-text);
        }
        
        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            background: linear-gradient(180deg, var(--soft-blue-dark) 0%, var(--soft-blue) 100%);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        
        /* Topbar */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 999;
            background: var(--soft-card) !important;
            border-bottom: 1px solid var(--soft-blue-light);
        }
        
        /* Cards */
        .card {
            background: var(--soft-card) !important;
            border: 1px solid var(--soft-blue-light) !important;
            color: var(--soft-text);
        }
        
        .card-header {
            background: var(--soft-card) !important;
            border-bottom: 1px solid var(--soft-blue-light) !important;
        }
        
        /* Stats Cards - soft blue accent */
        .stat-card {
            border-left: 4px solid var(--soft-blue) !important;
        }
        
        .stat-card .stat-label {
            color: var(--soft-blue-dark);
        }
        
        .stat-card .stat-value {
            color: var(--soft-text);
        }
        
        .stat-card .icon-box {
            background: var(--soft-blue-light);
            color: var(--soft-blue-dark);
        }
        
        /* Buttons */
        .btn-dark {
            background: var(--soft-blue-dark) !important;
            border-color: var(--soft-blue-dark) !important;
        }
        
        .btn-dark:hover {
            background: var(--soft-blue) !important;
            border-color: var(--soft-blue) !important;
        }
        
        .btn-outline-dark {
            color: var(--soft-blue-dark) !important;
            border-color: var(--soft-blue-light) !important;
        }
        
        .btn-outline-dark:hover {
            background: var(--soft-blue-light) !important;
            color: var(--soft-blue-dark) !important;
        }
        
        /* Custom logout button: black bg, yellow text */
        .btn-logout-custom {
            background-color: #000 !important;
            color: #ffc107 !important;
            border: 2px solid #ffc107 !important;
        }
        
        .btn-logout-custom:hover {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
        
        /* List group */
        .list-group-item {
            background: transparent !important;
            border-color: var(--soft-blue-light) !important;
            color: var(--soft-text);
        }
        
        /* Icons background */
        .bg-primary { background: var(--soft-blue) !important; }
        .bg-success { background: #5cb8a8 !important; }
        .bg-warning { background: #f6c23e !important; }
        .bg-info { background: #4dcbd6 !important; }
        
        .bg-opacity-10 { background: var(--soft-blue-light) !important; }
        .bg-opacity-25 { background: rgba(108, 155, 207, 0.25) !important; }
        
        /* Text colors */
        .text-secondary { color: var(--soft-text-muted) !important; }
        .text-primary { color: var(--soft-blue-dark) !important; }
        
        /* Main content margin */
        .main-content {
            margin-left: 250px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <!-- Logo -->
                    <div class="px-4 pb-4 mb-3 border-bottom border-light border-opacity-25">
                        <a class="d-flex align-items-center text-decoration-none" href="#">
                            <i class="fas fa-paw text-warning me-2 fs-4"></i>
                            <span class="fs-5 fw-bold text-white">PawShop</span>
                        </a>
                    </div>

                    <!-- Navigation -->
                    <ul class="nav flex-column px-3 mb-4">
                        <li class="nav-item">
                            <a class="nav-link active rounded mb-2" href="#">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded mb-2" href="#">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Pesanan Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded mb-2" href="#">
                                <i class="fas fa-user me-2"></i>
                                Profil
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Topbar -->
                <div class="topbar mb-4 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link d-md-none me-2 text-secondary" type="button">
                                <i class="fas fa-bars fs-5"></i>
                            </button>
                            <h5 class="mb-0 d-none d-md-block fw-bold" style="color: var(--soft-text);">Dashboard</h5>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-none d-sm-block" style="color: var(--soft-text);">
                                <i class="fas fa-user-circle me-1 text-warning"></i> <?= htmlspecialchars($user['name']); ?>
                            </span>
                            <!-- Logout button: black bg, yellow text -->
                            <a href="../public/logout.php" class="btn btn-logout-custom btn-sm fw-semibold">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Welcome Banner -->
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--soft-blue-light);">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="fw-bold mb-2" style="color: var(--soft-blue-dark);">Halo, <?= htmlspecialchars($user['name']); ?>! 👋</h1>
                                <p class="mb-0 fs-5" style="color: var(--soft-text-muted);">Selamat datang di dashboard pelanggan PawShop. Kelola pesanan, jadwal grooming, dan kebutuhan peliharaan Anda dengan mudah.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="#" class="btn btn-dark btn-lg">
                                    <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0 stat-label">Pesanan Aktif</h5>
                                    <div class="icon-box rounded-3 p-2">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1 stat-value">3</h2>
                                <p class="small mb-0" style="color: var(--soft-text-muted);">Sedang dalam pengiriman</p>
                                <a href="#" class="btn btn-link text-decoration-none p-0 mt-2 small" style="color: var(--soft-blue-dark);">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-left-color: #5cb8a8 !important;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0" style="color: #4a998a;">Jadwal Layanan</h5>
                                    <div class="icon-box rounded-3 p-2" style="background: #e8f6f3; color: #4a998a;">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1 stat-value">1</h2>
                                <p class="small mb-0" style="color: var(--soft-text-muted);">Grooming kucing - Besok</p>
                                <a href="#" class="btn btn-link text-decoration-none p-0 mt-2 small" style="color: #4a998a;">Ubah Jadwal <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-left-color: #f6c23e !important;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0" style="color: #c9a53a;">Poin Reward</h5>
                                    <div class="icon-box rounded-3 p-2" style="background: #fef5e7; color: #c9a53a;">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1 stat-value">1.250</h2>
                                <p class="small mb-0" style="color: var(--soft-text-muted);">Tukar dengan voucher diskon</p>
                                <a href="#" class="btn btn-link text-decoration-none p-0 mt-2 small" style="color: #c9a53a;">Tukar Poin <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity & Quick Menu -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header border-bottom-0 pt-4 px-4">
                                <h5 class="fw-bold mb-0" style="color: var(--soft-blue-dark);"><i class="fas fa-history me-2" style="color: var(--soft-text-muted);"></i>Aktivitas Terbaru</h5>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 py-3 d-flex align-items-start">
                                        <div class="rounded-circle p-2 me-3 mt-1" style="background: #e8f6f3;">
                                            <i class="fas fa-check small" style="color: #5cb8a8;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold" style="color: var(--soft-text);">Pesanan #PS-8842 telah dikirim</h6>
                                            <p class="small mb-0" style="color: var(--soft-text-muted);">Royal Canin Adult Dog (10kg) • JNE Reguler</p>
                                        </div>
                                        <span class="badge ms-auto" style="background: var(--soft-blue-light); color: var(--soft-text-muted);">2 jam lalu</span>
                                    </div>
                                    <div class="list-group-item px-0 py-3 d-flex align-items-start">
                                        <div class="rounded-circle p-2 me-3 mt-1" style="background: #fef5e7;">
                                            <i class="fas fa-clock small" style="color: #f6c23e;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold" style="color: var(--soft-text);">Menunggu pembayaran: Grooming Kucing</h6>
                                            <p class="small mb-0" style="color: var(--soft-text-muted);">Full Grooming • Sabtu, 25 Apr 2026 • 10:00 WIB</p>
                                        </div>
                                        <span class="badge ms-auto" style="background: var(--soft-blue-light); color: var(--soft-text-muted);">5 jam lalu</span>
                                    </div>
                                    <div class="list-group-item px-0 py-3 d-flex align-items-start">
                                        <div class="rounded-circle p-2 me-3 mt-1" style="background: #e8f4fb;">
                                            <i class="fas fa-gift small" style="color: #4dcbd6;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold" style="color: var(--soft-text);">Voucher diskon 15% ditambahkan</h6>
                                            <p class="small mb-0" style="color: var(--soft-text-muted);">Berlaku untuk pembelian di atas Rp 500.000</p>
                                        </div>
                                        <span class="badge ms-auto" style="background: var(--soft-blue-light); color: var(--soft-text-muted);">1 hari lalu</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header border-bottom-0 pt-4 px-4">
                                <h5 class="fw-bold mb-0" style="color: var(--soft-blue-dark);"><i class="fas fa-bolt me-2" style="color: var(--soft-text-muted);"></i>Menu Cepat</h5>
                            </div>
                            <div class="card-body px-4 pb-4 d-grid gap-2">
                                <a href="#" class="btn btn-outline-dark w-100 text-start fw-semibold">
                                    <i class="fas fa-shopping-cart me-2" style="color: var(--soft-blue);"></i> Belanja Produk
                                </a>
                                <a href="#" class="btn btn-outline-dark w-100 text-start fw-semibold">
                                    <i class="fas fa-cut me-2" style="color: #5cb8a8;"></i> Booking Grooming
                                </a>
                                <a href="#" class="btn btn-outline-dark w-100 text-start fw-semibold">
                                    <i class="fas fa-stethoscope me-2" style="color: #e74a3b;"></i> Konsultasi Dokter
                                </a>
                                <a href="#" class="btn btn-outline-dark w-100 text-start fw-semibold">
                                    <i class="fas fa-user-edit me-2" style="color: #4dcbd6;"></i> Edit Profil
                                </a>
                                <a href="#" class="btn btn-outline-dark w-100 text-start fw-semibold">
                                    <i class="fas fa-headset me-2" style="color: #f6c23e;"></i> Pusat Bantuan
                                </a>
                                <div class="border-top my-2" style="border-color: var(--soft-blue-light) !important;"></div>
                                <a href="../public/logout.php" class="btn btn-danger w-100 text-start fw-semibold">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="py-4 mt-5" style="background: var(--soft-blue-dark);">
                    <div class="text-center">
                        <p class="mb-0 small" style="color: rgba(255,255,255,0.7);">&copy; 2026 PawShop. Dashboard Pelanggan. Semua hak dilindungi.</p>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>