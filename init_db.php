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
    
    // Drop triggers first
    $pdo->exec("DROP TRIGGER IF EXISTS calculate_subtotal");
    $pdo->exec("DROP TRIGGER IF EXISTS update_order_total");
    $pdo->exec("DROP TRIGGER IF EXISTS update_loyalty_points");
    $pdo->exec("DROP TRIGGER IF EXISTS check_product_price");
    
    // Drop views
    $pdo->exec("DROP VIEW IF EXISTS CustomerOrderSummary");
    
    // Drop functions
    $pdo->exec("DROP FUNCTION IF EXISTS get_customers_by_product");
    $pdo->exec("DROP FUNCTION IF EXISTS get_product_info");
    
    // Drop procedures
    $pdo->exec("DROP PROCEDURE IF EXISTS get_product_details");
    
    // Drop tables
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
  preferences TEXT,
  loyalty_points INT DEFAULT 0,
  customer_tier ENUM('Bronze', 'Silver', 'Gold', 'Platinum') DEFAULT 'Bronze'
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
  stock_status ENUM('In Stock', 'Low Stock', 'Out of Stock') DEFAULT 'In Stock',
  FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id) ON DELETE SET NULL
)",

"CREATE TABLE IF NOT EXISTS Orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10,2) DEFAULT 0.00,
  status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  discount_applied DECIMAL(10,2) DEFAULT 0.00,
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
  delivery_status ENUM('scheduled', 'in-transit', 'delivered', 'failed', 'ready') DEFAULT 'scheduled',
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

// ========== USER-DEFINED FUNCTIONS ==========

