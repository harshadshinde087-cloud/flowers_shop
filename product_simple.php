<?php
// Standalone product details page - no dependencies
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

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product details
$product = null;
$category = null;
$related_products = [];

if ($product_id > 0) {
    try {
        // Get product details
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.id as category_id 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Get category info
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$product['category_id']]);
            $category = $stmt->fetch();
            
            // Get related products (same category, excluding current product)
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND stock_quantity > 0 ORDER BY RAND() LIMIT 4");
            $stmt->execute([$product['category_id'], $product_id]);
            $related_products = $stmt->fetchAll();
        }
        
    } catch(PDOException $e) {
        $error = "Error fetching product data";
    }
}

// Get cart count if user is logged in
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_count = $stmt->fetch()['count'];
    } catch(PDOException $e) {
        $cart_count = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product'; ?> - Flower Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .product-image-large img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 50%;
        }
        .product-card .product-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="index_simple.php">
                    🌸 <strong>Flower Shop</strong>
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
                            <a class="nav-link position-relative" href="cart_simple.php">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cart_count > 0): ?>
                                    <span class="cart-count"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
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
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login_simple.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register_simple.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Product Details Section -->
    <section class="product-details py-5">
        <div class="container">
            <?php if ($product): ?>
                <div class="row">
                    <!-- Product Image -->
                    <div class="col-md-6 mb-4">
                        <div class="product-image-large">
                            <?php if ($product['image']): ?>
                                <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/600x600/f0f0f0/666666?text=Product+Image" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="col-md-6 mb-4">
                        <div class="product-info-large">
                            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                            
                            <div class="product-meta mb-3">
                                <span class="category-tag"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <span class="stock-badge">In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                                <?php else: ?>
                                    <span class="stock-badge out-of-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-price-large mb-4">
                                <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                            </div>
                            
                            <div class="product-description mb-4">
                                <h5>Description</h5>
                                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                            </div>
                            
                            <div class="product-actions-large">
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="cart_simple.php?add=<?php echo $product['id']; ?>" class="btn btn-primary-custom btn-lg">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </a>
                                    <?php else: ?>
                                        <a href="login_simple.php" class="btn btn-primary-custom btn-lg">
                                            <i class="fas fa-user"></i> Login to Buy
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="fas fa-times"></i> Out of Stock
                                    </button>
                                <?php endif; ?>
                                
                                <a href="products_simple.php" class="btn btn-outline-custom btn-lg">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                            
                            <div class="product-features mt-4">
                                <h6>Product Features</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> Fresh flowers sourced daily</li>
                                    <li><i class="fas fa-check text-success"></i> Handcrafted arrangements</li>
                                    <li><i class="fas fa-check text-success"></i> Same-day delivery available</li>
                                    <li><i class="fas fa-check text-success"></i> 100% satisfaction guarantee</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Products -->
                <?php if (!empty($related_products)): ?>
                    <div class="mt-5">
                        <h3 class="mb-4">Related Products</h3>
                        <div class="row">
                            <?php foreach ($related_products as $related): ?>
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <?php if ($related['image']): ?>
                                                <img src="uploads/<?php echo $related['image']; ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/300x200/f0f0f0/666666?text=Product" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-info">
                                            <h6><?php echo htmlspecialchars($related['name']); ?></h6>
                                            <p class="text-muted small"><?php echo htmlspecialchars(substr($related['description'], 0, 50)); ?>...</p>
                                            <div class="product-price">
                                                <span class="current-price">$<?php echo number_format($related['price'], 2); ?></span>
                                            </div>
                                            <div class="product-actions">
                                                <a href="product_simple.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-custom">View Details</a>
                                                <?php if (isset($_SESSION['user_id'])): ?>
                                                    <a href="cart_simple.php?add=<?php echo $related['id']; ?>" class="btn btn-sm btn-primary-custom">Add to Cart</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Product Not Found -->
                <div class="text-center py-5">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h4>Product Not Found</h4>
                    <p class="text-muted">The product you're looking for doesn't exist or has been removed.</p>
                    <a href="products_simple.php" class="btn btn-primary-custom">Browse All Products</a>
                </div>
            <?php endif; ?>
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
