document.addEventListener('DOMContentLoaded', function() {
    // Form validation for checkout
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (!validateCheckoutForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Form validation for registration
    const registerForm = document.querySelector('.auth-form');
    if (registerForm && registerForm.querySelector('[name="confirm_password"]')) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegistrationForm()) {
                e.preventDefault();
            }
        });
    }
});

function validateCheckoutForm() {
    // Get form fields
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const postalCode = document.getElementById('postal_code').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    // Validate fields
    if (!firstName || !lastName || !email || !address || !city || !postalCode || !phone) {
        alert('Please fill in all required fields');
        return false;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    // Validate phone number (simple validation)
    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(phone.replace(/[^0-9]/g, ''))) {
        alert('Please enter a valid 10-digit phone number');
        return false;
    }
    
    return true;
}

function validateRegistrationForm() {
    // Get form fields
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validate fields
    if (!username || !email || !password || !confirmPassword) {
        alert('Please fill in all required fields');
        return false;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    // Validate password length
    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        return false;
    }
    
    // Validate password match
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    
    return true;
}