// Function 1: Get customers who bought a specific product (single return - list of customer names)
$pdo->exec("DROP FUNCTION IF EXISTS get_customers_by_product");
$pdo->exec("
CREATE FUNCTION get_customers_by_product(product_name VARCHAR(150))
RETURNS TEXT
DETERMINISTIC
BEGIN
    DECLARE customer_list TEXT;
    
    -- Get list of customers who bought this product
    SELECT GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ')
    INTO customer_list
    FROM Customers c
    INNER JOIN Orders o ON c.customer_id = o.customer_id
    INNER JOIN Order_Details od ON o.order_id = od.order_id
    INNER JOIN Products p ON od.product_id = p.product_id
    WHERE p.name = product_name;
    
    RETURN COALESCE(customer_list, 'No customers found');
END
");

// Function 2: Get vendor name and product category by product name (multiple values as concatenated string)
$pdo->exec("DROP FUNCTION IF EXISTS get_product_info");
$pdo->exec("
CREATE FUNCTION get_product_info(product_name VARCHAR(150))
RETURNS VARCHAR(500)
DETERMINISTIC
BEGIN
    DECLARE vendor_name VARCHAR(150);
    DECLARE product_category VARCHAR(100);
    DECLARE result VARCHAR(500);
    
    -- Get vendor name and category for the product
    SELECT v.vendor_name, p.category
    INTO vendor_name, product_category
    FROM Products p
    LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
    WHERE p.name = product_name
    LIMIT 1;
    
    -- Return multiple values as formatted string
    SET result = CONCAT(
        'Vendor: ', COALESCE(vendor_name, 'Unknown'),
        ' | Category: ', COALESCE(product_category, 'Unknown')
    );
    
    RETURN result;
END
");

// ========== STORED PROCEDURE (Returns multiple separate values via OUT parameters) ==========
// Procedure: Get vendor name and category by product name (multiple OUT parameters - BEST approach)
$pdo->exec("DROP PROCEDURE IF EXISTS get_product_details");
$pdo->exec("
CREATE PROCEDURE get_product_details(
    IN product_name VARCHAR(150),
    OUT vendor_name VARCHAR(150),
    OUT product_category VARCHAR(100)
)
BEGIN
    -- Get vendor name and category for the product
    SELECT v.vendor_name, p.category
    INTO vendor_name, product_category
    FROM Products p
    LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
    WHERE p.name = product_name
    LIMIT 1;
END
");

// ========== TRIGGERS ==========
// Trigger 1: Calculate subtotal BEFORE INSERT on Order_Details
$pdo->exec("DROP TRIGGER IF EXISTS calculate_subtotal");
$pdo->exec("
CREATE TRIGGER calculate_subtotal
BEFORE INSERT ON Order_Details
FOR EACH ROW
BEGIN
    DECLARE product_price DECIMAL(10,2);
    
    -- Get the product price
    SELECT price INTO product_price 
    FROM Products 
    WHERE product_id = NEW.product_id;
    
    -- Calculate subtotal
    SET NEW.subtotal = product_price * NEW.quantity;
END
");

// Trigger 2: Update order total AFTER INSERT on Order_Details
$pdo->exec("DROP TRIGGER IF EXISTS update_order_total");
$pdo->exec("
CREATE TRIGGER update_order_total
AFTER INSERT ON Order_Details
FOR EACH ROW
BEGIN
    DECLARE order_total DECIMAL(10,2);
    
    -- Calculate total from all order details
    SELECT COALESCE(SUM(subtotal), 0) INTO order_total
    FROM Order_Details
    WHERE order_id = NEW.order_id;
    
    -- Update the order total
    UPDATE Orders 
    SET total_amount = order_total
    WHERE order_id = NEW.order_id;
END
");

// Trigger 3: Update loyalty points AFTER UPDATE on Orders (when status changes to delivered)
$pdo->exec("DROP TRIGGER IF EXISTS update_loyalty_points");
$pdo->exec("
CREATE TRIGGER update_loyalty_points
AFTER UPDATE ON Orders
FOR EACH ROW
BEGIN
    -- Award loyalty points when order is delivered
    IF NEW.status = 'delivered' AND OLD.status != 'delivered' THEN
        UPDATE Customers
        SET loyalty_points = loyalty_points + FLOOR(NEW.total_amount)
        WHERE customer_id = NEW.customer_id;
    END IF;
END
");

// Trigger 4: Check product price BEFORE INSERT/UPDATE on Products
$pdo->exec("DROP TRIGGER IF EXISTS check_product_price");
$pdo->exec("
CREATE TRIGGER check_product_price
BEFORE INSERT ON Products
FOR EACH ROW
BEGIN
    -- Ensure price is not negative
    IF NEW.price < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product price cannot be negative';
    END IF;
END
");

// Insert sample data if no customers exist
$count = $pdo->query('SELECT COUNT(*) FROM Customers')->fetchColumn();
if ($count == 0) {
  $pdo->beginTransaction();
  // Customers (6 rows) - with loyalty_points and customer_tier
  $pdo->exec("INSERT INTO Customers (name,email,phone,address,preferences,loyalty_points,customer_tier) VALUES
  ('Alice Green','alice@example.com','111-222-3333','123 Green St','organic',0,'Bronze'),
  ('Bob Brown','bob@example.com','222-333-4444','456 Market Ave','vegan',0,'Bronze'),
  ('Carol White','carol@example.com','333-444-5555','789 Orchard Rd','gluten-free',0,'Bronze'),
  ('David Black','david@example.com','444-555-6666','321 Farm Blvd','low-carb',0,'Bronze'),
  ('Eve Stone','eve@example.com','555-666-7777','654 Hill Ln','vegetarian',0,'Bronze'),
  ('Frank Ocean','frank@example.com','666-777-8888','987 Coast Dr','organic',0,'Bronze')");

  // Vendors (6 rows)
  $pdo->exec("INSERT INTO Vendors (vendor_name,contact_email,phone,location) VALUES
  ('Fresh Farms','fresh@example.com','333-444-5555','North Town'),
  ('Eco Produce','eco@example.com','444-555-6666','West Side'),
  ('Green Valley','valley@example.com','555-444-3333','East Grove'),
  ('Urban Harvest','urban@example.com','777-888-9999','Downtown'),
  ('Local Organics','local@example.com','888-999-0000','South Market'),
  ('Sunshine Goods','sun@example.com','999-000-1111','Riverside')");

  // Products (6 rows) - with stock_status ENUM
  $pdo->exec("INSERT INTO Products (vendor_id,name,category,price,sustainability_tag,stock_status) VALUES
  (1,'Organic Apple','Fruits',0.99,'organic','In Stock'),
  (1,'Banana','Fruits',0.59,'fair-trade','In Stock'),
  (2,'Almond Milk','Dairy Alternatives',3.49,'vegan','In Stock'),
  (3,'Kale','Vegetables',2.50,'organic','Low Stock'),
  (4,'Brown Rice','Grains',4.00,'whole-grain','In Stock'),
  (5,'Quinoa','Grains',6.00,'superfood','In Stock')");

  // Orders (6 rows) - using ENUM status, triggers will calculate total_amount
  // Insert with total_amount = 0, the trigger will update it when order details are inserted
  $pdo->exec("INSERT INTO Orders (customer_id,order_date,total_amount,status,discount_applied) VALUES
  (1,NOW(),0,'pending',0),
  (2,NOW(),0,'pending',0),
  (3,NOW(),0,'pending',0),
  (4,NOW(),0,'pending',0),
  (5,NOW(),0,'pending',0),
  (1,NOW(),0,'pending',0)");

  // Order_Details - Triggers will automatically calculate subtotal and update order totals
  // Don't set subtotal manually - let the trigger calculate it
  $pdo->exec("INSERT INTO Order_Details (order_id,product_id,quantity) VALUES
  (1,5,10),(1,4,3),
  (2,3,1),
  (3,5,5),(3,2,1),
  (4,4,3),(4,1,5),
  (5,2,10),
  (6,1,3)");

  // Delivery entries (6 rows) - using ENUM delivery_status
  $pdo->exec("INSERT INTO Delivery (order_id,delivery_method,delivery_status,estimated_time) VALUES
  (1,'Standard','scheduled',DATE_ADD(NOW(), INTERVAL 2 DAY)),
  (2,'Express','scheduled',DATE_ADD(NOW(), INTERVAL 1 DAY)),
  (3,'Standard','scheduled',DATE_ADD(NOW(), INTERVAL 3 DAY)),
  (4,'Pickup','ready',NOW()),
  (5,'Express','scheduled',DATE_ADD(NOW(), INTERVAL 2 DAY)),
  (6,'Standard','scheduled',DATE_ADD(NOW(), INTERVAL 3 DAY))");

  // Update some orders to 'delivered' status to trigger loyalty points
  $pdo->exec("UPDATE Orders SET status = 'delivered' WHERE order_id IN (1,3,4,6)");

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
    
    <div class="mt-8 bg-green-50 border-l-4 border-green-500 p-4">
      <h2 class="text-xl font-bold text-green-800 mb-3">✅ Implemented SQL Features</h2>
      
      <div class="mb-4">
        <h3 class="font-bold text-green-700">📊 User-Defined Data Types (ENUM):</h3>
        <ul class="list-disc ml-6 text-gray-700">
          <li><strong>customer_tier:</strong> 'Bronze', 'Silver', 'Gold', 'Platinum' (in Customers table)</li>
          <li><strong>stock_status:</strong> 'In Stock', 'Low Stock', 'Out of Stock' (in Products table)</li>
          <li><strong>order status:</strong> 'pending', 'processing', 'shipped', 'delivered', 'cancelled'</li>
          <li><strong>delivery_status:</strong> 'scheduled', 'in-transit', 'delivered', 'failed', 'ready'</li>
        </ul>
      </div>

      <div class="mb-4">
        <h3 class="font-bold text-green-700">⚡ Triggers:</h3>
        <ul class="list-disc ml-6 text-gray-700">
          <li><strong>calculate_subtotal:</strong> BEFORE INSERT on Order_Details - Auto-calculates subtotal</li>
          <li><strong>update_order_total:</strong> AFTER INSERT on Order_Details - Updates order total</li>
          <li><strong>update_loyalty_points:</strong> AFTER UPDATE on Orders - Awards points when delivered</li>
          <li><strong>check_product_price:</strong> BEFORE INSERT on Products - Validates price is not negative</li>
        </ul>
      </div>

      <div class="mb-4">
        <h3 class="font-bold text-green-700">🔧 User-Defined Functions:</h3>
        <ul class="list-disc ml-6 text-gray-700">
          <li><strong>get_customers_by_product(product_name):</strong> Returns list of customers who bought a product</li>
          <li><strong>get_product_info(product_name):</strong> Returns vendor name AND category (multi-value as string)</li>
        </ul>
      </div>

      <div class="mb-4">
        <h3 class="font-bold text-green-700">📦 Stored Procedures:</h3>
        <ul class="list-disc ml-6 text-gray-700">
          <li><strong>get_product_details(IN, OUT, OUT):</strong> Returns vendor name AND category as separate OUT parameters</li>
        </ul>
      </div>
    </div>

    <p class="mt-4"><a href="index.php" class="text-blue-600 hover:underline">Go to Dashboard</a></p>
    <p>To reset the schema (drop all tables, triggers, functions) click: 
      <a href="init_db.php?reset=1" onclick="return confirm('Drop all tables, triggers, and functions? This is destructive.')" class="text-red-600 hover:underline">Reset Database</a>
    </p>
  </div>
</body>
</html>
