<?php
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($category_id <= 0) {
    header('Location: index.php?page=products');
    exit;
}

// Get category information
$stmt = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php?page=products');
    exit;
}

// Get subcategories
$stmt = $db->prepare("SELECT * FROM kategoriler WHERE kategori_ust_id = ? ORDER BY kategori_adi");
$stmt->execute([$category_id]);
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get products in this category and its subcategories
$subcategoryIds = [$category_id];

// Recursive function to get all subcategory IDs
function getSubcategoryIds($parentId, $db, &$ids) {
    $stmt = $db->prepare("SELECT kategori_id FROM kategoriler WHERE kategori_ust_id = ?");
    $stmt->execute([$parentId]);
    $children = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($children as $childId) {
        $ids[] = $childId;
        getSubcategoryIds($childId, $db, $ids);
    }
}

getSubcategoryIds($category_id, $db, $subcategoryIds);

// Create placeholders for the IN clause
$placeholders = str_repeat('?,', count($subcategoryIds) - 1) . '?';
$stmt = $db->prepare("SELECT * FROM urunler WHERE urun_durum = 1 AND urun_kategori IN ($placeholders) ORDER BY urun_id DESC");
$stmt->execute($subcategoryIds);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get parent category if this is a subcategory
$parentCategory = null;
if ($category['kategori_ust_id'] > 0) {
    $stmt = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id = ?");
    $stmt->execute([$category['kategori_ust_id']]);
    $parentCategory = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                    <i class="fas fa-home mr-2"></i>
                    Ana Sayfa
                </a>
            </li>
            <?php if ($parentCategory): ?>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="index.php?page=category&id=<?php echo $parentCategory['kategori_id']; ?>" class="text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                            <?php echo htmlspecialchars($parentCategory['kategori_adi']); ?>
                        </a>
                    </div>
                </li>
            <?php endif; ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        <?php echo htmlspecialchars($category['kategori_adi']); ?>
                    </span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2"><?php echo htmlspecialchars($category['kategori_adi']); ?></h1>
        <?php if (!empty($category['kategori_aciklama'])): ?>
            <p class="text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($category['kategori_aciklama']); ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Subcategories -->
    <?php if (!empty($subcategories)): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Alt Kategoriler</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($subcategories as $subcategory): ?>
                    <a href="index.php?page=category&id=<?php echo $subcategory['kategori_id']; ?>" class="card rounded-2xl p-6 text-center hover-lift transition-all group">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800 transition-colors">
                            <i class="fas fa-folder text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($subcategory['kategori_adi']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Products -->
    <?php if (!empty($products)): ?>
        <div>
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?php echo htmlspecialchars($category['kategori_adi']); ?> Ürünleri
                </h2>
                <a href="index.php?page=products&category=<?php echo $category_id; ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                    Tüm Ürünleri Gör
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="card rounded-2xl overflow-hidden hover-lift transition-all">
                        <?php if ($product['urun_resim']): ?>
                            <img src="<?php echo htmlspecialchars($product['urun_resim']); ?>" alt="<?php echo htmlspecialchars($product['urun_baslik']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="bg-gray-200 dark:bg-gray-700 w-full h-48 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 dark:text-gray-500 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2"><?php echo htmlspecialchars($product['urun_baslik']); ?></h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4"><?php echo htmlspecialchars(substr($product['urun_aciklama'], 0, 100)); ?>...</p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?php echo formatCurrency($product['urun_fiyat']); ?></span>
                                <a href="index.php?page=product&id=<?php echo $product['urun_id']; ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    İncele
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif (empty($subcategories)): ?>
        <div class="text-center py-12">
            <i class="fas fa-box-open text-gray-400 dark:text-gray-500 text-5xl mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Bu kategoride henüz ürün bulunmuyor</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Yeni ürünler yakında eklenecek.</p>
            <a href="index.php?page=products" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-shopping-bag mr-2"></i> Tüm Ürünleri Gör
            </a>
        </div>
    <?php endif; ?>
</div>