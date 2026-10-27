<?php
session_start();
require_once '../../config/Database.php';
require_once '../../classes/Product.php';

$db = new Database();
$conn = $db->getConnection();
$product = new Product($conn);
$result = $product->getAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - PawShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-boxes me-2"></i>Kelola Produk</h3>
        <a href="create.php" class="btn btn-warning">
            <i class="fas fa-plus me-1"></i>Tambah Produk
        </a>
    </div>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- DataTable -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table id="tableProduct" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result && $result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-info"><?= ucfirst($row['category']) ?></span></td>
                        <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                        <td><?= $row['stock'] ?></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete" 
                                    data-id="<?= $row['id'] ?>" 
                                    data-name="<?= htmlspecialchars($row['name']) ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data produk</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
// Inisialisasi DataTables
$(document).ready(function() {
    $('#tableProduct').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });
});

// Delete dengan konfirmasi + AJAX
$(document).on('click', '.btn-delete', function() {
    let id = $(this).data('id');
    let name = $(this).data('name');
    
    if (confirm("Yakin ingin menghapus produk \"" + name + "\"?")) {
        $.ajax({
            url: 'delete.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                window.location.reload();
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    }
});
</script>
</body>
</html>