<?php
echo "<h2>Force Admin Account Setup</h2>";

// Direct database setup
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'flower_shop';

try {
    // Connect to MySQL first (without database)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connected to MySQL server</p>";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database '$dbname' ready</p>";
    
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Create admin table if not exists
    $create_table = "
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($create_table);
    echo "<p style='color: green;'>✅ Admin table ready</p>";
    
    // Delete any existing admin accounts
    $pdo->exec("DELETE FROM admin");
    echo "<p style='color: orange;'>🔄 Cleared existing admin accounts</p>";
    
    // Create new admin account
    $username = 'admin';
    $email = 'admin@flowershop.com';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password]);
    
    echo "<p style='color: green;'>✅ Admin account created successfully!</p>";
    
    // Verify the account
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✅ Admin account verified in database</p>";
        echo "<p><strong>Login Details:</strong></p>";
        echo "<p>Username: $username</p>";
        echo "<p>Password: $password</p>";
        
        // Test password verification
        if (password_verify($password, $admin['password'])) {
            echo "<p style='color: green;'>✅ Password verification test PASSED!</p>";
        } else {
            echo "<p style='color: red;'>❌ Password verification test FAILED!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to verify admin account</p>";
    }
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<p>1. <a href='admin/login_simple.php' style='color: blue; font-weight: bold;'>Try Login Now</a></p>";
    echo "<p>2. Use: admin / admin123</p>";
    echo "<p>3. If still fails, check your XAMPP MySQL service</p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>Database name is correct: '$dbname'</li>";
    echo "<li>MySQL credentials are correct</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<a href='admin/login_simple.php'>Go to Simple Login</a> | ";
echo "<a href='index.php'>Main Website</a>";
?>
