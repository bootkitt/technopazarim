<?php
// Get product ID
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Track product view
if ($productId > 0) {
    trackProductView($db, $productId, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
}

if ($productId <= 0) {
    header('Location: index.php?page=products');
    exit;
}

// Fetch product details
$stmt = $db->prepare("SELECT u.*, k.kategori_adi FROM urunler u INNER JOIN kategoriler k ON u.urun_kategori = k.kategori_id WHERE u.urun_id = ? AND u.urun_durum = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php?page=products');
    exit;
}

// Update view count
$stmt = $db->prepare("UPDATE urunler SET urun_hit = urun_hit + 1 WHERE urun_id = ?");
$stmt->execute([$productId]);

// Fetch product images (if multiple)
$images = [];
if ($product['urun_resimler']) {
    $images = explode(',', $product['urun_resimler']);
} elseif ($product['urun_resim']) {
    $images[] = $product['urun_resim'];
} else {
    $images[] = 'assets/images/default-product.png';
}

// Fetch product reviews
$stmt = $db->prepare("SELECT y.*, u.uye_adi FROM yorumlar y INNER JOIN uyeler u ON y.uye_id = u.uye_id WHERE y.urun_id = ? AND y.yorum_durum = 1 ORDER BY y.yorum_tarih DESC");
$stmt->execute([$productId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$avgRating = 0;
if (!empty($reviews)) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += $review['yorum_puan'];
    }
    $avgRating = round($totalRating / count($reviews), 1);
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php?page=home" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                    <i class="fas fa-home mr-2"></i>Anasayfa
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                    <a href="index.php?page=products" class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Ürünler</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($product['urun_baslik']); ?></span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div>
            <div class="relative overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                <?php if (count($images) > 1): ?>
                    <div id="productCarousel" class="carousel relative">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'block' : 'hidden'; ?>">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product['urun_baslik']); ?>" class="w-full h-auto object-contain max-h-[500px]">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                        <?php foreach ($images as $index => $image): ?>
                            <button class="carousel-indicator w-3 h-3 rounded-full <?php echo $index === 0 ? 'bg-white' : 'bg-white/50'; ?>" data-index="<?php echo $index; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <img src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($product['urun_baslik']); ?>" class="w-full h-auto object-contain max-h-[500px]">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo htmlspecialchars($product['urun_baslik']); ?></h1>
            
            <div class="flex items-center mb-6">
                <span class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 text-sm font-medium px-3 py-1 rounded-full">
                    <?php echo htmlspecialchars($product['kategori_adi']); ?>
                </span>
                <div class="ml-4 flex items-center">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'; ?>"></i>
                    <?php endfor; ?>
                    <span class="ml-2 text-gray-600 dark:text-gray-400">(<?php echo count($reviews); ?> yorum)</span>
                </div>
            </div>
            
            <div class="mb-8">
                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-4">
                    <?php echo formatCurrency($product['urun_fiyat']); ?>
                </div>
                
                <div class="prose prose-indigo dark:prose-invert max-w-none mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ürün Açıklaması</h3>
                    <p class="text-gray-600 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($product['urun_aciklama'])); ?></p>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Stok Durumu</h3>
                    <?php if ($product['urun_stok'] > 0): ?>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="text-green-600 dark:text-green-400 font-medium">Stokta (<?php echo $product['urun_stok']; ?> adet)</span>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 mr-2"></i>
                            <span class="text-red-600 dark:text-red-400 font-medium">Stokta yok</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <?php if ($product['urun_stok'] > 0): ?>
                    <div class="flex items-center">
                        <button class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-l-md text-gray-600 dark:text-gray-300 decrease-quantity">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="quantity" class="w-16 h-8 text-center border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" value="1" min="1" max="<?php echo $product['urun_stok']; ?>">
                        <button class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-r-md text-gray-600 dark:text-gray-300 increase-quantity">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <input type="hidden" id="quantity" value="1">
                    <div class="text-red-600 dark:text-red-400 font-medium mb-2">
                        <i class="fas fa-exclamation-circle mr-1"></i> Bu ürün stokta yok. Yine de sepete ekleyebilirsiniz.
                    </div>
                <?php endif; ?>
                <button class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover-lift add-to-cart" data-product-id="<?php echo $product['urun_id']; ?>">
                    <i class="fas fa-shopping-cart mr-2"></i> Sepete Ekle
                </button>
                <a href="index.php?page=checkout&product=<?php echo $product['urun_id']; ?>" class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all hover-lift">
                    <i class="fas fa-bolt mr-2"></i> Hemen Satın Al
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="mt-16">
        <div class="border-t border-gray-200 dark:border-gray-700 pt-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Ürün Yorumları</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <?php if (isLoggedIn()): ?>
                        <div class="card rounded-2xl overflow-hidden mb-8">
                            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Yorum Yap</h3>
                            </div>
                            <div class="p-6">
                                <form id="reviewForm">
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Puanınız</label>
                                        <div id="rating" class="flex">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star rating-star text-2xl cursor-pointer text-gray-300 dark:text-gray-600 hover:text-yellow-400" data-rating="<?php echo $i; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" id="reviewRating" name="rating" value="5">
                                    </div>
                                    <div class="mb-6">
                                        <label for="reviewTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Başlık</label>
                                        <input type="text" id="reviewTitle" name="title" required 
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div class="mb-6">
                                        <label for="reviewContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yorumunuz</label>
                                        <textarea id="reviewContent" name="content" rows="4" required 
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        Yorumu Gönder
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card rounded-2xl overflow-hidden mb-8">
                            <div class="p-6">
                                <div class="text-center py-8">
                                    <i class="fas fa-user-lock text-gray-400 dark:text-gray-500 text-3xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Yorum yapmak için giriş yapmalısınız</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-6">Yorum yapmak için hesabınıza giriş yapın veya yeni bir hesap oluşturun.</p>
                                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                                        <a href="index.php?page=login" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                                            <i class="fas fa-sign-in-alt mr-2"></i> Giriş Yap
                                        </a>
                                        <a href="index.php?page=kayit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-white bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <i class="fas fa-user-plus mr-2"></i> Kayıt Ol
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-comment-slash text-gray-400 dark:text-gray-500 text-3xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Henüz yorum yapılmamış</h3>
                            <p class="text-gray-500 dark:text-gray-400">Bu ürüne henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($reviews as $review): ?>
                                <div class="card rounded-2xl overflow-hidden">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($review['yorum_baslik']); ?></h4>
                                                <div class="flex items-center mt-1">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?php echo $i <= $review['yorum_puan'] ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'; ?>"></i>
                                                    <?php endfor; ?>
                                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                                        by <?php echo htmlspecialchars($review['uye_adi']); ?> - <?php echo date('d.m.Y', strtotime($review['yorum_tarih'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mt-4 text-gray-600 dark:text-gray-300">
                                            <?php echo nl2br(htmlspecialchars($review['yorum_icerik'])); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <div class="card rounded-2xl overflow-hidden sticky top-8">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Ürün Bilgileri</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Ortalama Puan</h4>
                                    <div class="flex items-center mt-1">
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $avgRating; ?></span>
                                        <div class="ml-2 flex">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">(<?php echo count($reviews); ?> yorum)</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</h4>
                                    <p class="mt-1 text-gray-900 dark:text-white"><?php echo htmlspecialchars($product['kategori_adi']); ?></p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Stok Durumu</h4>
                                    <p class="mt-1 <?php echo $product['urun_stok'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                                        <?php echo $product['urun_stok'] > 0 ? 'Stokta (' . $product['urun_stok'] . ' adet)' : 'Stokta yok'; ?>
                                    </p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Görüntülenme</h4>
                                    <p class="mt-1 text-gray-900 dark:text-white"><?php echo $product['urun_hit']; ?> kez görüntülendi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carousel functionality
    const carouselItems = document.querySelectorAll('.carousel-item');
    const carouselIndicators = document.querySelectorAll('.carousel-indicator');
    
    if (carouselItems.length > 1) {
        carouselIndicators.forEach(indicator => {
            indicator.addEventListener('click', function() {
                const index = this.dataset.index;
                
                // Hide all items
                carouselItems.forEach(item => {
                    item.classList.add('hidden');
                    item.classList.remove('block');
                });
                
                // Show selected item
                carouselItems[index].classList.remove('hidden');
                carouselItems[index].classList.add('block');
                
                // Update indicators
                carouselIndicators.forEach((ind, i) => {
                    if (i == index) {
                        ind.classList.remove('bg-white/50');
                        ind.classList.add('bg-white');
                    } else {
                        ind.classList.remove('bg-white');
                        ind.classList.add('bg-white/50');
                    }
                });
            });
        });
    }
    
    // Rating stars
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('reviewRating');
    
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            ratingInput.value = rating;
            
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('text-gray-300', 'dark:text-gray-600');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300', 'dark:text-gray-600');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const rating = this.dataset.rating;
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('text-gray-300', 'dark:text-gray-600');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300', 'dark:text-gray-600');
                }
            });
        });
    });
    
    // Reset stars on mouseout
    document.getElementById('rating').addEventListener('mouseout', function() {
        const currentRating = ratingInput.value;
        ratingStars.forEach((s, index) => {
            if (index < currentRating) {
                s.classList.remove('text-gray-300', 'dark:text-gray-600');
                s.classList.add('text-yellow-400');
            } else {
                s.classList.remove('text-yellow-400');
                s.classList.add('text-gray-300', 'dark:text-gray-600');
            }
        });
    });
    
    // Quantity selector functionality
    const decreaseButton = document.querySelector('.decrease-quantity');
    const increaseButton = document.querySelector('.increase-quantity');
    const quantityInput = document.getElementById('quantity');
    
    if (decreaseButton && increaseButton && quantityInput) {
        decreaseButton.addEventListener('click', function() {
            let value = parseInt(quantityInput.value) || 1;
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        increaseButton.addEventListener('click', function() {
            let value = parseInt(quantityInput.value) || 1;
            const max = parseInt(quantityInput.max) || 999;
            if (value < max) {
                quantityInput.value = value + 1;
            }
        });
        
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value) || 1;
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
        });
    }
    
    // Add to cart functionality - using event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart')) {
            const button = e.target.closest('.add-to-cart');
            
            // Prevent multiple clicks
            if (button.classList.contains('adding-to-cart')) {
                return;
            }
            
            button.classList.add('adding-to-cart');
            
            console.log('Add to cart button clicked');
            const productId = button.dataset.productId;
            const quantity = parseInt(document.getElementById('quantity')?.value) || 1;
            
            // Send form data with quantity
            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                button.classList.remove('adding-to-cart');
                if (data.success) {
                    showMessage('Ürün sepete eklendi', 'success');
                    // Update cart count in header
                    updateCartCount(data.cart_count);
                } else {
                    showMessage(data.message || 'Bir hata oluştu', 'danger');
                }
            })
            .catch(error => {
                button.classList.remove('adding-to-cart');
                console.error('Error:', error);
                showMessage('Bir hata oluştu', 'danger');
            });
        }
    });
    
    // Review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('product_id', <?php echo $productId; ?>);
            
            fetch('ajax/submit_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Yorumunuz başarıyla gönderildi!', 'success');
                    reviewForm.reset();
                    // Reset stars
                    ratingInput.value = 5;
                    ratingStars.forEach((s, index) => {
                        if (index < 5) {
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                            s.classList.add('text-yellow-400');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300', 'dark:text-gray-600');
                        }
                    });
                } else {
                    showMessage(data.message || 'Bir hata oluştu.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Bir hata oluştu.', 'danger');
            });
        });
    }
});
</script>