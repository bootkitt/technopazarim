<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz veri']);
    exit;
}

$product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$stock = isset($input['stock']) ? (int)$input['stock'] : 0;

if (!$product_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz ürün ID']);
    exit;
}

try {
    // Update product stock
    $stmt = $db->prepare("UPDATE urunler SET stok = ? WHERE urun_id = ?");
    $result = $stmt->execute([$stock, $product_id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Stok başarıyla güncellendi']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Stok güncellenemedi']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Bir hata oluştu: ' . $e->getMessage()]);
}