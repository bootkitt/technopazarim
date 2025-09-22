<?php
$pageTitle = "Sıkça Sorulan Sorular";
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="card rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Sıkça Sorulan Sorular</h1>
        
        <div class="prose prose-lg dark:prose-invert max-w-none">
            <div class="space-y-6">
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Dijital ürünler nedir?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Dijital ürünler, internet üzerinden anında teslim edilen ürünlerdir. 
                            Yazılım lisansları, oyunlar, mobil uygulamalar ve diğer dijital içerikler 
                            bu kategoriye girer. Satın alma işlemi tamamlandıktan hemen sonra 
                            ürün bilgileri e-posta adresinize gönderilir veya kullanıcı hesabınıza 
                            eklenir.
                        </p>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ürünleri nasıl alabilirim?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Ürün satın almak için aşağıdaki adımları izleyebilirsiniz:
                        </p>
                        <ol class="list-decimal pl-8 mt-3 space-y-2">
                            <li>İlgilendiğiniz ürünü seçin</li>
                            <li>"Sepete Ekle" butonuna tıklayın</li>
                            <li>Sepetinize gidin ve "Ödeme Yap" butonuna tıklayın</li>
                            <li>Üye girişi yapın veya misafir olarak devam edin</li>
                            <li>Adres ve ödeme bilgilerinizi girin</li>
                            <li>Ödemenizi tamamlayın</li>
                            <li>Ürün bilgileri e-posta adresinize gönderilir</li>
                        </ol>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ödemeyi nasıl yapabilirim?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Güvenli ödeme sistemimiz üzerinden aşağıdaki ödeme yöntemlerini kullanabilirsiniz:
                        </p>
                        <ul class="list-disc pl-8 mt-3 space-y-2">
                            <li>Kredi kartı (Visa, MasterCard)</li>
                            <li>Banka kartı</li>
                            <li>Havale/EFT</li>
                            <li>PayPal</li>
                            <li>Shopier</li>
                        </ul>
                        <p class="mt-3">
                            Tüm işlemler 256-bit SSL sertifikası ile şifrelenir ve güvenli bir şekilde 
                            gerçekleştirilir.
                        </p>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ürünü aldıktan sonra nasıl kullanırım?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Satın alma işlemi tamamlandıktan sonra ürün bilgileri e-posta adresinize 
                            gönderilir. E-postada yer alan talimatları izleyerek ürünü 
                            kullanabilirsiniz. Bazı ürünler için ek kurulum veya aktivasyon 
                            işlemleri gerekebilir.
                        </p>
                        <p class="mt-3">
                            Ürünle ilgili teknik sorunlar için destek ekibimiz size yardımcı olur.
                        </p>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">İade edebilir miyim?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Dijital ürünlerin doğası gereği, lisans anahtarları, yazılım ürünleri 
                            ve dijital içerikler iade edilemez. Satın alma işlemi tamamlandıktan 
                            sonra müşteriye teslim edilen dijital ürünlerde cayma hakkı kullanılamaz.
                        </p>
                        <p class="mt-3">
                            Ancak ürün açıklamasında belirtilen özelliklerle uyuşmaması, 
                            ürünün çalışmaması veya teknik bir sorun içermesi durumunda 
                            istisnai olarak iade işlemi yapılabilir.
                        </p>
                        <p class="mt-3">
                            Detaylı bilgi için <a href="index.php?page=iade" class="text-indigo-600 dark:text-indigo-400 hover:underline">İade ve İptal Koşulları</a> sayfamızı inceleyebilirsiniz.
                        </p>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ürünler orijinal mi?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Evet, sitemizde yer alan tüm ürünler %100 orijinaldir ve üretici 
                            garantilidir. Ürünler doğrudan üreticilerden veya yetkili distribütörlerden 
                            temin edilmektedir.
                        </p>
                        <p class="mt-3">
                            Her ürün için geçerli lisans anahtarları sağlanır ve bu anahtarlar 
                            üretici sistemlerinde kayıtlıdır.
                        </p>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Destek hizmeti veriyor musunuz?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Evet, 7/24 destek hizmeti sunmaktayız. Ürünlerle ilgili teknik 
                            sorunlar, kullanım soruları veya diğer talepleriniz için aşağıdaki 
                            yollarla bizimle iletişime geçebilirsiniz:
                        </p>
                        <ul class="list-disc pl-8 mt-3 space-y-2">
                            <li>E-posta: destek@<?php echo $_SERVER['HTTP_HOST']; ?></li>
                            <li>Canlı destek: Web sitemizdeki "Canlı Destek" butonu</li>
                            <li>Destek talebi: Hesabınızdaki "Destek" bölümünden</li>
                        </ul>
                    </div>
                </div>
                
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <button class="flex justify-between items-center w-full p-6 text-left bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleFAQ(this)">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Hesabımı nasıl oluşturabilirim?</h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300">
                        <p>
                            Hesap oluşturmak için aşağıdaki adımları izleyebilirsiniz:
                        </p>
                        <ol class="list-decimal pl-8 mt-3 space-y-2">
                            <li>Web sitemize girin</li>
                            <li>Sağ üst köşedeki "Kayıt Ol" butonuna tıklayın</li>
                            <li>Gerekli bilgileri girin (ad, e-posta, şifre)</li>
                            <li>E-posta adresinize gelen onay mailini onaylayın</li>
                            <li>Hesabınıza giriş yapın</li>
                        </ol>
                        <p class="mt-3">
                            Hesabınızla siparişlerinizi takip edebilir, önceki alışverişlerinizi 
                            görüntüleyebilir ve destek talepleri oluşturabilirsiniz.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                <h3 class="text-xl font-semibold text-indigo-800 dark:text-indigo-200 mb-3">Başka bir sorunuz mu var?</h3>
                <p class="text-indigo-700 dark:text-indigo-300 mb-4">
                    Sıkça sorulan sorular bölümünde bulamadığınız sorularınız için destek ekibimiz 
                    size yardımcı olmaktan memnuniyet duyar.
                </p>
                <a href="index.php?page=iletisim" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-envelope mr-2"></i>
                    İletişim Formu
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>