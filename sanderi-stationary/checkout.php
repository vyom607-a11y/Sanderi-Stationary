<?php
declare(strict_types=1);
require_once __DIR__ . '/store.php';
require_once __DIR__ . '/otp.php';
require_verified_customer();

$items = cart_items();
if (!$items) { header('Location: cart.php'); exit; }
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['recipient_name'] ?? '');
    $mobile = normalize_mobile($_POST['mobile'] ?? '');
    $line = trim($_POST['address_line'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal = trim($_POST['postal_code'] ?? '');
    if ($name === '' || strlen($mobile) !== 12 || $line === '' || $city === '' || $state === '' || !preg_match('/^[0-9]{5,10}$/', $postal)) {
        $message = 'Please enter a complete delivery address.';
    } else {
        try {
            $pdo = db();
            $pdo->beginTransaction();
            $total = 0;
            foreach ($items as $item) {
                $lock = $pdo->prepare('SELECT stock FROM products WHERE id=? FOR UPDATE');
                $lock->execute([$item['id']]);
                if ((int) $lock->fetchColumn() < $item['quantity']) throw new RuntimeException('Stock changed for ' . $item['name'] . '.');
                $total += $item['final_price'] * $item['quantity'];
            }
            $snapshot = json_encode(compact('name', 'mobile', 'line', 'city', 'state', 'postal'), JSON_THROW_ON_ERROR);
            $order = $pdo->prepare('INSERT INTO orders (user_id,total_amount,address_snapshot) VALUES (?,?,?)');
            $order->execute([$_SESSION['user_id'], $total, $snapshot]);
            $orderId = (int) $pdo->lastInsertId();
            foreach ($items as $item) {
                $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity) VALUES (?,?,?,?,?)')->execute([$orderId, $item['id'], $item['name'], $item['final_price'], $item['quantity']]);
                $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=?')->execute([$item['quantity'], $item['id']]);
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            header('Location: orders.php?placed=' . $orderId);
            exit;
        } catch (Throwable $error) {
            if (db()->inTransaction()) db()->rollBack();
            $message = $error->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Checkout</title><link rel="stylesheet" href="style.css"><script src="app.js" defer></script></head>
<body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="cart.php">Back to cart</a></nav></header>
<main class="page-shell"><section class="form-card"><div class="eyebrow">Book now</div><h1>Book now</h1><?php if ($message): ?><p class="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><label>Recipient name<input name="recipient_name" required></label><label>Mobile number<input name="mobile" inputmode="tel" required></label><label>Address<input name="address_line" required></label><label>City<input name="city" required></label><label>State<input name="state" required></label><label>Pin code<input name="postal_code" required></label><button>Book now</button></form></section></main></body></html>
