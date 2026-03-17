<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Admin Login Issue</h2>";

// Check database connection first
try {
    require_once 'includes/config.php';
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check if admin table exists
try {
    $stmt = $db->query("SHOW TABLES LIKE 'admin'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<p style='color: green;'>✅ Admin table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Admin table does not exist</p>";
        echo "<p>Creating admin table...</p>";
        
        $create_table_sql = "
        CREATE TABLE admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $db->query($create_table_sql);
        echo "<p style='color: green;'>✅ Admin table created</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Table check error: " . $e->getMessage() . "</p>";
}

// Check admin records
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM admin");
    $result = $stmt->fetch();
    $admin_count = $result['count'];
    
    echo "<p>Admin records found: $admin_count</p>";
    
    if ($admin_count > 0) {
        $stmt = $db->query("SELECT * FROM admin");
        $admins = $stmt->fetchAll();
        
        foreach ($admins as $admin) {
            echo "<p>Admin found: " . $admin['username'] . " | " . $admin['email'] . "</p>";
            
            // Test password verification
            $test_password = 'admin123';
            if (password_verify($test_password, $admin['password'])) {
                echo "<p style='color: green;'>✅ Password 'admin123' works for " . $admin['username'] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Password 'admin123' does NOT work for " . $admin['username'] . "</p>";
                
                // Update password
                $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
                $update_stmt = $db->query("UPDATE admin SET password = ? WHERE id = ?");
                $update_stmt->execute([$new_hash, $admin['id']]);
                echo "<p style='color: orange;'>🔄 Updated password for " . $admin['username'] . "</p>";
                
                // Test again
                if (password_verify('admin123', $new_hash)) {
                    echo "<p style='color: green;'>✅ Password verification now works!</p>";
                }
            }
        }
    } else {
        echo "<p style='color: red;'>❌ No admin records found. Creating admin...</p>";
        
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->query("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['admin', 'admin@flowershop.com', $password]);
        
        echo "<p style='color: green;'>✅ Admin account created</p>";
        echo "<p>Username: admin</p>";
        echo "<p>Password: admin123</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Admin check error: " . $e->getMessage() . "</p>";
}

// Test login process manually
echo "<hr><h3>Manual Login Test</h3>";
try {
    $username = 'admin';
    $password = 'admin123';
    
    $stmt = $db->query("SELECT * FROM admin WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>✅ Admin found in database</p>";
        echo "<p>Username: " . $admin['username'] . "</p>";
        echo "<p>Email: " . $admin['email'] . "</p>";
        
        if (password_verify($password, $admin['password'])) {
            echo "<p style='color: green;'>✅ Manual login test SUCCESSFUL!</p>";
        } else {
            echo "<p style='color: red;'>❌ Manual login test FAILED!</p>";
            echo "<p>Password provided: admin123</p>";
            echo "<p>Password hash in DB: " . $admin['password'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Admin not found with username: $username</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Manual test error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='admin/login.php'>Try Admin Login</a> | ";
echo "<a href='index.php'>Main Website</a>";
?>
