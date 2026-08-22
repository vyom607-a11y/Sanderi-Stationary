<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';

if (empty($_SESSION['otp_user_id'])) { header('Location: login.php'); exit; }
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $statement = db()->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
    $statement->execute([$_SESSION['otp_user_id']]);
    $user = $statement->fetch();
    $valid = $user && $user['otp_expires_at'] && strtotime($user['otp_expires_at']) >= time() && password_verify(trim($_POST['otp'] ?? ''), $user['otp_hash'] ?? '');
    if (!$valid) {
        $message = 'OTP is invalid or expired.';
    } else {
        db()->prepare('UPDATE users SET mobile_verified=1, otp_hash=NULL, otp_expires_at=NULL WHERE id=?')->execute([$user['id']]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['otp_user_id'], $_SESSION['otp_purpose']);
        header('Location: profile.php');
        exit;
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Verify mobile | Sanderi Stationary</title><link rel="stylesheet" href="style.css"><script src="app.js" defer></script></head><body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a></header><main class="page-shell"><section class="form-card"><div class="eyebrow">One last step</div><h1>Verify mobile number</h1><p>Enter the 6-digit code sent by SMS.</p><?php if (SHOW_OTP_ON_SCREEN && isset($_SESSION['demo_otp'])): ?><p class="demo-otp">Testing OTP: <?= htmlspecialchars($_SESSION['demo_otp']) ?></p><?php endif; ?><?php if($message): ?><p class="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><label>Verification code<input name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="6 digit OTP" required></label><button>Verify OTP</button></form></section></main></body></html>
