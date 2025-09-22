<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Dijital Stok Yönetimi';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_stock'])) {
        $product_id = (int)$_POST['product_id'];
        $stock = (int)$_POST['stock'];
        
        try {
            $stmt = $db->prepare("UPDATE urunler SET urun_stok = ? WHERE urun_id = ?");
            $stmt->execute([$stock, $product_id]);
            $success = "Stok başarıyla güncellendi.";
        } catch (Exception $e) {
            $error = "Stok güncellenirken bir hata oluştu: " . $e->getMessage();
        }
    }
    
    // Handle stock delivery (row-based)
    if (isset($_POST['deliver_stock'])) {
        $product_id = (int)$_POST['product_id'];
        $delivery_code = trim($_POST['delivery_code']);
        
        try {
            // Check if this is a valid stock code
            $stmt = $db->prepare("SELECT * FROM dijital_stok WHERE urun_id = ? AND stok_kodu = ? AND stok_durum = 'aktif'");
            $stmt->execute([$product_id, $delivery_code]);
            $stockCode = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stockCode) {
                // Mark the stock code as used
                $stmt = $db->prepare("UPDATE dijital_stok SET stok_durum = 'kullanildi', kullanilan_siparis_id = NULL WHERE stok_id = ?");
                $stmt->execute([$stockCode['stok_id']]);
                
                // Decrease product stock count
                $stmt = $db->prepare("UPDATE urunler SET urun_stok = urun_stok - 1 WHERE urun_id = ?");
                $stmt->execute([$product_id]);
                
                $success = "Stok teslimi başarıyla gerçekleştirildi.";
            } else {
                $error = "Geçersiz veya kullanılmış stok kodu.";
            }
        } catch (Exception $e) {
            $error = "Stok teslimi sırasında bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Fetch products with stock info
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total products count
$stmt = $db->prepare("SELECT COUNT(*) FROM urunler");
$stmt->execute();
$totalProducts = $stmt->fetchColumn();

$totalPages = ceil($totalProducts / $limit);

// Get products with category names
$stmt = $db->prepare("
    SELECT u.*, k.kategori_adi 
    FROM urunler u 
    LEFT JOIN kategoriler k ON u.urun_kategori = k.kategori_id 
    ORDER BY u.urun_baslik ASC 
    LIMIT $offset, $limit
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dijital Stok Yönetimi</h1>
            <p class="text-gray-600 mt-1">Ürün stoklarını yönetin ve takip edin</p>
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

        <!-- Stock Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Ürün</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalProducts; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Stokta Olan</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $inStock = 0;
                            foreach ($products as $product) {
                                if ($product['urun_stok'] > 0) $inStock++;
                            }
                            echo $inStock;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Stokta Olmayan</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $outOfStock = 0;
                            foreach ($products as $product) {
                                if ($product['urun_stok'] <= 0) $outOfStock++;
                            }
                            echo $outOfStock;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Stock List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Ürün Stokları</h2>
            </div>
            <div class="p-6">
                <?php if (empty($products)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-cubes text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz ürün eklenmemiş</h3>
                        <p class="text-gray-500">Ürün ekledikten sonra stok bilgilerini burada yönetebilirsiniz</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Türü</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($products as $product): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $product['urun_id']; ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($product['urun_baslik']); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($product['kategori_adi'] ?? 'Kategori Yok'); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <?php if ($product['urun_stok'] > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Stoklu</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Stoksuz</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $product['urun_stok']; ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($product['urun_stok'] > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Stokta</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Stokta Yok</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <?php if ($product['urun_stok'] > 0): ?>
                                                    <button class="update-stock inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            data-id="<?php echo $product['urun_id']; ?>" 
                                                            data-name="<?php echo htmlspecialchars($product['urun_baslik']); ?>" 
                                                            data-stock="<?php echo $product['urun_stok']; ?>">
                                                        <i class="fas fa-edit mr-1"></i> Stok Güncelle
                                                    </button>
                                                <?php else: ?>
                                                    <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" disabled>
                                                        <i class="fas fa-edit mr-1"></i> Stok Güncelle
                                                    </button>
                                                <?php endif; ?>
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
                                    <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalProducts); ?> arası, toplam <?php echo $totalProducts; ?> ürün
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

<!-- Update Stock Modal -->
<div id="updateStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="update_stock" value="1">
                <input type="hidden" id="stock_product_id" name="product_id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Stok Güncelle</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="product_name" readonly>
                                </div>
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Yeni Stok Miktarı</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="stock" name="stock" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Güncelle
                    </button>
                    <button type="button" id="stock_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update stock button click
    const updateButtons = document.querySelectorAll('.update-stock');
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const stock = this.dataset.stock;
            
            document.getElementById('stock_product_id').value = id;
            document.getElementById('product_name').value = name;
            document.getElementById('stock').value = stock;
            
            document.getElementById('updateStockModal').classList.remove('hidden');
        });
    });
    
    // Cancel buttons
    document.getElementById('stock_cancel').addEventListener('click', function() {
        document.getElementById('updateStockModal').classList.add('hidden');
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const updateModal = document.getElementById('updateStockModal');
        
        if (event.target === updateModal) {
            updateModal.classList.add('hidden');
        }
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>