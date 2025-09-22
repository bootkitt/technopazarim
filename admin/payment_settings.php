<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Ödeme Ayarları';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_settings'])) {
        $shopier_api_key = trim($_POST['shopier_api_key']);
        $shopier_api_secret = trim($_POST['shopier_api_secret']);
        $shopier_site_url = trim($_POST['shopier_site_url']);
        
        try {
            // Update Shopier settings in odeme_ayarlari table
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'shopier_api_key'");
            $stmt->execute([$shopier_api_key]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'shopier_api_secret'");
            $stmt->execute([$shopier_api_secret]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'shopier_site_url'");
            $stmt->execute([$shopier_site_url]);
            
            $success = "Ödeme ayarları başarıyla güncellendi.";
        } catch (Exception $e) {
            $error = "Ayarlar güncellenirken bir hata oluştu: " . $e->getMessage();
        }
    }
    
    // Handle bank transfer settings
    if (isset($_POST['update_bank_settings'])) {
        $bank_name = trim($_POST['bank_name']);
        $account_owner = trim($_POST['account_owner']);
        $iban = trim($_POST['iban']);
        $swift_code = trim($_POST['swift_code']);
        $branch_code = trim($_POST['branch_code']);
        $account_number = trim($_POST['account_number']);
        
        try {
            // Update bank transfer settings in odeme_ayarlari table
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'banka_adi'");
            $stmt->execute([$bank_name]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'hesap_sahibi'");
            $stmt->execute([$account_owner]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'iban'");
            $stmt->execute([$iban]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'swift_kodu'");
            $stmt->execute([$swift_code]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'sube_kodu'");
            $stmt->execute([$branch_code]);
            
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'hesap_no'");
            $stmt->execute([$account_number]);
            
            $success = "Banka havale ayarları başarıyla güncellendi.";
        } catch (Exception $e) {
            $error = "Banka ayarları güncellenirken bir hata oluştu: " . $e->getMessage();
        }
    }
    
    // Handle active payment methods
    if (isset($_POST['update_payment_methods'])) {
        $active_methods = isset($_POST['active_methods']) ? $_POST['active_methods'] : [];
        $default_method = $_POST['default_method'] ?? 'bank_transfer';
        
        try {
            // Update active payment methods
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'active_payment_methods'");
            $stmt->execute([json_encode($active_methods)]);
            
            // Update default payment method
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'odeme_yontemi'");
            $stmt->execute([$default_method]);
            
            // Update active status
            $isActive = in_array($default_method, $active_methods) ? '1' : '0';
            $stmt = $db->prepare("UPDATE odeme_ayarlari SET ayar_deger = ? WHERE ayar_adi = 'aktif'");
            $stmt->execute([$isActive]);
            
            $success = "Ödeme yöntemleri başarıyla güncellendi.";
        } catch (Exception $e) {
            $error = "Ödeme yöntemleri güncellenirken bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Fetch current settings from odeme_ayarlari table
$stmt = $db->prepare("SELECT ayar_adi, ayar_deger FROM odeme_ayarlari WHERE ayar_adi IN ('shopier_api_key', 'shopier_api_secret', 'shopier_site_url', 'banka_adi', 'hesap_sahibi', 'iban', 'swift_kodu', 'sube_kodu', 'hesap_no', 'active_payment_methods', 'odeme_yontemi', 'aktif')");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Decode active payment methods
$activePaymentMethods = isset($settings['active_payment_methods']) ? json_decode($settings['active_payment_methods'], true) : ['bank_transfer'];
$defaultPaymentMethod = $settings['odeme_yontemi'] ?? 'bank_transfer';
$isActive = $settings['aktif'] ?? '1';

include_once __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Ödeme Ayarları</h1>
            <p class="text-gray-600 mt-1">Ödeme sistemleri için gerekli ayarları yapılandırın</p>
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

        <!-- Security Notice -->
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-red-800">Güvenlik Uyarısı</h3>
                    <div class="mt-2 text-red-700">
                        <p>API bilgilerinizi güvenli bir şekilde saklayın. Bu bilgiler üçüncü şahıslarla paylaşılmamalıdır. API anahtarlarınızın güvenliği sisteminizin güvenliği için kritik öneme sahiptir.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shopier Settings -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Shopier Ayarları</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Aktif
                </span>
            </div>
            <div class="p-6">
                <form method="POST">
                    <input type="hidden" name="update_settings" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="shopier_api_key" class="block text-sm font-medium text-gray-700 mb-1">API Anahtarı</label>
                            <div class="relative">
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10" id="shopier_api_key" name="shopier_api_key" 
                                       value="<?php echo htmlspecialchars($settings['shopier_api_key'] ?? ''); ?>">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="shopier_api_key">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Shopier panelinizden aldığınız API anahtarını girin.</p>
                        </div>
                        <div>
                            <label for="shopier_api_secret" class="block text-sm font-medium text-gray-700 mb-1">API Şifresi</label>
                            <div class="relative">
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10" id="shopier_api_secret" name="shopier_api_secret" 
                                       value="<?php echo htmlspecialchars($settings['shopier_api_secret'] ?? ''); ?>">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="shopier_api_secret">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Shopier panelinizden aldığınız API şifresini girin.</p>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="shopier_site_url" class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
                        <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="shopier_site_url" name="shopier_site_url" 
                               value="<?php echo htmlspecialchars($settings['shopier_site_url'] ?? ''); ?>" 
                               placeholder="https://ornek.com">
                        <p class="mt-1 text-sm text-gray-500">Shopier ödeme bildirimlerinin geleceği URL adresi.</p>
                    </div>
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input id="testMode" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" disabled>
                            <label for="testMode" class="ml-2 block text-sm text-gray-700">
                                Test Modu (Yakında)
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bank Transfer Settings -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Banka Havale Ayarları</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo in_array('bank_transfer', $activePaymentMethods) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo in_array('bank_transfer', $activePaymentMethods) ? 'Aktif' : 'Pasif'; ?>
                </span>
            </div>
            <div class="p-6">
                <form method="POST">
                    <input type="hidden" name="update_bank_settings" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Banka Adı</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="bank_name" name="bank_name" 
                                   value="<?php echo htmlspecialchars($settings['banka_adi'] ?? ''); ?>">
                        </div>
                        <div>
                            <label for="account_owner" class="block text-sm font-medium text-gray-700 mb-1">Hesap Sahibi</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="account_owner" name="account_owner" 
                                   value="<?php echo htmlspecialchars($settings['hesap_sahibi'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="iban" class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="iban" name="iban" 
                                   value="<?php echo htmlspecialchars($settings['iban'] ?? ''); ?>" placeholder="TR00 0000 0000 0000 0000 0000 00">
                        </div>
                        <div>
                            <label for="swift_code" class="block text-sm font-medium text-gray-700 mb-1">SWIFT/BIC Kodu</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="swift_code" name="swift_code" 
                                   value="<?php echo htmlspecialchars($settings['swift_kodu'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="branch_code" class="block text-sm font-medium text-gray-700 mb-1">Şube Kodu</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="branch_code" name="branch_code" 
                                   value="<?php echo htmlspecialchars($settings['sube_kodu'] ?? ''); ?>">
                        </div>
                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Hesap Numarası</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="account_number" name="account_number" 
                                   value="<?php echo htmlspecialchars($settings['hesap_no'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Payment Methods -->
        <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Ödeme Yöntemleri Ayarları</h2>
            </div>
            <div class="p-6">
                <form method="POST">
                    <input type="hidden" name="update_payment_methods" value="1">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Aktif Ödeme Yöntemleri</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <input id="method_shopier" type="checkbox" name="active_methods[]" value="shopier" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?php echo in_array('shopier', $activePaymentMethods) ? 'checked' : ''; ?>>
                                <label for="method_shopier" class="ml-2 block text-sm text-gray-700">
                                    Shopier
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="method_bank_transfer" type="checkbox" name="active_methods[]" value="bank_transfer" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?php echo in_array('bank_transfer', $activePaymentMethods) ? 'checked' : ''; ?>>
                                <label for="method_bank_transfer" class="ml-2 block text-sm text-gray-700">
                                    Banka Havale/EFT
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="method_paypal" type="checkbox" name="active_methods[]" value="paypal" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" disabled>
                                <label for="method_paypal" class="ml-2 block text-sm text-gray-700">
                                    PayPal (Yakında)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="method_credit_card" type="checkbox" name="active_methods[]" value="credit_card" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" disabled>
                                <label for="method_credit_card" class="ml-2 block text-sm text-gray-700">
                                    Kredi Kartı (Yakında)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="default_method" class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Ödeme Yöntemi</label>
                        <select id="default_method" name="default_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="bank_transfer" <?php echo $defaultPaymentMethod == 'bank_transfer' ? 'selected' : ''; ?>>Banka Havale/EFT</option>
                            <option value="shopier" <?php echo $defaultPaymentMethod == 'shopier' ? 'selected' : ''; ?>>Shopier</option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Methods Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Desteklenen Ödeme Yöntemleri</h2>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-center justify-between py-2">
                            <span class="text-gray-700">Shopier</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo in_array('shopier', $activePaymentMethods) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo in_array('shopier', $activePaymentMethods) ? 'Aktif' : 'Pasif'; ?>
                            </span>
                        </li>
                        <li class="flex items-center justify-between py-2">
                            <span class="text-gray-700">Banka Havale/EFT</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo in_array('bank_transfer', $activePaymentMethods) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo in_array('bank_transfer', $activePaymentMethods) ? 'Aktif' : 'Pasif'; ?>
                            </span>
                        </li>
                        <li class="flex items-center justify-between py-2">
                            <span class="text-gray-700">PayPal</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Planlanıyor
                            </span>
                        </li>
                        <li class="flex items-center justify-between py-2">
                            <span class="text-gray-700">Kredi Kartı</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Planlanıyor
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Güvenlik Bilgileri</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-lock text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">SSL Sertifikası</h3>
                                <div class="mt-1 text-sm text-blue-700">
                                    <p>Tüm ödeme işlemleri SSL sertifikası ile korunmaktadır. Kullanıcılarınızın bilgileri güvenli bir şekilde işlenmektedir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-sync text-yellow-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Otomatik Yedekleme</h3>
                                <div class="mt-1 text-sm text-yellow-700">
                                    <p>Ödeme ayarlarınız otomatik olarak yedeklenmektedir. Herhangi bir sorun durumunda hızlıca kurtarma yapılabilir.</p>
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
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>