<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// Handle cart operations
$message = '';
$message_type = '';

// Update cart quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity > 0) {
        try {
            // Check product stock
            $stmt = $db->query("SELECT c.*, p.stock_quantity FROM cart c LEFT JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
            $stmt->execute([$cart_id, $_SESSION['user_id']]);
            $cart_item = $stmt->fetch();
            
            if ($cart_item && $quantity <= $cart_item['stock_quantity']) {
                $stmt = $db->query("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
                $message = "Cart updated successfully!";
                $message_type = "success";
            } else {
                $message = "Not enough stock available!";
                $message_type = "danger";
            }
        } catch(PDOException $e) {
            $message = "Error updating cart. Please try again.";
            $message_type = "danger";
        }
    }
}

// Remove from cart
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    try {
        $stmt = $db->query("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
        $message = "Item removed from cart!";
        $message_type = "success";
    } catch(PDOException $e) {
        $message = "Error removing item. Please try again.";
        $message_type = "danger";
    }
}

// Get cart items
try {
    $stmt = $db->query("SELECT c.*, p.name, p.price, p.image, p.stock_quantity FROM cart c LEFT JOIN products p ON c.product_id = p.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
} catch(PDOException $e) {
    $error = "Error fetching cart items";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
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
            <h1 class="mb-0">Shopping Cart</h1>
            <p class="text-muted mb-0">Review your items before checkout</p>
        </div>
    </section>

    <!-- Cart Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php elseif (empty($cart_items)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h4>Your cart is empty</h4>
                    <p class="text-muted">Add some beautiful flowers and cards to your cart!</p>
                    <a href="products.php" class="btn btn-primary-custom">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-md-8">
                        <div class="cart-table">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo $item['image'] ? $item['image'] : 'https://via.placeholder.com/80x80?text=No+Image'; ?>" 
                                                             alt="<?php echo $item['name']; ?>" class="cart-item-image me-3">
                                                        <div>
                                                            <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                                                            <small class="text-muted">
                                                                <?php if ($item['stock_quantity'] < 10): ?>
                                                                    <span class="text-warning">Only <?php echo $item['stock_quantity']; ?> left!</span>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo format_price($item['price']); ?></td>
                                                <td>
                                                    <form method="POST" action="" class="d-inline">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="number" name="quantity" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                                               min="1" max="<?php echo $item['stock_quantity']; ?>" onchange="this.form.submit()">
                                                    </form>
                                                </td>
                                                <td><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                                                <td>
                                                    <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Remove this item from cart?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-md-4">
                        <div class="bg-white rounded p-4 shadow-sm">
                            <h5 class="mb-4">Order Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo format_price($total_amount); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery:</span>
                                <span class="text-success">FREE</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total:</strong>
                                <strong class="text-primary"><?php echo format_price($total_amount); ?></strong>
                            </div>
                            
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-truck"></i> Free delivery on all orders!
                            </div>
                            
                            <a href="checkout.php" class="btn btn-primary-custom w-100 mb-2">
                                Proceed to Checkout
                            </a>
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-lock"></i> Secure Checkout
                                </small>
                            </div>
                        </div>
                        
                        <!-- Discount Code -->
                        <div class="bg-white rounded p-4 shadow-sm mt-3">
                            <h6 class="mb-3">Have a discount code?</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter code">
                                <button class="btn btn-outline-secondary" type="button">Apply</button>
                            </div>
                        </div>
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
