<?php
// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

// Get current section
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Fetch user details
$stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: index.php?page=login');
    exit;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Hesabım</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
            Hesap ayarlarınızı yönetin ve siparişlerinizi görüntüleyin
        </p>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <div class="lg:w-1/4">
            <div class="card rounded-2xl overflow-hidden sticky top-8">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-user mr-2"></i> Hesabım
                    </h2>
                </div>
                <div class="p-4">
                    <nav class="space-y-1">
                        <a href="index.php?page=account&section=dashboard" 
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $section === 'dashboard' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                            <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                            Genel Bakış
                        </a>
                        <a href="index.php?page=account&section=orders" 
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $section === 'orders' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                            <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i>
                            Siparişlerim
                        </a>
                        <a href="index.php?page=account&section=downloads" 
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $section === 'downloads' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                            <i class="fas fa-download mr-3 w-5 text-center"></i>
                            İndirme Merkezi
                        </a>
                        <a href="index.php?page=account&section=support" 
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $section === 'support' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                            <i class="fas fa-headset mr-3 w-5 text-center"></i>
                            Destek Talepleri
                        </a>
                        <a href="index.php?page=account&section=profile" 
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $section === 'profile' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                            <i class="fas fa-cog mr-3 w-5 text-center"></i>
                            Profil Ayarları
                        </a>
                        <a href="logout.php" 
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                            <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                            Çıkış Yap
                        </a>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="lg:w-3/4">
            <?php
            switch ($section) {
                case 'dashboard':
                    include 'pages/account/dashboard.php';
                    break;
                case 'orders':
                    include 'pages/account/orders.php';
                    break;
                case 'downloads':
                    include 'pages/account/downloads.php';
                    break;
                case 'support':
                    include 'pages/account/support.php';
                    break;
                case 'profile':
                    include 'pages/account/profile.php';
                    break;
                default:
                    include 'pages/account/dashboard.php';
                    break;
            }
            ?>
        </div>
    </div>
</div>