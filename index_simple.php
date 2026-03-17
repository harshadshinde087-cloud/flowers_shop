<?php
// Standalone home page - no dependencies
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

// Get featured products
$featured_products = [];
$categories = [];
$latest_products = [];

try {
    // Get featured products
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 AND p.stock_quantity > 0 ORDER BY p.created_at DESC LIMIT 8");
    $featured_products = $stmt->fetchAll();
    
    // Get categories
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 8");
    $categories = $stmt->fetchAll();
    
    // Get latest products
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.stock_quantity > 0 ORDER BY p.created_at DESC LIMIT 8");
    $latest_products = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching data";
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
    <title>Online Flower & Greeting Delivery System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
<style>
        .hero-content {
            text-align: center;
            padding: 2rem;
        }
        .hero-image {
            text-align: center;
            padding: 2rem;
        }
        .hero-buttons {
            justify-content: center;
        }
        .product-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        .product-image-center {
            text-align: center;
            padding: 15px 15px 10px 15px;
        }
        .product-image-center img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
        }
        .product-info-center {
            text-align: center;
            padding: 10px 15px 15px 15px;
        }
        .product-info-middle {
            padding: 15px 15px 8px 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .product-info-right {
            padding: 15px 15px 8px 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
            text-align: right;
        }
        .product-actions {
            margin-top: 10px;
        }
        .product-actions .btn {
            margin: 2px;
            padding: 4px 8px;
            font-size: 11px;
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content text-center">
                        <h1>Fresh Flowers & Beautiful Greetings</h1>
                        <p>Express your emotions with our beautiful collection of fresh flowers and greeting cards. Perfect for every occasion!</p>
                        <div class="hero-buttons d-flex justify-content-center gap-2">
                            <a href="products_simple.php" class="btn btn-primary-custom">Shop Now</a>
                            <a href="#categories" class="btn btn-outline-custom">View Categories</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image text-center">
                        <img src="https://via.placeholder.com/600x400/ff6b9d/ffffff?text=Beautiful+Flowers" alt="Flowers" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="categories py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Shop by Category</h2>
                <p class="text-muted">Find the perfect flowers for your special occasion</p>
            </div>
            
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="category-card">
                            <div class="category-icon">
                                <?php
                                $icons = ['🌹', '💐', '🌺', '🌻', '🌷', '🌸', '🌼', '🌵'];
                                $icon = $icons[array_rand($icons)];
                                echo $icon;
                                ?>
                            </div>
                            <h5><?php echo htmlspecialchars($category['name']); ?></h5>
                            <p><?php echo htmlspecialchars(substr($category['description'], 0, 50)); ?>...</p>
                            <a href="category_simple.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-custom">View Products</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Featured Products</h2>
                <p class="text-muted">Handpicked favorites for you</p>
            </div>
            
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="product-card">
                            <div class="row g-0">
                                <div class="col-12">
                                    <div class="product-image-center">
                                        <?php if ($product['image']): ?>
                                            <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/300x200/f0f0f0/666666?text=Product" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="product-info-center">
                                        <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <div class="product-price">
                                            <span class="current-price">₹<?php echo number_format($product['price'], 2); ?></span>
                                        </div>
                                        <div class="product-actions">
                                            <a href="product_simple.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-custom">View</a>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <a href="cart_simple.php?add=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary-custom">Add</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Latest Products -->
    <section class="latest-products py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Latest Arrivals</h2>
                <p class="text-muted">Fresh additions to our collection</p>
            </div>
            
            <div class="row">
                <?php foreach ($latest_products as $product): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="product-card">
                            <div class="row g-0">
                                <div class="col-12">
                                    <div class="product-image-center">
                                        <?php if ($product['image']): ?>
                                            <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/300x200/f0f0f0/666666?text=Product" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="product-info-center">
                                        <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <div class="product-price">
                                            <span class="current-price">₹<?php echo number_format($product['price'], 2); ?></span>
                                        </div>
                                        <div class="product-actions">
                                            <a href="product_simple.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-custom">View</a>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <a href="cart_simple.php?add=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary-custom">Add</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
