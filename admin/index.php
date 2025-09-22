<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login');
    exit;
}

$page_title = 'Dashboard';

// Get admin info - using uyeler table instead of admins table
$stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ? AND uye_rutbe = 1 AND uye_onay = 1");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

// Check if admin user exists
if (!$admin) {
    // If admin not found, log out and redirect to login
    logoutAdmin();
    header('Location: login');
    exit;
}

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
$stats = $db->query($statsQuery)->fetch();

// Recent activities - using correct table names
$recentUsers = $db->query("SELECT uye_adi as username, uye_eposta as email, uye_tarih as created_at FROM uyeler ORDER BY uye_tarih DESC LIMIT 5")->fetchAll();
$pendingOrders = $db->query("SELECT s.*, u.uye_adi as username FROM siparisler s JOIN uyeler u ON s.uye_id = u.uye_id WHERE s.odeme_durum = 'beklemede' ORDER BY s.siparis_tarih DESC LIMIT 5")->fetchAll();
$completedOrders = $db->query("SELECT s.*, u.uye_adi as username FROM siparisler s JOIN uyeler u ON s.uye_id = u.uye_id WHERE s.odeme_durum = 'tamamlandi' ORDER BY s.siparis_tarih DESC LIMIT 10")->fetchAll();

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Sistem istatistiklerinize genel bakış</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Kullanıcı</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                </div>
            </div>

            <!-- New Users Today -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-user-plus text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Bugün Kayıt</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['new_users_today']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Deposits -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-arrow-down text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Yatırım</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($stats['total_deposits']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Withdrawals -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-arrow-up text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Çekim</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($stats['total_withdrawals']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Packages -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Aktif Paket</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['active_packages']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Open Tickets -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Açık Ticket</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['open_tickets']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Son Kayıtlar</h2>
                    <a href="users.php" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                        Tümünü Gör
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($recentUsers)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-users text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Henüz kullanıcı yok</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentUsers as $user): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo clean($user['username']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo clean($user['email']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d.m', strtotime($user['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Bekleyen Siparişler</h2>
                    <a href="orders.php" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                        Tümünü Gör
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($pendingOrders)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Bekleyen sipariş yok</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Tipi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($pendingOrders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo clean($order['username']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d.m.Y H:i', strtotime($order['siparis_tarih'])); ?></td>
                                            <td class="px-4 py-3 text-sm text-green-600 font-medium"><?php echo formatCurrency($order['siparis_toplam']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo clean($order['odeme_tipi'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Tamamlanan Siparişler</h2>
                    <a href="orders.php" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                        Tümünü Gör
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($completedOrders)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tamamlanan sipariş yok</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto" style="max-height: 300px; overflow-y: auto;">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($completedOrders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo clean($order['username']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d.m H:i', strtotime($order['siparis_tarih'])); ?></td>
                                            <td class="px-4 py-3 text-sm text-red-600 font-medium"><?php echo formatCurrency($order['siparis_toplam']); ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <?php
                                                $statusColors = [
                                                    'beklemede' => 'bg-yellow-100 text-yellow-800',
                                                    'tamamlandi' => 'bg-green-100 text-green-800',
                                                    'basarisiz' => 'bg-red-100 text-red-800',
                                                    'iade' => 'bg-blue-100 text-blue-800'
                                                ];
                                                $statusTexts = [
                                                    'beklemede' => 'Beklemede',
                                                    'tamamlandi' => 'Tamamlandı',
                                                    'basarisiz' => 'Başarısız',
                                                    'iade' => 'İade'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColors[$order['odeme_durum']]; ?>">
                                                    <?php echo $statusTexts[$order['odeme_durum']]; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Support Tickets -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Destek Talepleri</h2>
                    <a href="tickets.php" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                        Tümünü Gör
                    </a>
                </div>
                <div class="p-6">
                    <?php
                    // Get recent support tickets
                    $recentTickets = $db->query("SELECT d.*, u.uye_adi as username FROM destek_biletleri d JOIN uyeler u ON d.uye_id = u.uye_id ORDER BY d.bilet_tarih DESC LIMIT 5")->fetchAll();
                    ?>
                    <?php if (empty($recentTickets)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-headset text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Destek talebi yok</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentTickets as $ticket): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo clean($ticket['username']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d.m.Y H:i', strtotime($ticket['bilet_tarih'])); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo clean(substr($ticket['bilet_baslik'], 0, 20)) . (strlen($ticket['bilet_baslik']) > 20 ? '...' : ''); ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <?php
                                                $ticketStatusColors = [
                                                    'acik' => 'bg-green-100 text-green-800',
                                                    'kapali' => 'bg-gray-100 text-gray-800',
                                                    'beklemede' => 'bg-yellow-100 text-yellow-800'
                                                ];
                                                $ticketStatusTexts = [
                                                    'acik' => 'Açık',
                                                    'kapali' => 'Kapalı',
                                                    'beklemede' => 'Beklemede'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $ticketStatusColors[$ticket['bilet_durum']]; ?>">
                                                    <?php echo $ticketStatusTexts[$ticket['bilet_durum']]; ?>
                                                </span>
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

<?php include_once __DIR__ . '/includes/footer.php'; ?>