<?php
// Get all products
function getAllProducts($conn) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    $products = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get product by ID
function getProductById($conn, $id) {
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Add to cart
function addToCart($product_id, $quantity = 1) {
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if product already in cart
    $found = false;
    foreach($_SESSION['cart'] as $key => $item) {
        if($item['product_id'] == $product_id) {
            $_SESSION['cart'][$key]['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    // If not found, add new item
    if(!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
    }
}

// Update cart
function updateCart($product_id, $quantity) {
    foreach($_SESSION['cart'] as $key => $item) {
        if($item['product_id'] == $product_id) {
            if($quantity <= 0) {
                unset($_SESSION['cart'][$key]);
                // Reindex array
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $quantity;
            }
            break;
        }
    }
}

// Remove from cart
function removeFromCart($product_id) {
    foreach($_SESSION['cart'] as $key => $item) {
        if($item['product_id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

// Get cart items with product details
function getCartItems($conn) {
    if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $cart_items = [];
    
    foreach($_SESSION['cart'] as $item) {
        $product = getProductById($conn, $item['product_id']);
        if($product) {
            $cart_items[] = [
                'product' => $product,
                'quantity' => $item['quantity']
            ];
        }
    }
    
    return $cart_items;
}

// Calculate cart total
function getCartTotal($cart_items) {
    $total = 0;
    
    foreach($cart_items as $item) {
        $total += $item['product']['price'] * $item['quantity'];
    }
    
    return $total;
}

// Create order
function createOrder($conn, $user_id, $cart_items) {
    $total = getCartTotal($cart_items);
    
    // Insert order
    $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach($cart_items as $item) {
        $product_id = $item['product']['product_id'];
        $quantity = $item['quantity'];
        $price = $item['product']['price'];
        
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt->execute();
        
        // Update stock
        $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();
    }
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    return $order_id;
}

// Get user orders
function getUserOrders($conn, $user_id) {
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Get order items
function getOrderItems($conn, $order_id) {
    $sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
            JOIN products p ON oi.product_id = p.product_id 
            WHERE oi.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    
    return $items;
}
?>
