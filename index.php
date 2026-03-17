<?php
// Redirect to the working home page
header("Location: index_simple.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
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
                                <?php foreach ($categories as $category): ?>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Fresh Flowers & Beautiful Cards</h1>
            <p class="hero-subtitle">Express your emotions with our stunning collection of flowers and greeting cards</p>
            <a href="products.php" class="btn btn-primary-custom btn-lg">Shop Now</a>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Shop by Category</h2>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-3 mb-4">
                        <a href="category.php?id=<?php echo $category['id']; ?>" class="text-decoration-none">
                            <div class="category-card">
                                <div class="category-icon">
                                    <?php
                                    $icons = ['🌹', '💐', '💌', '🎁'];
                                    echo $icons[array_rand($icons)];
                                    ?>
                                </div>
                                <h5><?php echo $category['name']; ?></h5>
                                <p class="text-muted small"><?php echo $category['description']; ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Featured Products</h2>
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="product-card">
                            <img src="<?php echo $product['image'] ? $product['image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                 alt="<?php echo $product['name']; ?>" class="product-image">
                            <div class="product-body">
                                <h5 class="product-title"><?php echo $product['name']; ?></h5>
                                <p class="text-muted small"><?php echo $product['category_name']; ?></p>
                                <div class="product-price"><?php echo format_price($product['price']); ?></div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary-custom w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="products.php" class="btn btn-outline-primary">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Latest Products Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Latest Arrivals</h2>
            <div class="row">
                <?php foreach ($latest_products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="product-card">
                            <img src="<?php echo $product['image'] ? $product['image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                 alt="<?php echo $product['name']; ?>" class="product-image">
                            <div class="product-body">
                                <h5 class="product-title"><?php echo $product['name']; ?></h5>
                                <p class="text-muted small"><?php echo $product['category_name']; ?></p>
                                <div class="product-price"><?php echo format_price($product['price']); ?></div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary-custom w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-truck fa-3x text-primary"></i>
                    </div>
                    <h4>Free Delivery</h4>
                    <p class="text-muted">Free delivery on orders above ₹500</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-primary"></i>
                    </div>
                    <h4>Same Day Delivery</h4>
                    <p class="text-muted">Order before 2 PM for same day delivery</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-leaf fa-3x text-primary"></i>
                    </div>
                    <h4>Fresh Flowers</h4>
                    <p class="text-muted">100% fresh and high-quality flowers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>🌸 <?php echo SITE_NAME; ?></h5>
                    <p>Your trusted partner for beautiful flowers and greeting cards for every occasion.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
                        <li><a href="terms.php" class="text-white text-decoration-none">Terms & Conditions</a></li>
                        <li><a href="privacy.php" class="text-white text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-envelope"></i> <?php echo ADMIN_EMAIL; ?></p>
                    <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                    <p><i class="fas fa-map-marker-alt"></i> Delhi, India</p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
