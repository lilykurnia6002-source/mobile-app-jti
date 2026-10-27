<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawShop - Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-paw me-2 text-warning"></i>PawShop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="#home">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#products">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Layanan</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
            </ul>
            <div class="ms-lg-3">
                <a href="login.php" class="btn btn-outline-warning btn-sm me-2">Login</a>
                <a href="register.php" class="btn btn-warning btn-sm">Register</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="bg-dark text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 py-5">
                <span class="badge bg-warning text-dark mb-3">🐾 Pet Shop Terpercaya #1</span>
                <h1 class="display-4 fw-bold mb-3">Sahabat Terbaik<br>Untuk Peliharaan Anda</h1>
                <p class="lead text-secondary mb-4">Temukan makanan, aksesoris, dan layanan perawatan terbaik untuk anjing, kucing, dan hewan peliharaan kesayangan Anda.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="login.php" class="btn btn-warning btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Belanja Sekarang
                    </a>
                    <a href="#products" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-eye me-2"></i>Lihat Produk
                    </a>
                </div>
                <div class="mt-4 d-flex gap-4 text-secondary">
                    <div><i class="fas fa-check-circle text-warning me-2"></i>Produk Original</div>
                    <div><i class="fas fa-check-circle text-warning me-2"></i>Pengiriman Cepat</div>
                    <div><i class="fas fa-check-circle text-warning me-2"></i>Harga Terjangkau</div>
                </div>
            </div>
            <div class="col-lg-6 text-center py-5">
                <img src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=600&h=500&fit=crop" 
                     class="img-fluid rounded-4 shadow-lg" alt="Pet Shop"
                     onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 600 500%22><rect fill=%22%23333%22 width=%22600%22 height=%22500%22/><text x=%2250%25%22 y=%2250%25%22 fill=%22%23ffc107%22 font-size=%2280%22 text-anchor=%22middle%22 dy=%22.3em%22>🐶🐱🐰</text></svg>'">
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="bg-warning py-4">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3">
                <h3 class="fw-bold mb-0">5000+</h3>
                <p class="mb-0 text-dark">Pelanggan Puas</p>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold mb-0">200+</h3>
                <p class="mb-0 text-dark">Produk Tersedia</p>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold mb-0">50+</h3>
                <p class="mb-0 text-dark">Merek Premium</p>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold mb-0">24/7</h3>
                <p class="mb-0 text-dark">Layanan Customer</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Kategori Produk</h2>
            <p class="text-secondary">Temukan semua kebutuhan hewan peliharaan Anda</p>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100 py-4 rounded-4">
                    <div class="card-body">
                        <div class="display-1 mb-3">🐕</div>
                        <h5 class="fw-bold">Anjing</h5>
                        <p class="text-secondary small">Makanan, mainan & aksesoris</p>
                        <a href="login.php" class="btn btn-sm btn-outline-warning">Lihat Produk</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100 py-4 rounded-4">
                    <div class="card-body">
                        <div class="display-1 mb-3">🐈</div>
                        <h5 class="fw-bold">Kucing</h5>
                        <p class="text-secondary small">Makanan, litter & aksesoris</p>
                        <a href="login.php" class="btn btn-sm btn-outline-warning">Lihat Produk</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100 py-4 rounded-4">
                    <div class="card-body">
                        <div class="display-1 mb-3">🐦</div>
                        <h5 class="fw-bold">Burung</h5>
                        <p class="text-secondary small">Pakan, kandang & suplemen</p>
                        <a href="login.php" class="btn btn-sm btn-outline-warning">Lihat Produk</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100 py-4 rounded-4">
                    <div class="card-body">
                        <div class="display-1 mb-3">🐠</div>
                        <h5 class="fw-bold">Ikan</h5>
                        <p class="text-secondary small">Akuarium, pakan & filter</p>
                        <a href="login.php" class="btn btn-sm btn-outline-warning">Lihat Produk</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products -->
