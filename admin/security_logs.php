<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Güvenlik Kayıtları';

// Get filter parameters
$filterEventType = isset($_GET['event_type']) ? $_GET['event_type'] : '';
$filterUser = isset($_GET['user']) ? $_GET['user'] : '';
$filterDateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filterDateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build WHERE clause based on filters
$whereConditions = [];
$params = [];

if (!empty($filterEventType)) {
    $whereConditions[] = "g.olay_tipi = ?";
    $params[] = $filterEventType;
}

if (!empty($filterUser)) {
    $whereConditions[] = "(u.uye_adi LIKE ? OR u.uye_eposta LIKE ?)";
    $params[] = "%$filterUser%";
    $params[] = "%$filterUser%";
}

if (!empty($filterDateFrom)) {
    $whereConditions[] = "g.olay_tarihi >= ?";
    $params[] = $filterDateFrom;
}

if (!empty($filterDateTo)) {
    $whereConditions[] = "g.olay_tarihi <= ?";
    $params[] = $filterDateTo . ' 23:59:59';
}

$whereSQL = '';
if (!empty($whereConditions)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereConditions);
}

// Fetch security logs with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total logs count
$stmt = $db->prepare("SELECT COUNT(*) FROM guvenlik_kayitlari g LEFT JOIN uyeler u ON g.uye_id = u.uye_id $whereSQL");
$stmt->execute($params);
$totalLogs = $stmt->fetchColumn();

$totalPages = ceil($totalLogs / $limit);

// Get logs with user info
$sql = "
    SELECT g.*, u.uye_adi 
    FROM guvenlik_kayitlari g 
    LEFT JOIN uyeler u ON g.uye_id = u.uye_id 
    $whereSQL
    ORDER BY g.olay_tarihi DESC 
    LIMIT $offset, $limit
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get event types for filter dropdown
$stmt = $db->prepare("SELECT DISTINCT olay_tipi FROM guvenlik_kayitlari ORDER BY olay_tipi");
$stmt->execute();
$eventTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get users for filter dropdown (limit to 50 for performance)
$stmt = $db->prepare("SELECT DISTINCT u.uye_adi FROM guvenlik_kayitlari g LEFT JOIN uyeler u ON g.uye_id = u.uye_id WHERE u.uye_adi IS NOT NULL ORDER BY u.uye_adi LIMIT 50");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Güvenlik Kayıtları</h1>
            <p class="text-gray-600 mt-1">Sistemdeki güvenlik olaylarını görüntüleyin</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Kayıt</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalLogs; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Başarısız Girişler</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php
                            $stmt = $db->prepare("SELECT COUNT(*) FROM guvenlik_kayitlari WHERE olay_tipi = 'login_failed'");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Başarılı Girişler</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php
                            $stmt = $db->prepare("SELECT COUNT(*) FROM guvenlik_kayitlari WHERE olay_tipi = 'login_success'");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Filtrele</h2>
            </div>
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Olay Türü</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="event_type" name="event_type">
                            <option value="">Tümü</option>
                            <?php foreach ($eventTypes as $eventType): ?>
                                <option value="<?php echo htmlspecialchars($eventType); ?>" <?php echo $filterEventType === $eventType ? 'selected' : ''; ?>>
                                    <?php 
                                    $eventTypesLabels = [
                                        'login_success' => 'Başarılı Giriş',
                                        'login_failed' => 'Başarısız Giriş',
                                        'logout' => 'Çıkış',
                                        'password_change' => 'Şifre Değişikliği',
                                        'admin_login' => 'Yönetici Girişi',
                                        'admin_login_failed' => 'Başarısız Yönetici Girişi'
                                    ];
                                    echo htmlspecialchars($eventTypesLabels[$eventType] ?? $eventType);
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="user" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="user" name="user" value="<?php echo htmlspecialchars($filterUser); ?>" placeholder="Kullanıcı adı veya e-posta">
                    </div>
                    
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="date_from" name="date_from" value="<?php echo htmlspecialchars($filterDateFrom); ?>">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="date_to" name="date_to" value="<?php echo htmlspecialchars($filterDateTo); ?>">
                    </div>
                    
                    <div class="md:col-span-6 flex space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-filter mr-2"></i> Filtrele
                        </button>
                        <a href="security_logs.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-times mr-2"></i> Temizle
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Güvenlik Kayıtları</h2>
                <div class="relative">
                    <input type="text" class="w-full sm:w-64 px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ara...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($logs)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shield-alt text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz güvenlik kaydı bulunmuyor</h3>
                        <p class="text-gray-500">Sistemde güvenlik olayları oluştuğunda burada listelenecek</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Olay Türü</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Adresi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($logs as $log): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $log['kayit_id']; ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($log['uye_adi'] ?? 'Misafir'); ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php
                                            $eventTypes = [
                                                'login_success' => ['label' => 'Başarılı Giriş', 'class' => 'bg-green-100 text-green-800'],
                                                'login_failed' => ['label' => 'Başarısız Giriş', 'class' => 'bg-red-100 text-red-800'],
                                                'logout' => ['label' => 'Çıkış', 'class' => 'bg-blue-100 text-blue-800'],
                                                'password_change' => ['label' => 'Şifre Değişikliği', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'admin_login' => ['label' => 'Yönetici Girişi', 'class' => 'bg-purple-100 text-purple-800'],
                                                'admin_login_failed' => ['label' => 'Başarısız Yönetici Girişi', 'class' => 'bg-red-100 text-red-800']
                                            ];
                                            
                                            $eventType = $eventTypes[$log['olay_tipi']] ?? ['label' => $log['olay_tipi'], 'class' => 'bg-gray-100 text-gray-800'];
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $eventType['class']; ?>">
                                                <?php echo htmlspecialchars($eventType['label']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($log['aciklama']); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($log['ip_adresi']); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo date('d.m.Y H:i:s', strtotime($log['olay_tarihi'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-6 flex items-center justify-between border-t border-gray-200 pt-6">
                            <div class="hidden sm:block">
                                <p class="text-sm text-gray-700">
                                    <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalLogs); ?> arası, toplam <?php echo $totalLogs; ?> kayıt
                                </p>
                            </div>
                            <div class="flex-1 flex justify-between sm:justify-end">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?><?php echo http_build_query(array_filter(['event_type' => $filterEventType, 'user' => $filterUser, 'date_from' => $filterDateFrom, 'date_to' => $filterDateTo])); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Önceki
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?><?php echo http_build_query(array_filter(['event_type' => $filterEventType, 'user' => $filterUser, 'date_from' => $filterDateFrom, 'date_to' => $filterDateTo])); ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Sonraki
                                    </a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>