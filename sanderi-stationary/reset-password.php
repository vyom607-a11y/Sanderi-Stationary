<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';

if (empty($_SESSION['reset_user_id'])) { header('Location: forgot-password.php'); exit; }
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $query = db()->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
    $query->execute([$_SESSION['reset_user_id']]);
    $user = $query->fetch();
    $password = $_POST['password'] ?? '';
    $validOtp = $user && $user['otp_expires_at'] && strtotime($user['otp_expires_at']) >= time() && password_verify(trim($_POST['otp'] ?? ''), $user['otp_hash'] ?? '');
    if (!$validOtp) {
        $message = 'OTP is invalid or expired.';
    } elseif (strlen($password) < 8 || $password !== ($_POST['confirm_password'] ?? '')) {
        $message = 'Password must be at least 8 characters and match confirmation.';
    } else {
        db()->prepare('UPDATE users SET password_hash=?, otp_hash=NULL, otp_expires_at=NULL WHERE id=?')->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        unset($_SESSION['reset_user_id']);
        header('Location: login.php?reset=success');
        exit;
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reset Password | Sanderi Stationary</title><link rel="stylesheet" href="style.css?v=9"><script src="app.js?v=8" defer></script></head><body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="index.php">Home</a><a href="products.php">Shop</a><a href="login.php">Login</a></nav></header><main class="page-shell"><section class="form-card"><div class="eyebrow">Secure reset</div><h1>Set new password</h1><?php if ($message): ?><p class="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><label>Mobile OTP<input name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="6 digit OTP" required></label><label>New password<input type="password" name="password" placeholder="At least 8 characters" required></label><label>Confirm password<input type="password" name="confirm_password" placeholder="Repeat your password" required></label><button>Reset password</button></form></section></main></body></html>
