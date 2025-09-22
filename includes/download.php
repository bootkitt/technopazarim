<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

// Get parameters
$productId = isset($_GET['product']) ? intval($_GET['product']) : 0;
$orderId = isset($_GET['order']) ? intval($_GET['order']) : 0;
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validate parameters
if (!$productId || !$orderId || !$token) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Geçersiz istek';
    exit;
}

// Verify token (in a real implementation, you would check this against your secret)
// For demo purposes, we'll just check if it exists

// Check if user has purchased this product in this order
$stmt = $db->prepare("SELECT s.siparis_id FROM siparisler s 
                      INNER JOIN siparis_urunler su ON s.siparis_id = su.siparis_id 
                      WHERE s.siparis_id = ? AND su.urun_id = ? AND s.uye_id = ? AND s.odeme_durum = 'tamamlandi'");
$stmt->execute([$orderId, $productId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Bu ürünü indirme yetkiniz yok';
    exit;
}

// Get product details
$stmt = $db->prepare("SELECT * FROM urunler WHERE urun_id = ? AND urun_durum = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('HTTP/1.0 404 Not Found');
    echo 'Ürün bulunamadı';
    exit;
}

// For file products, we would serve the file
// For demo purposes, we'll just show a download page
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün İndir - <?php echo htmlspecialchars($product['urun_baslik']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-download me-2"></i>Ürün İndir</h5>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-file-download fa-5x text-primary mb-4"></i>
                        <h3><?php echo htmlspecialchars($product['urun_baslik']); ?></h3>
                        <p class="lead">Satın aldığınız ürün için indirme bağlantısı aşağıdadır.</p>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Bu bağlantı 24 saat boyunca geçerlidir. Lütfen dosyayı güvenli bir yere indirin.
                        </div>
                        
                        <a href="#" class="btn btn-success btn-lg">
                            <i class="fas fa-download me-2"></i>Şimdi İndir
                        </a>
                        
                        <div class="mt-4">
                            <a href="index.php?page=account&section=downloads" class="btn btn-outline-primary">
                                <i class="fas fa-history me-2"></i>İndirme Geçmişim
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>