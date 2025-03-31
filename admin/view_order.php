<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'];

// Get order details
$sql = "SELECT o.*, u.username, u.email, u.first_name, u.last_name FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$order = $result->fetch_assoc();

// Get order items
$order_items = getOrderItems($conn, $order_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order #<?php echo $order_id; ?> - PG Prints Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>PG<span>Prints</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li class="active"><a href="index.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-store"></i> View Store</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-content">
            <div class="admin-header">
                <h1>Order #<?php echo $order_id; ?> Details</h1>
                <div class="admin-user">
                    <span>Welcome, Admin</span>
                </div>
            </div>
            <div class="admin-main">
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Order Information</h2>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Orders</a>
                    </div>
                    <div class="card-body">
                        <div class="order-info-grid">
                            <div class="order-info-section">
                                <h3>Order Details</h3>
                                <div class="info-group">
                                    <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
                                    <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </p>
                                    <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
                                </div>
                                <div class="status-update-form">
                                    <form action="update_status.php" method="post">
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                        <div class="form-group">
                                            <label for="status">Update Status:</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="pending" <?php if($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                                <option value="processing" <?php if($order['status'] == 'processing') echo 'selected'; ?>>Processing</option>
                                                <option value="shipped" <?php if($order['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                                                <option value="delivered" <?php if($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                                                <option value="cancelled" <?php if($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                    </form>
                                </div>
                            </div>
                            <div class="order-info-section">
                                <h3>Customer Information</h3>
                                <div class="info-group">
                                    <p><strong>Name:</strong> <?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                    <p><strong>Username:</strong> <?php echo $order['username']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-items-section">
                            <h3>Order Items</h3>
                            <div class="table-responsive">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($order_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="product-info">
                                                        <img src="../<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                                                        <div>
                                                            <p><?php echo $item['name']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>
