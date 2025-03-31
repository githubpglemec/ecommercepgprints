<?php
session_start();
include 'includes/functions.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    if ($quantity > 0) {
        updateCart($product_id, $quantity);
    } else {
        removeFromCart($product_id);
    }
    
    $response['success'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
