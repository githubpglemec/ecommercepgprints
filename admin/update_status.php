<?php
session_start();
include '../includes/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        header('Location: view_order.php?id=' . $order_id . '&error=invalid_status');
        exit;
    }
    
    // Update order status
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        header('Location: view_order.php?id=' . $order_id . '&success=1');
    } else {
        header('Location: view_order.php?id=' . $order_id . '&error=db_error');
    }
    exit;
} 
// Handle GET request (from index page)
else if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    header('Location: view_order.php?id=' . $_GET['id']);
    exit;
} 
// Invalid request
else {
    header('Location: index.php');
    exit;
}
?>
