<?php
// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Tüm alanları doldurmanız gerekiyor.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi giriniz.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Şifreler eşleşmiyor.';
    } else {
        // Check if email already exists
        $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_eposta = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Bu e-posta adresi zaten kullanımda.';
        } else {
            // Check if username already exists
            $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_adi = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) {
                $error = 'Bu kullanıcı adı zaten kullanımda.';
            } else {
                // Create new user
                $hashedPassword = md5($password);
                $stmt = $db->prepare("INSERT INTO uyeler (uye_adi, uye_sifre, uye_eposta, uye_onay, uye_tarih) VALUES (?, ?, ?, 1, NOW())");
                if ($stmt->execute([$name, $hashedPassword, $email])) {
                    $success = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
                    // Clear form data
                    $name = '';
                    $email = '';
                } else {
                    $error = 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.';
                }
            }
        }
    }
}
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fas fa-user-plus text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Yeni Hesap Oluşturun
            </h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">
                Bilgilerinizi girerek hesap oluşturun
            </p>
        </div>

        <div class="card rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Adınız
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition"
                                value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            E-posta Adresi
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition"
                                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Şifre
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Şifreniz en az 6 karakter olmalıdır.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Şifre Tekrar
                        </label>
                        <div class="mt-1">
                            <input id="confirm_password" name="confirm_password" type="password" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover-lift">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-user-plus text-indigo-500 group-hover:text-indigo-400"></i>
                            </span>
                            Kayıt Ol
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 sm:px-8">
                <div class="text-sm text-center text-gray-600 dark:text-gray-400">
                    Zaten hesabınız var mı? 
                    <a href="index.php?page=login" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                        Giriş yapın
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>