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

$result = $product->store($data);

if ($result['status']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
    header("Location: create.php");
    exit;
}

header("Location: index.php");
exit;
?>