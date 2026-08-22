<?php
declare(strict_types=1);
require_once __DIR__ . '/../admin-auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM orders WHERE id=?')->execute([$id]);
    } elseif (($_POST['action'] ?? '') === 'update') {
        db()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'] ?? 'pending', $id]);
    }
    header('Location: orders.php');
    exit;
}
$orders = db()->query('SELECT o.*,u.full_name,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Orders Admin</title><link rel="stylesheet" href="../style.css?v=10"><script src="../app.js?v=10" defer></script></head>
<body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Admin</span></a><nav class="nav"><a href="index.php">Dashboard</a><a href="products.php">Products</a><a href="product-form.php">Add product</a><a class="active-menu" href="orders.php">Orders</a><a href="../admin-logout.php">Logout</a></nav></header>
<main class="page-shell"><h1>Orders</h1><div class="admin-table"><div class="table-head"><span>Order</span><span>Customer</span><span>Total</span><span>Status</span></div><?php foreach ($orders as $order): ?><div class="table-row"><span>#<?= $order['id'] ?><br><small><?= htmlspecialchars($order['created_at']) ?></small></span><span><?= htmlspecialchars($order['full_name']) ?><br><small><?= htmlspecialchars($order['email']) ?></small></span><span>₹<?= number_format((float) $order['total_amount'], 2) ?></span><div class="order-actions"><form method="post" class="order-update-form"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $order['id'] ?>"><input type="hidden" name="action" value="update"><select name="status"><?php foreach (['pending','processing','shipped','completed','cancelled'] as $status): ?><option <?= $order['status'] === $status ? 'selected' : '' ?>><?= $status ?></option><?php endforeach; ?></select><button class="admin-action update-action">Update</button></form><form method="post" onsubmit="return confirm('Delete this order permanently?');"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $order['id'] ?>"><input type="hidden" name="action" value="delete"><button class="admin-action delete-action">Delete</button></form></div></div><?php endforeach; ?></div></main></body></html>
