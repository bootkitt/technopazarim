<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Analiz ve İstatistikler';

// Fetch analytics data
// Total products
$stmt = $db->prepare("SELECT COUNT(*) as total FROM urunler");
$stmt->execute();
$totalProducts = $stmt->fetchColumn();

// Total orders
$stmt = $db->prepare("SELECT COUNT(*) as total FROM siparisler");
$stmt->execute();
$totalOrders = $stmt->fetchColumn();

// Total users
$stmt = $db->prepare("SELECT COUNT(*) as total FROM uyeler");
$stmt->execute();
$totalUsers = $stmt->fetchColumn();

// Total revenue
$stmt = $db->prepare("SELECT SUM(siparis_toplam) as total FROM siparisler WHERE odeme_durum = 'tamamlandi'");
$stmt->execute();
$totalRevenue = $stmt->fetchColumn();

// Recent orders
$stmt = $db->prepare("SELECT s.*, u.uye_adi FROM siparisler s INNER JOIN uyeler u ON s.uye_id = u.uye_id ORDER BY s.siparis_tarih DESC LIMIT 10");
$stmt->execute();
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent support tickets
$stmt = $db->prepare("SELECT d.*, u.uye_adi FROM destek_biletleri d INNER JOIN uyeler u ON d.uye_id = u.uye_id ORDER BY d.bilet_tarih DESC LIMIT 10");
$stmt->execute();
$recentTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get monthly revenue data for the last 6 months
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $stmt = $db->prepare("
        SELECT SUM(siparis_toplam) as revenue 
        FROM siparisler 
        WHERE odeme_durum = 'tamamlandi' 
        AND DATE_FORMAT(siparis_tarih, '%Y-%m') = ?
    ");
    $stmt->execute([$month]);
    $revenue = $stmt->fetchColumn() ?? 0;
    $monthlyRevenue[] = [
        'month' => date('M', strtotime("-$i months")),
        'revenue' => $revenue
    ];
}

// Get order status distribution
$stmt = $db->prepare("SELECT odeme_durum, COUNT(*) as count FROM siparisler GROUP BY odeme_durum");
$stmt->execute();
$orderStatusData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get user registration data for the last 6 months
$userRegistrations = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM uyeler 
        WHERE DATE_FORMAT(uye_tarih, '%Y-%m') = ?
    ");
    $stmt->execute([$month]);
    $count = $stmt->fetchColumn() ?? 0;
    $userRegistrations[] = [
        'month' => date('M', strtotime("-$i months")),
        'count' => $count
    ];
}

// Fetch analytics data for the new sections
// Visit analytics
$stmt = $db->prepare("SELECT COUNT(*) as total FROM analiz_ziyaretler");
$stmt->execute();
$totalVisits = $stmt->fetchColumn();

// Product view analytics
$stmt = $db->prepare("SELECT COUNT(*) as total FROM analiz_urun_goruntulemeler");
$stmt->execute();
$totalProductViews = $stmt->fetchColumn();

// Cart abandonment analytics
$stmt = $db->prepare("SELECT COUNT(*) as total FROM analiz_sepet_birakmalar");
$stmt->execute();
$totalCartAbandonments = $stmt->fetchColumn();

// Recent visits
$stmt = $db->prepare("SELECT a.*, u.uye_adi FROM analiz_ziyaretler a LEFT JOIN uyeler u ON a.uye_id = u.uye_id ORDER BY a.giris_tarihi DESC LIMIT 5");
$stmt->execute();
$recentVisits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent product views
$stmt = $db->prepare("SELECT a.*, u.uye_adi, ur.urun_baslik FROM analiz_urun_goruntulemeler a LEFT JOIN uyeler u ON a.uye_id = u.uye_id LEFT JOIN urunler ur ON a.urun_id = ur.urun_id ORDER BY a.goruntuleme_tarihi DESC LIMIT 5");
$stmt->execute();
$recentProductViews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent cart abandonments
$stmt = $db->prepare("SELECT a.*, u.uye_adi, ur.urun_baslik FROM analiz_sepet_birakmalar a LEFT JOIN uyeler u ON a.uye_id = u.uye_id LEFT JOIN urunler ur ON a.urun_id = ur.urun_id ORDER BY a.eklenme_tarihi DESC LIMIT 5");
$stmt->execute();
$recentCartAbandonments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Top viewed products
$stmt = $db->prepare("SELECT ur.urun_baslik, COUNT(a.urun_id) as view_count FROM analiz_urun_goruntulemeler a JOIN urunler ur ON a.urun_id = ur.urun_id GROUP BY a.urun_id ORDER BY view_count DESC LIMIT 5");
$stmt->execute();
$topViewedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cart abandonment rate
$stmt = $db->prepare("SELECT COUNT(*) as total_abandonments FROM analiz_sepet_birakmalar WHERE cikarma_tarihi IS NULL");
$stmt->execute();
$abandonedCarts = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) as total_completed FROM analiz_sepet_birakmalar WHERE cikarma_tarihi IS NOT NULL");
$stmt->execute();
$completedCarts = $stmt->fetchColumn();

