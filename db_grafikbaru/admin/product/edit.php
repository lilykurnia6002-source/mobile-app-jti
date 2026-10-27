<?php
require_once '../../classes/AccessControl.php';
AccessControl::requireLogin();

session_start();
require_once '../../config/Database.php';
require_once '../../classes/Product.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$product = new Product($conn);
$data = $product->getById($id);

if (!$data) {
    $_SESSION['error'] = 'Produk tidak ditemukan';
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - PawShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Produk</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="update.php">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Produk *</label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?= htmlspecialchars($data['name']) ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori *</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php 
                                    $categories = ['anjing','kucing','burung','ikan','reptil','lainnya'];
                                    foreach ($categories as $cat): 
                                    ?>
                                    <option value="<?= $cat ?>" 
                                            <?= $data['category'] === $cat ? 'selected' : '' ?>>
                                        <?= ucfirst($cat) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga (Rp) *</label>
                                <input type="number" name="price" class="form-control" required min="0"
                                       value="<?= htmlspecialchars($data['price']) ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control" min="0"
                                   value="<?= htmlspecialchars($data['stock']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL Gambar</label>
                            <input type="url" name="image" class="form-control" 
                                   value="<?= htmlspecialchars($data['image'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update
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