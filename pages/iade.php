<?php
$pageTitle = "İade ve İptal";
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="card rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">İade ve İptal Koşulları</h1>
        
        <div class="prose prose-lg dark:prose-invert max-w-none">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">1. Genel İlkeler</h2>
            <p class="mb-4">
                <?php echo SITE_NAME; ?> olarak müşteri memnuniyetini ön planda tutuyor ve ürünlerimizle ilgili 
                tüm sorularınızı yanıtlamaktan memnuniyet duyarız. Bu nedenle, siparişlerinizde dikkatli olmanızı 
                ve ürün açıklamalarını dikkatlice okumanızı öneririz.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">2. Cayma Hakkı</h2>
            <p class="mb-4">
                6502 sayılı Mesafeli Sözleşmeler Yönetmeliği kapsamında, müşterilerimiz 14 (on dört) gün 
                içerisinde hiçbir gerekçe göstermeksizin ve cezai şart ödemeksizin sözleşmeden cayma hakkına 
                sahiptir. Cayma hakkı süresi, sözleşmenin kurulmasından itibaren başlar.
            </p>
            <p class="mb-4">
                Cayma hakkının kullanılması için 14 (on dört) günlük süre içerisinde <?php echo SITE_NAME; ?> 
                müşteri hizmetlerine yazılı bildirimde bulunulması gerekmektedir. Bu bildirimi yapmak için 
                destek@<?php echo $_SERVER['HTTP_HOST']; ?> e-posta adresini kullanabilirsiniz.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">3. İade Koşulları</h2>
            <p class="mb-4">
                Dijital ürünlerin doğası gereği, lisans anahtarları, yazılım ürünleri ve dijital içerikler 
                iade edilemez. Satın alma işlemi tamamlandıktan sonra müşteriye teslim edilen dijital ürünlerde 
                cayma hakkı kullanılamaz.
            </p>
            <p class="mb-4">
                Ancak aşağıdaki durumlarda istisnai olarak iade işlemi yapılabilir:
            </p>
            <ul class="list-disc pl-8 mb-4 space-y-2">
                <li>Ürünün açıklamasında belirtilen özelliklerle uyuşmaması durumunda</li>
                <li>Ürünün çalışmaması veya teknik bir sorun içermesi durumunda</li>
                <li>Yanlış ürün gönderilmesi durumunda</li>
            </ul>
            
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">4. İade Süreci</h2>
            <p class="mb-4">
                İade talebinde bulunmak için destek sistemimiz üzerinden bize ulaşmanız gerekmektedir. 
                Talebiniz incelendikten sonra size iade prosedürü hakkında bilgi verilecektir.
            </p>
            <p class="mb-4">
                Onaylanan iade taleplerinde, ödemeniz orijinal ödeme yöntemi ile 30 gün içerisinde iade edilir.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">5. İptal Koşulları</h2>
            <p class="mb-4">
                Sipariş iptal talepleri, ödeme onaylandıktan sonra 2 saat içerisinde yapıldığı takdirde 
                işleme alınabilir. Bu sürenin ardından ürün teslim süreci başladığı için iptal işlemi 
                yapılamaz.
            </p>
            <p class="mb-4">
                Dijital ürünlerin doğası gereği, teslimat anında müşteriye teslim edildikten sonra sipariş 
                iptal edilemez.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">6. İletişim</h2>
            <p class="mb-4">
                İade ve iptal işlemleri ile ilgili tüm sorularınız için destek@<?php echo $_SERVER['HTTP_HOST']; ?> 
                adresinden bizimle iletişime geçebilirsiniz.
            </p>
        </div>
    </div>
</div>