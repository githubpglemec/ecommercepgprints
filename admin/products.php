<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get all products
$products = getAllProducts($conn);

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    // Delete product
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        header('Location: products.php?success=deleted');
        exit;
    } else {
        header('Location: products.php?error=delete_failed');
        exit;
    }
}

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];
    $stock = $_POST['stock'];
    
    // Update stock
    $sql = "UPDATE products SET stock_quantity = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $stock, $product_id);
    
    if ($stmt->execute()) {
        header('Location: products.php?success=updated');
        exit;
    } else {
        header('Location: products.php?error=update_failed');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - PG Prints Admin</title>
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
                    <li><a href="index.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li class="active"><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-store"></i> View Store</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-content">
            <div class="admin-header">
                <h1>Product Management</h1>
                <div class="admin-user">
                    <span>Welcome, Admin</span>
                </div>
            </div>
            <div class="admin-main">
                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php if($_GET['success'] == 'deleted'): ?>
                            <i class="fas fa-check-circle"></i> Product deleted successfully.
                        <?php elseif($_GET['success'] == 'updated'): ?>
                            <i class="fas fa-check-circle"></i> Product updated successfully.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php if($_GET['error'] == 'delete_failed'): ?>
                            <i class="fas fa-exclamation-circle"></i> Failed to delete product.
                        <?php elseif($_GET['error'] == 'update_failed'): ?>
                            <i class="fas fa-exclamation-circle"></i> Failed to update product.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Product Inventory</h2>
                    </div>
                    <div class="card-body">
                        <?php if(empty($products)): ?>
                            <div class="empty-data">
                                <i class="fas fa-box-open"></i>
                                <p>No products found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
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
                                        <?php foreach($products as $product): ?>
                                            <tr>
                                                <td>#<?php echo $product['product_id']; ?></td>
                                                <td>
                                                    <div class="product-thumbnail">
                                                        <img src="../<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                                                    </div>
                                                </td>
                                                <td><?php echo $product['name']; ?></td>
                                                <td>Rs. <?php echo number_format($product['price'], 2); ?></td>
                                                <td>
                                                    <form method="post" class="stock-form">
                                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                        <div class="stock-control">
                                                            <input type="number" name="stock" value="<?php echo $product['stock_quantity']; ?>" min="0" class="form-control stock-input">
                                                            <button type="submit" name="update_stock" class="btn-icon update" title="Update Stock">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="#" class="btn-icon view product-view" data-id="<?php echo $product['product_id']; ?>" title="View Product">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="products.php?delete=<?php echo $product['product_id']; ?>" class="btn-icon delete" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
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
    
    <!-- Product View Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="productDetails">
                <!-- Product details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Product view modal
        const modal = document.getElementById('productModal');
        const productViewBtns = document.querySelectorAll('.product-view');
        const closeBtn = document.querySelector('.close');
        const productDetails = document.getElementById('productDetails');
        
        productViewBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-id');
                
                // Fetch product details
                fetch(`../includes/get_product.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            productDetails.innerHTML = `
                                <div class="product-modal-content">
                                    <div class="product-modal-image">
                                        <img src="../${data.product.image_url}" alt="${data.product.name}">
                                    </div>
                                    <div class="product-modal-info">
                                        <h2>${data.product.name}</h2>
                                        <p class="product-modal-price">Rs. ${parseFloat(data.product.price).toFixed(2)}</p>
                                        <div class="product-modal-details">
                                            <p><strong>ID:</strong> #${data.product.product_id}</p>
                                            <p><strong>Stock:</strong> ${data.product.stock_quantity}</p>
                                            <p><strong>Added:</strong> ${new Date(data.product.created_at).toLocaleDateString()}</p>
                                        </div>
                                        <div class="product-modal-description">
                                            <h3>Description</h3>
                                            <p>${data.product.description || 'No description available.'}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            modal.style.display = 'block';
                        } else {
                            alert('Failed to load product details.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while loading product details.');
                    });
            });
        });
        
        // Close modal
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
    
    <style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 900px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        position: relative;
    }
    
    .close {
        position: absolute;
        right: 20px;
        top: 15px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .product-modal-content {
        display: flex;
        gap: 30px;
    }
    
    .product-modal-image {
        flex: 1;
        max-width: 400px;
    }
    
    .product-modal-image img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    
    .product-modal-info {
        flex: 1;
    }
    
    .product-modal-price {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 10px 0 20px;
    }
    
    .product-modal-details {
        margin-bottom: 20px;
    }
    
    .product-modal-details p {
        margin-bottom: 8px;
    }
    
    .product-modal-description h3 {
        margin-bottom: 10px;
        font-size: 18px;
    }
    
    .product-thumbnail {
        width: 60px;
        height: 60px;
        overflow: hidden;
        border-radius: 6px;
    }
    
    .product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .stock-control {
        display: flex;
        align-items: center;
    }
    
    .stock-input {
        width: 70px;
        padding: 6px;
        text-align: center;
    }
    
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background-color: var(--secondary-color);
        color: var(--primary-color);
        margin-left: 5px;
        transition: var(--transition);
    }
    
    .btn-icon:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    .btn-icon.delete {
        background-color: rgba(244, 67, 54, 0.1);
        color: var(--danger);
    }
    
    .btn-icon.delete:hover {
        background-color: var(--danger);
        color: white;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    @media (max-width: 768px) {
        .product-modal-content {
            flex-direction: column;
        }
        
        .product-modal-image {
            max-width: 100%;
        }
    }
    </style>
</body>
</html>
