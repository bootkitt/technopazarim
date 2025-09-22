<?php
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product_id']) || !isset($input['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}

$productId = intval($input['product_id']);
$orderId = intval($input['order_id']);

// Verify that the order belongs to the user and is completed
$stmt = $db->prepare("SELECT s.siparis_id FROM siparisler s 
                     INNER JOIN siparis_urunler su ON s.siparis_id = su.siparis_id 
                     WHERE s.siparis_id = ? AND s.uye_id = ? AND s.odeme_durum = 'tamamlandi' AND su.urun_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id'], $productId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz sipariş veya yetkisiz erişim']);
    exit;
}

// Get product name
$stmt = $db->prepare("SELECT urun_baslik FROM urunler WHERE urun_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
    exit;
}

// Get license key for this order and product
$stmt = $db->prepare("SELECT stok_kodu FROM dijital_stok 
                     WHERE urun_id = ? AND kullanilan_siparis_id = ? AND stok_durum = 'kullanildi' 
                     LIMIT 1");
$stmt->execute([$productId, $orderId]);
$license = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$license) {
    echo json_encode(['success' => false, 'message' => 'Lisans anahtarı bulunamadı']);
    exit;
}

echo json_encode([
    'success' => true,
    'product_name' => $product['urun_baslik'],
    'license_key' => $license['stok_kodu']
]);