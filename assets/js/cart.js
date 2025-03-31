document.addEventListener('DOMContentLoaded', function() {
    // Cart functionality
    initializeCart();
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('button[name="add_to_cart"]');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // We're using form submission for adding to cart
            // This is just for visual feedback
            const originalText = this.textContent;
            this.textContent = 'Adding...';
            
            setTimeout(() => {
                this.textContent = originalText;
            }, 1000);
        });
    });
});

function initializeCart() {
    // Quantity buttons in cart page
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

}

function updateCart(id, quantity) {
    // Show loading indicator
    const cartItems = document.querySelector('.cart-items');
    if (cartItems) {
        cartItems.classList.add('loading');
    }
    
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
        if (cartItems) {
            cartItems.classList.remove('loading');
        }
    });
}

// Update cart function
function updateCart(id, quantity) {
    // Show loading indicator
    const cartItems = document.querySelector('.cart-items');
    if (cartItems) {
        cartItems.classList.add('loading');
    }
    
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${id}&quantity=${quantity}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            console.error('Error updating cart:', data.message);
            alert('Failed to update cart. Please try again.');
            if (cartItems) {
                cartItems.classList.remove('loading');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        if (cartItems) {
            cartItems.classList.remove('loading');
        }
    });
}

