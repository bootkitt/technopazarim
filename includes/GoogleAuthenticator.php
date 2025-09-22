<?php
/**
 * Google Authenticator implementation for two-factor authentication
 */

class GoogleAuthenticator {
    protected $secretLength = 16;
    
    /**
     * Generate a secret key
     * 
     * @return string
     */
    public function generateSecret() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $this->secretLength; $i++) {
            $secret .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $secret;
    }
    
    /**
     * Get QR code URL for Google Authenticator app
     * 
     * @param string $name
     * @param string $secret
     * @param string $title
     * @return string
     */
    public function getQRCodeUrl($name, $secret, $title = null) {
        $data = 'otpauth://totp/' . rawurlencode($name) . '?secret=' . $secret;
        if ($title) {
            $data .= '&issuer=' . rawurlencode($title);
        }
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($data);
    }
    
    /**
     * Verify a code
     * 
     * @param string $secret
     * @param string $code
     * @param int $discrepancy
     * @return bool
     */
    public function verifyCode($secret, $code, $discrepancy = 1) {
        $currentTimeSlice = floor(time() / 30);
        
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $timeSlice = $currentTimeSlice + $i;
            $generatedCode = $this->getCode($secret, $timeSlice);
            
            if ($this->timingSafeEquals($generatedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get the code at a specific time slice
     * 
     * @param string $secret
     * @param int $timeSlice
     * @return string
     */
    public function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        
        $secretKey = $this->base32Decode($secret);
        
        // Pack time into binary string
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);
        
        // Hash it with users secret key
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        
        // Use last nibble of result as index/offset
        $offset = ord(substr($hash, -1)) & 0x0F;
        
        // Grab 4 bytes of the result
        $binary = substr($hash, $offset, 4);
        
        // Unpack binary value
        $value = unpack('N', $binary)[1];
        $value = $value & 0x7FFFFFFF;
        
        $modulo = pow(10, 6);
        
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Decode base32 encoded string
     * 
     * @param string $secret
     * @return string
     */
    private function base32Decode($secret) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $buffer = 0;
        $bufferBits = 0;
        $result = '';
        
        for ($i = 0; $i < strlen($secret); $i++) {
            $char = $secret[$i];
            $index = strpos($chars, $char);
            
            if ($index === false) {
                continue;
            }
            
            $buffer = ($buffer << 5) | $index;
            $bufferBits += 5;
            
            if ($bufferBits >= 8) {
                $bufferBits -= 8;
                $byte = ($buffer >> $bufferBits) & 0xFF;
                $result .= chr($byte);
            }
        }
        
        return $result;
    }
    
    /**
     * Timing safe equals comparison
     * 
     * @param string $safe
     * @param string $user
     * @return bool
     */
    private function timingSafeEquals($safe, $user) {
        if (function_exists('hash_equals')) {
            return hash_equals($safe, $user);
        }
        
        // Fall back to regular string comparison
        return $safe === $user;
    }
}