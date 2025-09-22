<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Dijital Stok Kodları';

// Handle form submissions
if ($_POST) {
    // Add new stock codes
    if (isset($_POST['add_stock_codes'])) {
        $product_id = (int)$_POST['product_id'];
        $stock_codes = trim($_POST['stock_codes']);
        
        // Split codes by newline
        $codes = array_filter(array_map('trim', explode("\n", $stock_codes)));
        
        try {
            $added_count = 0;
            foreach ($codes as $code) {
                if (!empty($code)) {
                    $stmt = $db->prepare("INSERT INTO dijital_stok (urun_id, stok_kodu, stok_durum) VALUES (?, ?, 'aktif')");
                    $stmt->execute([$product_id, $code]);
                    $added_count++;
                }
            }
            
            $success = "$added_count adet stok kodu başarıyla eklendi.";
        } catch (Exception $e) {
            $error = "Stok kodları eklenirken bir hata oluştu: " . $e->getMessage();
        }
    }
    
    // Delete stock code
    if (isset($_POST['delete_stock_code'])) {
        $stock_id = (int)$_POST['stock_id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM dijital_stok WHERE stok_id = ?");
            $stmt->execute([$stock_id]);
            
            $success = "Stok kodu başarıyla silindi.";
        } catch (Exception $e) {
            $error = "Stok kodu silinirken bir hata oluştu: " . $e->getMessage();
        }
    }
    
    // Bulk delete stock codes
    if (isset($_POST['bulk_delete'])) {
        $stock_ids = $_POST['stock_ids'] ?? [];
        
        if (!empty($stock_ids)) {
            try {
                $placeholders = str_repeat('?,', count($stock_ids) - 1) . '?';
                $stmt = $db->prepare("DELETE FROM dijital_stok WHERE stok_id IN ($placeholders)");
                $stmt->execute($stock_ids);
                
                $success = count($stock_ids) . " adet stok kodu başarıyla silindi.";
            } catch (Exception $e) {
                $error = "Stok kodları silinirken bir hata oluştu: " . $e->getMessage();
            }
        }
    }
}

