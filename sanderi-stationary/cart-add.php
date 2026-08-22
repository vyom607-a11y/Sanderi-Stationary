<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: 0;
$quantity = max(1, min(20, (int) ($_POST['quantity'] ?? 1)));
$query=db()->prepare('SELECT stock FROM products WHERE id=? AND is_active=1'); $query->execute([$id]); $stock=(int)$query->fetchColumn();
if ($stock > 0) { $_SESSION['cart'][$id] = min($stock, (int)($_SESSION['cart'][$id] ?? 0) + $quantity); }
$destination = ($_POST['buy_now'] ?? '') === '1' ? 'checkout.php' : 'cart.php';
header('Location: ' . $destination); exit;
