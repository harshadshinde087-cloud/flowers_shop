<?php
require_once 'includes/config.php';

// Get products with filtering
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort_by = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'latest';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = ["p.stock_quantity > 0"];
$params = [];

if ($category_id > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_id;
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Sorting
$order_by = "ORDER BY p.created_at DESC";
switch ($sort_by) {
    case 'price_low':
        $order_by = "ORDER BY p.price ASC";
        break;
    case 'price_high':
        $order_by = "ORDER BY p.price DESC";
        break;
    case 'name':
        $order_by = "ORDER BY p.name ASC";
        break;
    case 'featured':
        $order_by = "ORDER BY p.is_featured DESC, p.created_at DESC";
        break;
}

try {
    // Get total products count
    $count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
    $stmt = $db->query($count_sql);
    $stmt->execute($params);
    $total_products = $stmt->fetch()['total'];
    $total_pages = ceil($total_products / $per_page);
    
    // Get products
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id $where_clause $order_by LIMIT $per_page OFFSET $offset";
    $stmt = $db->query($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get categories for filter
    $stmt = $db->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching products";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - <?php echo SITE_NAME; ?></title>
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
                            <a class="nav-link active" href="products.php">All Products</a>
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

    <!-- Page Header -->
    <section class="py-4 bg-light">
        <div class="container">
            <h1 class="mb-0">All Products</h1>
            <p class="text-muted mb-0">Discover our beautiful collection of flowers and greeting cards</p>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-md-3 mb-4">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <h5 class="mb-3">Filters</h5>
                        
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6>Categories</h6>
                            <div class="list-group">
                                <a href="products.php" class="list-group-item list-group-item-action <?php echo $category_id == 0 ? 'active' : ''; ?>">
                                    All Categories
                                </a>
                                <?php foreach ($categories as $category): ?>
                                    <a href="products.php?category=<?php echo $category['id']; ?>" class="list-group-item list-group-item-action <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                        <?php echo $category['name']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Sort Options -->
                        <div class="mb-4">
                            <h6>Sort By</h6>
                            <select class="form-control" id="sortSelect" onchange="sortProducts()">
                                <option value="latest" <?php echo $sort_by == 'latest' ? 'selected' : ''; ?>>Latest First</option>
                                <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                                <option value="featured" <?php echo $sort_by == 'featured' ? 'selected' : ''; ?>>Featured First</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <p class="mb-0">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php elseif (empty($products)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h4>No products found</h4>
                            <p class="text-muted">Try adjusting your filters or browse all categories</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="product-card">
                                        <img src="<?php echo $product['image'] ? $product['image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                             alt="<?php echo $product['name']; ?>" class="product-image">
                                        <div class="product-body">
                                            <h5 class="product-title"><?php echo $product['name']; ?></h5>
                                            <p class="text-muted small"><?php echo $product['category_name']; ?></p>
                                            <div class="product-price"><?php echo format_price($product['price']); ?></div>
                                            <?php if ($product['is_featured']): ?>
                                                <span class="badge bg-warning mb-2">Featured</span>
                                            <?php endif; ?>
                                            <div class="d-grid gap-2">
                                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary-custom">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort_by; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort_by; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort_by; ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
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

    <script>
        function sortProducts() {
            const sortValue = document.getElementById('sortSelect').value;
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
