<?php
// Standalone products page - no dependencies
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

// Get products with filters
$products = [];
$categories = [];
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort = $_GET['sort'] ?? 'name';

try {
    // Get categories
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
    // Build query
    $where_clause = "WHERE p.stock_quantity > 0";
    $params = [];
    
    if ($category_id > 0) {
        $where_clause .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    // Sort options
    $sort_options = [
        'name' => 'p.name ASC',
        'price_low' => 'p.price ASC',
        'price_high' => 'p.price DESC',
        'newest' => 'p.created_at DESC',
        'featured' => 'p.is_featured DESC, p.name ASC'
    ];
    
    $order_by = $sort_options[$sort] ?? 'p.name ASC';
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            $where_clause 
            ORDER BY $order_by";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching products";
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
    <title>Products - Flower Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
                            <a class="nav-link active" href="products_simple.php">Products</a>
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

    <!-- Products Section -->
    <section class="products py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h1>Our Products</h1>
                    <p class="text-muted">Browse our beautiful collection of flowers and greeting cards</p>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end">
                        <select class="form-select" id="sortSelect" onchange="window.location.href='?category=<?php echo $category_id; ?>&sort='+this.value">
                            <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                            <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="featured" <?php echo $sort == 'featured' ? 'selected' : ''; ?>>Featured</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-md-3">
                    <div class="filters-card">
                        <h5>Categories</h5>
                        <div class="list-group">
                            <a href="products_simple.php" class="list-group-item <?php echo $category_id == 0 ? 'active' : ''; ?>">
                                All Categories
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="products_simple.php?category=<?php echo $category['id']; ?>" 
                                   class="list-group-item <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="col-md-9">
                    <div class="row">
                        <?php if (empty($products)): ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h4>No products found</h4>
                                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 col-sm-6 mb-4">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <?php if ($product['image']): ?>
                                                <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/300x300/f0f0f0/666666?text=Product" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <?php endif; ?>
                                            <?php if ($product['stock_quantity'] < 10): ?>
                                                <span class="stock-badge">Low Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-info">
                                            <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <div class="product-price">
                                                <span class="current-price">₹<?php echo number_format($product['price'], 2); ?></span>
                                            </div>
                                            <div class="product-actions">
                                                <a href="product_simple.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-custom">View</a>
                                                <?php if (isset($_SESSION['user_id'])): ?>
                                                    <a href="cart_simple.php?add=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary-custom">Add</a>
                                                <?php else: ?>
                                                    <a href="login_simple.php" class="btn btn-sm btn-primary-custom">Login</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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
