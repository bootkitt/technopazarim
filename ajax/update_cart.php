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

if (!$input || !isset($input['product_id']) || !isset($input['quantity'])) {
    // Invalid input data
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}

$productId = intval($input['product_id']);
$quantity = intval($input['quantity']);

// Processing product

// Validate product exists and is in stock
$stmt = $db->prepare("SELECT urun_id, urun_stok FROM urunler WHERE urun_id = ? AND urun_durum = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // Product not found or not active
    echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
    exit;
}

// Product found logging removed for production

if ($quantity > $product['urun_stok']) {
    // Insufficient stock
    echo json_encode(['success' => false, 'message' => 'Yeterli stok yok']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Update cart
if ($quantity <= 0) {
    // Remove from cart if quantity is 0 or less
    // Removing product from cart
    unset($_SESSION['cart'][$productId]);
} else {
    // Updating cart
    $_SESSION['cart'][$productId] = $quantity;
}

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
    'message' => 'Sepet güncellendi',
    'cart_count' => $cartCount,
    'total' => formatCurrency($total)
];

// Response logging removed for production

echo json_encode($result);
?>