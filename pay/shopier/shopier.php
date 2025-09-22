<?php
/**
 * Shopier Payment Gateway Integration
 * 
 * This class handles the integration with Shopier payment gateway
 * for processing digital goods payments
 */

class ShopierPayment {
    private $api_key;
    private $api_secret;
    private $base_url;
    private $site_url;
    
    public function __construct() {
        $this->api_key = SHOPIER_API_KEY;
        $this->api_secret = SHOPIER_API_SECRET;
        $this->base_url = 'https://www.shopier.com/ShowProduct/api_pay4.php';
        $this->site_url = SITE_URL;
    }
    
    /**
     * Generate payment form for Shopier
     * 
     * @param array $orderData Order information
     * @return string HTML form for payment
     */
    public function generatePaymentForm($orderData) {
        // Required parameters
        $params = [
            'API_key' => $this->api_key,
            'website_index' => 1, // This should be your website index from Shopier
            'platform_order_id' => $orderData['order_id'],
            'product_name' => $this->formatProductName($orderData['items']),
            'product_type' => 1, // Digital product
            'buyer_name' => $orderData['buyer_name'],
            'buyer_surname' => $orderData['buyer_surname'],
            'buyer_email' => $orderData['buyer_email'],
            'buyer_account_age' => 0,
            'buyer_id_nr' => $orderData['buyer_id'],
            'buyer_phone' => $orderData['buyer_phone'] ?? '',
            'billing_address' => $orderData['billing_address'] ?? '',
            'billing_city' => $orderData['billing_city'] ?? '',
            'billing_country' => 'Turkey',
            'billing_postcode' => $orderData['billing_postcode'] ?? '',
            'shipping_address' => $orderData['shipping_address'] ?? '',
            'shipping_city' => $orderData['shipping_city'] ?? '',
            'shipping_country' => 'Turkey',
            'shipping_postcode' => $orderData['shipping_postcode'] ?? '',
            'total_order_value' => number_format($orderData['total_amount'], 2, '.', ''),
            'currency' => 'TRY',
            'platform' => 0,
            'is_in_frame' => 0,
            'modul_version' => '1.0.0',
            'random_nr' => $this->generateRandomString(),
            'notification_url' => $this->site_url . '/pay/shopierNotify.php' // Notification URL
        ];
        
        // Generate signature
        $params['signature'] = $this->generateSignature($params);
        
        // Create form
        $form = '<form id="shopier_payment_form" action="' . $this->base_url . '" method="post">';
        foreach ($params as $key => $value) {
            $form .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
        }
        $form .= '<input type="hidden" name="customer_ip" value="' . $this->getCustomerIP() . '">';
        $form .= '<input type="submit" value="Ödeme Yap" class="btn btn-success">';
        $form .= '</form>';
        
        // Auto-submit JavaScript
        $form .= '<script>document.getElementById("shopier_payment_form").submit();</script>';
        
        return $form;
    }
    
