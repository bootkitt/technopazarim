<?php
// Fetch completed orders with digital products
$stmt = $db->prepare("SELECT s.siparis_id, s.siparis_tarih, su.urun_id, su.urun_adet, u.urun_baslik, u.urun_tip 
                     FROM siparisler s 
                     INNER JOIN siparis_urunler su ON s.siparis_id = su.siparis_id 
                     INNER JOIN urunler u ON su.urun_id = u.urun_id 
                     WHERE s.uye_id = ? AND s.odeme_durum = 'tamamlandi' AND u.urun_tip IN ('license', 'file')
                     ORDER BY s.siparis_tarih DESC");
$stmt->execute([$_SESSION['user_id']]);
$downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group downloads by order
$orders = [];
foreach ($downloads as $download) {
    $orderId = $download['siparis_id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'siparis_id' => $orderId,
            'siparis_tarih' => $download['siparis_tarih'],
            'items' => []
        ];
    }
    $orders[$orderId]['items'][] = $download;
}
?>

<div class="card rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">İndirilebilir Ürünler</h2>
    </div>
    <div class="p-6">
        <?php if (empty($orders)): ?>
            <div class="text-center py-12">
                <i class="fas fa-info-circle text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">İndirilebilir ürün bulunamadı</h3>
                <p class="text-gray-500 dark:text-gray-400">Tamamlanmış siparişiniz bulunmuyor veya indirilebilir ürün içermiyor.</p>
                <a href="index.php?page=products" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Ürünlere Gözat
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card rounded-xl overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sipariş #<?php echo $order['siparis_id']; ?></h3>
                        <span class="mt-2 sm:mt-0 text-sm text-gray-500 dark:text-gray-400"><?php echo date('d.m.Y H:i', strtotime($order['siparis_tarih'])); ?></span>
                    </div>
                    <div class="p-6">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex flex-col md:flex-row md:items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($item['urun_baslik']); ?></h4>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <?php if ($item['urun_tip'] === 'license'): ?>
                                            <i class="fas fa-key mr-2"></i>
                                            <span>Lisans Anahtarı</span>
                                        <?php elseif ($item['urun_tip'] === 'file'): ?>
                                            <i class="fas fa-file-download mr-2"></i>
                                            <span>Dosya İndirme</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($item['urun_tip'] === 'license'): ?>
                                        <!-- For license products, show license key -->
                                        <button class="view-license px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" data-product-id="<?php echo $item['urun_id']; ?>" data-order-id="<?php echo $order['siparis_id']; ?>">
                                            <i class="fas fa-key mr-2"></i> Lisans Anahtarını Görüntüle
                                        </button>
                                    <?php elseif ($item['urun_tip'] === 'file'): ?>
                                        <!-- For file products, show download link -->
                                        <a href="includes/download.php?order=<?php echo $order['siparis_id']; ?>&product=<?php echo $item['urun_id']; ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-download mr-2"></i> İndir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- License Key Modal -->
<div id="licenseModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Lisans Anahtarı</h3>
                        <div class="mt-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ürün:</label>
                                <div id="licenseProductName" class="font-medium text-gray-900 dark:text-white"></div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lisans Anahtarı:</label>
                                <div class="flex">
                                    <input type="text" id="licenseKey" readonly class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <button id="copyLicenseKey" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-300">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 p-3 rounded-md">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Bu lisans anahtarını ilgili platformda kullanabilirsiniz.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="closeLicenseModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View license key buttons
    const viewLicenseButtons = document.querySelectorAll('.view-license');
    const licenseModal = document.getElementById('licenseModal');
    const closeLicenseModal = document.getElementById('closeLicenseModal');
    
    viewLicenseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const orderId = this.dataset.orderId;
            
            // Fetch license key via AJAX
            fetch('ajax/get_license_key.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('licenseProductName').textContent = data.product_name;
                    document.getElementById('licenseKey').value = data.license_key;
                    licenseModal.classList.remove('hidden');
                } else {
                    showMessage(data.message || 'Lisans anahtarı alınamadı.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Bir hata oluştu.', 'danger');
            });
        });
    });
    
    // Close modal
    closeLicenseModal.addEventListener('click', function() {
        licenseModal.classList.add('hidden');
    });
    
    // Copy license key button
    const copyLicenseKeyButton = document.getElementById('copyLicenseKey');
    if (copyLicenseKeyButton) {
        copyLicenseKeyButton.addEventListener('click', function() {
            const licenseKeyInput = document.getElementById('licenseKey');
            licenseKeyInput.select();
            document.execCommand('copy');
            showMessage('Lisans anahtarı panoya kopyalandı!', 'success');
        });
    }
});
</script>