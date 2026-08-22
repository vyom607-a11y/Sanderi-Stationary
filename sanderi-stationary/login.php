<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/otp.php';
require_once __DIR__ . '/security.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $mobile = normalize_mobile($_POST['mobile'] ?? '');
    $statement = db()->prepare('SELECT * FROM users WHERE mobile = ? LIMIT 1');
    $statement->execute([$mobile]);
    $user = $statement->fetch();
    if (!$user || !password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        $message = 'Invalid email or password.';
    } else {
        try {
            $otp = create_otp();
            db()->prepare('UPDATE users SET otp_hash=?, otp_expires_at=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id=?')->execute([password_hash($otp, PASSWORD_DEFAULT), $user['id']]);
            if (!send_sms_otp($user['mobile'], $otp)) throw new RuntimeException('SMS provider rejected the request.');
            $_SESSION['otp_user_id'] = (int) $user['id'];
            $_SESSION['otp_purpose'] = 'login';
            header('Location: verify-otp.php');
            exit;
        } catch (Throwable $error) {
            $message = $error->getMessage();
        }
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | Sanderi Stationary</title><link rel="stylesheet" href="style.css?v=9"><script src="app.js?v=8" defer></script></head><body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="index.php">Home</a><a href="products.php">Shop</a><a href="cart.php">Cart</a><a href="wishlist.php">Wishlist</a><a class="active-menu" href="login.php">Login</a><a href="register.php">Register</a></nav></header><main class="page-shell"><section class="form-card"><div class="eyebrow">Welcome back</div><h1>Login</h1><?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?><p class="alert">Password reset successfully. You can login now.</p><?php endif; ?><?php if ($message): ?><p class="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><label>Mobile number<input name="mobile" placeholder="10 digit mobile number" inputmode="tel" required></label><label>Password<input type="password" name="password" placeholder="Your password" required></label><button>Login and send OTP</button></form><p class="meta"><a href="forgot-password.php">Forgot password?</a></p><p class="meta">New here? <a href="register.php">Create an account</a></p></section></main></body></html>
