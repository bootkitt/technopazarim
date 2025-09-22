<?php
// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'index.php?page=cart';
    header('Location: index.php?page=login');
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];

// Calculate cart total
$total = 0;
$cartItems = [];

if (!empty($cart)) {
    // Fetch product details for cart items
    $placeholders = str_repeat('?,', count($cart) - 1) . '?';
    $productIds = array_keys($cart);
    
    $stmt = $db->prepare("SELECT * FROM urunler WHERE urun_id IN ($placeholders) AND urun_durum = 1");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map products by ID for easy access
    $productMap = [];
    foreach ($products as $product) {
        $productMap[$product['urun_id']] = $product;
    }
    
    // Build cart items with product details
    foreach ($cart as $productId => $quantity) {
        if (isset($productMap[$productId])) {
            $product = $productMap[$productId];
            $itemTotal = $product['urun_fiyat'] * $quantity;
            $total += $itemTotal;
            
            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'item_total' => $itemTotal
            ];
        }
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Sepetim</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Sepetinizdeki ürünleri buradan yönetebilirsiniz</p>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <div class="text-center py-12">
            <i class="fas fa-shopping-cart text-gray-400 dark:text-gray-500 text-5xl mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sepetiniz Boş</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Alışverişe devam etmek için ürün ekleyin.</p>
            <a href="index.php?page=products" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-shopping-cart mr-2"></i>
                Ürünlere Gözat
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="card rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Sepetteki Ürünler</h2>
                    </div>
                    <div class="p-6">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="flex flex-col sm:flex-row items-center py-6 border-b border-gray-200 dark:border-gray-700 last:border-0" data-product-id="<?php echo $item['product']['urun_id']; ?>">
                                <div class="w-24 h-24 flex-shrink-0 mb-4 sm:mb-0 sm:mr-6">
                                    <?php if ($item['product']['urun_resim']): ?>
                                        <img src="<?php echo htmlspecialchars($item['product']['urun_resim']); ?>" alt="<?php echo htmlspecialchars($item['product']['urun_baslik']); ?>" class="w-full h-full object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="bg-gray-200 dark:bg-gray-700 rounded-lg w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex-1 text-center sm:text-left mb-4 sm:mb-0">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($item['product']['urun_baslik']); ?></h3>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1"><?php echo htmlspecialchars(substr($item['product']['urun_aciklama'], 0, 100)); ?>...</p>
                                    <div class="mt-2 font-bold text-indigo-600 dark:text-indigo-400"><?php echo formatCurrency($item['product']['urun_fiyat']); ?></div>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row items-center gap-4">
                                    <div class="flex items-center">
                                        <button class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-l-md text-gray-600 dark:text-gray-300 decrease-quantity" data-product-id="<?php echo $item['product']['urun_id']; ?>">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input w-16 h-8 text-center border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" data-product-id="<?php echo $item['product']['urun_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['urun_stok']; ?>">
                                        <button class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-r-md text-gray-600 dark:text-gray-300 increase-quantity" data-product-id="<?php echo $item['product']['urun_id']; ?>">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="text-center sm:text-right">
                                        <div class="font-bold text-gray-900 dark:text-white"><?php echo formatCurrency($item['item_total']); ?></div>
                                        <button class="mt-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 remove-from-cart" data-product-id="<?php echo $item['product']['urun_id']; ?>">
                                            <i class="fas fa-trash mr-1"></i> Kaldır
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="card rounded-2xl overflow-hidden sticky top-8">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Sipariş Özeti</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Alt Toplam</span>
                                <span class="font-medium text-gray-900 dark:text-white"><?php echo formatCurrency($total); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Kargo</span>
                                <span class="font-medium text-green-600 dark:text-green-400">Ücretsiz</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Toplam</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white cart-total"><?php echo formatCurrency($total); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 space-y-4">
                            <a href="index.php?page=checkout" class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-lock mr-2"></i> Ödeme Yap
                            </a>
                            <a href="index.php?page=products" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Alışverişe Devam Et
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity change functionality
    const decreaseButtons = document.querySelectorAll('.decrease-quantity');
    const increaseButtons = document.querySelectorAll('.increase-quantity');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    // Log that the DOM is loaded and elements are found
    console.log('Cart page loaded. Found buttons:', {
        decrease: decreaseButtons.length,
        increase: increaseButtons.length,
        inputs: quantityInputs.length
    });
    
    decreaseButtons.forEach(button => {
        // Remove any existing event listeners to prevent duplicates
        const clone = button.cloneNode(true);
        button.parentNode.replaceChild(clone, button);
        
        clone.addEventListener('click', function() {
            console.log('Decrease button clicked');
            const productId = this.dataset.productId;
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let value = parseInt(input.value) || 1;
            if (value > 1) {
                value--;
                input.value = value;
                updateCartQuantity(productId, value);
            }
        });
    });
    
    increaseButtons.forEach(button => {
        // Remove any existing event listeners to prevent duplicates
        const clone = button.cloneNode(true);
        button.parentNode.replaceChild(clone, button);
        
        clone.addEventListener('click', function() {
            console.log('Increase button clicked');
            const productId = this.dataset.productId;
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let value = parseInt(input.value) || 1;
            const max = parseInt(input.max) || 999;
            if (value < max) {
                value++;
                input.value = value;
                updateCartQuantity(productId, value);
            }
        });
    });
    
    quantityInputs.forEach(input => {
        // Remove any existing event listeners to prevent duplicates
        const clone = input.cloneNode(true);
        input.parentNode.replaceChild(clone, input);
        
        clone.addEventListener('change', function() {
            console.log('Quantity input changed');
            const productId = this.dataset.productId;
            let value = parseInt(this.value) || 1;
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
            updateCartQuantity(productId, value);
        });
    });
    
    // Remove from cart functionality
    const removeFromCartButtons = document.querySelectorAll('.remove-from-cart');
    console.log('Remove buttons found:', removeFromCartButtons.length);
    
    removeFromCartButtons.forEach(button => {
        // Remove any existing event listeners to prevent duplicates
        const clone = button.cloneNode(true);
        button.parentNode.replaceChild(clone, button);
        
        clone.addEventListener('click', function() {
            console.log('Remove button clicked');
            const productId = this.dataset.productId;
            removeFromCart(productId);
        });
    });
});

