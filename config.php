<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ticket_system');

// Application configuration
define('APP_NAME', 'Support Ticket Management System');
define('BASE_URL', 'http://localhost/Ticket_System/');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

session_start();

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['employee_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit();
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateTicketId($topic) {
    $prefixes = [
        'Feature Request' => 'FR',
        'Sales' => 'SL',
        'Usage Guide' => 'UG',
        'Bugs and Technical Issues' => 'BT',
        'General' => 'GE'
    ];
    
    $prefix = isset($prefixes[$topic]) ? $prefixes[$topic] : 'GE';
    $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    
    return strtoupper(substr($topic, 0, 4)) . '-' . $prefix . '-' . $random;
}

function getSpecializationArray($specialization_string) {
    return explode(',', $specialization_string);
}

function formatSpecialization($specialization_string) {
    $specs = explode(',', $specialization_string);
    return implode(', ', $specs);
}
?>
