<?php
require_once __DIR__ . '/../config.php';

// Track visit
trackVisit($db, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null, 'homepage');

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

// Function to display categories with limit
function displayCategories($categories, $level = 0, $mainLimit = 4, $subLimit = 3) {
    $html = '';
    $mainCount = 0;
    
    foreach ($categories as $category) {
        // Limit main categories
        if ($level == 0 && $mainCount >= $mainLimit) {
            break;
        }
        
        $indent = str_repeat('&nbsp;', $level * 4);
        $hasChildren = isset($category['children']) && !empty($category['children']);
        $caret = $hasChildren ? '<i class="fas fa-caret-right text-gray-400 ml-1 text-xs"></i>' : '';
        
        $html .= '<a href="index.php?page=category&id=' . $category['kategori_id'] . '" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">';
        $html .= '<div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-3">';
        $html .= '<i class="fas fa-folder text-indigo-600 dark:text-indigo-400"></i>';
        $html .= '</div>';
        $html .= '<span class="text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 font-medium">' . $indent . htmlspecialchars($category['kategori_adi']) . '</span>';
        $html .= $caret;
        $html .= '<i class="fas fa-chevron-right text-gray-400 ml-auto text-sm opacity-0 group-hover:opacity-100 transition-opacity"></i>';
        $html .= '</a>';
        
        // Display subcategories if they exist, with limit
        if ($hasChildren) {
            $html .= '<div class="ml-6 border-l border-gray-200 dark:border-gray-700">';
            $subCount = 0;
            foreach ($category['children'] as $subCategory) {
                if ($subCount >= $subLimit) {
                    // Add a link to view all subcategories if there are more
                    $html .= '<a href="index.php?page=category&id=' . $category['kategori_id'] . '" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group text-indigo-600 dark:text-indigo-400 text-sm">';
                    $html .= '<span class="font-medium">Tümünü Gör (' . count($category['children']) . ')</span>';
                    $html .= '<i class="fas fa-chevron-right text-indigo-400 ml-auto text-sm"></i>';
                    $html .= '</a>';
                    break;
                }
                
                $html .= '<a href="index.php?page=category&id=' . $subCategory['kategori_id'] . '" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">';
                $html .= '<div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">';
                $html .= '<i class="fas fa-folder-open text-purple-600 dark:text-purple-400"></i>';
                $html .= '</div>';
                $html .= '<span class="text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400 font-medium">' . htmlspecialchars($subCategory['kategori_adi']) . '</span>';
                $html .= '<i class="fas fa-chevron-right text-gray-400 ml-auto text-sm opacity-0 group-hover:opacity-100 transition-opacity"></i>';
                $html .= '</a>';
                $subCount++;
            }
            $html .= '</div>';
        }
        
        if ($level == 0) {
            $mainCount++;
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

// Fetch featured products
$stmt = $db->prepare("SELECT * FROM urunler WHERE urun_durum = 1 ORDER BY urun_hit DESC LIMIT 6");
$stmt->execute();
$featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section with Modern Design -->
<section class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-900 dark:to-gray-900/90">
  <div class="container mx-auto px-4 py-16 md:py-24">
    <div class="flex flex-col lg:flex-row items-center">
      <div class="lg:w-1/2 mb-12 lg:mb-0 lg:pr-12">
        <div class="mb-6">
          <span class="inline-flex items-center px-4 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-semibold">
            <i class="fas fa-crown mr-2"></i>
            Premium Dijital Pazar
          </span>
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
          Dijital Ürünlerde 
          <span class="text-indigo-600 dark:text-indigo-400">Güvenilir</span> Adres
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl">
          En yeni ve popüler dijital ürünleri uygun fiyatlarla satın alın. Anında teslimat, 7/24 destek ve %100 güvenli alışveriş deneyimi.
        </p>
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <a href="index.php?page=products" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-xl inline-flex items-center transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
            <i class="fas fa-shopping-bag mr-3"></i>
            Ürünleri Keşfet
          </a>
          <a href="#featured" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-bold py-4 px-8 rounded-xl inline-flex items-center transition-all duration-300 shadow">
            <i class="fas fa-star mr-3 text-indigo-600"></i>
            Öne Çıkanlar
          </a>
        </div>
      </div>
      <div class="lg:w-1/2 flex justify-center">
        <div class="relative w-full max-w-lg">
          <div class="absolute -top-6 -left-6 w-64 h-64 bg-indigo-200 dark:bg-indigo-900/30 rounded-full mix-blend-multiply filter blur-2xl opacity-30"></div>
          <div class="absolute -bottom-6 -right-6 w-64 h-64 bg-purple-200 dark:bg-purple-900/30 rounded-full mix-blend-multiply filter blur-2xl opacity-30"></div>
          
          <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="p-6">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Popüler Ürünler</h3>
                <span class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 text-xs font-bold px-2.5 py-0.5 rounded-full">SON</span>
              </div>
              
              <div class="space-y-4">
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-laptop text-indigo-600 dark:text-indigo-400"></i>
                  </div>
                  <div class="ml-4 flex-grow">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Premium Yazılım</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Anında teslimat</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">₺299</p>
                  </div>
                </div>
                
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-purple-600 dark:text-purple-400"></i>
                  </div>
                  <div class="ml-4 flex-grow">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Mobil Uygulamalar</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">7/24 destek</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">₺149</p>
                  </div>
                </div>
                
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-gamepad text-pink-600 dark:text-pink-400"></i>
                  </div>
                  <div class="ml-4 flex-grow">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Oyunlar</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Çok oyunculu</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">₺89</p>
                  </div>
                </div>
              </div>
              
              <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-all duration-300">
                  Tüm Ürünleri Gör
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Main Content with Sidebar Layout -->
<section class="py-16">
  <div class="container mx-auto px-4">
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Sidebar with Categories -->
      <div class="lg:w-1/4">
        <div class="card rounded-2xl p-6 sticky top-24">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Kategoriler</h2>
          <div class="space-y-1">
            <?php echo displayCategories($categoryTree, 0, 4, 3); ?>
          </div>
          
          <!-- Additional Sidebar Content -->
          <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Fırsatlar</h3>
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl p-4 text-white">
              <p class="font-bold">Yeni Üye Kampanyası</p>
              <p class="text-sm opacity-90 mt-1">İlk alışverişinizde %15 indirim</p>
              <a href="index.php?page=kayit" class="mt-3 bg-white text-indigo-600 text-sm font-bold py-2 px-4 rounded-lg w-full inline-block text-center">Hemen Üye Ol</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="lg:w-3/4">
        <!-- Featured Products -->
        <section id="featured" class="mb-16">
          <div class="flex justify-between items-center mb-8">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Öne Çıkan Ürünler</h2>
              <p class="text-gray-600 dark:text-gray-300">En çok tercih edilen dijital ürünler</p>
            </div>
            <a href="index.php?page=products" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium inline-flex items-center transition-colors">
              Tüm Ürünler
              <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredProducts as $product): ?>
              <div class="card rounded-2xl overflow-hidden hover-lift transition-all duration-300 group">
                <div class="relative">
                  <?php if ($product['urun_resim']): ?>
                    <img src="<?php echo htmlspecialchars($product['urun_resim']); ?>" alt="<?php echo htmlspecialchars($product['urun_baslik']); ?>" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                  <?php else: ?>
                    <div class="bg-gray-200 border-2 border-dashed w-full h-48 flex items-center justify-center">
                      <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                  <?php endif; ?>
                  <div class="absolute top-3 right-3 bg-indigo-600 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                    ÖNE ÇIKAN
                  </div>
                </div>
                
                <div class="p-5">
                  <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($product['urun_baslik']); ?></h3>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400"><?php echo formatCurrency($product['urun_fiyat']); ?></span>
                  </div>
                  <p class="text-gray-600 dark:text-gray-300 text-sm mb-4"><?php echo htmlspecialchars(substr($product['urun_aciklama'], 0, 80)); ?>...</p>
                  <div class="flex justify-between items-center">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                      <i class="fas fa-star text-yellow-400 mr-1"></i>
                      <span>4.8</span>
                    </div>
                    <a href="index.php?page=product&id=<?php echo $product['urun_id']; ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                      İncele
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
        
        <!-- Why Choose Us Section -->
        <section class="bg-gradient-to-br from-gray-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-8">
          <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Neden Bizi Tercih Etmelisiniz?</h2>
            <p class="text-gray-600 dark:text-gray-300">Müşteri memnuniyetini ön planda tutan hizmet anlayışımız</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
              <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-bolt text-indigo-600 dark:text-indigo-400 text-xl"></i>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Anında Teslimat</h3>
              <p class="text-gray-600 dark:text-gray-300">Ödemeniz onaylandıktan hemen sonra ürününüzü teslim alırsınız.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
              <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-shield-alt text-purple-600 dark:text-purple-400 text-xl"></i>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Güvenli Alışveriş</h3>
              <p class="text-gray-600 dark:text-gray-300">3D Secure ile güvenli ödeme yapın, bilgileriniz korunur.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
              <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-headset text-pink-600 dark:text-pink-400 text-xl"></i>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">7/24 Destek</h3>
              <p class="text-gray-600 dark:text-gray-300">Herhangi bir sorunuzda destek ekibimiz size yardımcı olur.</p>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</section>