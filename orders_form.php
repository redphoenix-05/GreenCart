<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
$customers = $pdo->query('SELECT customer_id, name FROM Customers')->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = ['customer_id'=>'','order_date'=>'','total_amount'=>'0.00','status'=>'pending'];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Orders WHERE order_id = ?');
    $stmt->execute([$id]);
    $order = $stmt->fetch() ?: $order;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $customer_id = $_POST['customer_id'] ?: null;
    $order_date = $_POST['order_date'] ?: null;
    $status = $_POST['status'] ?? 'pending';
    if (!empty($_POST['order_id'])) {
        $stmt = $pdo->prepare('UPDATE Orders SET customer_id=?, order_date=?, status=? WHERE order_id=?');
        $stmt->execute([$customer_id,$order_date,$status,(int)$_POST['order_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Orders (customer_id,order_date,total_amount,status) VALUES (?,?,0.00,?)');
        $stmt->execute([$customer_id,$order_date,$status]);
    }
    header('Location: orders_list.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title><?= $id ? 'Edit' : 'Add' ?> Order</title><link rel="stylesheet" href="css/style.css"></head>
<body><div class="container">
<h1><?= $id ? 'Edit' : 'Add' ?> Order</h1>
<form method="post">
<?php if ($id): ?><input type="hidden" name="order_id" value="<?=htmlspecialchars($id)?>"><?php endif; ?>
<div class="form-row"><label>Customer</label><select name="customer_id"><option value="">-- none --</option><?php foreach($customers as $c): ?><option value="<?=htmlspecialchars($c['customer_id'])?>" <?=($order['customer_id']==$c['customer_id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option><?php endforeach; ?></select></div>
<div class="form-row"><label>Order Date</label><input type="datetime-local" name="order_date" value="<?= $order['order_date'] ? date('Y-m-d\TH:i', strtotime($order['order_date'])) : '' ?>"></div>
<div class="form-row"><label>Status</label><input type="text" name="status" value="<?=htmlspecialchars($order['status'])?>"></div>
<button class="btn" type="submit">Save</button> <a href="orders_list.php">Cancel</a>
</form></div></body></html>
