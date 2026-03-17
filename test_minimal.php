<?php
echo "<h2>Minimal Admin Test</h2>";

// Most basic connection test
try {
    $conn = new mysqli('localhost', 'root', '', 'flower_shop');
    
    if ($conn->connect_error) {
        die("<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>");
    }
    
    echo "<p style='color: green;'>✅ MySQLi connection successful</p>";
    
    // Create admin table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Admin table ready</p>";
    } else {
        echo "<p style='color: red;'>❌ Table creation failed</p>";
    }
    
    // Clear existing admins
    $conn->query("DELETE FROM admin");
    
    // Create admin with simple password
    $username = 'admin';
    $password = 'admin123';
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed);
    $email = 'admin@flowershop.com';
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Admin created with MySQLi</p>";
        
        // Test login
        $result = $conn->query("SELECT * FROM admin WHERE username = 'admin'");
        $admin = $result->fetch_assoc();
        
        if ($admin && password_verify('admin123', $admin['password'])) {
            echo "<p style='color: green;'>✅ Login test PASSED!</p>";
            
            // Start session and login
            session_start();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            
            echo "<p style='color: blue;'>🎉 You are now logged in!</p>";
            echo "<p><a href='admin/dashboard.php' style='font-weight: bold;'>Go to Admin Dashboard</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Login test failed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Admin creation failed</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Login Credentials:</strong></p>";
echo "<p>Username: admin</p>";
echo "<p>Password: admin123</p>";
echo "<hr>";
echo "<a href='admin/login_simple.php'>Try Simple Login</a> | ";
echo "<a href='setup_admin.php'>Run Setup Script</a>";
?>
