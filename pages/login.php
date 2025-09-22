<?php
// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_POST) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($email) || empty($password)) {
        $error = 'E-posta ve şifre alanları boş bırakılamaz.';
    } else {
        // Check user credentials
        $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_eposta = ? AND uye_onay = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && md5($password) === $user['uye_sifre']) {
            // Password is correct
            
            // Save cart data before logging in
            $cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            
            $_SESSION['user_id'] = $user['uye_id'];
            $_SESSION['user_role'] = $user['uye_rutbe'] == 1 ? 'admin' : 'user';
            
            // Restore cart data after logging in
            if (!empty($cartData)) {
                $_SESSION['cart'] = $cartData;
            }
            
            // Log successful login
            logSecurityEvent($user['uye_id'], 'login_success', 'User logged in successfully', $db);
            
            // Check if 2FA is enabled
            if ($user['uye_2fa_enabled'] == 1) {
                // Redirect to 2FA verification page
                header('Location: index.php?page=login_2fa');
                exit;
            }
            
            // Redirect to intended page or home
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            // Log failed login attempt
            if ($user) {
                logSecurityEvent($user['uye_id'], 'login_failed', 'Invalid password attempt', $db);
            } else {
                // Log failed login for non-existent user
                logSecurityEvent(null, 'login_failed', 'Invalid email attempt: ' . $email, $db);
            }
            
            $error = 'E-posta veya şifre hatalı.';
        }
    }
}
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fas fa-sign-in-alt text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Hesabınıza Giriş Yapın
            </h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">
                Devam etmek için bilgilerinizi girin
            </p>
        </div>

        <div class="card rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            E-posta Adresi
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Şifre
                            </label>
                            <div class="text-sm">
                                <a href="#" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                                    Şifrenizi mi unuttunuz?
                                </a>
                            </div>
                        </div>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Beni hatırla
                        </label>
                    </div>

                    <div>
                        <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover-lift">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-indigo-500 group-hover:text-indigo-400"></i>
                            </span>
                            Giriş Yap
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 sm:px-8">
                <div class="text-sm text-center text-gray-600 dark:text-gray-400">
                    Hesabınız yok mu? 
                    <a href="index.php?page=kayit" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                        Kayıt olun
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>