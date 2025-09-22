<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Ürün Yönetimi';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category_id = (int)$_POST['category_id'];
        $stock = (int)$_POST['stock'];
        $stock_type = $_POST['stock_type'] ?? 'license'; // default to 'license'
        
        // Handle image upload
        $main_image = null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $upload_dir = __DIR__ . '/../uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $filename = uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
                    $main_image = 'uploads/products/' . $filename;
                }
            }
        }
        
        if (!empty($name) && $price > 0) {
            try {
                $stmt = $db->prepare("INSERT INTO urunler (urun_baslik, urun_aciklama, urun_fiyat, urun_kategori, urun_stok, urun_tip, urun_resim, urun_durum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $category_id, $stock, $stock_type, $main_image, 1]);
                $success = "Ürün başarıyla eklendi.";
            } catch (Exception $e) {
                $error = "Ürün eklenirken bir hata oluştu: " . $e->getMessage();
            }
        } else {
            $error = "Ürün adı ve fiyatı boş olamaz.";
        }
    } elseif (isset($_POST['edit_product'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category_id = (int)$_POST['category_id'];
        $stock = (int)$_POST['stock'];
        $stock_type = $_POST['stock_type'] ?? 'license'; // default to 'license'
        
        // Handle image upload
        $main_image = null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $upload_dir = __DIR__ . '/../uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $filename = uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
                    $main_image = 'uploads/products/' . $filename;
                }
            }
        }
        
        if (!empty($name) && $price > 0) {
            try {
                if ($main_image) {
                    $stmt = $db->prepare("UPDATE urunler SET urun_baslik = ?, urun_aciklama = ?, urun_fiyat = ?, urun_kategori = ?, urun_stok = ?, urun_tip = ?, urun_resim = ? WHERE urun_id = ?");
                    $stmt->execute([$name, $description, $price, $category_id, $stock, $stock_type, $main_image, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE urunler SET urun_baslik = ?, urun_aciklama = ?, urun_fiyat = ?, urun_kategori = ?, urun_stok = ?, urun_tip = ? WHERE urun_id = ?");
                    $stmt->execute([$name, $description, $price, $category_id, $stock, $stock_type, $id]);
                }
                $success = "Ürün başarıyla güncellendi.";
            } catch (Exception $e) {
                $error = "Ürün güncellenirken bir hata oluştu: " . $e->getMessage();
            }
        } else {
            $error = "Ürün adı ve fiyatı boş olamaz.";
        }
    } elseif (isset($_POST['delete_product'])) {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM urunler WHERE urun_id = ?");
            $stmt->execute([$id]);
            $success = "Ürün başarıyla silindi.";
        } catch (Exception $e) {
            $error = "Ürün silinirken bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Fetch categories for dropdown with hierarchical structure
$stmt = $db->prepare("SELECT kategori_id, kategori_adi, kategori_ust_id FROM kategoriler ORDER BY kategori_ust_id, kategori_adi ASC");
$stmt->execute();
$allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build hierarchical category list
function buildCategoryOptions($categories, $parentId = 0, $level = 0) {
    $options = [];
    foreach ($categories as $category) {
        if ($category['kategori_ust_id'] == $parentId) {
            $category['kategori_adi'] = str_repeat('— ', $level) . $category['kategori_adi'];
            $options[] = $category;
            $options = array_merge($options, buildCategoryOptions($categories, $category['kategori_id'], $level + 1));
        }
    }
    return $options;
}

$categories = buildCategoryOptions($allCategories);

// Fetch products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
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
            <h1 class="text-2xl font-bold text-gray-900">Ürün Yönetimi</h1>
            <p class="text-gray-600 mt-1">Ürünleri ekleyin, düzenleyin ve silin</p>
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
                        <p class="text-sm font-medium text-gray-600">Stoktaki Ürünler</p>
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
                        <p class="text-sm font-medium text-gray-600">Düşük Stok</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $lowStock = 0;
                            foreach ($products as $product) {
                                if ($product['urun_stok'] > 0 && $product['urun_stok'] < 5) $lowStock++;
                            }
                            echo $lowStock;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Stokta Yok</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $outOfStock = 0;
                            foreach ($products as $product) {
                                if ($product['urun_stok'] == 0) $outOfStock++;
                            }
                            echo $outOfStock;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product Form -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Yeni Ürün Ekle</h2>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" name="add_product" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="name" name="name" required>
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Fiyat (₺)</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="category_id" name="category_id" required>
                                <option value="">Kategori Seçin</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['kategori_id']; ?>">
                                        <?php echo htmlspecialchars($category['kategori_adi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="stock_type" class="block text-sm font-medium text-gray-700 mb-1">Stok Türü</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="stock_type" name="stock_type" required>
                                <option value="license">Stoklu Ürün</option>
                                <option value="service">Stoksuz Satış</option>
                            </select>
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="stock" name="stock" min="0" value="0">
                        </div>
                        <div>
                            <label for="main_image" class="block text-sm font-medium text-gray-700 mb-1">Ana Resim</label>
                            <div class="drag-drop-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition-colors" id="dragDropArea">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 mb-1">Dosyaları buraya sürükleyin</p>
                                <p class="text-gray-400 text-sm mb-2">veya</p>
                                <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Dosya Seçin</button>
                                <input type="file" class="hidden" id="main_image" name="main_image" accept="image/*">
                            </div>
                            <div id="imagePreview" class="mt-2 hidden">
                                <img src="" alt="Önizleme" class="max-w-full h-32 object-contain">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-plus mr-2"></i>Ürün Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Tüm Ürünler</h2>
                <div class="relative">
                    <input type="text" class="w-full sm:w-64 px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ara...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($products)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Henüz ürün eklenmemiş</h3>
                        <p class="text-gray-500">Yukarıdaki formu kullanarak yeni ürün ekleyebilirsiniz</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Türü</th>
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
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo number_format($product['urun_fiyat'], 2, ',', '.'); ?> ₺</td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($product['urun_stok'] == 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Stokta Yok</span>
                                            <?php elseif ($product['urun_stok'] < 5): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><?php echo $product['urun_stok']; ?> adet</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><?php echo $product['urun_stok']; ?> adet</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php
                                            $stockTypeTexts = [
                                                'license' => 'Stoklu',
                                                'service' => 'Stoksuz'
                                            ];
                                            $stockType = $product['urun_tip'] ?? 'license';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $stockType === 'license' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'; ?>">
                                                <?php echo $stockTypeTexts[$stockType]; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($product['urun_stok'] > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button class="edit-product inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        data-id="<?php echo $product['urun_id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($product['urun_baslik']); ?>" 
                                                        data-description="<?php echo htmlspecialchars($product['urun_aciklama'] ?? ''); ?>" 
                                                        data-price="<?php echo $product['urun_fiyat']; ?>" 
                                                        data-category="<?php echo $product['urun_kategori']; ?>" 
                                                        data-stock="<?php echo $product['urun_stok']; ?>"
                                                        data-stocktype="<?php echo $product['urun_tip'] ?? 'license'; ?>">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Düzenle
                                                </button>
                                                <button class="delete-product inline-flex items-center px-3 py-1.5 border border-transparent text-sm rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                        data-id="<?php echo $product['urun_id']; ?>">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Sil
                                                </button>
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

<!-- Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_product" value="1">
                <input type="hidden" id="edit_id" name="id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Ürün Düzenle</h3>
                            <div class="mt-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_name" name="name" required>
                                    </div>
                                    <div>
                                        <label for="edit_price" class="block text-sm font-medium text-gray-700 mb-1">Fiyat (₺)</label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_price" name="price" step="0.01" min="0" required>
                                    </div>
                                    <div>
                                        <label for="edit_category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_category_id" name="category_id" required>
                                            <option value="">Kategori Seçin</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['kategori_id']; ?>">
                                                    <?php echo htmlspecialchars($category['kategori_adi']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="edit_stock_type" class="block text-sm font-medium text-gray-700 mb-1">Stok Türü</label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_stock_type" name="stock_type" required>
                                            <option value="license">Stoklu Ürün</option>
                                            <option value="service">Stoksuz Satış</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="edit_stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_stock" name="stock" min="0">
                                    </div>
                                    <div>
                                        <label for="edit_main_image" class="block text-sm font-medium text-gray-700 mb-1">Ana Resim</label>
                                        <div class="drag-drop-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition-colors" id="editDragDropArea">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-600 mb-1">Dosyaları buraya sürükleyin</p>
                                            <p class="text-gray-400 text-sm mb-2">veya</p>
                                            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Dosya Seçin</button>
                                            <input type="file" class="hidden" id="edit_main_image" name="main_image" accept="image/*">
                                        </div>
                                        <div id="editImagePreview" class="mt-2 hidden">
                                            <img src="" alt="Önizleme" class="max-w-full h-32 object-contain">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="edit_description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Güncelle
                    </button>
                    <button type="button" id="edit_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Product Modal -->
<div id="deleteProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="delete_product" value="1">
                <input type="hidden" id="delete_id" name="id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Ürün Sil</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Sil
                    </button>
                    <button type="button" id="delete_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit product button click
    const editButtons = document.querySelectorAll('.edit-product');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;
            const price = this.dataset.price;
            const categoryId = this.dataset.category;
            const stock = this.dataset.stock;
            const stockType = this.dataset.stocktype || 'license';
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_stock_type').value = stockType;
            
            document.getElementById('editProductModal').classList.remove('hidden');
        });
    });
    
    // Delete product button click
    const deleteButtons = document.querySelectorAll('.delete-product');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            
            document.getElementById('delete_id').value = id;
            
            document.getElementById('deleteProductModal').classList.remove('hidden');
        });
    });
    
    // Cancel buttons
    document.getElementById('edit_cancel').addEventListener('click', function() {
        document.getElementById('editProductModal').classList.add('hidden');
    });
    
    document.getElementById('delete_cancel').addEventListener('click', function() {
        document.getElementById('deleteProductModal').classList.add('hidden');
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const editModal = document.getElementById('editProductModal');
        const deleteModal = document.getElementById('deleteProductModal');
        
        if (event.target === editModal) {
            editModal.classList.add('hidden');
        }
        
        if (event.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });
    
    // Drag and drop functionality for add product form
    const dragDropArea = document.getElementById('dragDropArea');
    const fileInput = document.getElementById('main_image');
    const imagePreview = document.getElementById('imagePreview');
    
    if (dragDropArea && fileInput) {
        dragDropArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        dragDropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dragDropArea.classList.add('border-blue-400', 'bg-blue-50');
        });
        
        dragDropArea.addEventListener('dragleave', () => {
            dragDropArea.classList.remove('border-blue-400', 'bg-blue-50');
        });
        
        dragDropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dragDropArea.classList.remove('border-blue-400', 'bg-blue-50');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                previewImage(e.dataTransfer.files[0], imagePreview);
            }
        });
        
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                previewImage(fileInput.files[0], imagePreview);
            }
        });
    }
    
    // Drag and drop functionality for edit product form
    const editDragDropArea = document.getElementById('editDragDropArea');
    const editFileInput = document.getElementById('edit_main_image');
    const editImagePreview = document.getElementById('editImagePreview');
    
    if (editDragDropArea && editFileInput) {
        editDragDropArea.addEventListener('click', () => {
            editFileInput.click();
        });
        
        editDragDropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            editDragDropArea.classList.add('border-blue-400', 'bg-blue-50');
        });
        
        editDragDropArea.addEventListener('dragleave', () => {
            editDragDropArea.classList.remove('border-blue-400', 'bg-blue-50');
        });
        
        editDragDropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            editDragDropArea.classList.remove('border-blue-400', 'bg-blue-50');
            
            if (e.dataTransfer.files.length) {
                editFileInput.files = e.dataTransfer.files;
                previewImage(e.dataTransfer.files[0], editImagePreview);
            }
        });
        
        editFileInput.addEventListener('change', () => {
            if (editFileInput.files.length) {
                previewImage(editFileInput.files[0], editImagePreview);
            }
        });
    }
    
    // Image preview function
    function previewImage(file, previewContainer) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = previewContainer.querySelector('img');
                if (img) {
                    img.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>