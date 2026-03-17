<?php
// Test database connection
require_once 'includes/config.php';

echo "<h2>Database Connection Test</h2>";

if (isset($db) && $db->conn) {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    try {
        // Test query
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>✅ Database query successful!</p>";
        echo "<p>Products in database: " . $result['count'] . "</p>";
        
        // Test categories
        $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>Categories in database: " . $result['count'] . "</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color: red;'>❌ Query error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
}

echo "<hr>";
echo "<h3>Configuration Check:</h3>";
echo "<p>DB_HOST: " . DB_HOST . "</p>";
echo "<p>DB_NAME: " . DB_NAME . "</p>";
echo "<p>DB_USER: " . DB_USER . "</p>";
echo "<p>Current working directory: " . getcwd() . "</p>";

echo "<hr>";
echo "<a href='index.php'>Go to Main Website</a> | ";
echo "<a href='admin/login.php'>Go to Admin Panel</a>";
?>
