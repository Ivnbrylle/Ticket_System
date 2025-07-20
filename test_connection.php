<?php
/**
 * Database Connection Test File
 * Use this file to test your XAMPP MySQL connection
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';  // Default XAMPP password is empty
$database = 'ticket_system';

echo "<h2>XAMPP Database Connection Test</h2>";
echo "<p><strong>Testing connection to MySQL...</strong></p>";

try {
    // Test basic MySQL connection
    echo "1. Testing MySQL connection...<br>";
    $connection = new mysqli($host, $username, $password);
    
    if ($connection->connect_error) {
        die("<span style='color: red;'>✗ Connection failed: " . $connection->connect_error . "</span><br>");
    }
    echo "<span style='color: green;'>✓ MySQL connection successful!</span><br><br>";
    
    // Test if database exists
    echo "2. Checking if database 'ticket_system' exists...<br>";
    $result = $connection->query("SHOW DATABASES LIKE 'ticket_system'");
    
    if ($result->num_rows == 0) {
        echo "<span style='color: orange;'>⚠ Database 'ticket_system' not found. Creating it...</span><br>";
        
        // Create database
        if ($connection->query("CREATE DATABASE ticket_system")) {
            echo "<span style='color: green;'>✓ Database 'ticket_system' created successfully!</span><br>";
        } else {
            echo "<span style='color: red;'>✗ Error creating database: " . $connection->error . "</span><br>";
        }
    } else {
        echo "<span style='color: green;'>✓ Database 'ticket_system' exists!</span><br>";
    }
    
    // Connect to the specific database
    echo "<br>3. Connecting to ticket_system database...<br>";
    $connection->select_db($database);
    echo "<span style='color: green;'>✓ Connected to ticket_system database!</span><br><br>";
    
    // Test PDO connection (what the app uses)
    echo "4. Testing PDO connection (used by the application)...<br>";
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span style='color: green;'>✓ PDO connection successful!</span><br><br>";
    
    // Check if tables exist
    echo "5. Checking database tables...<br>";
    $tables = ['employees', 'tickets', 'ticket_comments'];
    
    foreach ($tables as $table) {
        $result = $connection->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<span style='color: green;'>✓ Table '$table' exists</span><br>";
        } else {
            echo "<span style='color: orange;'>⚠ Table '$table' not found</span><br>";
        }
    }
    
    echo "<br><h3>Connection Summary:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> $host</li>";
    echo "<li><strong>Username:</strong> $username</li>";
    echo "<li><strong>Password:</strong> " . (empty($password) ? '(empty - default XAMPP)' : '(set)') . "</li>";
    echo "<li><strong>Database:</strong> $database</li>";
    echo "</ul>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>If tables don't exist, run the SQL from <strong>database.sql</strong> in phpMyAdmin</li>";
    echo "<li>Access phpMyAdmin at: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Once tables are created, you can access the application at: <a href='login.php'>Login Page</a></li>";
    echo "</ol>";
    
    $connection->close();
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Error: " . $e->getMessage() . "</span><br>";
    echo "<br><h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure XAMPP is running</li>";
    echo "<li>Start Apache and MySQL in XAMPP Control Panel</li>";
    echo "<li>Check if MySQL is running on port 3306</li>";
    echo "<li>Verify your database credentials</li>";
    echo "</ul>";
}
?>

<link href="css/test-connection.css" rel="stylesheet">
