<?php
// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'index.php?page=checkout';
    header('Location: index.php?page=login');
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];

// Get active payment methods from settings
$paymentSettings = getPaymentSettings($db);
$activePaymentMethods = isset($paymentSettings['active_payment_methods']) ? json_decode($paymentSettings['active_payment_methods'], true) : ['bank_transfer'];
$defaultPaymentMethod = $paymentSettings['odeme_yontemi'] ?? 'bank_transfer';

// Check if a specific product is requested for direct purchase
$directProductId = isset($_GET['product']) ? intval($_GET['product']) : 0;
if ($directProductId > 0 && !isset($cart[$directProductId])) {
    // Add product to cart for direct purchase
    $cart[$directProductId] = 1;
    $_SESSION['cart'] = $cart;
}

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

// Fetch user details
$stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

// Get first and last name from user's full name
$fullName = $user['uye_adi'];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];
$lastName = implode(' ', array_slice($nameParts, 1));

// Check if 2FA is required for this transaction
$requires2FA = $user['uye_2fa_enabled'] == 1;
$show2FAForm = false;
$errorMessage = '';

// Handle 2FA verification if required
if ($requires2FA && $_POST && isset($_POST['verify_2fa_checkout'])) {
    $code = isset($_POST['two_factor_code']) ? trim($_POST['two_factor_code']) : '';
    
    if (empty($code)) {
        $errorMessage = 'Lütfen 2FA kodunu girin.';
        $show2FAForm = true;
    } else {
        // Verify 2FA code
        require_once 'includes/GoogleAuthenticator.php';
        $ga = new GoogleAuthenticator();
        
        if ($ga->verifyCode($user['uye_2fa_secret'], $code)) {
            // 2FA verified, proceed with checkout
            $_SESSION['2fa_verified_for_checkout'] = true;
            $show2FAForm = false;
        } else {
            $errorMessage = 'Geçersiz 2FA kodu. Lütfen tekrar deneyin.';
            $show2FAForm = true;
        }
    }
}

