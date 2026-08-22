<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
function require_admin(): void { if (empty($_SESSION['admin_id'])) { header('Location: admin-login.php'); exit; } }