// Get product ID for filtering
$product_filter = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Fetch products for dropdown
$stmt = $db->prepare("SELECT urun_id, urun_baslik FROM urunler ORDER BY urun_baslik ASC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch stock codes with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query based on filter
$where_clause = "";
$params = [];

if ($product_filter > 0) {
    $where_clause = "WHERE ds.urun_id = ?";
    $params[] = $product_filter;
}

// Get total stock codes count
$stmt = $db->prepare("SELECT COUNT(*) FROM dijital_stok ds $where_clause");
$stmt->execute($params);
$totalStockCodes = $stmt->fetchColumn();

$totalPages = ceil($totalStockCodes / $limit);

// Get stock codes with product names
$stmt = $db->prepare("
    SELECT ds.*, u.urun_baslik 
    FROM dijital_stok ds 
    LEFT JOIN urunler u ON ds.urun_id = u.urun_id 
    $where_clause
    ORDER BY ds.stok_id DESC 
    LIMIT $offset, $limit
");
$stmt->execute($params);
$stockCodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dijital Stok Kodları</h1>
            <p class="text-gray-600 mt-1">Ürünler için dijital stok kodlarını yönetin</p>
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

        <!-- Add Stock Codes Form -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Yeni Stok Kodları Ekle</h2>
            </div>
            <div class="p-6">
                <form method="POST">
                    <input type="hidden" name="add_stock_codes" value="1">
                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Ürün</label>
                            <select id="product_id" name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Ürün Seçin</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['urun_id']; ?>"><?php echo htmlspecialchars($product['urun_baslik']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="stock_codes" class="block text-sm font-medium text-gray-700 mb-1">Stok Kodları</label>
                            <textarea id="stock_codes" name="stock_codes" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Her satıra bir stok kodu girin..." required></textarea>
                            <p class="mt-1 text-sm text-gray-500">Her satıra bir stok kodu girin. Boş satırlar göz ardı edilecektir.</p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-plus mr-2"></i>
                            Stok Kodları Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stock Codes List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Stok Kodları</h2>
                <div class="flex items-center space-x-4">
                    <form method="GET" class="flex items-center">
                        <select name="product_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tüm Ürünler</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['urun_id']; ?>" <?php echo $product_filter == $product['urun_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($product['urun_baslik']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-filter mr-1"></i> Filtrele
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($stockCodes)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-key text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz stok kodu eklenmemiş</h3>
                        <p class="text-gray-500">Yukarıdaki formu kullanarak stok kodları ekleyebilirsiniz</p>
                    </div>
                <?php else: ?>
                    <form method="POST" id="bulkDeleteForm">
                        <input type="hidden" name="bulk_delete" value="1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Kodu</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanılan Sipariş</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($stockCodes as $stockCode): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm">
                                                <input type="checkbox" name="stock_ids[]" value="<?php echo $stockCode['stok_id']; ?>" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded stock-checkbox">
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $stockCode['stok_id']; ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($stockCode['urun_baslik'] ?? 'Bilinmeyen Ürün'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-900 font-mono"><?php echo htmlspecialchars(substr($stockCode['stok_kodu'], 0, 20)) . (strlen($stockCode['stok_kodu']) > 20 ? '...' : ''); ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <?php
                                                $statusClasses = [
                                                    'aktif' => 'bg-green-100 text-green-800',
                                                    'kullanildi' => 'bg-blue-100 text-blue-800',
                                                    'iptal' => 'bg-red-100 text-red-800'
                                                ];
                                                $statusTexts = [
                                                    'aktif' => 'Aktif',
                                                    'kullanildi' => 'Kullanıldı',
                                                    'iptal' => 'İptal'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClasses[$stockCode['stok_durum']]; ?>">
                                                    <?php echo $statusTexts[$stockCode['stok_durum']]; ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo $stockCode['kullanilan_siparis_id'] ? '#' . $stockCode['kullanilan_siparis_id'] : '-'; ?></td>
                                            <td class="px-4 py-3 text-right text-sm font-medium">
                                                <form method="POST" class="inline-block">
                                                    <input type="hidden" name="delete_stock_code" value="1">
                                                    <input type="hidden" name="stock_id" value="<?php echo $stockCode['stok_id']; ?>">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="return confirm('Bu stok kodunu silmek istediğinize emin misiniz?')">
                                                        <i class="fas fa-trash mr-1"></i> Sil
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Bulk Actions -->
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <button type="button" id="bulkDeleteBtn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                                    <i class="fas fa-trash mr-1"></i> Seçilenleri Sil
                                </button>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav class="flex items-center justify-between">
                                    <div class="hidden sm:block">
                                        <p class="text-sm text-gray-700">
                                            <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalStockCodes); ?> arası, toplam <?php echo $totalStockCodes; ?> stok kodu
                                        </p>
                                    </div>
                                    <div class="flex-1 flex justify-between sm:justify-end">
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?php echo $page - 1; ?>&product_id=<?php echo $product_filter; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Önceki
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <a href="?page=<?php echo $page + 1; ?>&product_id=<?php echo $product_filter; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Sonraki
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkboxes
    const selectAllCheckbox = document.getElementById('select-all');
    const stockCheckboxes = document.querySelectorAll('.stock-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    
    selectAllCheckbox.addEventListener('change', function() {
        stockCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });
    
    stockCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkDeleteButton);
    });
    
    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.stock-checkbox:checked').length;
        bulkDeleteBtn.disabled = checkedCount === 0;
    }
    
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedCount = document.querySelectorAll('.stock-checkbox:checked').length;
        if (checkedCount > 0) {
            if (confirm(checkedCount + ' adet stok kodunu silmek istediğinize emin misiniz?')) {
                bulkDeleteForm.submit();
            }
        }
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>