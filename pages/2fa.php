<?php
// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

require_once 'includes/GoogleAuthenticator.php';

$ga = new GoogleAuthenticator();

// Fetch user details
$stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: index.php?page=login');
    exit;
}

$message = '';
$messageType = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['enable_2fa']) && !$user['uye_2fa_enabled']) {
        // Generate secret and enable 2FA
        $secret = $ga->generateSecret();
        
        // Update user with secret
        $stmt = $db->prepare("UPDATE uyeler SET uye_2fa_secret = ? WHERE uye_id = ?");
        $result = $stmt->execute([$secret, $_SESSION['user_id']]);
        
        if ($result) {
            $user['uye_2fa_secret'] = $secret;
            $message = '2FA etkinleştirildi. Lütfen QR kodu tarayın ve kodu doğrulayın.';
            $messageType = 'success';
        } else {
            $message = '2FA etkinleştirilirken bir hata oluştu.';
            $messageType = 'danger';
        }
    } elseif (isset($_POST['verify_2fa']) && !empty($_POST['code']) && !empty($user['uye_2fa_secret']) && !$user['uye_2fa_enabled']) {
        // Verify 2FA code
        $code = trim($_POST['code']);
        
        if ($ga->verifyCode($user['uye_2fa_secret'], $code)) {
            // Enable 2FA
            $stmt = $db->prepare("UPDATE uyeler SET uye_2fa_enabled = 1 WHERE uye_id = ?");
            $result = $stmt->execute([$_SESSION['user_id']]);
            
            if ($result) {
                $user['uye_2fa_enabled'] = 1;
                $message = '2FA başarıyla etkinleştirildi!';
                $messageType = 'success';
            } else {
                $message = '2FA etkinleştirilirken bir hata oluştu.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Geçersiz kod. Lütfen tekrar deneyin.';
            $messageType = 'danger';
        }
    } elseif (isset($_POST['disable_2fa']) && $user['uye_2fa_enabled']) {
        // Disable 2FA
        $stmt = $db->prepare("UPDATE uyeler SET uye_2fa_enabled = 0, uye_2fa_secret = NULL WHERE uye_id = ?");
        $result = $stmt->execute([$_SESSION['user_id']]);
        
        if ($result) {
            $user['uye_2fa_enabled'] = 0;
            $user['uye_2fa_secret'] = null;
            $message = '2FA devre dışı bırakıldı.';
            $messageType = 'success';
        } else {
            $message = '2FA devre dışı bırakılırken bir hata oluştu.';
            $messageType = 'danger';
        }
    }
}

// Generate QR code URL if user has a secret but hasn't enabled 2FA yet
$qrCodeUrl = '';
if (!empty($user['uye_2fa_secret']) && !$user['uye_2fa_enabled']) {
    $qrCodeUrl = $ga->getQRCodeUrl(
        SITE_NAME . ' (' . $user['uye_eposta'] . ')',
        $user['uye_2fa_secret'],
        SITE_NAME
    );
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">İki Faktörlü Kimlik Doğrulama (2FA)</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
            Hesabınıza ek güvenlik katmanı ekleyin
        </p>
    </div>
    
    <?php if ($message): ?>
        <div class="mb-8 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200'; ?>">
            <i class="<?php echo $messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?> mr-2"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">2FA Ayarları</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    İki Faktörlü Kimlik Doğrulama (2FA), hesabınıza ek bir güvenlik katmanı sağlar. Etkinleştirildiğinde, giriş yaparken şifrenizin yanı sıra mobil uygulamanızdan alınan tek seferlik bir kod daha girmeniz gerekir.
                </p>
                
                <?php if ($user['uye_2fa_enabled']): ?>
                    <!-- 2FA is enabled -->
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i> 2FA etkin durumda.
                    </div>
                    
                    <form method="POST">
                        <button type="submit" name="disable_2fa" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                            onclick="return confirm('2FA devre dışı bırakılsın mı? Bu işlem güvenlik riski oluşturabilir.')">
                            <i class="fas fa-times-circle mr-2"></i> 2FA Devre Dışı Bırak
                        </button>
                    </form>
                <?php else: ?>
                    <!-- 2FA is disabled -->
                    <?php if (empty($user['uye_2fa_secret'])): ?>
                        <!-- No secret generated yet -->
                        <div class="mb-6 p-4 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg">
                            <i class="fas fa-info-circle mr-2"></i> 2FA henüz etkin değil.
                        </div>
                        
                        <form method="POST">
                            <button type="submit" name="enable_2fa" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <i class="fas fa-shield-alt mr-2"></i> 2FA Etkinleştir
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- Secret generated but not verified -->
                        <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-2"></i> 2FA henüz tamamlanmadı. Lütfen aşağıdaki adımları izleyin.
                        </div>
                        
                        <div class="space-y-8">
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">1. QR Kodu Tara</h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Google Authenticator, Authy veya benzeri bir 2FA uygulaması kullanarak aşağıdaki QR kodu tarayın:
                                </p>
                                <div class="text-center mb-4">
                                    <img src="<?php echo $qrCodeUrl; ?>" alt="2FA QR Code" class="mx-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    Alternatif olarak, aşağıdaki gizli anahtarı uygulamanıza manuel olarak ekleyin:
                                </p>
                                <code class="inline-block px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg text-sm">
                                    <?php echo $user['uye_2fa_secret']; ?>
                                </code>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">2. Kodu Doğrula</h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Uygulamanızdan alınan 6 haneli kodu aşağıya girin:
                                </p>
                                <form method="POST">
                                    <div class="mb-4">
                                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            6 Haneli Kod
                                        </label>
                                        <input type="text" id="code" name="code" maxlength="6" pattern="[0-9]{6}" required
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-center text-lg font-mono">
                                    </div>
                                    <button type="submit" name="verify_2fa" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <i class="fas fa-check mr-2"></i> Doğrula ve Etkinleştir
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Nasıl Çalışır?</h2>
            </div>
            <div class="p-6">
                <ol class="space-y-4">
                    <li class="flex">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">1</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-medium text-gray-800 dark:text-white">Uygulama İndirin</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">
                                Google Authenticator, Authy veya benzeri bir 2FA uygulaması indirin.
                            </p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">2</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-medium text-gray-800 dark:text-white">QR Kodu Tarayın</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">
                                Yukarıdaki QR kodu uygulamanızla tarayın.
                            </p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">3</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-medium text-gray-800 dark:text-white">Kodu Girin</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">
                                Uygulamanızdan alınan 6 haneli kodu doğrulama alanına girin.
                            </p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">4</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-medium text-gray-800 dark:text-white">Güvende Kalın</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">
                                Artık giriş yaparken hem şifrenizi hem de 2FA kodunuzu girmeniz gerekecek.
                            </p>
                        </div>
                    </li>
                </ol>
                
                <div class="mt-8 p-4 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Not:</strong> 2FA kodunuzun süresi dolarsa, uygulama otomatik olarak yeni bir kod oluşturur. Her 30 saniyede bir değişir.
                </div>
            </div>
        </div>
    </div>
</div>