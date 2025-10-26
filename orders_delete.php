<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
if (!isset($_GET['id'])) { header('Location: orders_list.php'); exit; }
$id = (int)$_GET['id'];
$stmt = $pdo->prepare('DELETE FROM Orders WHERE order_id = ?');
$stmt->execute([$id]);
header('Location: orders_list.php');
exit;
