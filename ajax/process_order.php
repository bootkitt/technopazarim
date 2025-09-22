<?php
require_once '../config.php';
require_once '../pay/shopier/shopier.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

// For users with 2FA enabled, verify they've completed 2FA for this checkout
$stmt = $db->prepare("SELECT uye_2fa_enabled FROM uyeler WHERE uye_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['uye_2fa_enabled'] == 1 && 
    (!isset($_SESSION['2fa_verified_for_checkout']) || !$_SESSION['2fa_verified_for_checkout'])) {
    echo json_encode(['success' => false, 'message' => '2FA doğrulaması gereklidir. Lütfen ödeme yapmadan önce 2FA kodunuzu doğrulayın.']);
    exit;
}

// Get form data
$firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';
$paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

// Validate required fields (matching the form fields that have 'required' attribute)
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Tüm alanları doldurun']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz e-posta adresi']);
    exit;
}

// Check if cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Sepetiniz boş']);
    exit;
}

$cart = $_SESSION['cart'];

// Calculate total and validate cart items
$total = 0;
$cartItems = [];

if (!empty($cart)) {
    $placeholders = str_repeat('?,', count($cart) - 1) . '?';
    $productIds = array_keys($cart);
    
    $stmt = $db->prepare("SELECT * FROM urunler WHERE urun_id IN ($placeholders) AND urun_durum = 1");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map products by ID for easy access
    $productMap = [];
    foreach ($products as $product) {
        $productMap[$product['urun_id']] = $product;
    }
    
    // Build cart items with product details and calculate total
    foreach ($cart as $productId => $quantity) {
        if (isset($productMap[$productId])) {
            $product = $productMap[$productId];
            $itemTotal = $product['urun_fiyat'] * $quantity;
            $total += $itemTotal;
            
            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'item_total' => $itemTotal
            ];
        }
    }
}

if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Sepetinizde geçerli ürün yok']);
    exit;
}

// Additional security: Check for suspicious order patterns
// For example, very high value orders or multiple orders in short time
$recentOrders = checkRecentOrders($_SESSION['user_id'], $db);
if ($recentOrders['suspicious']) {
    logSuspiciousActivity($_SESSION['user_id'], 'High frequency order attempt', $db);
    echo json_encode(['success' => false, 'message' => 'Şüpheli aktivite tespit edildi. Lütfen daha sonra tekrar deneyin.']);
    exit;
}

// Start database transaction
$db->beginTransaction();