// Show 2FA form if required and not yet verified
if ($requires2FA && (!isset($_SESSION['2fa_verified_for_checkout']) || !$_SESSION['2fa_verified_for_checkout'])) {
    $show2FAForm = true;
}
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ödeme Yap</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Siparişinizi tamamlamak için bilgilerinizi girin</p>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <div class="text-center py-12">
            <i class="fas fa-shopping-cart text-gray-400 dark:text-gray-500 text-5xl mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sepetiniz Boş</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Ödeme yapmak için sepetinize ürün ekleyin.</p>
            <a href="index.php?page=products" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-shopping-cart mr-2"></i>
                Ürünlere Gözat
            </a>
        </div>
    <?php elseif ($show2FAForm): ?>
        <!-- 2FA Verification Form -->
        <div class="max-w-md mx-auto">
            <div class="card rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">İki Faktörlü Kimlik Doğrulama</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Güvenliğiniz için ödeme yapmadan önce 2FA doğrulaması gereklidir.</p>
                    
                    <?php if ($errorMessage): ?>
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-6">
                            <label for="two_factor_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">6 Haneli 2FA Kodu</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="two_factor_code" name="two_factor_code" maxlength="6" pattern="[0-9]{6}" required placeholder="000000">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Google Authenticator, Authy veya benzeri bir uygulamadan alınan kodu girin.</p>
                        </div>
                        <button type="submit" name="verify_2fa_checkout" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i> Doğrula ve Devam Et
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center text-sm">
                        <a href="index.php?page=2fa" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">2FA Ayarları</a> | 
                        <a href="index.php?page=account" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Hesap Ayarları</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="card rounded-2xl overflow-hidden mb-6">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Fatura Adresi</h2>
                    </div>
                    <div class="p-6">
                        <form id="checkoutForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="firstName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ad *</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="firstName" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>
                                </div>
                                <div>
                                    <label for="lastName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Soyad *</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="lastName" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-posta Adresi *</label>
                                <input type="email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="email" name="email" value="<?php echo htmlspecialchars($user['uye_eposta']); ?>" required>
                            </div>
                            <div class="mb-6">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon *</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="phone" name="phone" value="<?php echo htmlspecialchars($user['uye_telefon'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adres</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="address" name="address" rows="3"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Şehir</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="city" name="city">
                                </div>
                                <div>
                                    <label for="zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posta Kodu</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="zip" name="zip">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Ödeme Yöntemi</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($activePaymentMethods)): ?>
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium">Şu anda ödeme kabul etmiyoruz</p>
                                    </div>
                                </div>
                            </div>
                            <button id="completeOrder" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                <i class="fas fa-lock mr-2"></i> Ödemeyi Tamamla
                            </button>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php if (in_array('shopier', $activePaymentMethods)): ?>
                                    <div class="flex items-center">
                                        <input id="shopier" name="paymentMethod" type="radio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500" value="shopier" <?php echo ($defaultPaymentMethod == 'shopier') ? 'checked' : ''; ?>>
                                        <label for="shopier" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <div class="flex items-center">
                                                <i class="fab fa-cc-visa text-2xl mr-2"></i>
                                                <i class="fab fa-cc-mastercard text-2xl mr-2"></i>
                                                <i class="fab fa-cc-amex text-2xl mr-2"></i>
                                                <span>Kredi Kartı (Shopier ile)</span>
                                            </div>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (in_array('bank_transfer', $activePaymentMethods)): ?>
                                    <div class="flex items-center">
                                        <input id="bankTransfer" name="paymentMethod" type="radio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500" value="bankTransfer" <?php echo ($defaultPaymentMethod == 'bank_transfer') ? 'checked' : ''; ?>>
                                        <label for="bankTransfer" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <div class="flex items-center">
                                                <i class="fas fa-university text-2xl mr-2"></i>
                                                <span>Banka Havalesi</span>
                                            </div>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!in_array('shopier', $activePaymentMethods) && !in_array('bank_transfer', $activePaymentMethods)): ?>
                                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium">Şu anda ödeme kabul etmiyoruz</p>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="completeOrder" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                        <i class="fas fa-lock mr-2"></i> Ödemeyi Tamamla
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="card rounded-2xl overflow-hidden sticky top-8">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Sipariş Özeti</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4 mb-6">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($item['product']['urun_baslik']); ?> (x<?php echo $item['quantity']; ?>)</span>
                                    <span class="font-medium text-gray-900 dark:text-white"><?php echo formatCurrency($item['item_total']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Alt Toplam</span>
                                <span class="font-medium text-gray-900 dark:text-white"><?php echo formatCurrency($total); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Kargo</span>
                                <span class="font-medium text-green-600 dark:text-green-400">Ücretsiz</span>
                            </div>
                            <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Toplam</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white"><?php echo formatCurrency($total); ?></span>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button id="completeOrder" class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <i class="fas fa-lock mr-2"></i> Ödemeyi Tamamla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const completeOrderBtn = document.getElementById('completeOrder');
    
    if (completeOrderBtn) {
        completeOrderBtn.addEventListener('click', function(e) {
            // Prevent any default form submission
            e.preventDefault();
            
            // Get form data
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            
            // Get payment method
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (paymentMethod) {
                formData.append('payment_method', paymentMethod.value);
            }
            
            // Add cart items
            const cartItems = <?php echo json_encode(array_map(function($item) {
                return [
                    'product_id' => $item['product']['urun_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['urun_fiyat']
                ];
            }, $cartItems)); ?>;
            
            formData.append('cart_items', JSON.stringify(cartItems));
            
            // Validate required fields (matching the form fields that have 'required' attribute)
            const firstName = form.querySelector('#firstName').value.trim();
            const lastName = form.querySelector('#lastName').value.trim();
            const email = form.querySelector('#email').value.trim();
            const phone = form.querySelector('#phone').value.trim();
            
            if (!firstName || !lastName || !email || !phone) {
                showMessage('Tüm alanları doldurun', 'danger');
                return;
            }
            
            // Show loading indicator
            completeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> İşleniyor...';
            completeOrderBtn.disabled = true;
            
            // Submit order
            fetch('ajax/process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset button
                completeOrderBtn.innerHTML = '<i class="fas fa-lock mr-2"></i> Ödemeyi Tamamla';
                completeOrderBtn.disabled = false;
                
                if (data.success) {
                    if (data.redirect_url) {
                        // Check if the redirect_url is a data URL containing HTML form (for Shopier)
                        if (data.redirect_url.startsWith('data:text/html,')) {
                            // Extract the HTML form and submit it
                            const formHtml = decodeURIComponent(data.redirect_url.substring('data:text/html,'.length));
                            document.write(formHtml);
                        } else {
                            // Redirect to payment gateway
                            window.location.href = data.redirect_url;
                        }
                    } else if (data.bank_transfer_info) {
                        // Show bank transfer information with confirmation button
                        showBankTransferInfo(data);
                    } else {
                        // Show success message for other payment methods
                        showMessage('Siparişiniz başarıyla oluşturuldu!', 'success');
                        // Clear 2FA verification session
                        fetch('ajax/clear_2fa_session.php', {
                            method: 'POST'
                        });
                        setTimeout(() => {
                            window.location.href = 'index.php?page=account&section=orders';
                        }, 2000);
                    }
                } else {
                    showMessage(data.message || 'Bir hata oluştu.', 'danger');
                }
            })
            .catch(error => {
                // Reset button
                completeOrderBtn.innerHTML = '<i class="fas fa-lock mr-2"></i> Ödemeyi Tamamla';
                completeOrderBtn.disabled = false;
                
                console.error('Error:', error);
                showMessage('Bir hata oluştu.', 'danger');
            });
        });
    }
});

