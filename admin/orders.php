<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Sipariş Yönetimi';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_status'])) {
        $order_id = (int)$_POST['order_id'];
        $status = $_POST['status'];
        
        try {
            $stmt = $db->prepare("UPDATE siparisler SET odeme_durum = ? WHERE siparis_id = ?");
            $stmt->execute([$status, $order_id]);
            
            // If this is an AJAX request from order_details.php, return JSON response
            if (strpos($_SERVER['HTTP_REFERER'], 'order_details.php') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Sipariş durumu başarıyla güncellendi.']);
                exit;
            }
            
            $success = "Sipariş durumu başarıyla güncellendi.";
        } catch (Exception $e) {
            // If this is an AJAX request from order_details.php, return JSON response
            if (strpos($_SERVER['HTTP_REFERER'], 'order_details.php') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sipariş durumu güncellenirken bir hata oluştu: ' . $e->getMessage()]);
                exit;
            }
            
            $error = "Sipariş durumu güncellenirken bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Fetch orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total orders count and status breakdown
$stmt = $db->prepare("SELECT COUNT(*) FROM siparisler");
$stmt->execute();
$totalOrders = $stmt->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalOrders / $limit);

// Get status counts
$stmt = $db->prepare("SELECT odeme_durum, COUNT(*) as count FROM siparisler GROUP BY odeme_durum");
$stmt->execute();
$statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get orders
$stmt = $db->prepare("
    SELECT s.*, u.uye_adi, u.uye_eposta 
    FROM siparisler s 
    INNER JOIN uyeler u ON s.uye_id = u.uye_id 
    ORDER BY s.siparis_tarih DESC 
    LIMIT $offset, $limit
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Sipariş Yönetimi</h1>
            <p class="text-gray-600 mt-1">Tüm siparişleri görüntüleyin ve yönetin</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Sipariş</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tamamlanan</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $statusCounts['tamamlandi'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Beklemede</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $statusCounts['beklemede'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Başarısız</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $statusCounts['basarisiz'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Tüm Siparişler</h2>
                <div class="relative">
                    <input type="text" class="w-full sm:w-64 px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ara...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($orders)): ?>
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-posta</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">#<?php echo $order['siparis_id']; ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($order['uye_adi']); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($order['uye_eposta']); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($order['siparis_toplam']); ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php
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
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClasses[$order['odeme_durum']]; ?>">
                                                <?php echo $statusTexts[$order['odeme_durum']]; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo date('d.m.Y H:i', strtotime($order['siparis_tarih'])); ?></td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button class="view-order inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        data-id="<?php echo $order['siparis_id']; ?>">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Detay
                                                </button>
                                                <div class="relative inline-block text-left">
                                                    <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            id="menu-button-<?php echo $order['siparis_id']; ?>" aria-expanded="false" aria-haspopup="true">
                                                        <i class="fas fa-cog mr-1"></i>
                                                        İşlem
                                                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                                    </button>
                                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
                                                         id="menu-<?php echo $order['siparis_id']; ?>" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-<?php echo $order['siparis_id']; ?>">
                                                        <div class="py-1" role="none">
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="update_status" value="1">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['siparis_id']; ?>">
                                                                <input type="hidden" name="status" value="beklemede">
                                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Beklemede</button>
                                                            </form>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="update_status" value="1">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['siparis_id']; ?>">
                                                                <input type="hidden" name="status" value="tamamlandi">
                                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Tamamlandı</button>
                                                            </form>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="update_status" value="1">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['siparis_id']; ?>">
                                                                <input type="hidden" name="status" value="basarisiz">
                                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Başarısız</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
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
                                    <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalOrders); ?> arası, toplam <?php echo $totalOrders; ?> sipariş
                                </p>
                            </div>
                            <div class="flex-1 flex justify-between sm:justify-end">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Önceki
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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

<!-- View Order Modal -->
<div id="viewOrderModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Sipariş Detayları</h3>
                            <button id="closeOrderModal" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-4" id="order-details">
                            <!-- Order details will be loaded here via AJAX -->
                            <div class="text-center py-8">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p class="mt-2 text-gray-600">Yükleniyor...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="closeOrderModalBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View order button click
    const viewButtons = document.querySelectorAll('.view-order');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.id;
            loadOrderDetails(orderId);
        });
    });
    
    // Close modal buttons
    document.getElementById('closeOrderModal').addEventListener('click', function() {
        document.getElementById('viewOrderModal').classList.add('hidden');
    });
    
    document.getElementById('closeOrderModalBtn').addEventListener('click', function() {
        document.getElementById('viewOrderModal').classList.add('hidden');
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('viewOrderModal');
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
    
    // Handle dropdown menus
    const dropdownButtons = document.querySelectorAll('[id^="menu-button-"]');
    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const orderId = this.id.replace('menu-button-', '');
            const menu = document.getElementById('menu-' + orderId);
            menu.classList.toggle('hidden');
        });
    });
    
    // Close dropdowns when clicking outside
    window.addEventListener('click', function() {
        const dropdowns = document.querySelectorAll('[id^="menu-"]');
        dropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    });
});

function loadOrderDetails(orderId) {
    fetch('order_details.php?id=' + orderId)
        .then(response => response.text())
        .then(html => {
            document.getElementById('order-details').innerHTML = html;
            document.getElementById('viewOrderModal').classList.remove('hidden');
            
            // Re-bind event listeners for the loaded content
            if (document.getElementById('updateStatusForm')) {
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
                            loadOrderDetails(orderId);
                        } else {
                            // Show error message
                            alert('Hata: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Bir hata oluştu: ' + error.message);
                    });
                });
            }
        })
        .catch(error => {
            document.getElementById('order-details').innerHTML = '<p class="text-red-500">Sipariş detayları yüklenirken bir hata oluştu.</p>';
            document.getElementById('viewOrderModal').classList.remove('hidden');
        });
}
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
