<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get all orders
$sql = "SELECT o.*, u.username, u.email FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.created_at DESC LIMIT 5";
$result = $conn->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Get all products
$products = getAllProducts($conn);

// Get all messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
$messages = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PG Prints</title>
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
                    <li><a href="#orders"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="#products"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="#messages"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-store"></i> View Store</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-content">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="admin-user">
                    <span>Welcome, Admin</span>
                </div>
            </div>
            <div class="admin-main">
    <!-- Orders Section -->
    <div id="orders" class="admin-section">
        <div class="admin-card">
            <div class="card-header">
                <h2>Recent Orders</h2>
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <div class="empty-data">
                        <i class="fas fa-shopping-bag"></i>
                        <p>No orders found</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
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
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['username']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</td>
                                        <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td><span class="<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                        <td>
                                            <!-- Action buttons -->
                                            <div class="action-buttons">
                                                <!-- View Order -->
                                                <a href="view_order.php?id=<?php echo $order['order_id']; ?>" title="View Order" class="btn-icon view">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Update Status -->
                                                <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                                                    <a href="update_status.php?id=<?php echo $order['order_id']; ?>" title="Update Status" class="btn-icon edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div id="products" class="admin-section">
        <div class="admin-card">
            <div class="card-header">
                <h2>Product Inventory</h2>
                <a href="products.php" class="btn btn-primary">Manage Products</a>
            </div>
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <div class="empty-data">
                        <i class="fas fa-box-open"></i>
                        <p>No products found</p>
                    </div>
                <?php else: ?>
                    <!-- Responsive Table -->
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($products, 0, 5) as $product): ?>
                                <!-- Product Row -->
                                <tr>
                                    <td>#<?php echo $product['product_id']; ?></td>
                                    <!-- Product Thumbnail -->
                                    <td><img src="../<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;"></td>

                                    <!-- Product Details -->
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>

                                    <!-- Price -->
                                    <td>Rs. <?php echo number_format($product['price'], 2); ?></td>

                                    <!-- Stock Quantity -->
                                    <td><?php echo $product['stock_quantity']; ?></td>

                                    <!-- Actions -->
                                    <td><a href="#" title="View Product" class="btn-icon view"><i class="fas fa-eye"></i></a></td>

                                </tr>

                            <?php endforeach; ?>
                        </tbody>

                    </table>

                <?php endif; ?>
            </div>
        </div>
    </div>



                <!-- Messages Section -->
                <div id="messages" class="admin-section">
                    <div class="admin-card">
                        <div class="card-header">
                            <h2>Recent Messages</h2>
                            <a href="messages.php" class="btn btn-primary">View All Messages</a>
                        </div>
                        <div class="card-body">
                            <?php if(empty($messages)): ?>
                                <div class="empty-data">
                                    <i class="fas fa-envelope-open"></i>
                                    <p>No messages found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Subject</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($messages as $message): ?>
                                                <tr class="<?php echo $message['is_read'] ? '' : 'unread'; ?>">
                                                    <td>#<?php echo $message['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                                                    <td>
                                                        <span class="status-badge <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                                                            <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="#" class="btn-icon view message-view" data-id="<?php echo $message['id']; ?>" title="View Message">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php if(!$message['is_read']): ?>
                                                                <a href="mark_read.php?id=<?php echo $message['id']; ?>" class="btn-icon edit" title="Mark as Read">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
    <script src="admin.js"></script>
</body>
</html>
