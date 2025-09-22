<?php
// Initialize variables if not set
if (!isset($admin) && isAdmin()) {
    $stmt = $db->prepare("SELECT * FROM uyeler WHERE uye_id = ? AND uye_rutbe = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch();
}

if (!isset($stats)) {
    // Get basic stats for navigation
    $statsQuery = "SELECT COUNT(*) as open_tickets FROM destek_biletleri WHERE bilet_durum = 'acik'";
    $statsResult = $db->query($statsQuery);
    $stats = $statsResult ? $statsResult->fetch() : ['open_tickets' => 0];
    
    // Ensure stats array has required keys
    if (!$stats) {
        $stats = ['open_tickets' => 0];
    }
}

// Ensure admin array has required keys
if (!$admin) {
    $admin = ['uye_adi' => 'Admin', 'uye_id' => 0];
}
?>
<!DOCTYPE html>
<html lang="tr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Panel - <?php echo SITE_NAME; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Investment Platform Admin Panel">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/favicon.ico">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Smooth animations */
        * {
            transition: all 0.2s ease;
        }
        
        /* Glass morphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
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
    </style>
    
    <script>
        // Configure Tailwind
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full">
    
    <!-- Main Layout -->
    <div class="min-h-full flex">
        
        <!-- Mobile Backdrop -->
        <div id="mobile-backdrop" class="fixed inset-0 bg-black bg-opacity-50 lg:hidden hidden" onclick="toggleSidebar()"></div>
        
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl border-r border-gray-200" id="sidebar">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold gradient-text">Admin Panel</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    
                    <!-- Dashboard -->
                    <a href="index" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index' ? 'active' : ''; ?>">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                    
                    <!-- Users -->
                    <a href="users" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'users' ? 'active' : ''; ?>">
                        <i class="fas fa-users mr-3"></i>
                        Kullanıcılar
                    </a>
                    
                    <!-- Products -->
                    <div class="nav-group">
                        <div class="nav-group-title">
                            <i class="fas fa-box mr-3"></i>
                            Ürünler
                            <i class="fas fa-chevron-down ml-auto text-xs"></i>
                        </div>
                        <div class="nav-group-items">
                            <a href="products.php" class="nav-subitem <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                                <i class="fas fa-box mr-3"></i>
                                Tüm Ürünler
                            </a>
                            <a href="categories.php" class="nav-subitem <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                                <i class="fas fa-folder mr-3"></i>
                                Kategoriler
                            </a>
                            <a href="stock.php" class="nav-subitem <?php echo basename($_SERVER['PHP_SELF']) == 'stock.php' ? 'active' : ''; ?>">
                                <i class="fas fa-cubes mr-3"></i>
                                Stok Yönetimi
                            </a>
                            <a href="digital_stock.php" class="nav-subitem <?php echo basename($_SERVER['PHP_SELF']) == 'digital_stock.php' ? 'active' : ''; ?>">
                                <i class="fas fa-key mr-3"></i>
                                Dijital Stok Kodları
                            </a>
                        </div>
                    </div>
                    
                    <!-- Orders -->
                    <a href="orders.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Siparişler
                    </a>
                    
                    <!-- Support -->
                    <a href="tickets.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'tickets.php' ? 'active' : ''; ?>">
                        <i class="fas fa-ticket-alt mr-3"></i>
                        Destek Talepleri
                    </a>
                    
                    <!-- Analytics -->
                    <a href="analytics.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line mr-3"></i>
                        Analizler
                    </a>
                    
                    <!-- Security -->
                    <a href="security_logs.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'security_logs.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shield-alt mr-3"></i>
                        Güvenlik Kayıtları
                    </a>
                    
                    <!-- Settings -->
                    <a href="payment_settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'payment_settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card mr-3"></i>
                        Ödeme Ayarları
                    </a>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="absolute bottom-0 w-full p-4 border-t border-gray-200">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span>Güvenli Bağlantı</span>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col" id="main-content">
            
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 z-40">
                <div class="flex items-center justify-between h-16 px-6">
                    
                    <!-- Left side -->
                    <div class="flex items-center">
                        <button class="p-2 rounded-lg hover:bg-gray-100 lg:hidden" onclick="toggleSidebar()">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        
                        <!-- Breadcrumb -->
                        <nav class="hidden lg:flex items-center space-x-2 text-sm text-gray-500 ml-4 lg:ml-0">
                            <a href="index.php" class="hover:text-gray-700">Admin</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <span class="text-gray-900 font-medium">
                                <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                            </span>
                        </nav>
                    </div>
                    
                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="p-2 rounded-lg hover:bg-gray-100 relative">
                                <i class="fas fa-bell text-gray-600"></i>
                                <?php if (isset($stats['open_tickets']) && $stats['open_tickets'] > 0): ?>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                    <?php echo min($stats['open_tickets'], 9); ?>
                                </span>
                                <?php endif; ?>
                            </button>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100" id="user-menu-button">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        <?php echo strtoupper(substr($admin['uye_adi'] ?? 'A', 0, 1)); ?>
                                    </span>
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin['uye_adi'] ?? 'Admin'); ?></p>
                                    <p class="text-xs text-gray-500">Admin</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                                 id="user-menu-dropdown"
                                 style="display: none;">
                                <a href="profile" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user mr-3"></i>
                                    Profil
                                </a>
                                <a href="settings" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog mr-3"></i>
                                    Ayarlar
                                </a>
                                <div class="border-t border-gray-100 my-2"></div>
                                <a href="logout" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Çıkış Yap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

<style>
/* Navigation Styles */
.nav-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
}

