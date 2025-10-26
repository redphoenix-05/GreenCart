<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
$vendors = $pdo->query('SELECT vendor_id, vendor_name FROM Vendors')->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = ['vendor_id'=>'','name'=>'','category'=>'','price'=>'','sustainability_tag'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Products WHERE product_id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch() ?: $product;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $vendor_id = $_POST['vendor_id'] ?: null;
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? null;
    $price = $_POST['price'] ?? 0;
    $tag = $_POST['sustainability_tag'] ?? null;
    if (!empty($_POST['product_id'])) {
        $stmt = $pdo->prepare('UPDATE Products SET vendor_id=?, name=?, category=?, price=?, sustainability_tag=? WHERE product_id=?');
        $stmt->execute([$vendor_id,$name,$category,$price,$tag,(int)$_POST['product_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Products (vendor_id,name,category,price,sustainability_tag) VALUES (?,?,?,?,?)');
        $stmt->execute([$vendor_id,$name,$category,$price,$tag]);
    }
    header('Location: products_list.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title><?= $id ? 'Edit' : 'Add' ?> Product</title><link rel="stylesheet" href="css/style.css"></head>
<body><div class="container">
<h1><?= $id ? 'Edit' : 'Add' ?> Product</h1>
<form method="post">
<?php if ($id): ?><input type="hidden" name="product_id" value="<?=htmlspecialchars($id)?>"><?php endif; ?>
<div class="form-row"><label>Vendor</label><select name="vendor_id"><option value="">-- none --</option><?php foreach($vendors as $v): ?><option value="<?=htmlspecialchars($v['vendor_id'])?>" <?=($product['vendor_id']==$v['vendor_id'])?'selected':''?>><?=htmlspecialchars($v['vendor_name'])?></option><?php endforeach; ?></select></div>
<div class="form-row"><label>Name</label><input type="text" name="name" required value="<?=htmlspecialchars($product['name'])?>"></div>
<div class="form-row"><label>Category</label><input type="text" name="category" value="<?=htmlspecialchars($product['category'])?>"></div>
<div class="form-row"><label>Price</label><input type="number" step="0.01" name="price" value="<?=htmlspecialchars($product['price'])?>"></div>
<div class="form-row"><label>Sustainability Tag</label><input type="text" name="sustainability_tag" value="<?=htmlspecialchars($product['sustainability_tag'])?>"></div>
<button class="btn" type="submit">Save</button> <a href="products_list.php">Cancel</a>
</form></div></body></html>
