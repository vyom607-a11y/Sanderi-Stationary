<?php
declare(strict_types=1); require_once __DIR__ . '/security.php'; require_login();
$id=(int)($_POST['product_id']??0);$q=db()->prepare('SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?');$q->execute([$_SESSION['user_id'],$id]);if($q->fetchColumn())db()->prepare('DELETE FROM wishlist WHERE user_id=? AND product_id=?')->execute([$_SESSION['user_id'],$id]);else db()->prepare('INSERT IGNORE INTO wishlist (user_id,product_id) VALUES (?,?)')->execute([$_SESSION['user_id'],$id]);header('Location: '.($_SERVER['HTTP_REFERER']??'products.php'));exit;
