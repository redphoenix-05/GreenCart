<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
$orders = $pdo->query('SELECT order_id FROM Orders')->fetchAll();
$products = $pdo->query('SELECT product_id,name,price FROM Products')->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$od = ['order_id'=>'','product_id'=>'','quantity'=>1,'subtotal'=>'0.00'];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Order_Details WHERE order_detail_id = ?');
    $stmt->execute([$id]);
    $od = $stmt->fetch() ?: $od;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $order_id = $_POST['order_id'] ?: null;
    $product_id = $_POST['product_id'] ?: null;
    $quantity = (int)($_POST['quantity']?:1);
    $price = 0;
    if ($product_id) {
        $p = $pdo->prepare('SELECT price FROM Products WHERE product_id=?'); $p->execute([$product_id]); $pr = $p->fetch(); $price = $pr ? $pr['price'] : 0;
    }
    $subtotal = $quantity * $price;
    if (!empty($_POST['order_detail_id'])) {
        $stmt = $pdo->prepare('UPDATE Order_Details SET order_id=?, product_id=?, quantity=?, subtotal=? WHERE order_detail_id=?');
        $stmt->execute([$order_id,$product_id,$quantity,$subtotal,(int)$_POST['order_detail_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Order_Details (order_id,product_id,quantity,subtotal) VALUES (?,?,?,?)');
        $stmt->execute([$order_id,$product_id,$quantity,$subtotal]);
    }
    // Recalculate order total
    $s = $pdo->prepare('SELECT COALESCE(SUM(subtotal),0) FROM Order_Details WHERE order_id = ?');
    $s->execute([$order_id]);
    $total = $s->fetchColumn();
    $u = $pdo->prepare('UPDATE Orders SET total_amount = ? WHERE order_id = ?');
    $u->execute([$total,$order_id]);

    header('Location: order_details_list.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title><?= $id ? 'Edit' : 'Add' ?> Order Detail</title><link rel="stylesheet" href="css/style.css"></head>
<body><div class="container">
<h1><?= $id ? 'Edit' : 'Add' ?> Order Detail</h1>
<form method="post">
<?php if ($id): ?><input type="hidden" name="order_detail_id" value="<?=htmlspecialchars($id)?>"><?php endif; ?>
<div class="form-row"><label>Order</label><select name="order_id"><?php foreach($orders as $o): ?><option value="<?=htmlspecialchars($o['order_id'])?>" <?=($od['order_id']==$o['order_id'])?'selected':''?>><?=htmlspecialchars($o['order_id'])?></option><?php endforeach; ?></select></div>
<div class="form-row"><label>Product</label><select name="product_id" onchange="this.form.submit()"><option value="">-- choose --</option><?php foreach($products as $p): ?><option value="<?=htmlspecialchars($p['product_id'])?>" <?=($od['product_id']==$p['product_id'])?'selected':''?>><?=htmlspecialchars($p['name'])?> (<?=htmlspecialchars($p['price'])?>)</option><?php endforeach; ?></select></div>
<div class="form-row"><label>Quantity</label><input type="number" name="quantity" value="<?=htmlspecialchars($od['quantity'])?>"></div>
<button class="btn" type="submit">Save</button> <a href="order_details_list.php">Cancel</a>
</form></div></body></html>
