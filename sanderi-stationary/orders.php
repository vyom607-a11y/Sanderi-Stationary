<?php
declare(strict_types=1);
require_once __DIR__ . '/security.php';
require_login();
require_once __DIR__ . '/db.php';

$query = db()->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC');
$query->execute([$_SESSION['user_id']]);
$orders = $query->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Orders</title><link rel="stylesheet" href="style.css?v=9"><script src="app.js?v=8" defer></script></head>
<body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="index.php">Home</a><a href="products.php">Shop</a><a href="cart.php">Cart</a><a class="my-orders-menu active" href="orders.php">My Orders</a><a href="wishlist.php">Wishlist</a><a href="profile.php">Profile</a><a href="logout.php">Logout</a></nav></header>
<main class="page-shell"><div class="eyebrow">Your purchases</div><h1>My Orders</h1><?php if (isset($_GET['placed'])): ?><p class="alert">Order #<?= (int) $_GET['placed'] ?> placed successfully.</p><?php endif; ?>
<div class="cart-list"><?php foreach ($orders as $order): ?><article class="cart-row"><div><h2>Order #<?= $order['id'] ?></h2><p><?= htmlspecialchars($order['created_at']) ?> · <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></p></div><strong>₹<?= number_format((float) $order['total_amount'], 2) ?></strong></article><?php endforeach; ?></div>
<?php if (!$orders): ?><p>No orders yet.</p><?php endif; ?></main></body></html>
