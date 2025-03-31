<?php
include 'includes/header.php';
include 'includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

// Check if cart is empty
$cart_items = getCartItems($conn);
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$cart_total = getCartTotal($cart_items);

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = createOrder($conn, $_SESSION['user_id'], $cart_items);
    
    if ($order_id) {
        $_SESSION['order_success'] = true;
        header('Location: order_history.php');
        exit;
    }
}

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<section class="checkout-section">
    <div class="container">
        <div class="section-title">
            <h2>Checkout</h2>
            <p>Complete your purchase</p>
        </div>
        
        <div class="checkout-content">
            <div class="checkout-form">
                <form method="post" id="checkout-form">
                    <div class="form-section">
                        <h3>Shipping Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo $user['first_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo $user['last_name']; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="cash" name="payment_method" value="cash" checked>
                                <label for="cash">Cash on Delivery</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </form>
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="order-items">
                    <?php foreach($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="<?php echo $item['product']['image_url']; ?>" alt="<?php echo $item['product']['name']; ?>">
                            </div>
                            <div class="item-details">
                                <h4><?php echo $item['product']['name']; ?></h4>
                                <p>Quantity: <?php echo $item['quantity']; ?></p>
                                <p>Rs. <?php echo number_format($item['product']['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>Rs. <?php echo number_format($cart_total, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>Rs. <?php echo number_format($cart_total, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/validation.js"></script>

<?php include 'includes/footer.php'; ?>
