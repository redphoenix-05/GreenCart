<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
if (!isset($_GET['id'])) { header('Location: delivery_list.php'); exit; }
$id = (int)$_GET['id'];
$stmt = $pdo->prepare('DELETE FROM Delivery WHERE delivery_id = ?');
$stmt->execute([$id]);
header('Location: delivery_list.php');
exit;