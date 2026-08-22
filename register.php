<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/otp.php';
require_once __DIR__ . '/security.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $mobile = normalize_mobile($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Enter a valid email address.';
    } elseif ($name === '' || strlen($mobile) !== 12 || strlen($password) < 8 || $password !== ($_POST['confirm_password'] ?? '')) {
        $message = 'Enter valid details. Password must be at least 8 characters.';
    } else {
        $pdo = null;
        try {
            $email = $email !== '' ? $email : $mobile . '@local.sanderi';
            $otp = create_otp();
            $pdo = db();
            $pdo->beginTransaction();
            $statement = $pdo->prepare('INSERT INTO users (full_name,email,mobile,password_hash,otp_hash,otp_expires_at) VALUES (?,?,?,?,?,DATE_ADD(NOW(), INTERVAL 10 MINUTE))');
            $statement->execute([$name, $email, $mobile, password_hash($password, PASSWORD_DEFAULT), password_hash($otp, PASSWORD_DEFAULT)]);
            $userId = (int) $pdo->lastInsertId();
            if (!send_sms_otp($mobile, $otp)) throw new RuntimeException('SMS provider rejected the request.');
            $pdo->commit();
            $_SESSION['otp_user_id'] = $userId;
            $_SESSION['otp_purpose'] = 'register';
            header('Location: verify-otp.php');
            exit;
        } catch (Throwable $error) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
            $message = $error instanceof PDOException && $error->getCode() === '23000' ? 'Email or mobile already registered.' : $error->getMessage();
        }
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register | Sanderi Stationary</title><link rel="stylesheet" href="style.css?v=9"><script src="app.js?v=8" defer></script></head><body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="index.php">Home</a><a href="products.php">Shop</a><a href="cart.php">Cart</a><a href="wishlist.php">Wishlist</a><a href="login.php">Login</a><a class="active-menu" href="register.php">Register</a></nav></header><main class="page-shell"><section class="form-card"><div class="eyebrow">Join the study shelf</div><h1>Create account</h1><?php if ($message): ?><p class="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><label>Full name<input name="full_name" placeholder="Your full name" required></label><label>Mobile number<input name="mobile" placeholder="10 digit mobile" inputmode="tel" required></label><label>Email address <span class="optional-label">(optional)</span><input type="email" name="email" placeholder="you@example.com"></label><label>Password<input type="password" name="password" placeholder="At least 8 characters" required></label><label>Confirm password<input type="password" name="confirm_password" placeholder="Repeat your password" required></label><button>Register and send mobile OTP</button></form><p class="meta">Already have an account? <a href="login.php">Login</a></p></section></main></body></html>
