<?php
// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_profile'])) {
        // Update profile
        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        // Validate input
        if (empty($firstName) || empty($lastName) || empty($email)) {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Tüm alanları doldurun.</div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Geçersiz e-posta adresi.</div>';
        } else {
            // Check if email is already taken by another user
            $stmt = $db->prepare("SELECT uye_id FROM uyeler WHERE uye_eposta = ? AND uye_id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.</div>';
            } else {
                // Update profile
                $fullName = $firstName . ' ' . $lastName;
                $stmt = $db->prepare("UPDATE uyeler SET uye_adi = ?, uye_eposta = ? WHERE uye_id = ?");
                $result = $stmt->execute([$fullName, $email, $_SESSION['user_id']]);
                
                if ($result) {
                    echo '<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">Profil bilgileriniz güncellendi.</div>';
                    // Refresh user data
                    $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Profil bilgileri güncellenirken bir hata oluştu.</div>';
                }
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Change password
        $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate input
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Tüm alanları doldurun.</div>';
        } elseif (strlen($newPassword) < 6) {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Yeni şifre en az 6 karakter olmalıdır.</div>';
        } elseif ($newPassword !== $confirmPassword) {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Yeni şifreler eşleşmiyor.</div>';
        } else {
            // Verify current password
            $stmt = $db->prepare("SELECT uye_sifre FROM uyeler WHERE uye_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData && hash_equals($userData['uye_sifre'], md5($currentPassword))) {
                // Update password
                $stmt = $db->prepare("UPDATE uyeler SET uye_sifre = ? WHERE uye_id = ?");
                $result = $stmt->execute([md5($newPassword), $_SESSION['user_id']]);
                
                if ($result) {
                    echo '<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">Şifreniz başarıyla değiştirildi.</div>';
                } else {
                    echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Şifre değiştirilirken bir hata oluştu.</div>';
                }
            } else {
                echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">Mevcut şifreniz yanlış.</div>';
            }
        }
    } elseif (isset($_POST['update_2fa'])) {
        // Update 2FA settings
        $enable2fa = isset($_POST['enable_2fa']) ? 1 : 0;
        
        if ($enable2fa && empty($user['uye_2fa_secret'])) {
            // Generate 2FA secret if enabling for the first time
            $secret = bin2hex(random_bytes(16));
            $stmt = $db->prepare("UPDATE uyeler SET uye_2fa_enabled = ?, uye_2fa_secret = ? WHERE uye_id = ?");
            $result = $stmt->execute([$enable2fa, $secret, $_SESSION['user_id']]);
        } else {
            // Just update the enabled status
            $stmt = $db->prepare("UPDATE uyeler SET uye_2fa_enabled = ? WHERE uye_id = ?");
            $result = $stmt->execute([$enable2fa, $_SESSION['user_id']]);
        }
        
        if ($result) {
            echo '<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">2FA ayarları güncellendi.</div>';
            // Refresh user data
            $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo '<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">2FA ayarları güncellenirken bir hata oluştu.</div>';
        }
    }
}

// Split full name into first and last name for the form
$nameParts = explode(' ', $user['uye_adi'], 2);
$firstName = $nameParts[0];
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';
?>
  
    <div class="card rounded-2xl overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex overflow-x-auto">
                <button class="profile-tab px-6 py-4 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 whitespace-nowrap" data-tab="profile">
                    Profil Bilgileri
                </button>
                <button class="profile-tab px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap" data-tab="security">
                    Güvenlik
                </button>
                <button class="profile-tab px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap" data-tab="twofa">
                    İki Faktörlü Kimlik Doğrulama
                </button>
            </nav>
        </div>
        
        <div class="p-6">
            <!-- Profile Information Tab -->
            <div class="tab-content" id="profile-tab-content">
                <form method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ad</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Soyad</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-posta Adresi</label>
                        <input type="email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="email" name="email" value="<?php echo htmlspecialchars($user['uye_eposta']); ?>" required>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-save mr-2"></i> Değişiklikleri Kaydet
                    </button>
                </form>
            </div>
            
            <!-- Security Tab -->
            <div class="tab-content hidden" id="security-tab-content">
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="mb-6">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mevcut Şifre</label>
                        <input type="password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-6">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yeni Şifre</label>
                        <input type="password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="new_password" name="new_password" required>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Şifreniz en az 6 karakter olmalıdır.</p>
                    </div>
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yeni Şifre (Tekrar)</label>
                        <input type="password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-key mr-2"></i> Şifreyi Değiştir
                    </button>
                </form>
            </div>
            
            <!-- 2FA Tab -->
            <div class="tab-content hidden" id="twofa-tab-content">
                <form method="POST">
                    <input type="hidden" name="update_2fa" value="1">
                    <div class="flex items-center mb-6">
                        <label for="enable_2fa" class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" id="enable_2fa" name="enable_2fa" class="sr-only" <?php echo $user['uye_2fa_enabled'] ? 'checked' : ''; ?>>
                                <div class="block w-14 h-7 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-transform transform <?php echo $user['uye_2fa_enabled'] ? 'translate-x-7' : ''; ?>"></div>
                            </div>
                            <div class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">İki Faktörlü Kimlik Doğrulamayı Etkinleştir</div>
                        </label>
                    </div>
                    
                    <?php if ($user['uye_2fa_enabled']): ?>
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg">
                            <h6 class="font-medium mb-2">İki Faktörlü Kimlik Doğrulama Etkin</h6>
                            <p class="mb-3">Kimlik doğrulama uygulamanızda aşağıdaki QR kodu veya gizli anahtarı kullanın:</p>
                            <div>
                                <strong class="block mb-2">Gizli Anahtar:</strong>
                                <div class="flex">
                                    <input type="text" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" value="<?php echo htmlspecialchars($user['uye_2fa_secret']); ?>" readonly>
                                    <button type="button" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-300" onclick="copyToClipboard('<?php echo $user['uye_2fa_secret']; ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-2"></i> İki faktörlü kimlik doğrulama devre dışı. Hesabınızın güvenliği için etkinleştirmenizi öneririz.
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-save mr-2"></i> Ayarları Güncelle
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active classes
            tabs.forEach(t => {
                t.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                t.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            
            // Add active classes to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            this.classList.add('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show the selected tab content
            const tabId = this.dataset.tab + '-tab-content';
            document.getElementById(tabId).classList.remove('hidden');
        });
    });
});

function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    showMessage('Gizli anahtar panoya kopyalandı!', 'success');
}

// Toggle switch functionality
document.addEventListener('DOMContentLoaded', function() {
    const toggleSwitch = document.getElementById('enable_2fa');
    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', function() {
            const toggleBall = this.nextElementSibling.querySelector('div:nth-child(2)');
            if (this.checked) {
                toggleBall.classList.add('translate-x-7');
            } else {
                toggleBall.classList.remove('translate-x-7');
            }
        });
    }
});
</script>