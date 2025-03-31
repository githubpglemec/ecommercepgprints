<?php
include 'includes/header.php';
include 'includes/functions.php';

// Check if cart is empty
$cart_items = getCartItems($conn);
$cart_total = getCartTotal($cart_items);
?>

<section class="cart-section">
    <div class="container">
        <div class="section-title">
            <h2>Your Shopping Cart</h2>
            <p>Review your items and proceed to checkout</p>
        </div>

        <?php if(empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="index.php#gallery" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $item): ?>
                                <tr>
                                    <td class="product-info">
                                        <img src="<?php echo $item['product']['image_url']; ?>" alt="<?php echo $item['product']['name']; ?>">
                                        <div>
                                            <h4><?php echo $item['product']['name']; ?></h4>
                                        </div>
                                    </td>
                                    <td class="product-price">Rs. <?php echo number_format($item['product']['price'], 2); ?></td>
                                    <td class="product-quantity">
                                        <div class="quantity-control">
                                            <button class="quantity-btn minus" data-id="<?php echo $item['product']['product_id']; ?>">-</button>
                                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock_quantity']; ?>" 
                                                   class="quantity-input" data-id="<?php echo $item['product']['product_id']; ?>">
                                            <button class="quantity-btn plus" data-id="<?php echo $item['product']['product_id']; ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="product-total">Rs. <?php echo number_format($item['product']['price'] * $item['quantity'], 2); ?></td>
                                    <td class="product-remove">
                                    <button class="remove-btn" data-id="<?php echo $item['product']['product_id']; ?>">
                                    <i class="fas fa-trash"></i>
                                    </button>
    
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="cart-summary">
                    <h3>Order Summary</h3>
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
                    <div class="checkout-actions">
                        <a href="index.php#gallery" class="btn btn-secondary">Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    const minusBtns = document.querySelectorAll('.quantity-btn.minus');
    const plusBtns = document.querySelectorAll('.quantity-btn.plus');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const removeBtns = document.querySelectorAll('.remove-btn');
    
    // Decrease quantity
    minusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
            let value = parseInt(input.value);
            if (value > 1) {
                value--;
                input.value = value;
                updateCart(id, value);
            }
        });
    });
    
    // Increase quantity
    plusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
            let value = parseInt(input.value);
            const max = parseInt(input.getAttribute('max'));
            if (value < max) {
                value++;
                input.value = value;
                updateCart(id, value);
            }
        });
    });
    
    // Input change
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const id = this.dataset.id;
            let value = parseInt(this.value);
            const max = parseInt(this.getAttribute('max'));
            
            if (isNaN(value) || value < 1) {
                value = 1;
            } else if (value > max) {
                value = max;
            }
            
            this.value = value;
            updateCart(id, value);
        });
    });
    
    // Remove item
    removeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            updateCart(id, 0);
        });
    });
    
    // Update cart function
    function updateCart(id, quantity) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${id}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