<section id="products" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Produk Terlaris</h2>
            <p class="text-secondary">Pilihan terbaik untuk hewan peliharaan Anda</p>
        </div>
        <div class="row g-4">
            <!-- Product 1 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🦴</span>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-danger mb-2">-20%</span>
                        <h6 class="card-title fw-bold">Royal Canin Adult Dog</h6>
                        <p class="text-secondary small mb-2">Makanan anjing premium 10kg</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-decoration-line-through text-secondary small">Rp 850.000</span>
                                <h6 class="text-warning fw-bold mb-0">Rp 680.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 2 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🐟</span>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-success mb-2">Baru</span>
                        <h6 class="card-title fw-bold">Whiskas Tuna Cat Food</h6>
                        <p class="text-secondary small mb-2">Makanan kucing rasa tuna 5kg</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning fw-bold mb-0">Rp 195.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 3 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🏠</span>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">Best Seller</span>
                        <h6 class="card-title fw-bold">Cat House Cozy Bed</h6>
                        <p class="text-secondary small mb-2">Tempat tidur kucing empuk</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-decoration-line-through text-secondary small">Rp 350.000</span>
                                <h6 class="text-warning fw-bold mb-0">Rp 275.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 4 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🎾</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Dog Chew Toy Set</h6>
                        <p class="text-secondary small mb-2">Set mainan kunyah anjing 5pcs</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning fw-bold mb-0">Rp 125.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 5 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🧴</span>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-danger mb-2">-15%</span>
                        <h6 class="card-title fw-bold">Pet Shampoo Anti Flea</h6>
                        <p class="text-secondary small mb-2">Shampo anti kutu 500ml</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-decoration-line-through text-secondary small">Rp 95.000</span>
                                <h6 class="text-warning fw-bold mb-0">Rp 80.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 6 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🐹</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Hamster Cage Deluxe</h6>
                        <p class="text-secondary small mb-2">Kandang hamster 2 lantai</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning fw-bold mb-0">Rp 450.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 7 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🥫</span>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-success mb-2">Baru</span>
                        <h6 class="card-title fw-bold">Pedigree Wet Food</h6>
                        <p class="text-secondary small mb-2">Makanan basah anjing 12pcs</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning fw-bold mb-0">Rp 165.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product 8 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                        <span class="display-4">🐢</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Reptile Terrarium Kit</h6>
                        <p class="text-secondary small mb-2">Kit terrarium reptil lengkap</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning fw-bold mb-0">Rp 750.000</h6>
                            </div>
                        </div>
                        <a href="login.php" class="btn btn-warning btn-sm w-100 mt-3">
                            <i class="fas fa-cart-plus me-1"></i>Beli
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="login.php" class="btn btn-dark btn-lg px-5">
                Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Services -->
