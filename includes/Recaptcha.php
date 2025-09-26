<?php
// app/Helpers/Recaptcha.php

class Recaptcha 
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    private const MIN_SCORE = 0.5; // Minimum güvenlik skoru (0.0-1.0)
    
    /**
     * Get reCAPTCHA secret key from config
     */
    private static function getSecretKey()
    {
        // First try to get from config constant
        if (defined('RECAPTCHA_SECRET_KEY')) {
            return RECAPTCHA_SECRET_KEY;
        }
        // Fallback to environment variable
        return Environment::get('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');
    }

    /**
     * Get reCAPTCHA site key from config
     */
    public static function getSiteKey()
    {
        // First try to get from config constant
        if (defined('RECAPTCHA_SITE_KEY')) {
            return RECAPTCHA_SITE_KEY;
        }
        // Fallback to environment variable
        return Environment::get('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI');
    }

    /**
     * reCAPTCHA v3 token'ını doğrular
     * 
     * @param string $token reCAPTCHA token
     * @param string $action Beklenen action (örn: 'login')
     * @param string|null $remoteip Kullanıcı IP adresi (opsiyonel)
     * @return array ['success' => bool, 'score' => float, 'action' => string, 'errors' => array]
     */
    public static function verify($token, $action = 'login', $remoteip = null)
    {
        // Token boş mu kontrol et
        if (empty($token)) {
            return [
                'success' => false,
                'score' => 0.0,
                'action' => '',
                'errors' => ['Token eksik']
            ];
        }

        // IP adresi otomatik algıla
        if ($remoteip === null) {
            $remoteip = self::getRealIpAddr();
        }

        // POST verilerini hazırla
        $postData = [
            'secret' => self::getSecretKey(),
            'response' => $token,
            'remoteip' => $remoteip
        ];

        try {
            // cURL ile Google'a doğrulama isteği gönder
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::VERIFY_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // cURL hatası kontrol et
            if ($curlError) {
                error_log("reCAPTCHA cURL error: " . $curlError);
                return [
                    'success' => false,
                    'score' => 0.0,
                    'action' => '',
                    'errors' => ['Ağ hatası: ' . $curlError]
                ];
            }

            // HTTP response kontrol et
            if ($httpCode !== 200) {
                error_log("reCAPTCHA HTTP error: " . $httpCode);
                return [
                    'success' => false,
                    'score' => 0.0,
                    'action' => '',
                    'errors' => ['HTTP hatası: ' . $httpCode]
                ];
            }

            // JSON response'u çöz
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("reCAPTCHA JSON error: " . json_last_error_msg());
                return [
                    'success' => false,
                    'score' => 0.0,
                    'action' => '',
                    'errors' => ['JSON parse hatası']
                ];
            }

            // Sonucu işle
            $success = isset($result['success']) && $result['success'] === true;
            $score = isset($result['score']) ? (float)$result['score'] : 0.0;
            $resultAction = isset($result['action']) ? $result['action'] : '';
            $errors = isset($result['error-codes']) ? $result['error-codes'] : [];

            // Action kontrol et
            if ($success && $action && $resultAction !== $action) {
                $success = false;
                $errors[] = 'Action uyuşmazlığı';
            }

            // Skor kontrol et
            if ($success && $score < self::MIN_SCORE) {
                $success = false;
                $errors[] = 'Düşük güvenlik skoru: ' . $score;
            }

            return [
                'success' => $success,
                'score' => $score,
                'action' => $resultAction,
                'errors' => $errors
            ];

        } catch (Exception $e) {
            error_log("reCAPTCHA exception: " . $e->getMessage());
            return [
                'success' => false,
                'score' => 0.0,
                'action' => '',
                'errors' => ['İstisna hatası: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Gerçek IP adresini al (proxy'ler için)
     */
    private static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '127.0.0.1';
    }

    /**
     * Minimum skoru ayarla
     */
    public static function setMinScore($score)
    {
        if ($score >= 0.0 && $score <= 1.0) {
            // Bu dinamik değişiklik için bir config sistemi gerekebilir
            // Şimdilik sabit MIN_SCORE kullanıyoruz
        }
    }

    /**
     * reCAPTCHA v2 response'unu doğrular
     * 
     * @param string $response reCAPTCHA response (g-recaptcha-response)
     * @param string|null $remoteip Kullanıcı IP adresi (opsiyonel)
     * @return array ['success' => bool, 'error-codes' => array]
     */
    public static function verifyV2($response, $remoteip = null)
    {
        // Response boş mu kontrol et
        if (empty($response)) {
            return [
                'success' => false,
                'error-codes' => ['missing-input-response']
            ];
        }

        // IP adresi otomatik algıla
        if ($remoteip === null) {
            $remoteip = self::getRealIpAddr();
        }

        // POST verilerini hazırla
        $postData = [
            'secret' => self::getSecretKey(),
            'response' => $response,
            'remoteip' => $remoteip
        ];

        try {
            // cURL ile Google'a doğrulama isteği gönder
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::VERIFY_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $responseData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // cURL hatası kontrol et
            if ($curlError) {
                error_log("reCAPTCHA v2 cURL error: " . $curlError);
                return [
                    'success' => false,
                    'error-codes' => ['network-error']
                ];
            }

            // HTTP response kontrol et
            if ($httpCode !== 200) {
                error_log("reCAPTCHA v2 HTTP error: " . $httpCode);
                return [
                    'success' => false,
                    'error-codes' => ['http-error']
                ];
            }

            // JSON response'u çöz
            $result = json_decode($responseData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("reCAPTCHA v2 JSON error: " . json_last_error_msg());
                return [
                    'success' => false,
                    'error-codes' => ['json-parse-error']
                ];
            }

            // Sonucu döndür
            return [
                'success' => isset($result['success']) && $result['success'] === true,
                'error-codes' => isset($result['error-codes']) ? $result['error-codes'] : []
            ];

        } catch (Exception $e) {
            error_log("reCAPTCHA v2 exception: " . $e->getMessage());
            return [
                'success' => false,
                'error-codes' => ['exception-error']
            ];
        }
    }

    /**
     * Test modunda dummy doğrulama (development için)
     */
    public static function verifyTest($token, $action = 'login')
    {
        if (empty($token)) {
            return [
                'success' => false,
                'score' => 0.0,
                'action' => '',
                'errors' => ['Token eksik']
            ];
        }

        // Test modunda her zaman başarılı
        return [
            'success' => true,
            'score' => 0.9,
            'action' => $action,
            'errors' => []
        ];
    }
}
?>