function updateCartQuantity(productId, quantity) {
    console.log('Updating cart quantity:', { productId, quantity });
    
    // Make sure we're sending valid data
    const data = {
        product_id: parseInt(productId),
        quantity: parseInt(quantity)
    };
    
    console.log('Sending data:', data);
    
    fetch('ajax/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response received:', response);
        return response.json();
    })
    .then(data => {
        console.log('Parsed data:', data);
        if (data.success) {
            // Update cart total
            document.querySelector('.cart-total').textContent = data.total;
            // Update page title cart count
            updateCartCount(data.cart_count);
            // Show success message
            showMessage('Sepet güncellendi', 'success');
        } else {
            showMessage(data.message || 'Bir hata oluştu', 'danger');
            // Log debug information
            if (data.debug_raw) {
                console.log('Raw data sent:', data.debug_raw);
            }
            if (data.debug_parsed) {
                console.log('Parsed data received:', data.debug_parsed);
            }
            if (data.post_data) {
                console.log('Post data received:', data.post_data);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Bir hata oluştu', 'danger');
    });
}

function removeFromCart(productId) {
    console.log('Removing from cart:', productId);
    
    // Make sure we're sending valid data
    const data = {
        product_id: parseInt(productId)
    };
    
    console.log('Sending data:', data);
    
    fetch('ajax/remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response received:', response);
        return response.json();
    })
    .then(data => {
        console.log('Parsed data:', data);
        if (data.success) {
            // Reload the page to update the cart display
            location.reload();
        } else {
            showMessage(data.message || 'Bir hata oluştu', 'danger');
            // Log debug information
            if (data.debug_raw) {
                console.log('Raw data sent:', data.debug_raw);
            }
            if (data.debug_parsed) {
                console.log('Parsed data received:', data.debug_parsed);
            }
            if (data.post_data) {
                console.log('Post data received:', data.post_data);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Bir hata oluştu', 'danger');
    });
}

function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        if (count > 0) {
            element.textContent = count;
            element.style.display = 'flex';
        } else {
            element.style.display = 'none';
        }
    });
}
</script>