<?php
declare(strict_types=1);
require_once __DIR__ . '/store.php';

if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int) $_GET['remove']]);
    header('Location: cart.php');
    exit;
}
$items = cart_items();
$total = cart_total($items);
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Your Cart</title><link rel="stylesheet" href="style.css?v=9"><script src="app.js?v=8" defer></script></head>
<body>
<header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="products.php">Continue shopping</a><?php if(isset($_SESSION['user_id'])): ?><a class="my-orders-menu" href="orders.php">My Orders</a><?php endif; ?><a href="profile.php">Profile</a></nav></header>
<main class="page-shell"><div class="eyebrow">Ready when you are</div><h1>Your cart</h1>
<?php if (!$items): ?>
  <section class="form-card"><p>Your cart is empty.</p><a class="button" href="products.php">Browse products</a></section>
<?php else: ?>
  <div class="cart-list">
    <?php foreach ($items as $item): ?>
      <article class="cart-row"><div><h2><?= htmlspecialchars($item['name']) ?></h2><p><?= $item['quantity'] ?> × ₹<?= number_format($item['final_price'], 2) ?></p></div><strong>₹<?= number_format($item['final_price'] * $item['quantity'], 2) ?></strong><a class="remove-button" href="cart.php?remove=<?= $item['id'] ?>">Remove</a></article>
    <?php endforeach; ?>
  </div>
  <section class="cart-total"><h2>Total: ₹<?= number_format($total, 2) ?></h2><a class="button" href="checkout.php">Proceed to checkout</a></section>
<?php endif; ?>
</main>
</body>
</html>
