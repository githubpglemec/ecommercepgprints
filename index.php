<?php
include 'includes/header.php';
include 'includes/functions.php';

// Get all products
$products = getAllProducts($conn);

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1;
    
    addToCart($product_id, $quantity);
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF'] . '#gallery');
    exit;
}
?>
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <video autoplay muted loop playsinline class="hero-video">
                <source src="assets/Vid.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="hero-overlay">
                <h1>Fine Art Photography Prints</h1>
                <p>Transform your space with stunning, limited edition photography prints by poozan.</p>
                <a href="#gallery" class="btn btn-primary">Explore Gallery</a>
            </div>
        </div>
    </div>
</section>


<!-- Gallery/Products Section -->
<section id="gallery" class="gallery">
    <div class="container">
        <div class="section-title">
            <h2>Our Collection</h2>
            <p>Discover unique photography prints that tell a story and elevate your space</p>
        </div>
        
        <div class="products-grid">
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                    <?php echo "<!-- Debug: Image URL = " . $product['image_url'] . " -->"; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-actions">
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>About PG Prints</h2>
                <p>PG Prints is a curated collection of fine art photography prints that capture moments of beauty, emotion, and storytelling. Our mission is to bring exceptional photographic art into your spaces.</p>
                <p>Each print is carefully produced using premium archival materials to ensure longevity and vibrant colors that will stand the test of time.</p>
                <p>We work with talented photographers who have a unique vision and perspective, bringing their art directly to your walls.</p>
                <a href="#contact" class="btn btn-primary">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact">
    <div class="container">
        <div class="section-title">
            <h2>Contact Us</h2>
            <p>Have questions or need assistance? We're here to help.</p>
        </div>
        
        <div class="contact-content">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p>info@pgprints.com</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <p>+977 9840755930</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Kathmandu, Nepal</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <p>Monday - Friday: 9am - 6pm</p>
                </div>
            </div>
            
            <div class="contact-form">
    <h3>Send a Message</h3>
    <div id="form-response" class="alert" style="display: none;"></div>
    <form id="contact-form">
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="contact-email">Your Email</label>
            <input type="email" id="contact-email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="message">Your Message</label>
            <textarea id="message" name="message" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
