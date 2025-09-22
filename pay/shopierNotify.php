<?php
/**
 * Shopier Payment Notification Handler
 * 
 * This script handles payment notifications from Shopier after a successful payment.
 * It verifies the payment and updates the order status accordingly.
 */

// Set content type
header('Content-Type: text/plain; charset=utf-8');

// Log the notification for debugging purposes
$logData = date('Y-m-d H:i:s') . " - Shopier notification received: " . json_encode($_POST) . "\n";
file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);

// Include required files
require_once '../config.php';
require_once '../pay/shopier/shopier.php';

// Create Shopier instance
$shopier = new ShopierPayment();

try {
    // Log the start of verification
    $logData = date('Y-m-d H:i:s') . " - Starting payment verification\n";
    file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);
    
    // Verify the payment notification
    if ($shopier->verifyPayment($_POST)) {
        // Log successful verification
        $logData = date('Y-m-d H:i:s') . " - Payment verification successful\n";
        file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);
        
        // Process successful payment
        if ($shopier->processSuccessfulPayment($_POST)) {
            // Payment processed successfully
            $logData = date('Y-m-d H:i:s') . " - Shopier payment processed successfully for order: " . $_POST['platform_order_id'] . "\n";
            file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);
            echo 'OK';
            exit;
        } else {
            // Error processing payment
            $logData = date('Y-m-d H:i:s') . " - Shopier payment processing failed for order: " . $_POST['platform_order_id'] . "\n";
            file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);
            http_response_code(500);
            echo 'Payment processing failed';
            exit;
        }
    } else {
        // Invalid payment notification
        $logData = date('Y-m-d H:i:s') . " - Invalid Shopier payment notification received\n";
        $logData .= "POST data: " . json_encode($_POST) . "\n";
        
        // Log specific validation failures
        if (!isset($_POST['platform_order_id'])) {
            $logData .= "Missing platform_order_id\n";
        }
        if (!isset($_POST['API_key'])) {
            $logData .= "Missing API_key\n";
        }
        if (!isset($_POST['status'])) {
            $logData .= "Missing status\n";
        }
        if (!isset($_POST['total_order_value'])) {
            $logData .= "Missing total_order_value\n";
        }
        if (!isset($_POST['signature'])) {
            $logData .= "Missing signature\n";
        }
        
        file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);
        http_response_code(400);
        echo 'Invalid payment notification';
        exit;
    }
} catch (Exception $e) {
    // Log any exceptions
    $logData = date('Y-m-d H:i:s') . " - Exception in Shopier notification handler: " . $e->getMessage() . "\n";
    $logData .= "Exception trace: " . $e->getTraceAsString() . "\n";
    file_put_contents('../logs/shopier_notifications.log', $logData, FILE_APPEND | LOCK_EX);
    http_response_code(500);
    echo 'Internal server error';
    exit;
}