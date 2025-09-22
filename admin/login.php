<?php
require_once __DIR__ . '/../config.php';

// Redirect if already logged in
if (isAdmin()) {
    header('Location: index');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Tüm alanları doldurunuz';
    } else {
        // Check admin credentials - using uyeler table instead of admins table
        $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_adi = ? AND uye_rutbe = 1 AND uye_onay = 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && verifyPassword($password, $admin['uye_sifre'])) {
            loginAdmin($admin['uye_id'], $admin['uye_adi']);
            
            // Log admin login
            logSecurityEvent($admin['uye_id'], 'admin_login', 'Admin login: ' . $username, $db);
            
            header('Location: index');
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - <?php echo SITE_NAME; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Investment Platform Admin Login">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/favicon.ico">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Custom gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Glass morphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Animated background */
        .animated-bg {
            background: linear-gradient(-45deg, #667eea, #764ba2, #6b73ff, #9aa5ff);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Custom input focus styles */
        .custom-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            border-color: #6366f1;
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full animated-bg">
    
    <!-- Login Container -->
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            
            <!-- Logo and Title -->
            <div class="text-center">
                <div class="mx-auto w-20 h-20 bg-white rounded-2xl shadow-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-shield-alt text-3xl gradient-text"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Admin Panel</h2>
                <p class="text-white/80">Güvenli giriş yapın</p>
            </div>
            
            <!-- Login Form -->
            <div class="glass rounded-3xl shadow-2xl p-8">
                <?php if ($error): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo clean($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>
                            Kullanıcı Adı
                        </label>
                        <input type="text" id="username" name="username" required
                               class="custom-input appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-xl focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Kullanıcı adınızı girin"
                               value="<?php echo clean($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>
                            Şifre
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="custom-input appearance-none relative block w-full px-4 py-3 pr-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-xl focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                   placeholder="Şifrenizi girin">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword()">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password-toggle"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Beni hatırla
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white gradient-bg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all duration-200 hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt group-hover:text-white/80"></i>
                            </span>
                            Giriş Yap
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Security Notice -->
            <div class="text-center">
                <div class="flex items-center justify-center text-white/70 text-sm">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span>Bu alan yöneticiler için güvenli bir alandır</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to Site -->
    <div class="fixed bottom-6 left-6">
        <a href="<?php echo SITE_URL; ?>" 
           class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-xl hover:bg-white/30 transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Ana Siteye Dön
        </a>
    </div>
    
    <!-- JavaScript -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Add form submission animation
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Giriş yapılıyor...';
            
            // Re-enable after 3 seconds if no redirect (for error cases)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 3000);
        });
        
        // Focus first input on load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Add Enter key navigation
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });
        
        // Add subtle animations
        window.addEventListener('load', function() {
            document.body.style.opacity = '0';
            document.body.style.transform = 'translateY(20px)';
            document.body.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
                document.body.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>