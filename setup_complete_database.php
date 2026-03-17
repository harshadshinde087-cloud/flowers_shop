<?php
echo "<h2>Complete Database Setup</h2>";

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'flower_shop';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connected to MySQL</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database ready</p>";
    
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Create tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            category_id INT,
            stock_quantity INT DEFAULT 0,
            image VARCHAR(255),
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            order_number VARCHAR(50) NOT NULL UNIQUE,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
            shipping_address TEXT,
            payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT,
            product_id INT,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            product_id INT,
            quantity INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (product_id) REFERENCES products(id),
            UNIQUE KEY unique_cart_item (user_id, product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    echo "<p style='color: green;'>✅ All tables created</p>";
    
    // Insert default data
    $pdo->exec("DELETE FROM admin");
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin@flowershop.com', $password]);
    echo "<p style='color: green;'>✅ Admin account created</p>";
    
    // Insert categories
    $pdo->exec("DELETE FROM categories");
    $categories = [
        ['Flowers', 'Fresh and beautiful flowers for every occasion'],
        ['Bouquets', 'Artistically arranged flower bouquets'],
        ['Greeting Cards', 'Beautiful greeting cards for all occasions'],
        ['Combos', 'Special flower and card combinations']
    ];
    
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute($cat);
    }
    echo "<p style='color: green;'>✅ Categories added</p>";
    
    // Insert sample products
    $pdo->exec("DELETE FROM products");
    $products = [
        ['Red Rose Bouquet', 'Beautiful arrangement of fresh red roses perfect for expressing love', 45.99, 2, 50, 'red_rose_bouquet.jpg', 1],
        ['White Lily Flowers', 'Elegant white lilies that bring peace and serenity', 35.99, 1, 30, 'white_lily.jpg', 0],
        ['Birthday Greeting Card', 'Colorful birthday card with heartfelt message', 5.99, 3, 100, 'birthday_card.jpg', 0],
        ['Anniversary Combo', 'Special combo of flowers and card for anniversary celebrations', 55.99, 4, 25, 'anniversary_combo.jpg', 1],
        ['Pink Carnations', 'Soft pink carnations that symbolize gratitude and love', 28.99, 1, 40, 'pink_carnations.jpg', 0],
        ['Thank You Card', 'Elegant thank you card for expressing gratitude', 4.99, 3, 80, 'thank_you_card.jpg', 0],
        ['Mixed Flower Bouquet', 'Vibrant mix of seasonal flowers in beautiful arrangement', 52.99, 2, 35, 'mixed_bouquet.jpg', 1],
        ['Get Well Soon Card', 'Cheerful get well soon card to brighten someone\'s day', 6.99, 3, 60, 'get_well_card.jpg', 0],
        ['Sunflower Bundle', 'Bright and cheerful sunflowers that bring happiness', 32.99, 1, 45, 'sunflower_bundle.jpg', 0],
        ['Wedding Combo', 'Premium wedding combo with exotic flowers and card', 89.99, 4, 15, 'wedding_combo.jpg', 1]
    ];
    
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($product);
    }
    echo "<p style='color: green;'>✅ Sample products added</p>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>🎉 Database Setup Complete!</h3>";
    echo "<p><strong>What was created:</strong></p>";
    echo "<ul>";
    echo "<li>✅ All required tables</li>";
    echo "<li>✅ Admin account (admin/admin123)</li>";
    echo "<li>✅ 4 Categories</li>";
    echo "<li>✅ 10 Sample products</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<p>1. <a href='admin/login.php' style='color: blue; font-weight: bold;'>Go to Admin Panel</a></p>";
    echo "<p>2. <a href='index.php' style='color: blue; font-weight: bold;'>Go to Main Website</a></p>";
    echo "<p>3. <a href='admin/dashboard.php' style='color: blue; font-weight: bold;'>Go to Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='admin/login.php'>Admin Login</a> | ";
echo "<a href='index.php'>Main Website</a>";
?>
