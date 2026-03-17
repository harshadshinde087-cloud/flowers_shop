<?php
echo "<h2>Database Connection Test</h2>";

// Test 1: Direct constants definition
echo "<h3>Test 1: Define Constants First</h3>";
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'flower_shop');

echo "<p>✅ Constants defined</p>";

// Test 2: Include config
echo "<h3>Test 2: Include Config</h3>";
try {
    require_once 'includes/config.php';
    echo "<p style='color: green;'>✅ Config included successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config failed: " . $e->getMessage() . "</p>";
}

// Test 3: Test database connection
echo "<h3>Test 3: Database Connection</h3>";
if (isset($db) && $db->conn) {
    echo "<p style='color: green;'>✅ Database object created</p>";
    
    try {
        $stmt = $db->query("SELECT 1 as test");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p style='color: green;'>✅ Database query successful: " . $result['test'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Query failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Database object not created</p>";
}

// Test 4: Test categories page
echo "<h3>Test 4: Categories Page</h3>";
echo "<p><a href='admin/categories.php'>Test Categories Page</a></p>";

echo "<hr>";
echo "<h3>Connection Status:</h3>";
echo "<p><strong>MySQL Service:</strong> Make sure MySQL is running in XAMPP</p>";
echo "<p><strong>Database:</strong> flower_shop</p>";
echo "<p><strong>Host:</strong> localhost</p>";
echo "<p><strong>User:</strong> root</p>";

echo "<hr>";
echo "<a href='admin/login.php'>Admin Login</a> | ";
echo "<a href='index.php'>Main Website</a>";
?>
