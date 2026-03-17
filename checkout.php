<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// Get cart items
try {
    $stmt = $db->query("SELECT c.*, p.name, p.price, p.stock_quantity FROM cart c LEFT JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        redirect('cart.php');
    }
    
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Get user details
    $stmt = $db->query("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
} catch(PDOException $e) {
    $error = "Error preparing checkout";
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = clean_input($_POST['shipping_address']);
    $payment_method = clean_input($_POST['payment_method']);
    $notes = clean_input($_POST['notes']);
    
    $errors = [];
    
    if (empty($shipping_address)) {
        $errors[] = "Shipping address is required";
    }
    
    if (empty($payment_method)) {
        $errors[] = "Payment method is required";
    }
    
    // Check stock availability
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock_quantity']) {
            $errors[] = "Insufficient stock for " . $item['name'];
        }
    }
    
    if (empty($errors)) {
        try {
            // Start transaction
            $db->conn->beginTransaction();
            
            // Create order
            $order_number = generate_order_number();
            $stmt = $db->query("INSERT INTO orders (user_id, order_number, total_amount, shipping_address, payment_method, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$_SESSION['user_id'], $order_number, $total_amount, $shipping_address, $payment_method]);
            $order_id = $db->lastInsertId();
            
            // Add order items
            foreach ($cart_items as $item) {
                $stmt = $db->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                
                // Update product stock
                $stmt = $db->query("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Clear cart
            $stmt = $db->query("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Commit transaction
            $db->conn->commit();
            
            // Redirect to order confirmation
            $_SESSION['order_success'] = $order_number;
            redirect('order_confirmation.php');
            
        } catch(PDOException $e) {
            $db->conn->rollBack();
            $errors[] = "Error placing order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    🌸 <?php echo SITE_NAME; ?>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">All Products</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                                Categories
                            </a>
                            <ul class="dropdown-menu">
                                <?php 
                                $stmt = $db->query("SELECT * FROM categories ORDER BY name");
                                $categories = $stmt->fetchAll();
                                foreach ($categories as $category): 
                                ?>
                                    <li><a class="dropdown-item" href="category.php?id=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <form class="d-flex me-3" method="GET" action="search.php">
                                <input class="form-control me-2" type="search" name="q" placeholder="Search products..." style="border-radius: 20px; padding: 5px 15px;">
                                <button class="btn btn-outline-light" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart"></i> Cart
                                <span class="cart-badge"><?php echo get_cart_count(); ?></span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Header -->
    <section class="py-4 bg-light">
        <div class="container">
            <h1 class="mb-0">Checkout</h1>
            <p class="text-muted mb-0">Complete your order details</p>
        </div>
    </section>

    <!-- Checkout Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-1"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <!-- Order Summary -->
                    <div class="col-md-5 mb-4">
                        <div class="bg-white rounded p-4 shadow-sm">
                            <h5 class="mb-4">Order Summary</h5>
                            
                            <div class="mb-3">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × <?php echo format_price($item['price']); ?></small>
                                        </div>
                                        <span><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo format_price($total_amount); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery:</span>
                                <span class="text-success">FREE</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="text-primary fs-5"><?php echo format_price($total_amount); ?></strong>
                            </div>
                            
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-truck"></i> Free delivery on all orders!
                            </div>
                        </div>
                    </div>
                    
                    <!-- Checkout Form -->
                    <div class="col-md-7">
                        <div class="bg-white rounded p-4 shadow-sm mb-4">
                            <h5 class="mb-4">Shipping Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Shipping Address *</label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded p-4 shadow-sm mb-4">
                            <h5 class="mb-4">Payment Method</h5>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                                </label>
                                <div class="text-muted small mt-1">Pay when you receive your order</div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="Credit/Debit Card">
                                <label class="form-check-label" for="card">
                                    <i class="fas fa-credit-card"></i> Credit/Debit Card
                                </label>
                                <div class="text-muted small mt-1">Secure online payment (Coming Soon)</div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded p-4 shadow-sm mb-4">
                            <h5 class="mb-4">Order Notes (Optional)</h5>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Special instructions for delivery..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                            <button type="submit" class="btn btn-primary-custom btn-lg">
                                <i class="fas fa-check"></i> Place Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
