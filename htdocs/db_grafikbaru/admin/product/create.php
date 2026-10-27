<?php

session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - PawShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Produk Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="store.php">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori *</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="anjing">Anjing</option>
                                    <option value="kucing">Kucing</option>
                                    <option value="burung">Burung</option>
                                    <option value="ikan">Ikan</option>
                                    <option value="reptil">Reptil</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga (Rp) *</label>
                                <input type="number" name="price" class="form-control" required min="0">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control" min="0" value="0">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL Gambar</label>
                            <input type="url" name="image" class="form-control" placeholder="https://...">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>