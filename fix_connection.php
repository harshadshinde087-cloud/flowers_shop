<?php
echo "<h2>Database Connection Diagnostic Tool</h2>";

// Test different connection methods
echo "<h3>Step 1: Check XAMPP Services</h3>";
echo "<p><strong>Before continuing, make sure:</strong></p>";
echo "<ul>";
echo "<li>XAMPP Control Panel is open</li>";
echo "<li>Apache service is running (green)</li>";
echo "<li>MySQL service is running (green)</li>";
echo "<li>No other services are using port 3306</li>";
echo "</ul>";

echo "<h3>Step 2: Test Connection Methods</h3>";

// Test 1: Basic MySQL connection
echo "<h4>Test 1: Basic MySQL Connection</h4>";
try {
    $conn = new mysqli('localhost', 'root', '', 'flower_shop');
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ MySQLi failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ MySQLi connection successful!</p>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ MySQLi exception: " . $e->getMessage() . "</p>";
}

// Test 2: PDO connection
echo "<h4>Test 2: PDO Connection</h4>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=flower_shop", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ PDO connection successful!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ PDO failed: " . $e->getMessage() . "</p>";
}

// Test 3: Connection to MySQL server only
echo "<h4>Test 3: MySQL Server Connection (no database)</h4>";
try {
    $pdo = new PDO("mysql:host=localhost", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ MySQL server connection successful!</p>";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS flower_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database 'flower_shop' created/verified!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ MySQL server connection failed: " . $e->getMessage() . "</p>";
}

// Test 4: Check if MySQL is running on different ports
echo "<h4>Test 4: Check Different Ports</h4>";
$ports = [3306, 3307, 3308];
foreach ($ports as $port) {
    try {
        $pdo = new PDO("mysql:host=localhost;port=$port", 'root', '');
        echo "<p style='color: green;'>✅ MySQL running on port $port</p>";
        $pdo = null;
        break;
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠️ Port $port: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Step 3: Solutions</h3>";
echo "<div class='solutions'>";
echo "<h4>🔧 Quick Fixes:</h4>";
echo "<ol>";
echo "<li><strong>Restart XAMPP:</strong> Stop both Apache and MySQL, then start MySQL first, then Apache</li>";
echo "<li><strong>Check Port 3306:</strong> Make sure no other program is using port 3306</li>";
echo "<li><strong>Firewall:</strong> Temporarily disable Windows Firewall</li>";
echo "<li><strong>Antivirus:</strong> Temporarily disable antivirus software</li>";
echo "<li><strong>Run as Administrator:</strong> Run XAMPP Control Panel as Administrator</li>";
echo "</ol>";

echo "<h4>🔄 Alternative Solutions:</h4>";
echo "<ol>";
echo "<li><strong>Use MariaDB instead:</strong> Some XAMPP versions use MariaDB instead of MySQL</li>";
echo "<li><strong>Change MySQL Port:</strong> Configure MySQL to use a different port</li>";
echo "<li><strong>Reinstall XAMPP:</strong> As last resort, reinstall XAMPP</li>";
echo "</ol>";

echo "<h4>📝 Configuration Check:</h4>";
echo "<p>Check your XAMPP MySQL configuration:</p>";
echo "<ul>";
echo "<li>Go to XAMPP Control Panel → MySQL → Config → my.ini</li>";
echo "<li>Check that port = 3306</li>";
echo "<li>Check that bind-address = 127.0.0.1 or localhost</li>";
echo "</ul>";
echo "</div>";

echo "<h3>Step 4: Test After Fix</h3>";
echo "<p>After trying the fixes above, test your connection:</p>";
echo "<ul>";
echo "<li><a href='test_db.php'>Test Database Connection</a></li>";
echo "<li><a href='admin/login.php'>Test Admin Login</a></li>";
echo "<li><a href='index.php'>Test Main Website</a></li>";
echo "</ul>";

echo "<style>";
echo ".solutions { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }";
echo "h4 { color: #007bff; margin-top: 15px; }";
echo "ol, ul { line-height: 1.6; }";
echo "</style>";
?>
