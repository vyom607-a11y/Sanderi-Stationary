<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'sanderi_stationary';
const DB_USER = 'root';
const DB_PASS = '';
const SMS_API_KEY = '3N1goOV5CZQdHpmRIGkC5pzsZ7VpGWgCvbRQ7q3CLDs5JiJQgjocMeq8GC2S'; // Replace this placeholder with your Fast2SMS API key.
const SMS_API_URL = 'https://www.fast2sms.com/dev/bulkV2';
const SMS_CA_BUNDLE = 'C:\\Wamp64\\bin\\php\\php8.0.26\\extras\\ssl\\cacert.pem';
const LOCAL_OTP_MODE = true; // Instant local OTP mode for the demo/submission.
const SHOW_OTP_ON_SCREEN = true; // Testing only. Set false before production use.
const OTP_SCREEN_FALLBACK = true; // Continue with the displayed OTP when the SMS provider is unavailable.
const APP_NAME = 'Sanderi Stationary';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();
