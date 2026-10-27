<?php
require_once '../../classes/AccessControl.php';
AccessControl::requireLogin();

session_start();
require_once '../../config/Database.php';
require_once '../../classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$product = new Product($conn);

$data = [
    'name' => trim($_POST['name']),
    'category' => $_POST['category'],
    'price' => floatval($_POST['price']),
    'stock' => intval($_POST['stock']),
    'description' => trim($_POST['description']),
    'image' => trim($_POST['image'])
];

$result = $product->update($id, $data);

$_SESSION[$result['status'] ? 'success' : 'error'] = $result['message'];
header("Location: index.php");
exit;
?>