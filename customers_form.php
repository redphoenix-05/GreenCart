<?php
// customers_form.php - Create / Update form for Customers
require_once __DIR__ . '/db.php';
$pdo = getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customer = ['name'=>'','email'=>'','phone'=>'','address'=>'','preferences'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Customers WHERE customer_id = ?');
    $stmt->execute([$id]);
    $customer = $stmt->fetch() ?: $customer;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $address = $_POST['address'] ?? null;
    $preferences = $_POST['preferences'] ?? null;

    if (!empty($_POST['customer_id'])) {
        // UPDATE operation (SQL UPDATE)
        $stmt = $pdo->prepare('UPDATE Customers SET name=?, email=?, phone=?, address=?, preferences=? WHERE customer_id=?');
        $stmt->execute([$name, $email, $phone, $address, $preferences, (int)$_POST['customer_id']]);
    } else {
        // CREATE operation (SQL INSERT)
        $stmt = $pdo->prepare('INSERT INTO Customers (name,email,phone,address,preferences) VALUES (?,?,?,?,?)');
        $stmt->execute([$name, $email, $phone, $address, $preferences]);
    }
    header('Location: customers_list.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $id ? 'Edit' : 'Add' ?> Customer - GreenCart</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h1><?= $id ? 'Edit' : 'Add' ?> Customer</h1>
    <form method="post">
      <?php if ($id): ?><input type="hidden" name="customer_id" value="<?=htmlspecialchars($id)?>"><?php endif; ?>
      <div class="form-row">
        <label>Name</label>
        <input type="text" name="name" required value="<?=htmlspecialchars($customer['name'])?>">
      </div>
      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" value="<?=htmlspecialchars($customer['email'])?>">
      </div>
      <div class="form-row">
        <label>Phone</label>
        <input type="text" name="phone" value="<?=htmlspecialchars($customer['phone'])?>">
      </div>
      <div class="form-row">
        <label>Address</label>
        <textarea name="address"><?=htmlspecialchars($customer['address'])?></textarea>
      </div>
      <div class="form-row">
        <label>Preferences</label>
        <textarea name="preferences"><?=htmlspecialchars($customer['preferences'])?></textarea>
      </div>
      <button class="btn" type="submit">Save</button>
      <a href="customers_list.php">Cancel</a>
    </form>
  </div>
</body>
</html>
