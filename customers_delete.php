<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
if (!isset($_GET['id'])) { header('Location: customers_list.php'); exit; }
$id = (int)$_GET['id'];
$stmt = $pdo->prepare('DELETE FROM Customers WHERE customer_id = ?');
$stmt->execute([$id]);
header('Location: customers_list.php');
exit;
