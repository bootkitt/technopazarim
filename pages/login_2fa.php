<?php
// Check if user is in 2FA verification process
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once 'includes/GoogleAuthenticator.php';

// Fetch user details
$stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['uye_2fa_enabled'] != 1) {
    // User doesn't have 2FA enabled, redirect to home
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_POST) {
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    
    if (empty($code)) {
        $message = 'Lütfen 6 haneli kodu girin.';
        $messageType = 'danger';
    } else {
        // Verify 2FA code
        $ga = new GoogleAuthenticator();
        
        if ($ga->verifyCode($user['uye_2fa_secret'], $code)) {
            // 2FA verified successfully
            $_SESSION['2fa_verified'] = true;
            
            // Log successful 2FA verification
            logSecurityEvent($user['uye_id'], '2fa_success', '2FA verification successful during login', $db);
            
            // Redirect to intended page or home
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            // Log failed 2FA attempt
            logSecurityEvent($user['uye_id'], '2fa_failed', 'Invalid 2FA code attempt', $db);
            
            $message = 'Geçersiz kod. Lütfen tekrar deneyin.';
            $messageType = 'danger';
        }
    }
}
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fas fa-shield-alt text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                İki Faktörlü Kimlik Doğrulama
            </h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">
                Devam etmek için kimlik doğrulayıcı uygulamanızdan alınan 6 haneli kodu girin
            </p>
        </div>

        <div class="card rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <?php if ($message): ?>
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="mb-6 p-4 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    Hesabınızda iki faktörlü kimlik doğrulama etkin. Devam etmek için kimlik doğrulayıcı uygulamanızdan alınan 6 haneli kodu girin.
                </div>
                
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            6 Haneli Kod
                        </label>
                        <div class="mt-1">
                            <input id="code" name="code" type="text" maxlength="6" pattern="[0-9]{6}" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-center text-lg font-mono transition"
                                placeholder="000000">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Google Authenticator, Authy veya benzeri bir uygulamadan alınan kodu girin.
                            </p>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover-lift">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-check text-indigo-500 group-hover:text-indigo-400"></i>
                            </span>
                            Doğrula ve Devam Et
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 sm:px-8">
                <div class="text-sm text-center">
                    <a href="index.php?page=logout" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                        <i class="fas fa-sign-out-alt mr-1"></i> Çıkış Yap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>