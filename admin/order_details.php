<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_status'])) {
        $order_id = (int)$_POST['order_id'];
        $status = $_POST['status'];
        
        try {
            $stmt = $db->prepare("UPDATE siparisler SET odeme_durum = ? WHERE siparis_id = ?");
            $stmt->execute([$status, $order_id]);
            
            // Return JSON response for AJAX
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Sipariş durumu başarıyla güncellendi.']);
            exit;
        } catch (Exception $e) {
            // Return JSON response for AJAX
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sipariş durumu güncellenirken bir hata oluştu: ' . $e->getMessage()]);
            exit;
        }
    }
}

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    echo '<p class="text-red-500">Geçersiz sipariş ID\'si.</p>';
    exit;
}

try {
    // Fetch order details with user info
    $stmt = $db->prepare("
        SELECT s.*, u.uye_adi, u.uye_eposta, u.uye_telefon 
        FROM siparisler s 
        INNER JOIN uyeler u ON s.uye_id = u.uye_id 
        WHERE s.siparis_id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo '<p class="text-red-500">Sipariş bulunamadı.</p>';
        exit;
    }
    
    // Fetch order items
    $stmt = $db->prepare("
        SELECT si.*, u.urun_baslik 
        FROM siparis_icerik si 
        INNER JOIN urunler u ON si.urun_id = u.urun_id 
        WHERE si.siparis_id = ?
    ");
    $stmt->execute([$order_id]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    echo '<p class="text-red-500">Sipariş detayları alınırken bir hata oluştu: ' . $e->getMessage() . '</p>';
    exit;
}

// Status display helper
$statusClasses = [
    'beklemede' => 'bg-yellow-100 text-yellow-800',
    'tamamlandi' => 'bg-green-100 text-green-800',
    'basarisiz' => 'bg-red-100 text-red-800'
];
$statusTexts = [
    'beklemede' => 'Beklemede',
    'tamamlandi' => 'Tamamlandı',
    'basarisiz' => 'Başarısız'
];
?>

<div class="space-y-6">
    <!-- Order Info -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="text-sm font-medium text-gray-500">Sipariş ID</h4>
                <p class="text-lg font-medium">#<?php echo $order['siparis_id']; ?></p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Sipariş Tarihi</h4>
                <p class="text-lg font-medium"><?php echo date('d.m.Y H:i', strtotime($order['siparis_tarih'])); ?></p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Durum</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClasses[$order['odeme_durum']]; ?>">
                    <?php echo $statusTexts[$order['odeme_durum']]; ?>
                </span>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Toplam Tutar</h4>
                <p class="text-lg font-medium"><?php echo formatCurrency($order['siparis_toplam']); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Customer Info -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">Müşteri Bilgileri</h3>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Ad Soyad</h4>
                    <p class="text-gray-900"><?php echo htmlspecialchars($order['uye_adi']); ?></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">E-posta</h4>
                    <p class="text-gray-900"><?php echo htmlspecialchars($order['uye_eposta']); ?></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Telefon</h4>
                    <p class="text-gray-900"><?php echo htmlspecialchars($order['uye_telefon'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">Sipariş İçeriği</h3>
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adet</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($item['urun_baslik']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($item['urun_fiyat']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $item['urun_adet']; ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($item['urun_fiyat'] * $item['urun_adet']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Update Status Form -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">Sipariş Durumu Güncelle</h3>
        <form method="POST" id="updateStatusForm" class="bg-white border border-gray-200 rounded-lg p-4">
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" name="order_id" value="<?php echo $order['siparis_id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="beklemede" <?php echo $order['odeme_durum'] == 'beklemede' ? 'selected' : ''; ?>>Beklemede</option>
                        <option value="tamamlandi" <?php echo $order['odeme_durum'] == 'tamamlandi' ? 'selected' : ''; ?>>Tamamlandı</option>
                        <option value="basarisiz" <?php echo $order['odeme_durum'] == 'basarisiz' ? 'selected' : ''; ?>>Başarısız</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Durumu Güncelle
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('order_details.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            // Reload order details to show updated status
            loadOrderDetails(<?php echo $order_id; ?>);
        } else {
            // Show error message
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
    });
});
</script>