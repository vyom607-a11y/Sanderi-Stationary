<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_login();
$statement = db()->prepare('SELECT full_name,email,mobile,mobile_verified FROM users WHERE id=?');
$statement->execute([$_SESSION['user_id']]);
$user = $statement->fetch();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Profile | Sanderi Stationary</title><link rel="stylesheet" href="style.css?v=9"><script src="app.js?v=8" defer></script></head>
<body>
<header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="index.php">Home</a><a href="products.php">Shop</a><a href="cart.php">Cart</a><a href="orders.php">My Orders</a><a href="wishlist.php">Wishlist</a><a class="active-menu" href="profile.php">Profile</a><a href="logout.php">Logout</a></nav></header>
<main class="page-shell"><section class="profile-card"><div class="eyebrow">Your study shelf</div><h1>Welcome, <?= htmlspecialchars($user['full_name']) ?></h1><div class="meta"><p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p><p><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile']) ?></p><p><strong>Verification:</strong> <?= $user['mobile_verified'] ? 'Verified' : 'Pending' ?></p></div><div class="actions"><a class="button" href="index.php">Go to Home Page</a></div></section></main>
</body>
</html>
