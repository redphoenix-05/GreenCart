<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
if (!isset($_GET['id'])) { header('Location: order_details_list.php'); exit; }
$id = (int)$_GET['id'];
// find order id
$stmt = $pdo->prepare('SELECT order_id FROM Order_Details WHERE order_detail_id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
$order_id = $row ? $row['order_id'] : null;

$d = $pdo->prepare('DELETE FROM Order_Details WHERE order_detail_id = ?');
$d->execute([$id]);
if ($order_id) {
    $s = $pdo->prepare('SELECT COALESCE(SUM(subtotal),0) FROM Order_Details WHERE order_id = ?');
    $s->execute([$order_id]);
    $total = $s->fetchColumn();
    $u = $pdo->prepare('UPDATE Orders SET total_amount = ? WHERE order_id = ?');
    $u->execute([$total,$order_id]);
}
header('Location: order_details_list.php'); exit;
