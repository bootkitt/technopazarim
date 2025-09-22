<header class="bg-white dark:bg-gray-900 shadow-lg sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      <!-- Logo Section -->
      <div class="flex items-center">
        <a href="index.php" class="flex items-center group">
          <div class="bg-gradient-to-br from-indigo-600 to-purple-600 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:-translate-y-1">
            <i class="fas fa-store text-white text-2xl"></i>
          </div>
          <div class="ml-3">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">TechnoPazarim</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">Teknoloji Pazarı</p>
          </div>
        </a>
      </div>
      
      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center space-x-1">
        <a href="index.php" class="px-5 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-all duration-200 relative group">
          <span>Ana Sayfa</span>
          <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 rounded-full transition-all duration-300 group-hover:w-full"></span>
        </a>
        <a href="index.php?page=products" class="px-5 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-all duration-200 relative group">
          <span>Ürünler</span>
          <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 rounded-full transition-all duration-300 group-hover:w-full"></span>
        </a>
        <a href="index.php?page=kategoriler" class="px-5 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-all duration-200 relative group">
          <span>Kategoriler</span>
          <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 rounded-full transition-all duration-300 group-hover:w-full"></span>
        </a>
        <a href="index.php?page=hakkimizda" class="px-5 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-all duration-200 relative group">
          <span>Hakkımızda</span>
          <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 rounded-full transition-all duration-300 group-hover:w-full"></span>
        </a>
        <a href="index.php?page=sss" class="px-5 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-all duration-200 relative group">
          <span>S.S.S</span>
          <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 rounded-full transition-all duration-300 group-hover:w-full"></span>
        </a>
        <a href="index.php?page=iletisim" class="px-5 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-all duration-200 relative group">
          <span>İletişim</span>
          <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 rounded-full transition-all duration-300 group-hover:w-full"></span>
        </a>
      </div>
      
      <!-- Right Side Controls -->
      <div class="flex items-center space-x-2">
        <!-- Search Button -->
        <button class="p-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Arama">
          <i class="fas fa-search"></i>
        </button>
        
        <!-- Theme Toggle -->
        <button class="theme-toggle p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 transition-colors" id="themeToggle" title="Tema Değiştir">
          <i class="fas fa-moon"></i>
        </button>
        
        <?php if (isLoggedIn()): ?>
          <!-- Cart -->
          <a href="index.php?page=cart" class="relative p-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <i class="fas fa-shopping-cart"></i>
            <?php
            $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
            if ($cartCount > 0):
            ?>
              <span class="absolute -top-1 -right-1 bg-gradient-to-r from-pink-500 to-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center shadow-sm"><?php echo $cartCount; ?></span>
            <?php endif; ?>
          </a>
          
          <!-- User Menu -->
          <div class="relative group">
            <button class="flex items-center space-x-2 p-2 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
              <div class="bg-gradient-to-br from-indigo-500 to-purple-500 w-8 h-8 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-white"></i>
              </div>
              <i class="fas fa-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
            </button>
            
            <div class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-xl py-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 border-0 ring-1 ring-black/5 dark:ring-white/10 transform translate-y-2 group-hover:translate-y-0">
              <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Kullanıcı'); ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
              </div>
              <a href="index.php?page=account" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-tachometer-alt mr-3 w-5 text-center text-indigo-500"></i>
                <span>Panel</span>
              </a>
              <a href="index.php?page=account&section=orders" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-shopping-bag mr-3 w-5 text-center text-indigo-500"></i>
                <span>Siparişlerim</span>
              </a>
              <a href="index.php?page=account&section=downloads" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-download mr-3 w-5 text-center text-indigo-500"></i>
                <span>İndirilenler</span>
              </a>
              <a href="index.php?page=account&section=support" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-headset mr-3 w-5 text-center text-indigo-500"></i>
                <span>Destek</span>
              </a>
              <a href="index.php?page=2fa" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-shield-alt mr-3 w-5 text-center text-indigo-500"></i>
                <span>2FA Ayarları</span>
              </a>
              <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
              <a href="index.php?page=logout" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center">
                <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                <span>Çıkış Yap</span>
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="index.php?page=login" class="px-4 py-2 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition-colors">Giriş Yap</a>
          <a href="index.php?page=kayit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Kayıt Ol</a>
        <?php endif; ?>
      </div>
      
      <!-- Mobile menu button -->
      <div class="md:hidden flex items-center">
        <button class="mobile-menu-button p-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>
    </div>
  </div>
  
  <!-- Mobile menu -->
  <div class="mobile-menu hidden md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="px-4 pt-4 pb-5 space-y-2">
      <a href="index.php" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Ana Sayfa</a>
      <a href="index.php?page=products" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Ürünler</a>
      <a href="index.php?page=kategoriler" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Kategoriler</a>
      <a href="index.php?page=hakkimizda" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Hakkımızda</a>
      <a href="index.php?page=sss" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">S.S.S</a>
      <a href="index.php?page=iletisim" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">İletişim</a>
      <?php if (isLoggedIn()): ?>
        <a href="index.php?page=account" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Hesabım</a>
        <a href="index.php?page=cart" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Sepetim</a>
        <a href="index.php?page=logout" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Çıkış Yap</a>
      <?php else: ?>
        <a href="index.php?page=login" class="text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 block px-4 py-3 rounded-xl text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Giriş Yap</a>
        <a href="index.php?page=kayit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white block px-4 py-3 rounded-xl text-base font-medium text-center mt-2">Kayıt Ol</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
// Theme toggle functionality for frontend
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = themeToggle.querySelector('i');
    
    // Check for saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    
    // Toggle theme on button click
    themeToggle.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });
    
    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }
    
    // Mobile menu toggle
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
    });
});
</script>