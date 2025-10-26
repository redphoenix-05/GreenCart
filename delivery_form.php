<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
$orders = $pdo->query('SELECT order_id FROM Orders')->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$d = ['order_id'=>'','delivery_method'=>'','delivery_status'=>'scheduled','estimated_time'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Delivery WHERE delivery_id = ?');
    $stmt->execute([$id]);
    $d = $stmt->fetch() ?: $d;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $order_id = $_POST['order_id'] ?: null;
    $method = $_POST['delivery_method'] ?? null;
    $status = $_POST['delivery_status'] ?? null;
    $eta = $_POST['estimated_time'] ?: null;
    if (!empty($_POST['delivery_id'])) {
        $stmt = $pdo->prepare('UPDATE Delivery SET order_id=?, delivery_method=?, delivery_status=?, estimated_time=? WHERE delivery_id=?');
        $stmt->execute([$order_id,$method,$status,$eta,(int)$_POST['delivery_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Delivery (order_id,delivery_method,delivery_status,estimated_time) VALUES (?,?,?,?)');
        $stmt->execute([$order_id,$method,$status,$eta]);
    }
    header('Location: delivery_list.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title><?= $id ? 'Edit' : 'Add' ?> Delivery</title><link rel="stylesheet" href="css/style.css"></head>
<body><div class="container">
<h1><?= $id ? 'Edit' : 'Add' ?> Delivery</h1>
<form method="post">
<?php if ($id): ?><input type="hidden" name="delivery_id" value="<?=htmlspecialchars($id)?>"><?php endif; ?>
<div class="form-row"><label>Order</label><select name="order_id"><?php foreach($orders as $o): ?><option value="<?=htmlspecialchars($o['order_id'])?>" <?=($d['order_id']==$o['order_id'])?'selected':''?>><?=htmlspecialchars($o['order_id'])?></option><?php endforeach; ?></select></div>
<div class="form-row"><label>Method</label><input type="text" name="delivery_method" value="<?=htmlspecialchars($d['delivery_method'])?>"></div>
<div class="form-row"><label>Status</label><input type="text" name="delivery_status" value="<?=htmlspecialchars($d['delivery_status'])?>"></div>
<div class="form-row"><label>Estimated Time</label><input type="datetime-local" name="estimated_time" value="<?= $d['estimated_time'] ? date('Y-m-d\TH:i', strtotime($d['estimated_time'])) : '' ?>"></div>
<button class="btn" type="submit">Save</button> <a href="delivery_list.php">Cancel</a>
</form></div></body></html>