$cartAbandonmentRate = ($abandonedCarts + $completedCarts) > 0 ? round(($abandonedCarts / ($abandonedCarts + $completedCarts)) * 100, 2) : 0;

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Analiz ve İstatistikler</h1>
            <p class="text-gray-600 mt-1">Sistem performansı ve istatistiksel veriler</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ürünler</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalProducts; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Siparişler</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Kullanıcılar</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalUsers; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-turkish-lira-sign text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Gelir</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalRevenue ?? 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Ziyaret</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalVisits; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-teal-100 text-teal-600 mr-4">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ürün Görüntüleme</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalProductViews; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-orange-100 text-orange-600 mr-4">
                        <i class="fas fa-shopping-basket text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sepet Bırakma</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalCartAbandonments; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Graphs -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Aylık Gelir Grafiği</h2>
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                </div>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Sipariş Durum Dağılımı</h2>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                </div>
                <div class="h-80">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Aylık Kullanıcı Kayıtları</h2>
                    <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                </div>
                <div class="h-80">
                    <canvas id="userRegistrationChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Hızlı İstatistikler</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-3"></div>
                            <span class="text-gray-700">Açık Destek Talepleri</span>
                        </div>
                        <span class="font-semibold text-gray-900">
                            <?php 
                            $stmt = $db->prepare("SELECT COUNT(*) FROM destek_biletleri WHERE bilet_durum = 'acik'");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-3"></div>
                            <span class="text-gray-700">Beklemede Olan Siparişler</span>
                        </div>
                        <span class="font-semibold text-gray-900">
                            <?php echo $orderStatusData['beklemede'] ?? 0; ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-red-500 mr-3"></div>
                            <span class="text-gray-700">Düşük Stoklu Ürünler</span>
                        </div>
                        <span class="font-semibold text-gray-900">
                            <?php 
                            $stmt = $db->prepare("SELECT COUNT(*) FROM urunler WHERE urun_stok < 5 AND urun_stok > 0");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gray-500 mr-3"></div>
                            <span class="text-gray-700">Stokta Olmayan Ürünler</span>
                        </div>
                        <span class="font-semibold text-gray-900">
                            <?php 
                            $stmt = $db->prepare("SELECT COUNT(*) FROM urunler WHERE urun_stok = 0");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border border-orange-100">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-orange-500 mr-3"></div>
                            <span class="text-gray-700">Sepet Bırakma Oranı</span>
                        </div>
                        <span class="font-semibold text-gray-900">
                            <?php echo $cartAbandonmentRate; ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Son Ziyaretler</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recentVisits)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-user-clock text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz ziyaret yok</h3>
                            <p class="text-gray-500">Yeni ziyaretler burada görünecek</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Adresi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentVisits as $visit): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($visit['ip_adresi']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($visit['uye_adi'] ?? 'Misafir'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo date('d.m.Y H:i', strtotime($visit['giris_tarihi'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">En Çok Görüntülenen Ürünler</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($topViewedProducts)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz ürün görüntüleme yok</h3>
                            <p class="text-gray-500">Yeni ürün görüntülemeleri burada görünecek</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Görüntüleme</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($topViewedProducts as $product): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($product['urun_baslik']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $product['view_count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Son Ürün Görüntülemeleri</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recentProductViews)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz ürün görüntüleme yok</h3>
                            <p class="text-gray-500">Yeni ürün görüntülemeleri burada görünecek</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentProductViews as $view): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($view['urun_baslik'] ?? 'Bilinmeyen Ürün'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($view['uye_adi'] ?? 'Misafir'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo date('d.m.Y H:i', strtotime($view['goruntuleme_tarihi'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Son Sepet Bırakmaları</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recentCartAbandonments)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz sepet bırakma yok</h3>
                            <p class="text-gray-500">Yeni sepet bırakmaları burada görünecek</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentCartAbandonments as $abandonment): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($abandonment['urun_baslik'] ?? 'Bilinmeyen Ürün'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($abandonment['uye_adi'] ?? 'Misafir'); ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <?php if ($abandonment['cikarma_tarihi']): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tamamlandı</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Bırakıldı</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Orders and Support Tickets -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Son Siparişler</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz sipariş yok</h3>
                            <p class="text-gray-500">Yeni siparişler burada görünecek</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">#<?php echo $order['siparis_id']; ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($order['uye_adi']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($order['siparis_toplam']); ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <?php
                                                switch ($order['odeme_durum']) {
                                                    case 'tamamlandi':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tamamlandı</span>';
                                                        break;
                                                    case 'beklemede':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Beklemede</span>';
                                                        break;
                                                    case 'basarisiz':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Başarısız</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Bilinmiyor</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Son Destek Talepleri</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recentTickets)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-headset text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz destek talebi yok</h3>
                            <p class="text-gray-500">Yeni destek talepleri burada görünecek</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentTickets as $ticket): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">#<?php echo $ticket['bilet_id']; ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($ticket['uye_adi']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(substr($ticket['bilet_baslik'], 0, 20)) . (strlen($ticket['bilet_baslik']) > 20 ? '...' : ''); ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <?php
                                                switch ($ticket['bilet_durum']) {
                                                    case 'acik':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Açık</span>';
                                                        break;
                                                    case 'beklemede':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Beklemede</span>';
                                                        break;
                                                    case 'kapali':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Kapalı</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Bilinmiyor</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthlyRevenue, 'month')); ?>,
            datasets: [{
                label: 'Aylık Gelir (₺)',
                data: <?php echo json_encode(array_column($monthlyRevenue, 'revenue')); ?>,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '₺' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
    
    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusChart = new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Tamamlandı', 'Beklemede', 'Başarısız'],
            datasets: [{
                data: [
                    <?php echo $orderStatusData['tamamlandi'] ?? 0; ?>,
                    <?php echo $orderStatusData['beklemede'] ?? 0; ?>,
                    <?php echo $orderStatusData['basarisiz'] ?? 0; ?>
                ],
                backgroundColor: [
                    'rgb(16, 185, 129)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // User Registration Chart
    const userRegistrationCtx = document.getElementById('userRegistrationChart').getContext('2d');
    const userRegistrationChart = new Chart(userRegistrationCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($userRegistrations, 'month')); ?>,
            datasets: [{
                label: 'Yeni Kullanıcılar',
                data: <?php echo json_encode(array_column($userRegistrations, 'count')); ?>,
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderColor: 'rgb(139, 92, 246)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>