<section id="services" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Layanan Kami</h2>
            <p class="text-secondary">Layanan lengkap untuk hewan peliharaan kesayangan Anda</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 rounded-4 py-4">
                    <div class="card-body">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                            <i class="fas fa-cut fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Grooming</h5>
                        <p class="text-secondary small">Perawatan bulu & mandi profesional untuk anjing dan kucing</p>
                        <a href="login.php" class="btn btn-outline-warning btn-sm">Booking</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 rounded-4 py-4">
                    <div class="card-body">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                            <i class="fas fa-stethoscope fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Klinik Hewan</h5>
                        <p class="text-secondary small">Konsultasi & perawatan kesehatan oleh dokter hewan berpengalaman</p>
                        <a href="login.php" class="btn btn-outline-warning btn-sm">Konsultasi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 rounded-4 py-4">
                    <div class="card-body">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                            <i class="fas fa-hotel fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Pet Hotel</h5>
                        <p class="text-secondary small">Penitipan hewan peliharaan dengan fasilitas nyaman dan aman</p>
                        <a href="login.php" class="btn btn-outline-warning btn-sm">Reservasi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 rounded-4 py-4">
                    <div class="card-body">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                            <i class="fas fa-truck fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Pengiriman</h5>
                        <p class="text-secondary small">Layanan antar produk & hewan peliharaan ke seluruh Indonesia</p>
                        <a href="login.php" class="btn btn-outline-warning btn-sm">Pesan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="py-5 bg-dark text-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <span class="badge bg-warning text-dark mb-3">PROMO SPESIAL</span>
                <h2 class="fw-bold display-5">Diskon Hingga 30% Untuk Member Baru!</h2>
                <p class="lead text-secondary mb-4">Daftar sekarang dan dapatkan voucher diskon untuk pembelian pertama Anda. Berlaku untuk semua produk.</p>
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-clock text-warning fa-2x"></i>
                    </div>
                    <div class="col">
                        <p class="mb-0 text-secondary">Penawaran berakhir dalam:</p>
                        <div id="countdown" class="d-flex gap-3 mt-2">
                            <div class="bg-secondary bg-opacity-25 rounded-3 px-3 py-2 text-center">
                                <span id="days" class="fs-4 fw-bold text-warning">00</span>
                                <div class="small text-secondary">Hari</div>
                            </div>
                            <div class="bg-secondary bg-opacity-25 rounded-3 px-3 py-2 text-center">
                                <span id="hours" class="fs-4 fw-bold text-warning">00</span>
                                <div class="small text-secondary">Jam</div>
                            </div>
                            <div class="bg-secondary bg-opacity-25 rounded-3 px-3 py-2 text-center">
                                <span id="minutes" class="fs-4 fw-bold text-warning">00</span>
                                <div class="small text-secondary">Menit</div>
                            </div>
                            <div class="bg-secondary bg-opacity-25 rounded-3 px-3 py-2 text-center">
                                <span id="seconds" class="fs-4 fw-bold text-warning">00</span>
                                <div class="small text-secondary">Detik</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <a href="register.php" class="btn btn-warning btn-lg px-5 py-3 fs-5">
                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Apa Kata Pelanggan</h2>
            <p class="text-secondary">Testimoni dari pelanggan setia kami</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-warning mb-3">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-secondary mb-4">"Produk berkualitas tinggi dengan harga yang sangat terjangkau. Pengiriman juga sangat cepat. Sangat puas belanja di PawShop!"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;">
                                <span class="fw-bold text-warning">AS</span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Andi Saputra</h6>
                                <small class="text-secondary">Pemilik Golden Retriever</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-warning mb-3">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-secondary mb-4">"Layanan groomingnya luar biasa! Kucing saya jadi bersih dan wangi. Staff-nya juga sangat ramah dan profesional."</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;">
                                <span class="fw-bold text-warning">RW</span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Rina Wijaya</h6>
                                <small class="text-secondary">Pemilik Persia Cat</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-warning mb-3">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="text-secondary mb-4">"Pet hotel-nya bersih dan nyaman. Saya bisa liburan dengan tenang karena tahu hewan saya terawat dengan baik."</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;">
                                <span class="fw-bold text-warning">BP</span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Budi Pratama</h6>
                                <small class="text-secondary">Pemilik Hamster & Kelinci</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About -->
