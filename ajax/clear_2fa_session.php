<?php
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

// Clear 2FA verification for checkout
if (isset($_SESSION['2fa_verified_for_checkout'])) {
    unset($_SESSION['2fa_verified_for_checkout']);
    echo json_encode(['success' => true, 'message' => '2FA session cleared']);
} else {
    echo json_encode(['success' => true, 'message' => 'No 2FA session to clear']);
}