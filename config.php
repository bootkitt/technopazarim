<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_URL', 'http://localhost/');
define('SITE_NAME', 'TechnoPazarim');

// Shopier API configuration (demo values)
define('SHOPIER_API_KEY', 'SHOPIER_KEY');
define('SHOPIER_API_SECRET', 'SHOPIER_SECRET_KEY');

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Session configuration
session_start();

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    // Check if user is logged in and has admin role
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

function formatCurrency($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

// Security function to log events
function logSecurityEvent($userId, $eventType, $description, $db) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $db->prepare("INSERT INTO guvenlik_kayitlari (uye_id, olay_tipi, aciklama, ip_adresi, kullanici_temsilcisi) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $eventType, $description, $ip, $userAgent]);
    } catch (Exception $e) {
        // Log the error but don't interrupt the main process
        error_log("Security log error: " . $e->getMessage());
    }
}

// Function to get client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Get payment settings from database
 * 
 * @param PDO $db Database connection
 * @return array Payment settings
 */
function getPaymentSettings($db) {
    try {
        $stmt = $db->prepare("SELECT ayar_adi, ayar_deger FROM odeme_ayarlari");
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convert to associative array
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['ayar_adi']] = $setting['ayar_deger'];
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error getting payment settings: " . $e->getMessage());
        return [];
    }
}

// Admin authentication functions
function loginAdmin($userId, $username) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_role'] = 'admin';
    $_SESSION['admin_username'] = $username;
}

function logoutAdmin() {
    // Clear admin-specific session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    unset($_SESSION['admin_username']);
}

function verifyPassword($password, $hashedPassword) {
    // For backward compatibility, we'll check both MD5 and password_hash
    if (md5($password) === $hashedPassword) {
        return true;
    }
    return password_verify($password, $hashedPassword);
}

function clean($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Track user visits
 * 
 * @param PDO $db Database connection
 * @param int $userId User ID (optional)
 * @param string $source Visit source (optional)
 */
function trackVisit($db, $userId = null, $source = null) {
    try {
        $ipAddress = get_client_ip();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $db->prepare("INSERT INTO analiz_ziyaretler (uye_id, ip_adresi, kullanici_temsilcisi, kaynak) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $ipAddress, $userAgent, $source]);
    } catch (Exception $e) {
        // Log error but don't interrupt main process
        error_log("Visit tracking error: " . $e->getMessage());
    }
}

/**
 * Track product views
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param int $userId User ID (optional)
 * @param int $duration Time spent on page (optional)
 */
function trackProductView($db, $productId, $userId = null, $duration = null) {
    try {
        $stmt = $db->prepare("INSERT INTO analiz_urun_goruntulemeler (uye_id, urun_id, kalma_suresi) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $productId, $duration]);
    } catch (Exception $e) {
        // Log error but don't interrupt main process
        error_log("Product view tracking error: " . $e->getMessage());
    }
}

/**
 * Track cart abandonment
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param int $userId User ID (optional)
 */
function trackCartAbandonment($db, $productId, $userId = null) {
    try {
        $stmt = $db->prepare("INSERT INTO analiz_sepet_birakmalar (uye_id, urun_id) VALUES (?, ?)");
        $stmt->execute([$userId, $productId]);
    } catch (Exception $e) {
        // Log error but don't interrupt main process
        error_log("Cart abandonment tracking error: " . $e->getMessage());
    }
}

/**
 * Update cart abandonment with removal time
 * 
 * @param PDO $db Database connection
 * @param int $abandonmentId Abandonment record ID
 */
function updateCartAbandonment($db, $abandonmentId) {
    try {
        $stmt = $db->prepare("UPDATE analiz_sepet_birakmalar SET cikarma_tarihi = NOW() WHERE birakma_id = ?");
        $stmt->execute([$abandonmentId]);
    } catch (Exception $e) {
        // Log error but don't interrupt main process
        error_log("Cart abandonment update error: " . $e->getMessage());
    }
}