try {
    // Create order
    // For bank transfer, set initial payment status to 'beklemede' (pending)
    $initialPaymentStatus = ($paymentMethod === 'bankTransfer') ? 'beklemede' : 'beklemede';
    $stmt = $db->prepare("INSERT INTO siparisler (uye_id, siparis_toplam, odeme_tipi, odeme_durum) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total, $paymentMethod, $initialPaymentStatus]);
    $orderId = $db->lastInsertId();
    
    // Insert order items
    $stmt = $db->prepare("INSERT INTO siparis_urunler (siparis_id, urun_id, urun_adet, urun_fiyat) VALUES (?, ?, ?, ?)");
    
    foreach ($cartItems as $item) {
        $stmt->execute([
            $orderId,
            $item['product']['urun_id'],
            $item['quantity'],
            $item['product']['urun_fiyat']
        ]);
        
        // Update product stock
        $stmt2 = $db->prepare("UPDATE urunler SET urun_stok = urun_stok - ? WHERE urun_id = ?");
        $stmt2->execute([$item['quantity'], $item['product']['urun_id']]);
    }
    
    // Commit transaction
    $db->commit();
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Clear 2FA verification for this checkout
    unset($_SESSION['2fa_verified_for_checkout']);
    
    // Get active payment methods
    $paymentSettings = getPaymentSettings($db);
    $activePaymentMethods = isset($paymentSettings['active_payment_methods']) ? json_decode($paymentSettings['active_payment_methods'], true) : ['bank_transfer'];
    
    // Check if the selected payment method is active
    if (!in_array($paymentMethod === 'bankTransfer' ? 'bank_transfer' : $paymentMethod, $activePaymentMethods)) {
        echo json_encode([
            'success' => false,
            'message' => 'Seçilen ödeme yöntemi şu anda devre dışı. Lütfen farklı bir ödeme yöntemi seçin.'
        ]);
        exit;
    }
    
    // Process payment based on method
    if ($paymentMethod === 'shopier') {
        // Initialize Shopier payment
        $shopier = new ShopierPayment();
        
        // Prepare order data for Shopier
        $orderData = [
            'order_id' => $orderId,
            'buyer_name' => $firstName,
            'buyer_surname' => $lastName,
            'buyer_email' => $email,
            'buyer_id' => $_SESSION['user_id'],
            'buyer_phone' => $phone,
            'billing_address' => $address,
            'billing_city' => $city,
            'billing_postcode' => $zip,
            'shipping_address' => $address,
            'shipping_city' => $city,
            'shipping_postcode' => $zip,
            'total_amount' => $total,
            'items' => array_map(function($item) {
                return [
                    'name' => $item['product']['urun_baslik'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['urun_fiyat']
                ];
            }, $cartItems)
        ];
        
        // Generate Shopier payment form
        $paymentForm = $shopier->generatePaymentForm($orderData);
        
        echo json_encode([
            'success' => true,
            'message' => 'Sipariş oluşturuldu',
            'redirect_url' => 'data:text/html,' . rawurlencode($paymentForm)
        ]);
    } else if ($paymentMethod === 'bankTransfer') {
        // For bank transfer, include bank account information
        $paymentSettings = getPaymentSettings($db);
        
        // Check if bank transfer is enabled
        if (!isset($paymentSettings['aktif']) || $paymentSettings['aktif'] != '1') {
            echo json_encode([
                'success' => false,
                'message' => 'Banka havalesi seçildi ancak bu ödeme yöntemi şu anda devre dışı. Lütfen farklı bir ödeme yöntemi seçin.'
            ]);
            exit;
        }
        
        // Check if bank transfer is properly configured
        if (empty($paymentSettings['iban']) && empty($paymentSettings['hesap_no'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Banka havalesi seçildi ancak banka bilgileri henüz yapılandırılmadı. Lütfen farklı bir ödeme yöntemi seçin veya site yöneticisine başvurun.'
            ]);
            exit;
        }
        
        // Create bank transfer instructions
        $bankTransferInfo = [
            'bank_name' => $paymentSettings['banka_adi'] ?? '',
            'account_holder' => $paymentSettings['hesap_sahibi'] ?? '',
            'iban' => $paymentSettings['iban'] ?? '',
            'swift_code' => $paymentSettings['swift_kodu'] ?? '',
            'account_number' => $paymentSettings['hesap_no'] ?? '',
            'branch_name' => $paymentSettings['sube_adi'] ?? '',
            'branch_code' => $paymentSettings['sube_kodu'] ?? ''
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Siparişiniz başarıyla oluşturuldu. Banka havalesi talimatları e-posta adresinize gönderildi.',
            'bank_transfer_info' => $bankTransferInfo,
            'order_id' => $orderId,
            'order_total' => $total,
            'payment_status' => 'pending_confirmation' // New status for frontend handling
        ]);
    } else {
        // Handle unknown payment method
        echo json_encode([
            'success' => false,
            'message' => 'Geçersiz ödeme yöntemi seçildi. Lütfen geçerli bir ödeme yöntemi seçin.'
        ]);
    }

} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage()
    ]);
}

// Function to check for recent orders that might be suspicious
function checkRecentOrders($userId, $db) {
    try {
        // Check if user has placed more than 3 orders in the last 10 minutes
        $stmt = $db->prepare("SELECT COUNT(*) as order_count FROM siparisler WHERE uye_id = ? AND siparis_tarih > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['order_count'] > 3) {
            return ['suspicious' => true, 'reason' => 'Too many orders in short time'];
        }
        
        return ['suspicious' => false];
    } catch (Exception $e) {
        // If there's an error in checking, we'll be cautious and treat as suspicious
        return ['suspicious' => true, 'reason' => 'Error checking orders'];
    }
}

// Function to log suspicious activity
function logSuspiciousActivity($userId, $activity, $db) {
    try {
        // In a real implementation, you might want to log this to a separate table
        // Suspicious activity logging removed for production
    } catch (Exception $e) {
        // Just log the error if logging fails
        // Failed to log suspicious activity
    }
}