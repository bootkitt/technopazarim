<?php
// Fetch order statistics
$stmt = $db->prepare("SELECT COUNT(*) as total_orders, SUM(siparis_toplam) as total_spent FROM siparisler WHERE uye_id = ? AND odeme_durum = 'tamamlandi'");
$stmt->execute([$_SESSION['user_id']]);
$orderStats = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent orders
$stmt = $db->prepare("SELECT * FROM siparisler WHERE uye_id = ? ORDER BY siparis_tarih DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch open support tickets
$stmt = $db->prepare("SELECT COUNT(*) as open_tickets FROM destek_biletleri WHERE uye_id = ? AND bilet_durum = 'acik'");
$stmt->execute([$_SESSION['user_id']]);
$ticketStats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Stats Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="card rounded-2xl p-6 hover-lift transition-all">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                <i class="fas fa-shopping-cart text-indigo-600 dark:text-indigo-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Sipariş</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $orderStats['total_orders'] ?? 0; ?></p>
            </div>
        </div>
    </div>
    
    <div class="card rounded-2xl p-6 hover-lift transition-all">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                <i class="fas fa-turkish-lira-sign text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Harcama</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo formatCurrency($orderStats['total_spent'] ?? 0); ?></p>
            </div>
        </div>
    </div>
    
    <div class="card rounded-2xl p-6 hover-lift transition-all">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <i class="fas fa-headset text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Açık Destek Talebi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $ticketStats['open_tickets'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="card rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Son Siparişler</h2>
    </div>
    <div class="p-6">
        <?php if (empty($recentOrders)): ?>
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
                        <?php foreach ($recentOrders as $order): ?>
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