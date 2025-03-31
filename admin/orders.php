<?php
session_start();
include '../includes/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Fetch all orders
$sql = "SELECT o.*, u.username, u.email FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders - PG Prints Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>PG<span>Prints</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                    <li class="active"><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-store"></i> View Store</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="admin-content">
            <!-- Header -->
            <div class="admin-header">
                <h1>All Orders</h1>
                <div class="admin-user">
                    <span>Welcome, Admin</span>
                </div>
            </div>

            <!-- Main Content -->
            <div class="admin-main">
                <?php if (empty($orders)): ?>
                    <!-- No Orders Found -->
                    <div class="empty-data">
                        <i class="fas fa-shopping-bag"></i>
                        <p>No orders found</p>
                    </div>
                <?php else: ?>
                    <!-- Orders Table -->
                    <div class="admin-card">
                        <div class="card-header">
                            <h2>Orders List</h2>
                        </div>
                        <div class="card-body">
                            <!-- Responsive Table -->
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <!-- Order Row -->
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['username']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</td>
                                            <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <!-- Status Badge -->
                                            <td><span class="<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>

                                            <!-- Actions -->
                                            <td>
                                                <!-- View Order Button -->
                                                <a href="view_order.php?id=<?php echo $order['order_id']; ?>" title="View Order" class="btn-icon view">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Update Status Button -->
                                                <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                                                    <a href="update_status.php?id=<?php echo $order['order_id']; ?>" title="Update Status" class="btn-icon edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <?php include '../includes/footer.php'; ?>
        </div>

    </div>

    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>

</body>
</html>

