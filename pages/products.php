<?php
// Get filter parameters
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Function to build hierarchical category tree
function buildCategoryTree($categories, $parentId = 0) {
    $branch = array();
    
    foreach ($categories as $category) {
        if ($category['kategori_ust_id'] == $parentId) {
            $children = buildCategoryTree($categories, $category['kategori_id']);
            if ($children) {
                $category['children'] = $children;
            }
            $branch[] = $category;
        }
    }
    
    return $branch;
}

// Function to display categories recursively for filter
function displayCategoryFilter($categories, $selectedId = 0, $level = 0) {
    $html = '';
    
    foreach ($categories as $category) {
        $indent = str_repeat('&nbsp;', $level * 3);
        $selected = ($selectedId == $category['kategori_id']) ? 'selected' : '';
        $hasChildren = isset($category['children']) && !empty($category['children']);
        
        $html .= '<option value="' . $category['kategori_id'] . '" ' . $selected . '>' . $indent . htmlspecialchars($category['kategori_adi']) . '</option>';
        
        // Display subcategories if they exist
        if ($hasChildren) {
            $html .= displayCategoryFilter($category['children'], $selectedId, $level + 1);
        }
    }
    
    return $html;
}

// Fetch all categories
$stmt = $db->prepare("SELECT * FROM kategoriler ORDER BY kategori_ust_id, kategori_id");
$stmt->execute();
$allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build category tree
$categoryTree = buildCategoryTree($allCategories);

// Build query based on filters
$whereClause = "WHERE urun_durum = 1";
$params = [];

if ($category > 0) {
    // Get all subcategories of the selected category
    $subcategoryIds = [$category];
    
    // Recursive function to get all subcategory IDs
    function getSubcategoryIds($parentId, $categories, &$ids) {
        foreach ($categories as $category) {
            if ($category['kategori_ust_id'] == $parentId) {
                $ids[] = $category['kategori_id'];
                getSubcategoryIds($category['kategori_id'], $categories, $ids);
            }
        }
    }
    
    getSubcategoryIds($category, $allCategories, $subcategoryIds);
    
    // Create placeholders for the IN clause
    $placeholders = str_repeat('?,', count($subcategoryIds) - 1) . '?';
    $whereClause .= " AND urun_kategori IN ($placeholders)";
    $params = array_merge($params, $subcategoryIds);
}

if (!empty($search)) {
    $whereClause .= " AND (urun_baslik LIKE ? OR urun_aciklama LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Build order clause
$orderClause = "ORDER BY urun_id DESC";
switch ($sort) {
    case 'price_low':
        $orderClause = "ORDER BY urun_fiyat ASC";
        break;
    case 'price_high':
        $orderClause = "ORDER BY urun_fiyat DESC";
        break;
    case 'popular':
        $orderClause = "ORDER BY urun_hit DESC";
        break;
}

// Get total count
$stmt = $db->prepare("SELECT COUNT(*) FROM urunler $whereClause");
$stmt->execute($params);
$totalProducts = $stmt->fetchColumn();

// Get products
$stmt = $db->prepare("SELECT * FROM urunler $whereClause $orderClause LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages
$totalPages = ceil($totalProducts / $limit);

// Get category information for breadcrumb
$categoryInfo = null;
if ($category > 0) {
    $stmt = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id = ?");
    $stmt->execute([$category]);
    $categoryInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get parent category if this is a subcategory
    if ($categoryInfo && $categoryInfo['kategori_ust_id'] > 0) {
        $stmt = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id = ?");
        $stmt->execute([$categoryInfo['kategori_ust_id']]);
        $parentCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?php if ($categoryInfo): ?>
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                        <i class="fas fa-home mr-2"></i>
                        Ana Sayfa
                    </a>
                </li>
                <?php if (isset($parentCategory)): ?>
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
                            <?php echo htmlspecialchars($categoryInfo['kategori_adi']); ?>
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    <?php endif; ?>
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <?php if ($categoryInfo): ?>
                <?php echo htmlspecialchars($categoryInfo['kategori_adi']); ?> Ürünleri
            <?php else: ?>
                Ürünler
            <?php endif; ?>
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
            <?php echo $totalProducts; ?> ürün bulundu
        </p>
    </div>
    
    <!-- Filters moved above products -->
    <div class="card rounded-2xl p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="page" value="products">
            
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Arama
                </label>
                <input type="text" id="search" name="search" 
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    value="<?php echo htmlspecialchars($search); ?>" placeholder="Ürün ara...">
            </div>
            
            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Kategori
                </label>
                <select id="category" name="category" 
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="0">Tüm Kategoriler</option>
                    <?php echo displayCategoryFilter($categoryTree, $category); ?>
                </select>
            </div>
            
            <!-- Sort -->
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Sırala
                </label>
                <select id="sort" name="sort" 
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>En Yeniler</option>
                    <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Fiyat (Düşükten Yükseğe)</option>
                    <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Fiyat (Yüksekten Düşüğe)</option>
                    <option value="popular" <?php echo ($sort == 'popular') ? 'selected' : ''; ?>>Popülerlik</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Filtrele
                </button>
            </div>
        </form>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters - made sticky -->
        <div class="lg:w-1/4">
            <div class="card rounded-2xl p-6 sticky top-8">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kategoriler</h2>
                <div class="space-y-2 max-h-[calc(100vh-200px)] overflow-y-auto pr-2">
                    <?php foreach ($categoryTree as $cat): ?>
                        <a href="index.php?page=category&id=<?php echo $cat['kategori_id']; ?>" 
                           class="block p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors <?php echo ($category == $cat['kategori_id']) ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'; ?>">
                            <?php echo htmlspecialchars($cat['kategori_adi']); ?>
                        </a>
                        <?php if (isset($cat['children']) && !empty($cat['children'])): ?>
                            <div class="ml-4 border-l border-gray-200 dark:border-gray-700 pl-3">
                                <?php foreach ($cat['children'] as $subcat): ?>
                                    <a href="index.php?page=category&id=<?php echo $subcat['kategori_id']; ?>" 
                                       class="block p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors <?php echo ($category == $subcat['kategori_id']) ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'; ?>">
                                        <?php echo htmlspecialchars($subcat['kategori_adi']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Products -->
        <div class="lg:w-3/4">
            <?php if (empty($products)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500 text-5xl mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Ürün Bulunamadı</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Aradığınız kriterlere uygun ürün bulunamadı.</p>
                    <a href="index.php?page=products" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-redo mr-2"></i> Filtreleri Sıfırla
                    </a>
                </div>
            <?php else: ?>
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
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="mt-12 flex justify-center">
                        <nav class="inline-flex rounded-md shadow">
                            <?php if ($page > 1): ?>
                                <a href="?page=products&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page_num=<?php echo ($page - 1); ?>" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-l-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-chevron-left mr-2"></i> Önceki
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=products&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page_num=<?php echo $i; ?>" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium <?php echo ($i == $page) ? 'z-10 bg-indigo-50 dark:bg-indigo-900/30 border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600'; ?> border hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=products&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page_num=<?php echo ($page + 1); ?>" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-r-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Sonraki <i class="fas fa-chevron-right ml-2"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>