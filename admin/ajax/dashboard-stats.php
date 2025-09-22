<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

try {
    // Dashboard statistics - using correct table names
    $statsQuery = "
        SELECT 
            (SELECT COUNT(*) FROM uyeler) as total_users,
            (SELECT COUNT(*) FROM uyeler WHERE DATE(uye_tarih) = CURDATE()) as new_users_today,
            (SELECT COALESCE(SUM(siparis_toplam), 0) FROM siparisler WHERE odeme_durum = 'tamamlandi') as total_deposits,
            (SELECT COALESCE(SUM(siparis_toplam), 0) FROM siparisler WHERE odeme_durum = 'iade') as total_withdrawals,
            (SELECT COUNT(*) FROM siparisler WHERE siparis_durum = 'tamamlandi') as active_packages,
            (SELECT COUNT(*) FROM destek_biletleri WHERE bilet_durum = 'acik') as open_tickets
    ";
    $stats = $db->query($statsQuery)->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'stats' => $stats]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Bir hata oluştu: ' . $e->getMessage()]);
}