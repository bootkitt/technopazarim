<?php
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

// Get order ID from POST data
$orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz sipariş ID']);
    exit;
}

// Verify that the order belongs to the current user and is a bank transfer order
$stmt = $db->prepare("SELECT * FROM siparisler WHERE siparis_id = ? AND uye_id = ? AND odeme_tipi = 'bankTransfer'");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Sipariş bulunamadı veya bu işlem için yetkiniz yok']);
    exit;
}

// Check if the order is already confirmed or processed
if ($order['odeme_durum'] !== 'beklemede') {
    echo json_encode(['success' => false, 'message' => 'Sipariş zaten işleme alınmış']);
    exit;
}

// Update the order status to 'pending_verification' (we'll use 'beklemede' but with a note)
try {
    $stmt = $db->prepare("UPDATE siparisler SET odeme_durum = 'beklemede' WHERE siparis_id = ?");
    $result = $stmt->execute([$orderId]);
    
    if ($result) {
        // Log this action for admin reference
        $stmt = $db->prepare("INSERT INTO siparis_notlari (siparis_id, not_tipi, not_icerik, olusturan) VALUES (?, 'musteri_bildirimi', 'Müşteri banka havalesi yaptığını bildirdi', 'musteri')");
        $stmt->execute([$orderId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Ödeme bildiriminiz alındı. Siparişiniz en kısa sürede onaylanacaktır.',
            'order_id' => $orderId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ödeme bildirimi kaydedilemedi. Lütfen tekrar deneyin.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}