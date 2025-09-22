<?php
/**
 * Shopier Payment Callback Handler (Legacy)
 * 
 * This script redirects to the new notification handler for consistency.
 */

// Redirect to the new notification handler
header('Location: /pay/shopierNotify.php', true, 301);
exit;

require_once 'config.php';
require_once '../shopier/shopier.php';

// Create Shopier instance
$shopier = new ShopierPayment();

// Verify the payment notification
if ($shopier->verifyPayment($_POST)) {
    // Process successful payment
    if ($shopier->processSuccessfulPayment($_POST)) {
        // Payment processed successfully
        echo 'OK';
        exit;
    } else {
        // Error processing payment
        http_response_code(500);
        echo 'Payment processing failed';
        exit;
    }
} else {
    // Invalid payment notification
    http_response_code(400);
    echo 'Invalid payment notification';
    exit;
}