<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vendor = ['vendor_name'=>'','contact_email'=>'','phone'=>'','location'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Vendors WHERE vendor_id = ?');
    $stmt->execute([$id]);
    $vendor = $stmt->fetch() ?: $vendor;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = $_POST['vendor_name'] ?? '';
    $email = $_POST['contact_email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $location = $_POST['location'] ?? null;
    if (!empty($_POST['vendor_id'])) {
        $stmt = $pdo->prepare('UPDATE Vendors SET vendor_name=?, contact_email=?, phone=?, location=? WHERE vendor_id=?');
        $stmt->execute([$name,$email,$phone,$location,(int)$_POST['vendor_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Vendors (vendor_name,contact_email,phone,location) VALUES (?,?,?,?)');
        $stmt->execute([$name,$email,$phone,$location]);
    }
    header('Location: vendors_list.php'); exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title><?= $id ? 'Edit' : 'Add' ?> Vendor</title><link rel="stylesheet" href="css/style.css"></head>
<body><div class="container">
<h1><?= $id ? 'Edit' : 'Add' ?> Vendor</h1>
<form method="post">
<?php if ($id): ?><input type="hidden" name="vendor_id" value="<?=htmlspecialchars($id)?>"><?php endif; ?>
<div class="form-row"><label>Name</label><input type="text" name="vendor_name" required value="<?=htmlspecialchars($vendor['vendor_name'])?>"></div>
<div class="form-row"><label>Contact Email</label><input type="email" name="contact_email" value="<?=htmlspecialchars($vendor['contact_email'])?>"></div>
<div class="form-row"><label>Phone</label><input type="text" name="phone" value="<?=htmlspecialchars($vendor['phone'])?>"></div>
<div class="form-row"><label>Location</label><input type="text" name="location" value="<?=htmlspecialchars($vendor['location'])?>"></div>
<button class="btn" type="submit">Save</button> <a href="vendors_list.php">Cancel</a>
</form></div></body></html>
