<?php
require_once 'includes/config.php';

echo "<h2>Reset Admin Account</h2>";

try {
    // Delete existing admin if exists
    $stmt = $db->query("DELETE FROM admin WHERE username = 'admin'");
    
    // Create new admin with password 'admin123'
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->query("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin@flowershop.com', $password]);
    
    echo "<p style='color: green;'>✅ Admin account reset successfully!</p>";
    echo "<p><strong>Login Details:</strong></p>";
    echo "<p>Username: admin</p>";
    echo "<p>Password: admin123</p>";
    
    // Test the login
    $stmt = $db->query("SELECT * FROM admin WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify('admin123', $admin['password'])) {
        echo "<p style='color: green;'>✅ Login test successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Login test failed!</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='admin/login.php'>Go to Admin Login</a> | ";
echo "<a href='index.php'>Go to Main Website</a>";
?>
