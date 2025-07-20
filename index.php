<?php
require_once 'config.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Redirect to dashboard if logged in
redirect('dashboard.php');
?>
