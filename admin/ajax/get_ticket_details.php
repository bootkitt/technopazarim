<?php
require_once __DIR__ . '/../../config.php';

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$ticket_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz talep ID']);
    exit;
}

try {
    // Get ticket details with user info
    $stmt = $db->prepare("
        SELECT d.*, u.uye_adi, u.uye_eposta 
        FROM destek_biletleri d 
        INNER JOIN uyeler u ON d.uye_id = u.uye_id 
        WHERE d.bilet_id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        http_response_code(404);
        echo json_encode(['error' => 'Talep bulunamadı']);
        exit;
    }
    
    // Get ticket responses
    $stmt = $db->prepare("
        SELECT dy.*, u.uye_adi 
        FROM destek_yanitlari dy 
        LEFT JOIN uyeler u ON dy.uye_id = u.uye_id 
        WHERE dy.bilet_id = ? 
        ORDER BY dy.yanit_tarih ASC
    ");
    $stmt->execute([$ticket_id]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format ticket status for display
    $statusTexts = [
        'acik' => 'Açık',
        'beklemede' => 'Beklemede',
        'kapali' => 'Kapalı'
    ];
    
    $status = $statusTexts[$ticket['bilet_durum']] ?? $ticket['bilet_durum'];
    
    // Output HTML for the modal using Tailwind CSS
    echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">';
    echo '<div>';
    echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Talep Detayları</h3>';
    echo '<div class="bg-gray-50 rounded-lg p-4">';
    echo '<div class="space-y-3">';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">ID:</span>';
    echo '<span class="text-gray-900">#' . $ticket['bilet_id'] . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Kullanıcı:</span>';
    echo '<span class="text-gray-900">' . htmlspecialchars($ticket['uye_adi']) . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">E-posta:</span>';
    echo '<span class="text-gray-900">' . htmlspecialchars($ticket['uye_eposta']) . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Başlık:</span>';
    echo '<span class="text-gray-900">' . htmlspecialchars($ticket['bilet_baslik']) . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Durum:</span>';
    echo '<span class="text-gray-900">' . $status . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Tarih:</span>';
    echo '<span class="text-gray-900">' . date('d.m.Y H:i', strtotime($ticket['bilet_tarih'])) . '</span>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<div>';
    echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Talep İçeriği</h3>';
    echo '<div class="border border-gray-200 rounded-lg p-4 bg-white">';
    echo '<p class="text-gray-700 whitespace-pre-wrap">' . nl2br(htmlspecialchars($ticket['bilet_icerik'])) . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    if (!empty($responses)) {
        echo '<div class="border-t border-gray-200 pt-6 mt-6">';
        echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Yanıtlar</h3>';
        foreach ($responses as $response) {
            echo '<div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white">';
            echo '<div class="flex justify-between items-center mb-2">';
            echo '<strong class="text-gray-900">' . htmlspecialchars($response['uye_adi'] ?? 'Sistem') . '</strong>';
            echo '<span class="text-sm text-gray-500">' . date('d.m.Y H:i', strtotime($response['yanit_tarih'])) . '</span>';
            echo '</div>';
            echo '<p class="text-gray-700 whitespace-pre-wrap mt-2">' . nl2br(htmlspecialchars($response['yanit_icerik'])) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    // Response form
    echo '<div class="border-t border-gray-200 pt-6 mt-6">';
    echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Yanıt Ver</h3>';
    echo '<form method="POST" action="tickets.php" class="space-y-4">';
    echo '<input type="hidden" name="update_status" value="1">';
    echo '<input type="hidden" name="ticket_id" value="' . $ticket['bilet_id'] . '">';
    echo '<div>';
    echo '<label for="status" class="block text-sm font-medium text-gray-700 mb-1">Durum</label>';
    echo '<select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="status" name="status">';
    echo '<option value="acik" ' . ($ticket['bilet_durum'] === 'acik' ? 'selected' : '') . '>Açık</option>';
    echo '<option value="beklemede" ' . ($ticket['bilet_durum'] === 'beklemede' ? 'selected' : '') . '>Beklemede</option>';
    echo '<option value="kapali" ' . ($ticket['bilet_durum'] === 'kapali' ? 'selected' : '') . '>Kapalı</option>';
    echo '</select>';
    echo '</div>';
    echo '<div>';
    echo '<label for="response" class="block text-sm font-medium text-gray-700 mb-1">Yanıtınız</label>';
    echo '<textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="response" name="response" rows="4"></textarea>';
    echo '</div>';
    echo '<div class="flex justify-end">';
    echo '<button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">';
    echo '<i class="fas fa-paper-plane mr-2"></i> Yanıtı Gönder';
    echo '</button>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    
} catch (Exception $e) {
    http_response_code(500);
    echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">';
    echo '<div class="flex">';
    echo '<div class="flex-shrink-0">';
    echo '<i class="fas fa-exclamation-circle"></i>';
    echo '</div>';
    echo '<div class="ml-3">';
    echo '<p>Bir hata oluştu: ' . $e->getMessage() . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}