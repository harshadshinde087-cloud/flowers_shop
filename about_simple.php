<?php
// Standalone about page - no dependencies
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Flower Shop</title>
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
                            <a class="nav-link" href="products_simple.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="about_simple.php">About</a>
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

    <!-- About Section -->
    <section class="about py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h1>About Flower Shop</h1>
                    <p class="lead">Your trusted partner for beautiful flowers and heartfelt greetings since 2020.</p>
                    <p>We are passionate about helping you express your emotions through the beauty of fresh flowers and thoughtful greeting cards. Whether it's a celebration, a gesture of love, or a moment of sympathy, we're here to help you make it special.</p>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/500x400/ff6b9d/ffffff?text=Our+Flower+Shop" alt="About Flower Shop" class="img-fluid rounded">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h4>Fresh Flowers Daily</h4>
                        <p>We source the freshest flowers daily to ensure your arrangements last longer and look beautiful.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Made with Love</h4>
                        <p>Every arrangement is handcrafted with care and attention to detail by our experienced florists.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4>Fast Delivery</h4>
                        <p>Same-day delivery available for orders placed before 2 PM. We ensure your flowers arrive fresh and on time.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Meet Our Team</h2>
                <p class="text-muted">The passionate people behind Flower Shop</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="team-member text-center">
                        <img src="https://via.placeholder.com/200x200/ff6b9d/ffffff?text=Sarah" alt="Sarah Johnson" class="img-fluid rounded-circle mb-3">
                        <h5>Sarah Johnson</h5>
                        <p class="text-muted">Founder & Head Florist</p>
                        <p>With over 15 years of experience, Sarah brings creativity and passion to every arrangement.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="team-member text-center">
                        <img src="https://via.placeholder.com/200x200/ff6b9d/ffffff?text=Mike" alt="Mike Chen" class="img-fluid rounded-circle mb-3">
                        <h5>Mike Chen</h5>
                        <p class="text-muted">Operations Manager</p>
                        <p>Mike ensures smooth operations and timely delivery for all our customers.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="team-member text-center">
                        <img src="https://via.placeholder.com/200x200/ff6b9d/ffffff?text=Emma" alt="Emma Davis" class="img-fluid rounded-circle mb-3">
                        <h5>Emma Davis</h5>
                        <p class="text-muted">Customer Service Lead</p>
                        <p>Emma is dedicated to providing exceptional service and helping customers find the perfect flowers.</p>
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
