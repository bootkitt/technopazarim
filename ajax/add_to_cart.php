<?php
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

// Get form input
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}

// Validate product exists and is in stock
$stmt = $db->prepare("SELECT urun_id, urun_stok FROM urunler WHERE urun_id = ? AND urun_durum = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
    exit;
}

// Allow out-of-stock products to be added to cart
// if ($quantity > $product['urun_stok']) {
//     echo json_encode(['success' => false, 'message' => 'Yeterli stok yok']);
//     exit;
// }

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart - Always set the quantity rather than incrementing
// This ensures that each click adds exactly the selected quantity
if (isset($_SESSION['cart'][$productId])) {
    // Allow out-of-stock products to be added to cart
    // Check if the new quantity would exceed stock
    // if ($quantity > $product['urun_stok']) {
    //     echo json_encode(['success' => false, 'message' => 'Yeterli stok yok']);
    //     exit;
    // }
    // Set the quantity to exactly what was selected (don't increment)
    $_SESSION['cart'][$productId] = $quantity;
} else {
    // Allow out-of-stock products to be added to cart
    // if ($quantity > $product['urun_stok']) {
    //     echo json_encode(['success' => false, 'message' => 'Yeterli stok yok']);
    //     exit;
    // }
    $_SESSION['cart'][$productId] = $quantity;
    
    // Track cart abandonment when product is added to cart
    trackCartAbandonment($db, $productId, $_SESSION['user_id']);
}

// Calculate cart count
$cartCount = array_sum($_SESSION['cart']);

// Calculate cart total
$total = 0;
if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $productIds = array_keys($_SESSION['cart']);
    
    $stmt = $db->prepare("SELECT urun_id, urun_fiyat FROM urunler WHERE urun_id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        if (isset($_SESSION['cart'][$product['urun_id']])) {
            $total += $product['urun_fiyat'] * $_SESSION['cart'][$product['urun_id']];
        }
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Ürün sepete eklendi',
    'cart_count' => $cartCount,
    'cart_total_formatted' => formatCurrency($total)
]);
?>