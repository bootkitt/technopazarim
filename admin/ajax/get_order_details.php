<?php
require_once __DIR__ . '/../../config.php';

if (!isAdmin()) {
    http_response_code(403);
    echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">Yetkisiz erişim</div>';
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    http_response_code(400);
    echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">Geçersiz sipariş ID</div>';
    exit;
}

try {
    // Get order details with user info (removed uye_telefon since it doesn't exist)
    $stmt = $db->prepare("
        SELECT s.*, u.uye_adi, u.uye_eposta
        FROM siparisler s 
        INNER JOIN uyeler u ON s.uye_id = u.uye_id 
        WHERE s.siparis_id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        http_response_code(404);
        echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">Sipariş bulunamadı</div>';
        exit;
    }
    
    // Get order items
    $stmt = $db->prepare("SELECT * FROM siparis_urunler WHERE siparis_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format order status for display
    $statusTexts = [
        'beklemede' => 'Beklemede',
        'tamamlandi' => 'Tamamlandı',
        'basarisiz' => 'Başarısız',
        'iade' => 'İade'
    ];
    
    $paymentStatusTexts = [
        'beklemede' => 'Beklemede',
        'tamamlandi' => 'Tamamlandı',
        'basarisiz' => 'Başarısız',
        'iade' => 'İade'
    ];
    
    $status = $statusTexts[$order['siparis_durum']] ?? $order['siparis_durum'];
    $paymentStatus = $paymentStatusTexts[$order['odeme_durum']] ?? $order['odeme_durum'];
    
    // Output HTML for the modal using Tailwind CSS
    echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">';
    echo '<div>';
    echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Sipariş Detayları</h3>';
    echo '<div class="bg-gray-50 rounded-lg p-4">';
    echo '<div class="space-y-3">';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">ID:</span>';
    echo '<span class="text-gray-900">#' . $order['siparis_id'] . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Kullanıcı:</span>';
    echo '<span class="text-gray-900">' . htmlspecialchars($order['uye_adi']) . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">E-posta:</span>';
    echo '<span class="text-gray-900">' . htmlspecialchars($order['uye_eposta']) . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Sipariş Durumu:</span>';
    echo '<span class="text-gray-900">' . $status . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Ödeme Durumu:</span>';
    echo '<span class="text-gray-900">' . $paymentStatus . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Ödeme Tipi:</span>';
    echo '<span class="text-gray-900">' . htmlspecialchars($order['odeme_tipi'] ?? 'N/A') . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Tarih:</span>';
    echo '<span class="text-gray-900">' . date('d.m.Y H:i', strtotime($order['siparis_tarih'])) . '</span>';
    echo '</div>';
    echo '<div class="flex justify-between">';
    echo '<span class="font-medium text-gray-600">Toplam Tutar:</span>';
    echo '<span class="text-gray-900 font-semibold">' . formatCurrency($order['siparis_toplam']) . '</span>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<div>';
    echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Sipariş Ürünleri</h3>';
    echo '<div class="border border-gray-200 rounded-lg p-4 bg-white">';
    if (!empty($items)) {
        echo '<div class="space-y-3">';
        foreach ($items as $item) {
            echo '<div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">';
            echo '<div>';
            echo '<div class="font-medium text-gray-900">Ürün ID: ' . $item['urun_id'] . '</div>';
            echo '<div class="text-sm text-gray-500">Adet: ' . $item['urun_adet'] . '</div>';
            echo '</div>';
            echo '<div class="text-right">';
            echo '<div class="font-medium text-gray-900">' . formatCurrency($item['urun_fiyat']) . '</div>';
            echo '<div class="text-sm text-gray-500">Toplam: ' . formatCurrency($item['urun_adet'] * $item['urun_fiyat']) . '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p class="text-gray-500">Bu siparişte ürün bulunmamaktadır.</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Action buttons for order status management
    echo '<div class="border-t border-gray-200 pt-4">';
    echo '<h3 class="text-lg font-semibold text-gray-900 mb-4">Sipariş Durumunu Güncelle</h3>';
    echo '<div class="flex flex-wrap gap-2">';
    echo '<form method="POST" class="inline-block" id="statusForm">';
    echo '<input type="hidden" name="order_id" value="' . $order['siparis_id'] . '">';
    echo '<div class="flex flex-wrap gap-2">';
    
    // Status buttons
    $statuses = [
        'beklemede' => 'Beklemede',
        'tamamlandi' => 'Tamamlandı',
        'basarisiz' => 'Başarısız'
    ];
    
    foreach ($statuses as $statusKey => $statusLabel) {
        $buttonClass = ($order['odeme_durum'] === $statusKey) 
            ? 'bg-blue-600 text-white' 
            : 'bg-gray-200 text-gray-800 hover:bg-gray-300';
        
        echo '<button type="button" class="status-btn px-4 py-2 rounded-md text-sm font-medium transition-colors ' . $buttonClass . '" data-status="' . $statusKey . '">' . $statusLabel . '</button>';
    }
    
    echo '</div>';
    echo '</form>';
    echo '</div>';
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