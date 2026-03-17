<?php
require_once 'includes/config.php';

echo "<h2>Admin Login Test</h2>";

// Check if admin table exists and has data
try {
    $stmt = $db->query("SELECT * FROM admin");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✅ Admin account found!</p>";
        echo "<p>Username: " . $admin['username'] . "</p>";
        echo "<p>Email: " . $admin['email'] . "</p>";
        echo "<p>Password Hash: " . substr($admin['password'], 0, 20) . "...</p>";
        
        // Test password verification
        $test_password = 'admin123';
        if (password_verify($test_password, $admin['password'])) {
            echo "<p style='color: green;'>✅ Password verification successful!</p>";
        } else {
            echo "<p style='color: red;'>❌ Password verification failed!</p>";
            
            // Create new admin with correct password
            echo "<p>Creating new admin account...</p>";
            $new_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $db->query("UPDATE admin SET password = ? WHERE id = ?");
            $stmt->execute([$new_password, $admin['id']]);
            echo "<p style='color: green;'>✅ New admin account created!</p>";
            echo "<p>Try logging in again with: admin / admin123</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No admin account found!</p>";
        
        // Create admin account
        echo "<p>Creating admin account...</p>";
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->query("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['admin', 'admin@flowershop.com', $password]);
        echo "<p style='color: green;'>✅ Admin account created!</p>";
        echo "<p>Login with: admin / admin123</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Manual Admin Creation:</h3>";
echo "<p>If the above doesn't work, you can manually create the admin:</p>";
echo "<code>";
echo "INSERT INTO admin (username, email, password) VALUES ('admin', 'admin@flowershop.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');";
echo "</code>";

echo "<hr>";
echo "<a href='admin/login.php'>Try Login Again</a> | ";
echo "<a href='test_db.php'>Test Database</a>";
?>