.nav-item:hover {
    background-color: #f9fafb;
    color: #111827;
    transform: translateX(2px);
}

.nav-item.active {
    background-color: #eff6ff;
    color: #1d4ed8;
    border-right: 3px solid #2563eb;
    font-weight: 600;
}

.nav-group {
    margin-bottom: 1rem;
}

.nav-group-title {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 0.2s ease;
}

.nav-group-title:hover {
    color: #374151;
}

.nav-group-items {
    margin-left: 1.5rem;
    padding-left: 1rem;
    border-left: 1px solid #e5e7eb;
    margin-top: 0.25rem;
}

.nav-subitem {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    text-decoration: none;
    margin-bottom: 0.125rem;
}

.nav-subitem:hover {
    background-color: #f9fafb;
    color: #374151;
    transform: translateX(2px);
}

.nav-subitem.active {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}

/* Mobile responsive styles */
@media (max-width: 1024px) {
    #sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }
    
    #sidebar.show {
        transform: translateX(0) !important;
    }
    
    #main-content {
        padding-left: 0 !important;
    }
}

@media (min-width: 1024px) {
    #main-content {
        padding-left: 16rem;
    }
    
    #sidebar {
        transform: translateX(0) !important;
    }
}

/* Badge styles for notifications */
.nav-badge {
    background-color: #ef4444;
    color: white;
    font-size: 0.75rem;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    margin-left: auto;
    min-width: 1.25rem;
    text-align: center;
}
        
/* Mobile backdrop custom z-index */
#mobile-backdrop {
    z-index: 45;
}
        
/* Ensure sidebar is above backdrop */
#sidebar {
    z-index: 50;
}
</style>

<!-- Alpine.js for interactive components -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('mobile-backdrop');
    
    console.log('Toggle sidebar called', { 
        sidebarExists: !!sidebar, 
        backdropExists: !!backdrop,
        currentTransform: sidebar ? sidebar.style.transform : 'none',
        hasShowClass: sidebar ? sidebar.classList.contains('show') : false
    });
    
    sidebar.classList.toggle('show');
    
    if (sidebar.classList.contains('show')) {
        backdrop.classList.remove('hidden');
        console.log('Showing sidebar and backdrop');
    } else {
        backdrop.classList.add('hidden');
        console.log('Hiding sidebar and backdrop');
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('mobile-backdrop');
    const toggleBtn = event.target.closest('[onclick="toggleSidebar()"]');
    
    if (!sidebar.contains(event.target) && !toggleBtn && window.innerWidth < 1024) {
        sidebar.classList.remove('show');
        backdrop.classList.add('hidden');
    }
});

// Handle responsive sidebar
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const backdrop = document.getElementById('mobile-backdrop');
    
    if (window.innerWidth >= 1024) {
        sidebar.classList.remove('show');
        sidebar.style.transform = 'translateX(0)';
        mainContent.style.paddingLeft = '16rem';
        backdrop.classList.add('hidden');
    } else {
        sidebar.style.transform = 'translateX(-100%)';
        mainContent.style.paddingLeft = '0';
        backdrop.classList.add('hidden');
    }
});

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    console.log('DOM loaded, initializing sidebar');
    
    // Add smooth transitions
    sidebar.style.transition = 'transform 0.3s ease-in-out';
    mainContent.style.transition = 'padding-left 0.3s ease-in-out';
    
    // Set initial state based on screen size
    if (window.innerWidth < 1024) {
        sidebar.style.transform = 'translateX(-100%)';
        mainContent.style.paddingLeft = '0';
        console.log('Mobile mode: hiding sidebar');
    } else {
        sidebar.style.transform = 'translateX(0)';
        mainContent.style.paddingLeft = '16rem';
        console.log('Desktop mode: showing sidebar');
    }
});

// Navigation group toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const navGroups = document.querySelectorAll('.nav-group-title');
    
    navGroups.forEach(function(groupTitle) {
        groupTitle.addEventListener('click', function() {
            const groupItems = groupTitle.nextElementSibling;
            if (groupItems && groupItems.classList.contains('nav-group-items')) {
                groupItems.style.display = groupItems.style.display === 'none' ? 'block' : 'none';
                
                // Rotate chevron icon if exists
                const chevron = groupTitle.querySelector('.fa-chevron-down, .fa-chevron-right');
                if (chevron) {
                    chevron.classList.toggle('fa-chevron-down');
                    chevron.classList.toggle('fa-chevron-right');
                }
            }
        });
    });
    
    // Fallback user menu functionality for cases where Alpine.js fails
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');
    
    if (userMenuButton && userMenuDropdown) {
        let isOpen = false;
        
        userMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            isOpen = !isOpen;
            
            if (isOpen) {
                userMenuDropdown.style.display = 'block';
                userMenuDropdown.classList.add('opacity-100', 'scale-100');
                userMenuDropdown.classList.remove('opacity-0', 'scale-95');
            } else {
                userMenuDropdown.style.display = 'none';
                userMenuDropdown.classList.add('opacity-0', 'scale-95');
                userMenuDropdown.classList.remove('opacity-100', 'scale-100');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                isOpen = false;
                userMenuDropdown.style.display = 'none';
                userMenuDropdown.classList.add('opacity-0', 'scale-95');
                userMenuDropdown.classList.remove('opacity-100', 'scale-100');
            }
        });
        
        // Close dropdown when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) {
                isOpen = false;
                userMenuDropdown.style.display = 'none';
                userMenuDropdown.classList.add('opacity-0', 'scale-95');
                userMenuDropdown.classList.remove('opacity-100', 'scale-100');
            }
        });
    }
});
</script>