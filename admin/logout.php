<?php
require_once __DIR__ . '/../config.php';

if (isAdmin()) {
    // Log admin logout
    logSecurityEvent($_SESSION['user_id'], 'admin_logout', 'Admin logout: ' . $_SESSION['admin_username'], $db);
    logoutAdmin();
}

// Clear session array
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

header('Location: login');
exit;
?>