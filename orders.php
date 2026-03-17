<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get user orders
try {
    $stmt = $db->query("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching orders";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
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
            <h1 class="mb-0">My Orders</h1>
            <p class="text-muted mb-0">Track and manage your orders</p>
        </div>
    </section>

    <!-- Orders Section -->
    <section class="py-5">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php elseif (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No orders yet</h4>
                    <p class="text-muted">Start shopping to see your orders here!</p>
                    <a href="products.php" class="btn btn-primary-custom">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($orders as $order): ?>
                        <?php
                        // Get order items
                        $stmt = $db->query("SELECT oi.*, p.name as product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $stmt->execute([$order['id']]);
                        $order_items = $stmt->fetchAll();
                        ?>
                        
                        <div class="col-md-12 mb-4">
                            <div class="bg-white rounded p-4 shadow-sm">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="mb-1">Order #<?php echo $order['order_number']; ?></h5>
                                                <p class="text-muted mb-0">Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                                            </div>
                                            <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <?php foreach ($order_items as $item): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $item['product_name']; ?></h6>
                                                        <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                                    </div>
                                                    <span><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Total: <?php echo format_price($order['total_amount']); ?></strong>
                                                <br>
                                                <small class="text-muted">Payment: <?php echo $order['payment_method']; ?></small>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#orderDetails<?php echo $order['id']; ?>">
                                                View Details
                                            </button>
                                        </div>
                                        
                                        <!-- Order Details Collapse -->
                                        <div class="collapse mt-3" id="orderDetails<?php echo $order['id']; ?>">
                                            <div class="border-top pt-3">
                                                <h6>Shipping Address</h6>
                                                <p class="mb-3"><?php echo nl2br($order['shipping_address']); ?></p>
                                                
                                                <h6>Order Timeline</h6>
                                                <div class="timeline">
                                                    <div class="timeline-item">
                                                        <i class="fas fa-check text-success"></i>
                                                        <div>
                                                            <strong>Order Placed</strong><br>
                                                            <small><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></small>
                                                        </div>
                                                    </div>
                                                    <?php if ($order['status'] != 'Pending'): ?>
                                                        <div class="timeline-item">
                                                            <i class="fas fa-check text-success"></i>
                                                            <div>
                                                                <strong>Processing</strong><br>
                                                                <small>Your order is being processed</small>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] == 'Shipped' || $order['status'] == 'Delivered'): ?>
                                                        <div class="timeline-item">
                                                            <i class="fas fa-check text-success"></i>
                                                            <div>
                                                                <strong>Shipped</strong><br>
                                                                <small>Your order has been shipped</small>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] == 'Delivered'): ?>
                                                        <div class="timeline-item">
                                                            <i class="fas fa-check text-success"></i>
                                                            <div>
                                                                <strong>Delivered</strong><br>
                                                                <small>Order delivered successfully</small>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 text-center">
                                        <?php if ($order['status'] == 'Pending'): ?>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-clock"></i><br>
                                                <small>Order is pending confirmation</small>
                                            </div>
                                        <?php elseif ($order['status'] == 'Processing'): ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-cog"></i><br>
                                                <small>Order is being processed</small>
                                            </div>
                                        <?php elseif ($order['status'] == 'Shipped'): ?>
                                            <div class="alert alert-primary">
                                                <i class="fas fa-truck"></i><br>
                                                <small>Order is on the way</small>
                                            </div>
                                        <?php elseif ($order['status'] == 'Delivered'): ?>
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle"></i><br>
                                                <small>Order delivered successfully</small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <a href="products.php" class="btn btn-outline-primary btn-sm">Reorder</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }
        .timeline-item i {
            position: absolute;
            left: -30px;
            top: 2px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