    /**
     * Verify payment notification from Shopier
     * 
     * @param array $postData POST data from Shopier
     * @return bool True if payment is valid
     */
    public function verifyPayment($postData) {
        // Check if required fields exist
        if (!isset($postData['platform_order_id']) || 
            !isset($postData['API_key']) || 
            !isset($postData['status']) || 
            !isset($postData['total_order_value']) || 
            !isset($postData['signature'])) {
            return false;
        }
        
        // Check API key
        if ($postData['API_key'] !== $this->api_key) {
            return false;
        }
        
        // Check status (1 = successful payment)
        if ($postData['status'] != 1) {
            return false;
        }
        
        // Additional security: Check if this order exists and is pending
        global $db;
        $stmt = $db->prepare("SELECT * FROM siparisler WHERE siparis_id = ? AND odeme_durum = 'beklemede'");
        $stmt->execute([intval($postData['platform_order_id'])]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            // Order doesn't exist or isn't pending
            return false;
        }
        
        // Verify signature
        $expectedSignature = $this->generateNotificationSignature($postData);
        if (!hash_equals($expectedSignature, $postData['signature'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate signature for payment request
     * 
     * @param array $params Payment parameters
     * @return string Signature
     */
    private function generateSignature($params) {
        // Sort parameters alphabetically
        ksort($params);
        
        // Create data string
        $data = '';
        foreach ($params as $key => $value) {
            if ($key !== 'signature') {
                $data .= $key . '=' . $value . '|';
            }
        }
        $data = rtrim($data, '|');
        
        // Generate signature using HMAC-SHA256
        return base64_encode(hash_hmac('sha256', $data, $this->api_secret, true));
    }
    
    /**
     * Generate signature for payment notification
     * 
     * @param array $postData Notification data
     * @return string Signature
     */
    private function generateNotificationSignature($postData) {
        // Create data string from notification parameters
        $data = $postData['platform_order_id'] . $postData['total_order_value'];
        
        // Generate signature using HMAC-SHA256
        return base64_encode(hash_hmac('sha256', $data, $this->api_secret, true));
    }
    
    /**
     * Format product name for Shopier
     * 
     * @param array $items Order items
     * @return string Formatted product name
     */
    private function formatProductName($items) {
        if (count($items) == 1) {
            return substr($items[0]['name'], 0, 50);
        } else {
            return 'Çoklu Ürün Satışı (' . count($items) . ' ürün)';
        }
    }
    
    /**
     * Generate random string for security
     * 
     * @param int $length Length of string
     * @return string Random string
     */
    private function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Get customer IP address
     * 
     * @return string IP address
     */
    private function getCustomerIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    
    /**
     * Process successful payment
     * 
     * @param array $paymentData Payment data from Shopier
     * @return bool True if processed successfully
     */
    public function processSuccessfulPayment($paymentData) {
        try {
            global $db;
            
            $orderId = intval($paymentData['platform_order_id']);
            
            // Additional security: Verify order hasn't already been processed
            $stmt = $db->prepare("SELECT odeme_durum FROM siparisler WHERE siparis_id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                return false;
            }
            
            // If order is already completed, don't process again
            if ($order['odeme_durum'] === 'tamamlandi') {
                return true; // Already processed, but not an error
            }
            
            // Update order status
            // Use platform_order_id if order_id is not available
            $shopierOrderId = isset($paymentData['order_id']) ? $paymentData['order_id'] : $paymentData['platform_order_id'];
            $stmt = $db->prepare("UPDATE siparisler SET odeme_durum = 'tamamlandi', shopier_order_id = ? WHERE siparis_id = ?");
            $result = $stmt->execute([
                $shopierOrderId,
                $orderId
            ]);
            
            if ($result) {
                // Deliver digital products
                $this->deliverDigitalProducts($orderId);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Shopier payment processing error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deliver digital products after successful payment
     * 
     * @param int $orderId Order ID
     * @return void
     */
    private function deliverDigitalProducts($orderId) {
        global $db;
        
        // Get order items
        $stmt = $db->prepare("SELECT su.*, u.urun_tip FROM siparis_urunler su INNER JOIN urunler u ON su.urun_id = u.urun_id WHERE su.siparis_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get user info
        $stmt = $db->prepare("SELECT uye_eposta FROM uyeler WHERE uye_id = (SELECT uye_id FROM siparisler WHERE siparis_id = ?)");
        $stmt->execute([$orderId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return;
        }
        
        // For each item, assign digital stock and send to user
        foreach ($items as $item) {
            if ($item['urun_tip'] === 'license') {
                // Get available license key
                $stmt = $db->prepare("SELECT stok_id, stok_kodu FROM dijital_stok WHERE urun_id = ? AND stok_durum = 'aktif' LIMIT 1");
                $stmt->execute([$item['urun_id']]);
                $license = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($license) {
                    // Mark license as used
                    $stmt = $db->prepare("UPDATE dijital_stok SET stok_durum = 'kullanildi', kullanilan_siparis_id = ? WHERE stok_id = ?");
                    $stmt->execute([$orderId, $license['stok_id']]);
                    
                    // Send license key to user via email
                    $this->sendLicenseKey($user['uye_eposta'], $license['stok_kodu']);
                }
            } elseif ($item['urun_tip'] === 'file') {
                // For file products, send download link
                $downloadLink = $this->generateDownloadLink($item['urun_id'], $orderId);
                $this->sendDownloadLink($user['uye_eposta'], $downloadLink);
            }
        }
    }
    
    /**
     * Send license key to user
     * 
     * @param string $email User email
     * @param string $licenseKey License key
     * @return void
     */
    private function sendLicenseKey($email, $licenseKey) {
        $subject = 'Satın Aldığınız Ürün - Lisans Anahtarı';
        $message = "Satın aldığınız ürün için lisans anahtarınız aşağıdadır:\n\n";
        $message .= "Lisans Anahtarı: " . $licenseKey . "\n\n";
        $message .= "Bu anahtarı ilgili platformda kullanabilirsiniz.\n";
        $message .= "Herhangi bir sorunuz olursa destek talebi oluşturabilirsiniz.\n\n";
        $message .= "Saygılarımızla,\n" . SITE_NAME . " Ekibi";
        
        // In a real implementation, you would send this via email
        // mail($email, $subject, $message);
        
        // For demo purposes, we'll just log it
        error_log("License key sent to $email: $licenseKey");
    }
    
    /**
     * Generate download link for file products
     * 
     * @param int $productId Product ID
     * @param int $orderId Order ID
     * @return string Download link
     */
    private function generateDownloadLink($productId, $orderId) {
        // Generate a secure token that includes the order ID
        $tokenData = $productId . '|' . $orderId . '|' . time();
        $token = base64_encode(hash_hmac('sha256', $tokenData, $this->api_secret, true));
        
        // In a real implementation, you would generate a secure download link
        // This is a placeholder implementation
        return SITE_URL . "/includes/download.php?product=" . $productId . "&order=" . $orderId . "&token=" . urlencode($token);
    }
    
    /**
     * Send download link to user
     * 
     * @param string $email User email
     * @param string $downloadLink Download link
     * @return void
     */
    private function sendDownloadLink($email, $downloadLink) {
        $subject = 'Satın Aldığınız Ürün - İndirme Bağlantısı';
        $message = "Satın aldığınız ürün için indirme bağlantınız aşağıdadır:\n\n";
        $message .= "İndirme Bağlantısı: " . $downloadLink . "\n\n";
        $message .= "Bu bağlantı 24 saat boyunca geçerlidir.\n";
        $message .= "Herhangi bir sorunuz olursa destek talebi oluşturabilirsiniz.\n\n";
        $message .= "Saygılarımızla,\n" . SITE_NAME . " Ekibi";
        
        // In a real implementation, you would send this via email
        // mail($email, $subject, $message);
        
        // For demo purposes, we'll just log it
        error_log("Download link sent to $email: $downloadLink");
    }
}