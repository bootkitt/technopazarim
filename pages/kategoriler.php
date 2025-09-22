<?php
$pageTitle = "Kategoriler";

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

// Function to display categories recursively
function displayCategoryGrid($categories, $level = 0) {
    $html = '';
    
    foreach ($categories as $category) {
        $hasChildren = isset($category['children']) && !empty($category['children']);
        
        $html .= '<div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">';
        $html .= '<a href="index.php?page=category&id=' . $category['kategori_id'] . '" class="flex items-center group">';
        $html .= '<div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-4">';
        $html .= '<i class="fas fa-folder text-indigo-600 dark:text-indigo-400"></i>';
        $html .= '</div>';
        $html .= '<div class="flex-grow">';
        $html .= '<h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">' . htmlspecialchars($category['kategori_adi']) . '</h3>';
        $html .= '<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">' . ($hasChildren ? count($category['children']) . ' alt kategori' : 'Ürünler') . '</p>';
        $html .= '</div>';
        $html .= '<i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400"></i>';
        $html .= '</a>';
        
        // Display subcategories if they exist
        if ($hasChildren) {
            $html .= '<div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">';
            $html .= '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">';
            foreach ($category['children'] as $subcat) {
                $html .= '<a href="index.php?page=category&id=' . $subcat['kategori_id'] . '" class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">';
                $html .= '<i class="fas fa-folder-open text-indigo-500 dark:text-indigo-400 mr-2"></i>';
                $html .= '<span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">' . htmlspecialchars($subcat['kategori_adi']) . '</span>';
                $html .= '</a>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
    }
    
    return $html;
}

// Fetch all categories
$stmt = $db->prepare("SELECT * FROM kategoriler ORDER BY kategori_ust_id, kategori_id");
$stmt->execute();
$allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build category tree
$categoryTree = buildCategoryTree($allCategories);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tüm Kategoriler</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
            <?php echo count($allCategories); ?> kategori arasından istediğiniz ürünü bulun
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php echo displayCategoryGrid($categoryTree); ?>
    </div>
</div>