<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Check if order was placed
if (!isset($_SESSION['order_success'])) {
    redirect('index.php');
}

$order_number = $_SESSION['order_success'];
unset($_SESSION['order_success']);

// Get order details
try {
    $stmt = $db->query("SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.order_number = ? AND o.user_id = ?");
    $stmt->execute([$order_number, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        redirect('index.php');
    }
    
    // Get order items
    $stmt = $db->query("SELECT oi.*, p.name as product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order['id']]);
    $order_items = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching order details";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .success-icon {
            font-size: 4rem;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        .order-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: left;
        }
    </style>
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

    <!-- Confirmation Section -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="confirmation-card">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        
                        <h2 class="mb-3">Order Placed Successfully!</h2>
                        <p class="text-muted mb-4">Thank you for your order. We'll send you an email with order details.</p>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php else: ?>
                            <div class="order-details">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Order Number</h6>
                                        <p class="mb-0"><strong><?php echo $order['order_number']; ?></strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Order Date</h6>
                                        <p class="mb-0"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Payment Method</h6>
                                        <p class="mb-0"><?php echo $order['payment_method']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Order Status</h6>
                                        <p class="mb-0">
                                            <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>Shipping Address</h6>
                                    <p class="mb-0"><?php echo nl2br($order['shipping_address']); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>Order Items</h6>
                                    <?php foreach ($order_items as $item): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><?php echo $item['product_name']; ?> (<?php echo $item['quantity']; ?>)</span>
                                            <span><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Amount:</strong>
                                        <strong class="text-primary"><?php echo format_price($order['total_amount']); ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle"></i> You will receive a confirmation email shortly with your order details.
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
                                <a href="orders.php" class="btn btn-primary-custom">
                                    <i class="fas fa-list"></i> View My Orders
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-home"></i> Continue Shopping
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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
