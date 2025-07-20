<?php
/**
 * Database Setup Script
 * This script will create the database and tables for the ticket system
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';  // Default XAMPP password is empty

echo "<h2>Database Setup Script</h2>";
echo "<p>This script will create the database and tables for the Support Ticket System.</p>";

try {
    // Connect to MySQL server
    $connection = new mysqli($host, $username, $password);
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<p style='color: green;'>✓ Connected to MySQL server</p>";
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS ticket_system";
    if ($connection->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Database 'ticket_system' created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating database: " . $connection->error . "</p>";
    }
    
    // Select the database
    $connection->select_db('ticket_system');
    
    // Create employees table
    $sql = "CREATE TABLE IF NOT EXISTS employees (
        employee_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        specialization SET('Feature Request', 'Sales', 'Usage Guide', 'Bugs and Technical Issues', 'General') NOT NULL,
        is_admin BOOLEAN DEFAULT FALSE,
        workload INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($connection->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'employees' created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating employees table: " . $connection->error . "</p>";
    }
    
    // Create tickets table
    $sql = "CREATE TABLE IF NOT EXISTS tickets (
        ticket_id VARCHAR(20) PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        topic ENUM('Feature Request', 'Sales', 'Usage Guide', 'Bugs and Technical Issues', 'General') NOT NULL,
        description TEXT,
        status ENUM('Open', 'In Progress', 'Closed') DEFAULT 'Open',
        assigned_to INT NULL,
        created_by INT NULL,
        priority ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (assigned_to) REFERENCES employees(employee_id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES employees(employee_id) ON DELETE SET NULL
    )";
    
    if ($connection->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'tickets' created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating tickets table: " . $connection->error . "</p>";
    }
    
    // Create ticket_comments table
    $sql = "CREATE TABLE IF NOT EXISTS ticket_comments (
        comment_id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id VARCHAR(20) NOT NULL,
        employee_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES tickets(ticket_id) ON DELETE CASCADE,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
    )";
    
    if ($connection->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'ticket_comments' created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating ticket_comments table: " . $connection->error . "</p>";
    }
    
    // Check if sample data already exists
    $result = $connection->query("SELECT COUNT(*) as count FROM employees");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "<br><h3>Inserting Sample Data...</h3>";
        
        // Insert sample employees
        $employees = [
            ['Jordon Dale', 'jordon.dale@company.com', 'Feature Request', 0],
            ['Gabriel Stanford', 'gabriel.stanford@company.com', 'Sales,Usage Guide', 0],
            ['Riordan Tony', 'riordan.tony@company.com', 'Sales', 0],
            ['Admin User', 'admin@company.com', 'Feature Request,Sales,Usage Guide,Bugs and Technical Issues,General', 1]
        ];
        
        foreach ($employees as $emp) {
            $hashed_password = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $connection->prepare("INSERT INTO employees (name, email, password, specialization, is_admin) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $emp[0], $emp[1], $hashed_password, $emp[2], $emp[3]);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Added employee: {$emp[0]}</p>";
            } else {
                echo "<p style='color: red;'>✗ Error adding employee {$emp[0]}: " . $connection->error . "</p>";
            }
        }
        
        // Insert sample tickets
        $tickets = [
            ['HOW-UG-01234', 'How to do something', 'Usage Guide', 'User needs guidance on how to perform a specific task', 'Open', NULL],
            ['ASSIS-GE-11111', 'Assistance needed', 'General', 'General assistance request', 'Open', NULL],
            ['THIS-BT-42512', 'This button doesn\'t work', 'Bugs and Technical Issues', 'User reporting a non-functional button', 'Open', NULL],
            ['PRICE-SL-21222', 'Price inquiry', 'Sales', 'Customer asking about pricing', 'Closed', 3],
            ['HOW-UG-12412', 'How to do this other thing', 'Usage Guide', 'Another usage guide request', 'In Progress', 2],
            ['COLLA-FR-50123', 'Collaboration online', 'Feature Request', 'Request for new collaboration features', 'In Progress', 4],
            ['REFUN-SL-19091', 'Refund', 'Sales', 'Customer requesting refund', 'Open', NULL],
            ['HELP-BT-12412', 'Help ERROR CODE 214', 'Bugs and Technical Issues', 'System error code 214', 'Open', NULL]
        ];
        
        foreach ($tickets as $ticket) {
            $stmt = $connection->prepare("INSERT INTO tickets (ticket_id, name, topic, description, status, assigned_to) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $ticket[0], $ticket[1], $ticket[2], $ticket[3], $ticket[4], $ticket[5]);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Added ticket: {$ticket[0]}</p>";
            } else {
                echo "<p style='color: red;'>✗ Error adding ticket {$ticket[0]}: " . $connection->error . "</p>";
            }
        }
    } else {
        echo "<p style='color: orange;'>⚠ Sample data already exists (found {$row['count']} employees)</p>";
    }
    
    echo "<br><h3 style='color: green;'>✓ Database setup completed successfully!</h3>";
    echo "<p><strong>You can now:</strong></p>";
    echo "<ul>";
    echo "<li><a href='login.php'>Login to the system</a></li>";
    echo "<li><a href='signup.php'>Create a new account</a></li>";
    echo "<li><a href='test_connection.php'>Test database connection</a></li>";
    echo "</ul>";
    
    echo "<br><h3>Default Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@company.com / password</li>";
    echo "<li><strong>User:</strong> jordon.dale@company.com / password</li>";
    echo "</ul>";
    
    $connection->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    line-height: 1.6;
    background-color: #f8f9fa;
}
h2, h3 {
    color: #333;
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
ul {
    margin: 10px 0;
}
</style>