<section id="about" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-warning text-dark mb-3">TENTANG KAMI</span>
                <h2 class="fw-bold display-6">PawShop - Sahabat Hewan Peliharaan Anda</h2>
                <p class="text-secondary lead mt-3">Sejak 2015, PawShop telah menjadi destinasi terpercaya untuk semua kebutuhan hewan peliharaan Anda. Kami menyediakan produk berkualitas tinggi dengan harga terjangkau.</p>
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>Produk 100% Original</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>Garansi Produk</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>Dokter Hewan Profesional</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>Gratis Konsultasi</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-warning bg-opacity-10 rounded-4 p-4 text-center">
                            <div class="display-4 mb-2">🐾</div>
                            <h6 class="fw-bold">Sejak 2015</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-warning bg-opacity-10 rounded-4 p-4 text-center">
                            <div class="display-4 mb-2">🏆</div>
                            <h6 class="fw-bold">Best Pet Shop</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-warning bg-opacity-10 rounded-4 p-4 text-center">
                            <div class="display-4 mb-2">❤️</div>
                            <h6 class="fw-bold">5000+ Happy Pets</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-warning bg-opacity-10 rounded-4 p-4 text-center">
                            <div class="display-4 mb-2">👨‍⚕️</div>
                            <h6 class="fw-bold">10+ Dokter Hewan</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section id="contact" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Hubungi Kami</h2>
            <p class="text-secondary">Ada pertanyaan? Jangan ragu untuk menghubungi kami</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;">
                            <i class="fas fa-map-marker-alt fa-lg text-warning"></i>
                        </div>
                        <h6 class="fw-bold">Alamat</h6>
                        <p class="text-secondary small mb-0">Jl. Hewan Lucu No. 123<br>Jakarta Selatan, 12345</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;">
                            <i class="fas fa-phone fa-lg text-warning"></i>
                        </div>
                        <h6 class="fw-bold">Telepon</h6>
                        <p class="text-secondary small mb-0">(021) 1234-5678<br>+62 812-3456-7890</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;">
                            <i class="fas fa-envelope fa-lg text-warning"></i>
                        </div>
                        <h6 class="fw-bold">Email</h6>
                        <p class="text-secondary small mb-0">info@pawshop.id<br>cs@pawshop.id</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <h4 class="fw-bold mb-3"><i class="fas fa-paw me-2 text-warning"></i>PawShop</h4>
                <p class="text-secondary">Pet shop terpercaya dengan produk berkualitas untuk semua hewan peliharaan kesayangan Anda.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3">Produk</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Makanan Anjing</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Makanan Kucing</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Aksesoris</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Obat & Vitamin</a></li>
                    <li><a href="login.php" class="text-secondary text-decoration-none">Mainan</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3">Layanan</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Grooming</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Klinik</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Pet Hotel</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Pengiriman</a></li>
                    <li><a href="login.php" class="text-secondary text-decoration-none">Adopsi</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3">Informasi</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Tentang Kami</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Karir</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Blog</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">FAQ</a></li>
                    <li><a href="#" class="text-secondary text-decoration-none">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3">Akun</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Login</a></li>
                    <li class="mb-2"><a href="register.php" class="text-secondary text-decoration-none">Register</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Keranjang</a></li>
                    <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none">Pesanan</a></li>
                    <li><a href="login.php" class="text-secondary text-decoration-none">Wishlist</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-secondary small mb-0">&copy; 2026 PawShop. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex gap-2 justify-content-md-end">
                    <span class="badge bg-secondary">VISA</span>
                    <span class="badge bg-secondary">MasterCard</span>
                    <span class="badge bg-secondary">BCA</span>
                    <span class="badge bg-secondary">Mandiri</span>
                    <span class="badge bg-secondary">GoPay</span>
                    <span class="badge bg-secondary">OVO</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-warning rounded-circle position-fixed bottom-0 end-0 m-4 shadow" style="width:50px;height:50px;display:none;z-index:999;">
    <i class="fas fa-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Countdown Timer
    function updateCountdown() {
        const now = new Date();
        const end = new Date();
        end.setDate(end.getDate() + 7);
        end.setHours(23, 59, 59, 0);
        
        const diff = end - now;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        document.getElementById('days').textContent = String(days).padStart(2, '0');
        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Back to Top Button
    const backToTopBtn = document.getElementById('backToTop');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Smooth scroll for nav links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            // Close mobile navbar
            const navCollapse = document.getElementById('navbarNav');
            if (navCollapse.classList.contains('show')) {
                new bootstrap.Collapse(navCollapse).hide();
            }
        });
    });

    // Active nav link on scroll
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (window.scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
</script>

</body>
</html>

