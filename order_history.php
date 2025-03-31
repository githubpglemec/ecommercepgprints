<?php
include 'includes/header.php';
include 'includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'order_history.php';
    header('Location: login.php');
    exit;
}

// Get user orders
$user_id = $_SESSION['user_id'];
$orders = getUserOrders($conn, $user_id);

// Check for order success message
$order_success = isset($_SESSION['order_success']) ? true : false;
unset($_SESSION['order_success']);
?>

<section class="orders-section">
    <div class="container">
        <div class="section-title">
            <h2>Order History</h2>
            <p>View your past orders and their status</p>
        </div>
        
        <?php if ($order_success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <p>Your order has been placed successfully! Thank you for shopping with us.</p>
            </div>
        <?php endif; ?>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <i class="fas fa-shopping-bag"></i>
                <h3>No orders yet</h3>
                <p>You haven't placed any orders yet.</p>
                <a href="index.php#gallery" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Order #<?php echo $order['order_id']; ?></h3>
                                <p>Placed on: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="order-status <?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <?php
                            $order_items = getOrderItems($conn, $order['order_id']);
                            foreach ($order_items as $item):
                            ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                                    </div>
                                    <div class="item-details">
                                        <h4><?php echo $item['name']; ?></h4>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                        <p>Rs. <?php echo number_format($item['price'], 2); ?> each</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                <span>Total:</span>
                                <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
