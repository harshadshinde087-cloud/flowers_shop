<?php
require_once 'includes/config.php';

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    redirect('products.php');
}

// Get product details
try {
    $stmt = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        redirect('products.php');
    }
    
    // Get related products
    $stmt = $db->query("SELECT * FROM products WHERE category_id = ? AND id != ? AND stock_quantity > 0 ORDER BY RAND() LIMIT 4");
    $stmt->execute([$product['category_id'], $product_id]);
    $related_products = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching product details";
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_to_cart') {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
    
    $quantity = intval($_POST['quantity']);
    
    if ($quantity > 0 && $quantity <= $product['stock_quantity']) {
        try {
            // Check if product already in cart
            $stmt = $db->query("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $cart_item = $stmt->fetch();
            
            if ($cart_item) {
                // Update quantity
                $new_quantity = $cart_item['quantity'] + $quantity;
                if ($new_quantity <= $product['stock_quantity']) {
                    $stmt = $db->query("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                    $stmt->execute([$new_quantity, $_SESSION['user_id'], $product_id]);
                    $message = "Cart updated successfully!";
                } else {
                    $message = "Not enough stock available!";
                }
            } else {
                // Add to cart
                $stmt = $db->query("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
                $message = "Product added to cart!";
            }
            $message_type = "success";
        } catch(PDOException $e) {
            $message = "Error adding to cart. Please try again.";
            $message_type = "danger";
        }
    } else {
        $message = "Invalid quantity!";
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .product-image-large {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-price-large {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: bold;
        }
        .stock-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .in-stock {
            background: #d4edda;
            color: #155724;
        }
        .low-stock {
            background: #fff3cd;
            color: #856404;
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
                                <?php if (is_logged_in()): ?>
                                    <span class="cart-badge"><?php echo get_cart_count(); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php if (is_logged_in()): ?>
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
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <section class="py-3 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none">Products</a></li>
                    <li class="breadcrumb-item"><a href="category.php?id=<?php echo $product['category_id']; ?>" class="text-decoration-none"><?php echo $product['category_name']; ?></a></li>
                    <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Product Details -->
    <section class="py-5">
        <div class="container">
            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-6 mb-4">
                    <img src="<?php echo $product['image'] ? $product['image'] : 'https://via.placeholder.com/600x400?text=No+Image'; ?>" 
                         alt="<?php echo $product['name']; ?>" class="product-image-large">
                </div>
                
                <!-- Product Info -->
                <div class="col-md-6 mb-4">
                    <h1 class="mb-3"><?php echo $product['name']; ?></h1>
                    <p class="text-muted mb-3">
                        <i class="fas fa-folder"></i> <?php echo $product['category_name']; ?>
                        <?php if ($product['is_featured']): ?>
                            <span class="badge bg-warning ms-2">Featured</span>
                        <?php endif; ?>
                    </p>
                    
                    <div class="product-price-large mb-3">
                        <?php echo format_price($product['price']); ?>
                    </div>
                    
                    <div class="mb-3">
                        <?php if ($product['stock_quantity'] > 10): ?>
                            <span class="stock-badge in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock_quantity']; ?> available)
                            </span>
                        <?php elseif ($product['stock_quantity'] > 0): ?>
                            <span class="stock-badge low-stock">
                                <i class="fas fa-exclamation-triangle"></i> Only <?php echo $product['stock_quantity']; ?> left!
                            </span>
                        <?php else: ?>
                            <span class="stock-badge" style="background: #f8d7da; color: #721c24;">
                                <i class="fas fa-times-circle"></i> Out of Stock
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p><?php echo nl2br($product['description']); ?></p>
                    </div>
                    
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add_to_cart">
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary-custom btn-lg flex-fill">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <a href="products.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock.
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <h6>Features:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> 100% Fresh Flowers</li>
                            <li><i class="fas fa-check text-success"></i> Same Day Delivery Available</li>
                            <li><i class="fas fa-check text-success"></i> Beautiful Packaging</li>
                            <li><i class="fas fa-check text-success"></i> Quality Guaranteed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <section class="py-5 bg-light">
            <div class="container">
                <h3 class="text-center mb-4">Related Products</h3>
                <div class="row">
                    <?php foreach ($related_products as $related_product): ?>
                        <div class="col-md-3 mb-4">
                            <div class="product-card">
                                <img src="<?php echo $related_product['image'] ? $related_product['image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                     alt="<?php echo $related_product['name']; ?>" class="product-image">
                                <div class="product-body">
                                    <h5 class="product-title"><?php echo $related_product['name']; ?></h5>
                                    <p class="text-muted small"><?php echo $product['category_name']; ?></p>
                                    <div class="product-price"><?php echo format_price($related_product['price']); ?></div>
                                    <a href="product.php?id=<?php echo $related_product['id']; ?>" class="btn btn-primary-custom w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

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
