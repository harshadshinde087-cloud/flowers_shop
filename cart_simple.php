<?php
// Standalone cart page - no dependencies
session_start();

// Simple database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'flower_shop';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_simple.php");
    exit();
}

// Handle cart operations
$message = '';
$message_type = '';

// Add to cart
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);
    $quantity = intval($_GET['quantity'] ?? 1);
    
    try {
        // Check if product exists and has stock
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND stock_quantity > 0");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $cart_item = $stmt->fetch();
            
            if ($cart_item) {
                // Update quantity
                $new_quantity = $cart_item['quantity'] + $quantity;
                if ($new_quantity <= $product['stock_quantity']) {
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                    $stmt->execute([$new_quantity, $_SESSION['user_id'], $product_id]);
                    $message = "Cart updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "Not enough stock available!";
                    $message_type = "warning";
                }
            } else {
                // Add to cart
                if ($quantity <= $product['stock_quantity']) {
                    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
                    $message = "Product added to cart!";
                    $message_type = "success";
                } else {
                    $message = "Not enough stock available!";
                    $message_type = "warning";
                }
            }
        } else {
            $message = "Product not available!";
            $message_type = "danger";
        }
    } catch(PDOException $e) {
        $message = "Error adding to cart: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Update cart quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            try {
                // Check stock
                $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();
                
                if ($product && $quantity <= $product['stock_quantity']) {
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                    $stmt->execute([$quantity, $_SESSION['user_id'], $product_id]);
                }
            } catch(PDOException $e) {
                // Continue with other items
            }
        }
    }
    $message = "Cart updated successfully!";
    $message_type = "success";
}

// Remove from cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $message = "Item removed from cart!";
        $message_type = "success";
    } catch(PDOException $e) {
        $message = "Error removing item: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Get cart items
$cart_items = [];
$total_amount = 0;

try {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image, p.stock_quantity 
                          FROM cart c 
                          LEFT JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
} catch(PDOException $e) {
    $error = "Error fetching cart items";
}

// Get cart count
$cart_count = count($cart_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Flower Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
<style>
        .cart-product-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-product-info {
            flex: 1;
        }
        .cart-table td {
            vertical-align: middle;
        }
        @media (max-width: 768px) {
            .cart-product-image {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="index_simple.php">
                    🌸 <strong></strong>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index_simple.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products_simple.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about_simple.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact_simple.php">Contact</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <div class="nav-link">
                                <i class="fas fa-search"></i>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative active" href="cart_simple.php">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cart_count > 0): ?>
                                    <span class="cart-count"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile_simple.php">Profile</a></li>
                                <li><a class="dropdown-item" href="orders_simple.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout_simple.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Cart Section -->
    <section class="cart py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <h3 class="mb-4">Shopping Cart</h3>
                        
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($cart_items)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h4>Your cart is empty</h4>
                                <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
                                <a href="products_simple.php" class="btn btn-primary-custom">Continue Shopping</a>
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="update_cart" value="1">
                                
                                <div class="table-responsive">
                                    <table class="table table-hover cart-table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cart_items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <?php if ($item['image']): ?>
                                                                    <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-product-image">
                                                                <?php else: ?>
                                                                    <div class="cart-product-image d-flex align-items-center justify-content-center bg-light">🌹</div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="cart-product-info">
                                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                                <small class="text-muted">Stock: <?php echo $item['stock_quantity']; ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                    <td>
                                                        <input type="number" name="quantities[<?php echo $item['product_id']; ?>]" 
                                                               value="<?php echo $item['quantity']; ?>" 
                                                               min="1" 
                                                               max="<?php echo $item['stock_quantity']; ?>" 
                                                               class="form-control form-control-sm" 
                                                               style="width: 80px;">
                                                    </td>
                                                    <td><strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                                                    <td>
                                                        <a href="cart_simple.php?remove=<?php echo $item['product_id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Remove this item from cart?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="products_simple.php" class="btn btn-outline-custom">
                                        <i class="fas fa-arrow-left"></i> Continue Shopping
                                    </a>
                                    <button type="submit" class="btn btn-primary-custom">
                                        <i class="fas fa-sync"></i> Update Cart
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <h5 class="mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total_amount, 2); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax:</span>
                            <span>$0.00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5>$<?php echo number_format($total_amount, 2); ?></h5>
                        </div>
                        
                        <?php if (!empty($cart_items)): ?>
                            <a href="checkout_simple.php" class="btn btn-primary-custom w-100">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>🌸 Flower Shop</h5>
                    <p>Your trusted partner for beautiful flowers and greeting cards for every occasion.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="products_simple.php">All Products</a></li>
                        <li><a href="about_simple.php">About Us</a></li>
                        <li><a href="contact_simple.php">Contact</a></li>
                        <li><a href="orders_simple.php">Track Order</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                    <p><i class="fas fa-envelope"></i> info@flowershop.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Flower Street, City</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 Flower Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
