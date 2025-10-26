<?php
// init_db.php - Creates database, tables and inserts sample data
require_once __DIR__ . '/db.php';

// Create database if not exists
try {
    $pdo = getPDO(false);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.DB_NAME.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
} catch (Exception $e) {
    die('Failed to create database: ' . $e->getMessage());
}

$pdo = getPDO(true);

// Drop tables if requested via ?reset=1
if (isset($_GET['reset']) && $_GET['reset']=='1') {
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $tables = ['Delivery','Order_Details','Orders','Products','Vendors','Customers'];
    foreach ($tables as $t) {
        $pdo->exec("DROP TABLE IF EXISTS `$t`");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    header('Location: init_db.php');
    exit;
}

// Create tables
$queries = [
"CREATE TABLE IF NOT EXISTS Customers (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(30),
  address TEXT,
  preferences TEXT
)",

"CREATE TABLE IF NOT EXISTS Vendors (
  vendor_id INT AUTO_INCREMENT PRIMARY KEY,
  vendor_name VARCHAR(150) NOT NULL,
  contact_email VARCHAR(150),
  phone VARCHAR(30),
  location VARCHAR(200)
)",

"CREATE TABLE IF NOT EXISTS Products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  vendor_id INT,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(100),
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  sustainability_tag VARCHAR(100),
  FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id) ON DELETE SET NULL
)",

"CREATE TABLE IF NOT EXISTS Orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10,2) DEFAULT 0.00,
  status VARCHAR(50) DEFAULT 'pending',
  FOREIGN KEY (customer_id) REFERENCES Customers(customer_id) ON DELETE SET NULL
)",

"CREATE TABLE IF NOT EXISTS Order_Details (
  order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  subtotal DECIMAL(10,2) DEFAULT 0.00,
  FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE SET NULL
)",

"CREATE TABLE IF NOT EXISTS Delivery (
  delivery_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  delivery_method VARCHAR(100),
  delivery_status VARCHAR(100) DEFAULT 'scheduled',
  estimated_time DATETIME,
  FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE
)",

"CREATE VIEW IF NOT EXISTS CustomerOrderSummary AS
SELECT c.customer_id, c.name, c.email, COUNT(o.order_id) AS orders_count, COALESCE(SUM(o.total_amount),0) AS total_spent
FROM Customers c LEFT JOIN Orders o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.name, c.email"
];

foreach ($queries as $q) {
    $pdo->exec($q);
}

// Insert sample data if no customers exist
$count = $pdo->query('SELECT COUNT(*) FROM Customers')->fetchColumn();
if ($count == 0) {
  $pdo->beginTransaction();
  // Customers (6 rows)
  $pdo->exec("INSERT INTO Customers (name,email,phone,address,preferences) VALUES
  ('Alice Green','alice@example.com','111-222-3333','123 Green St','organic'),
  ('Bob Brown','bob@example.com','222-333-4444','456 Market Ave','vegan'),
  ('Carol White','carol@example.com','333-444-5555','789 Orchard Rd','gluten-free'),
  ('David Black','david@example.com','444-555-6666','321 Farm Blvd','low-carb'),
  ('Eve Stone','eve@example.com','555-666-7777','654 Hill Ln','vegetarian'),
  ('Frank Ocean','frank@example.com','666-777-8888','987 Coast Dr','organic')");

  // Vendors (6 rows)
  $pdo->exec("INSERT INTO Vendors (vendor_name,contact_email,phone,location) VALUES
  ('Fresh Farms','fresh@example.com','333-444-5555','North Town'),
  ('Eco Produce','eco@example.com','444-555-6666','West Side'),
  ('Green Valley','valley@example.com','555-444-3333','East Grove'),
  ('Urban Harvest','urban@example.com','777-888-9999','Downtown'),
  ('Local Organics','local@example.com','888-999-0000','South Market'),
  ('Sunshine Goods','sun@example.com','999-000-1111','Riverside')");

  // Products (6 rows) - note: product_id 6 will be left un-ordered to demonstrate NOT EXISTS
  $pdo->exec("INSERT INTO Products (vendor_id,name,category,price,sustainability_tag) VALUES
  (1,'Organic Apple','Fruits',0.99,'organic'),
  (1,'Banana','Fruits',0.59,'fair-trade'),
  (2,'Almond Milk','Dairy Alternatives',3.49,'vegan'),
  (3,'Kale','Vegetables',2.50,'organic'),
  (4,'Brown Rice','Grains',4.00,'whole-grain'),
  (5,'Quinoa','Grains',6.00,'superfood')");

  // Orders (6 rows) - some customers have orders > 20 to satisfy subquery demos
  $pdo->exec("INSERT INTO Orders (customer_id,order_date,total_amount,status) VALUES
  (1,NOW(),47.50,'delivered'),
  (2,NOW(),3.49,'pending'),
  (3,NOW(),20.59,'delivered'),
  (4,NOW(),12.45,'delivered'),
  (5,NOW(),5.90,'pending'),
  (1,NOW(),2.97,'delivered')");

  // Order_Details - match subtotals to the Orders above (do NOT include product_id 6 so it stays un-ordered)
  $pdo->exec("INSERT INTO Order_Details (order_id,product_id,quantity,subtotal) VALUES
  (1,5,10,40.00),(1,4,3,7.50),
  (2,3,1,3.49),
  (3,5,5,20.00),(3,2,1,0.59),
  (4,4,3,7.50),(4,1,5,4.95),
  (5,2,10,5.90),
  (6,1,3,2.97)");

  // Delivery entries (6 rows) - one per order (some delivered, some scheduled)
  $pdo->exec("INSERT INTO Delivery (order_id,delivery_method,delivery_status,estimated_time) VALUES
  (1,'Standard','delivered',NOW()),
  (2,'Express','scheduled',DATE_ADD(NOW(), INTERVAL 2 DAY)),
  (3,'Standard','delivered',DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (4,'Pickup','ready',NOW()),
  (5,'Express','scheduled',DATE_ADD(NOW(), INTERVAL 3 DAY)),
  (6,'Standard','delivered',NOW())");

  $pdo->commit();
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>GreenCart - DB Initialization</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h1>GreenCart – Database Initialization</h1>
    <p>Database and tables have been created (or already existed). Sample data inserted if empty.</p>
    <p><a href="index.php">Go to Dashboard</a></p>
    <p>To reset the schema (drop all tables) click: <a href="init_db.php?reset=1" onclick="return confirm('Drop all tables? This is destructive.')">Reset Database</a></p>
  </div>
</body>
</html>
