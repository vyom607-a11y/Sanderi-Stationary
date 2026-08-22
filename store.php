<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';

function cart_items(): array
{
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_keys($cart);
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $query = db()->prepare("SELECT * FROM products WHERE id IN ($marks) AND is_active=1");
    $query->execute($ids);
    $items = [];
    foreach ($query as $product) {
        $product['quantity'] = min((int) ($cart[$product['id']] ?? 0), (int) $product['stock']);
        $product['final_price'] = (float) $product['price'] * (1 - (float) $product['discount_percent'] / 100);
        if ($product['quantity'] > 0) $items[] = $product;
    }
    return $items;
}

function cart_total(array $items): float
{
    return array_reduce($items, fn(float $total, array $item): float => $total + $item['final_price'] * $item['quantity'], 0.0);
}

function require_verified_customer(): void
{
    require_login();
    $query = db()->prepare('SELECT mobile_verified FROM users WHERE id=?');
    $query->execute([$_SESSION['user_id']]);
    if (!(int) $query->fetchColumn()) exit('Please verify your mobile number before placing an order.');
}
