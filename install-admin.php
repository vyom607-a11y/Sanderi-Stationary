<?php
declare(strict_types=1); require_once __DIR__ . '/db.php';
$email='admin@sanderi.local'; $password='Admin@12345'; $q=db()->prepare('INSERT INTO admins (name,email,password_hash) VALUES (?,?,?) ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash)'); $q->execute(['Store Admin',$email,password_hash($password,PASSWORD_DEFAULT)]); echo 'Admin created. Email: '.htmlspecialchars($email).' Password: '.htmlspecialchars($password).'. Delete this file after setup.';
