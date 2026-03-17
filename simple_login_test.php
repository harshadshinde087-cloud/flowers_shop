<?php
session_start();

// Simple database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'flower_shop';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Direct database connection successful</p>";
} catch(PDOException $e) {
    die("<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>");
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        
        echo "<p style='color: green;'>✅ Login successful! Redirecting to dashboard...</p>";
        echo "<script>setTimeout(() => window.location.href='admin/dashboard.php', 2000);</script>";
    } else {
        echo "<p style='color: red;'>❌ Login failed! Invalid username or password</p>";
    }
}

// Check if admin exists
$stmt = $pdo->query("SELECT COUNT(*) as count FROM admin");
$result = $stmt->fetch();
echo "<p>Admin count: " . $result['count'] . "</p>";

if ($result['count'] == 0) {
    echo "<p style='color: orange;'>Creating admin account...</p>";
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin@flowershop.com', $password]);
    echo "<p style='color: green;'>✅ Admin account created!</p>";
} else {
    $stmt = $pdo->query("SELECT * FROM admin LIMIT 1");
    $admin = $stmt->fetch();
    echo "<p>Admin username: " . $admin['username'] . "</p>";
    
    if (password_verify('admin123', $admin['password'])) {
        echo "<p style='color: green;'>✅ Password verification works!</p>";
    } else {
        echo "<p style='color: red;'>❌ Password verification failed!</p>";
        echo "<p>Updating password...</p>";
        $new_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $admin['id']]);
        echo "<p style='color: green;'>✅ Password updated!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        input { padding: 8px; width: 200px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>Simple Admin Login Test</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Username:</label><br>
            <input type="text" name="username" value="admin" required>
        </div>
        <div class="form-group">
            <label>Password:</label><br>
            <input type="password" name="password" value="admin123" required>
        </div>
        <div class="form-group">
            <button type="submit">Login</button>
        </div>
    </form>
    
    <hr>
    <p><strong>Default credentials:</strong></p>
    <p>Username: admin</p>
    <p>Password: admin123</p>
    
    <hr>
    <a href="admin/login.php">Try Original Login</a> | 
    <a href="index.php">Main Website</a>
</body>
</html>
