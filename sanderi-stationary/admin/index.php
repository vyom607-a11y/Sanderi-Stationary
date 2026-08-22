<?php
declare(strict_types=1);
require_once __DIR__ . '/../admin-auth.php';
require_admin();
$products = (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn();
$orders = (int) db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$users = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Dashboard</title><link rel="stylesheet" href="../style.css?v=9"><script src="../app.js?v=9" defer></script></head><body><header class="site-header"><a class="brand" href="index.php">Sanderi <span>Admin</span></a><nav class="nav"><a class="active-menu" href="index.php">Dashboard</a><a href="products.php">Products</a><a href="product-form.php">Add product</a><a href="orders.php">Orders</a><a href="../admin-logout.php">Logout</a></nav></header><main class="page-shell"><div class="eyebrow">Store overview</div><h1>Dashboard</h1><div class="stats-grid"><div class="stat-card"><strong><?= $products ?></strong><span>Products</span></div><div class="stat-card"><strong><?= $orders ?></strong><span>Orders</span></div><div class="stat-card"><strong><?= $users ?></strong><span>Customers</span></div></div></main></body></html>
