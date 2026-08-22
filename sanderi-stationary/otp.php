<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function normalize_mobile(string $mobile): string
{
    $digits = preg_replace('/\D+/', '', $mobile) ?? '';
    if (strlen($digits) === 10) {
        $digits = '91' . $digits;
    }
    return $digits;
}

function send_sms_otp(string $mobile, string $otp): bool
{
    if (LOCAL_OTP_MODE) {
        $_SESSION['demo_otp'] = $otp;
        file_put_contents(__DIR__ . '/storage/otp.log', date('c') . " mobile={$mobile} otp={$otp}\n", FILE_APPEND | LOCK_EX);
        return true;
    }
    if (SMS_API_KEY === '') {
        throw new RuntimeException('SMS API key is not configured in config.php.');
    }

    $apiMobile = substr($mobile, -10);
    if (!preg_match('/^[0-9]{10}$/', $apiMobile)) {
        throw new RuntimeException('Enter a valid 10-digit Indian mobile number.');
    }
    $payload = json_encode([
        'variables_values' => $otp,
        'numbers' => $apiMobile,
        'route' => 'otp',
    ], JSON_THROW_ON_ERROR);
    if (SHOW_OTP_ON_SCREEN) {
        $_SESSION['demo_otp'] = $otp;
    }
    $ch = curl_init(SMS_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['authorization: ' . SMS_API_KEY, 'Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CAINFO => SMS_CA_BUNDLE,
    ]);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === false) {
        if (OTP_SCREEN_FALLBACK) return true;
        throw new RuntimeException('SMS connection failed: ' . ($curlError ?: 'unknown cURL error'));
    }
    if ($status < 200 || $status >= 300) {
        if (OTP_SCREEN_FALLBACK) return true;
        $details = json_decode($response, true);
        $providerMessage = is_array($details) ? ($details['message'] ?? $details['error'] ?? '') : '';
        $providerMessage = $providerMessage ?: trim(strip_tags($response));
        throw new RuntimeException('SMS provider rejected the request (HTTP ' . $status . '). ' . ($providerMessage ?: 'Check the Fast2SMS Send OTP endpoint in API Reference.'));
    }
    $result = json_decode($response, true);
    if (is_array($result) && array_key_exists('return', $result) && $result['return'] !== true) {
        if (OTP_SCREEN_FALLBACK) return true;
        throw new RuntimeException('SMS provider rejected the request. Check your API key and Fast2SMS account.');
    }
    if (SHOW_OTP_ON_SCREEN) {
        $_SESSION['demo_otp'] = $otp;
    }
    return true;
}

function create_otp(): string
{
    return (string) random_int(100000, 999999);
}
