<?php
require_once '../config.php';

header('Content-Type: application/json');

// Log that the script is being accessed
// Script accessed

// Check if user is logged in
if (!isLoggedIn()) {
    // User not logged in
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

// Try to get JSON input
$json = file_get_contents('php://input');
$input = json_decode($json, true);

// Input logging removed for production

// Also check if data might be sent as form data
if (!$input && !empty($_POST)) {
    // Form data logging removed for production
    $input = $_POST;
}

if (!$input || !isset($input['product_id'])) {
    // Invalid input data
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}

$productId = intval($input['product_id']);

// Removing product

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove from cart
// Cart state logging removed for production

// Update cart abandonment record if it exists
if (isset($_SESSION['user_id'])) {
    // Find the abandonment record for this user and product
    $stmt = $db->prepare("SELECT birakma_id FROM analiz_sepet_birakmalar WHERE uye_id = ? AND urun_id = ? AND cikarma_tarihi IS NULL ORDER BY eklenme_tarihi DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    $abandonmentRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($abandonmentRecord) {
        // Update the abandonment record with removal time
        updateCartAbandonment($db, $abandonmentRecord['birakma_id']);
    }
}

unset($_SESSION['cart'][$productId]);
// Cart state logging removed for production

// Calculate cart total
$total = 0;
if (!empty($_SESSION['cart'])) {
    // Only create placeholders if cart is not empty
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $productIds = array_keys($_SESSION['cart']);
    
    // Product calculation logging removed for production
    
    $stmt = $db->prepare("SELECT urun_id, urun_fiyat FROM urunler WHERE urun_id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        if (isset($_SESSION['cart'][$product['urun_id']])) {
            $total += $product['urun_fiyat'] * $_SESSION['cart'][$product['urun_id']];
        }
    }
}
// If cart is empty, $total remains 0

// Total calculation logging removed for production

// Calculate cart count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}

// Cart count logging removed for production

$result = [
    'success' => true,
    'message' => 'Ürün sepetten kaldırıldı',
    'cart_count' => $cartCount,
    'total' => formatCurrency($total)
];

// Response logging removed for production

echo json_encode($result);
?>