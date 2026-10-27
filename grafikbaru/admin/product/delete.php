<?php
require_once '../../classes/AccessControl.php';
AccessControl::requireLogin();

session_start();
require_once '../../config/Database.php';
require_once '../../classes/Product.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status' => false, 'message' => 'ID tidak valid']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$product = new Product($conn);

$result = $product->delete($id);

if ($result['status']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

echo json_encode($result);
?>