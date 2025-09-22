<?php
// Check if viewing a specific order
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action === 'view' && $orderId > 0) {
    // Fetch order details
    $stmt = $db->prepare("SELECT s.*, u.uye_adi FROM siparisler s INNER JOIN uyeler u ON s.uye_id = u.uye_id WHERE s.siparis_id = ? AND s.uye_id = ?");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: index.php?page=account&section=orders');
        exit;
    }
    
    // Fetch order items
    $stmt = $db->prepare("SELECT su.*, u.urun_baslik FROM siparis_urunler su INNER JOIN urunler u ON su.urun_id = u.urun_id WHERE su.siparis_id = ?");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Sipariş Detayları</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-300">Sipariş #<?php echo $order['siparis_id']; ?> detayları</p>
        </div>
        <a href="index.php?page=account&section=orders" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Geri
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <div class="card rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Sipariş Kalemleri</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ürün</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Adet</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Birim Fiyat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Toplam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($item['urun_baslik']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $item['urun_adet']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo formatCurrency($item['urun_fiyat']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo formatCurrency($item['urun_fiyat'] * $item['urun_adet']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th colspan="3" class="px-6 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">Genel Toplam:</th>
                                    <th class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-white"><?php echo formatCurrency($order['siparis_toplam']); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="card rounded-2xl overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Sipariş Bilgileri</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sipariş ID:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">#<?php echo $order['siparis_id']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sipariş Tarihi:</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('d.m.Y H:i', strtotime($order['siparis_tarih'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Durum:</dt>
                            <dd class="text-sm">
                                <?php
                                switch ($order['odeme_durum']) {
                                    case 'tamamlandi':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Tamamlandı</span>';
                                        break;
                                    case 'beklemede':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Beklemede</span>';
                                        break;
                                    case 'basarisiz':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Başarısız</span>';
                                        break;
                                    case 'iade':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">İade</span>';
                                        break;
                                    default:
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Bilinmiyor</span>';
                                }
                                ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <div class="card rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Ödeme Bilgileri</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Tutar:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white"><?php echo formatCurrency($order['siparis_toplam']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ödeme Yöntemi:</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $order['odeme_tipi'] === 'shopier' ? 'Kredi Kartı (Shopier)' : 'Banka Havalesi'; ?></dd>
                        </div>
                    </dl>
                    
                    <?php if ($order['odeme_durum'] === 'tamamlandi'): ?>
                        <div class="mt-6">
                            <a href="index.php?page=account&section=downloads" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-2"></i> İndirme Merkezine Git
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // Fetch all orders
    $stmt = $db->prepare("SELECT * FROM siparisler WHERE uye_id = ? ORDER BY siparis_tarih DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="card rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Sipariş Geçmişi</h2>
        </div>
        <div class="p-6">
            <?php if (empty($orders)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-info-circle text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Henüz siparişiniz bulunmuyor</h3>
                    <p class="text-gray-500 dark:text-gray-400">Alışverişe başlamak için ürün ekleyin.</p>
                    <a href="index.php?page=products" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Ürünlere Gözat
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sipariş ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Toplam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#<?php echo $order['siparis_id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo date('d.m.Y H:i', strtotime($order['siparis_tarih'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo formatCurrency($order['siparis_toplam']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        switch ($order['odeme_durum']) {
                                            case 'tamamlandi':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Tamamlandı</span>';
                                                break;
                                            case 'beklemede':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Beklemede</span>';
                                                break;
                                            case 'basarisiz':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Başarısız</span>';
                                                break;
                                            case 'iade':
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">İade</span>';
                                                break;
                                            default:
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Bilinmiyor</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="index.php?page=account&section=orders&action=view&id=<?php echo $order['siparis_id']; ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            <i class="fas fa-eye mr-1"></i> Görüntüle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}
?>