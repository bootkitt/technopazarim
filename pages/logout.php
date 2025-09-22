<?php
require_once __DIR__ . '/../config.php';

// Log security event
if (isset($_SESSION['user_id'])) {
    logSecurityEvent($_SESSION['user_id'], 'logout', 'User logged out', $db);
}

// Save cart data before destroying session
$cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Destroy all session data
session_destroy();

// Start a new session and restore cart data
session_start();
if (!empty($cartData)) {
    $_SESSION['cart'] = $cartData;
}

// Redirect to home page
header('Location: index.php');
exit;