// Function to display bank transfer information with confirmation button
function showBankTransferInfo(data) {
    // Create a modal or information panel to display bank transfer details
    const bankInfo = data.bank_transfer_info;
    const orderId = data.order_id;
    const orderTotal = data.order_total;
    
    // Create HTML for bank transfer information
    let bankInfoHtml = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Banka Havalesi</h3>
                    <button onclick="closeBankTransferInfo()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Siparişiniz başarıyla oluşturuldu. Lütfen aşağıdaki banka hesabına <strong>${formatCurrency(orderTotal)}</strong> tutarında ödeme yapınız.
                    </p>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Banka Bilgileri</h4>
                        <div class="space-y-2 text-sm">
    `;
    
    if (bankInfo.bank_name) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Banka:</span><span class="font-medium">${bankInfo.bank_name}</span></div>`;
    }
    
    if (bankInfo.account_holder) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Hesap Sahibi:</span><span class="font-medium">${bankInfo.account_holder}</span></div>`;
    }
    
    if (bankInfo.iban) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">IBAN:</span><span class="font-medium font-mono">${bankInfo.iban}</span></div>`;
    }
    
    if (bankInfo.account_number) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Hesap No:</span><span class="font-medium">${bankInfo.account_number}</span></div>`;
    }
    
    if (bankInfo.branch_name) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Şube:</span><span class="font-medium">${bankInfo.branch_name}</span></div>`;
    }
    
    if (bankInfo.branch_code) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Şube Kodu:</span><span class="font-medium">${bankInfo.branch_code}</span></div>`;
    }
    
    if (bankInfo.swift_code) {
        bankInfoHtml += `<div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">SWIFT/Kod:</span><span class="font-medium">${bankInfo.swift_code}</span></div>`;
    }
    
    bankInfoHtml += `
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Ödeme Talimatları</h4>
                        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <li>• Ödeme yaparken açıklama kısmına <strong>Sipariş #${orderId}</strong> yazınız</li>
                            <li>• Ödeme onaylandıktan sonra siparişiniz işleme alınacaktır</li>
                            <li>• Ödeme onayı 1-2 iş günü sürebilir</li>
                        </ul>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="closeBankTransferInfo()" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Kapat
                    </button>
                    <button id="confirmPaymentBtn" data-order-id="${orderId}" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Ödemeyi Yaptım
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add the HTML to the document
    const bankInfoDiv = document.createElement('div');
    bankInfoDiv.innerHTML = bankInfoHtml;
    bankInfoDiv.id = 'bankTransferModal';
    document.body.appendChild(bankInfoDiv);
    
    // Add event listener to the confirmation button
    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        confirmBankTransfer(orderId);
    });
}

// Function to confirm bank transfer payment
function confirmBankTransfer(orderId) {
    // Show loading indicator
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> İşleniyor...';
    confirmBtn.disabled = true;
    
    // Send confirmation request
    fetch('ajax/confirm_bank_transfer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({order_id: orderId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showMessage(data.message || 'Ödeme bildiriminiz alındı!', 'success');
            
            // Close the modal after a delay
            setTimeout(() => {
                closeBankTransferInfo();
                // Redirect to orders page
                window.location.href = 'index.php?page=account&section=orders';
            }, 2000);
        } else {
            // Show error message
            showMessage(data.message || 'Bir hata oluştu.', 'danger');
            
            // Reset button
            confirmBtn.innerHTML = 'Ödemeyi Yaptım';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Bir hata oluştu.', 'danger');
        
        // Reset button
        confirmBtn.innerHTML = 'Ödemeyi Yaptım';
        confirmBtn.disabled = false;
    });
}

// Function to close bank transfer information
function closeBankTransferInfo() {
    const bankInfoModal = document.getElementById('bankTransferModal');
    if (bankInfoModal) {
        bankInfoModal.remove();
    }
}

// Function to format currency (matching the PHP function)
function formatCurrency(amount) {
    // Convert to number and format as Turkish currency
    const number = parseFloat(amount);
    return number.toLocaleString('tr-TR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' ₺';
}
</script>