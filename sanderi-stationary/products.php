<?php
declare(strict_types=1);
require_once __DIR__ . '/store.php';

$category = trim($_GET['category'] ?? '');
$standard = trim($_GET['standard'] ?? '');
$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$sql = 'SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.is_active=1';
$params = [];
if ($category !== '') { $sql .= ' AND c.slug=?'; $params[] = $category; }
if ($standard !== '') { $sql .= ' AND p.standard=?'; $params[] = $standard; }
$sql .= ' ORDER BY p.created_at DESC';
$query = db()->prepare($sql);
$query->execute($params);
$products = $query->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Shop | Sanderi Stationary</title>
  <link rel="stylesheet" href="style.css?v=8">
  <script src="app.js?v=8" defer></script>
</head>
<body>
<header class="site-header"><a class="brand" href="index.php">Sanderi <span>Stationary</span></a><nav class="nav"><a href="products.php">Shop</a><a href="cart.php">Cart (<?= count($_SESSION['cart'] ?? []) ?>)</a><?php if(isset($_SESSION['user_id'])): ?><a class="my-orders-menu" href="orders.php">My Orders</a><?php endif; ?><a href="profile.php">Profile</a></nav></header>
<main class="page-shell">
  <div class="eyebrow">The study shelf</div>
  <h1>Shop books & stationery</h1>
  <form class="filters" method="get">
    <select name="category"><option value="">All categories</option><?php foreach ($categories as $item): ?><option value="<?= htmlspecialchars($item['slug']) ?>" <?= $category === $item['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($item['name']) ?></option><?php endforeach; ?></select>
    <select name="standard"><option value="">All standards</option><?php for ($i=1; $i<=10; $i++): ?><option value="Standard <?= $i ?>" <?= $standard === "Standard $i" ? 'selected' : '' ?>>Standard <?= $i ?></option><?php endfor; ?></select>
    <button>Filter</button>
  </form>
  <div class="product-grid">
    <?php foreach ($products as $product): $final = (float) $product['price'] * (1 - (float) $product['discount_percent'] / 100); ?>
      <article class="product-card">
        <a href="product.php?id=<?= $product['id'] ?>"><div class="product-image" style="background-image:url('<?= htmlspecialchars($product['image_url'] ?: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=700') ?>')"></div></a>
        <div class="product-body">
          <div class="eyebrow"><?= htmlspecialchars($product['category_name'] ?? 'Stationery') ?></div>
          <h2><?= htmlspecialchars($product['name']) ?></h2>
          <p><?= htmlspecialchars($product['standard'] ?: 'All students') ?><?php if ($product['subject']): ?> · <?= htmlspecialchars($product['subject']) ?><?php endif; ?></p>
          <strong>₹<?= number_format($final, 2) ?></strong><?php if ($product['discount_percent'] > 0): ?><del>₹<?= number_format((float) $product['price'], 2) ?></del><?php endif; ?>
          <p class="stock <?= $product['stock'] ? '' : 'sold-out' ?>"><?= $product['stock'] ? $product['stock'] . ' available' : 'Out of Stock' ?></p>
          <div class="product-actions">
            <form method="post" action="cart-add.php"><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><button <?= $product['stock'] ? '' : 'disabled' ?>>Add to cart</button></form>
            <form method="post" action="cart-add.php"><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><input type="hidden" name="buy_now" value="1"><button class="buy-now-button" <?= $product['stock'] ? '' : 'disabled' ?>>Buy now</button></form>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <?php if (!$products): ?><p>No products found. Add products from the admin panel.</p><?php endif; ?>
</main>
</body>
</html>
