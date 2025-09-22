<?php
$pageTitle = "İletişim";
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="card rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">İletişim</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Bize Ulaşın</h2>
                
                <form class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adınız Soyadınız</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-posta Adresiniz</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konu</label>
                        <select id="subject" name="subject" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Lütfen bir konu seçin</option>
                            <option value="destek">Teknik Destek</option>
                            <option value="siparis">Sipariş Sorgulama</option>
                            <option value="odeme">Ödeme Sorunu</option>
                            <option value="geri_bildirim">Geri Bildirim</option>
                            <option value="diger">Diğer</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mesajınız</label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Mesaj Gönder
                    </button>
                </form>
            </div>
            
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">İletişim Bilgilerimiz</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-envelope text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">E-posta</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                destek@<?php echo $_SERVER['HTTP_HOST']; ?>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                7/24 destek hizmetimiz mevcuttur
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-phone-alt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Telefon</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                +90 (212) 555 12 34
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Hafta içi 09:00 - 18:00
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Adres</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                İstanbul, Türkiye
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Çalışma Saatleri</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                Hafta içi: 09:00 - 18:00<br>
                                Hafta sonu: 10:00 - 16:00
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-10">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Sosyal Medya</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>