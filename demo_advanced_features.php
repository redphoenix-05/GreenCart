<?php
// demo_advanced_features.php - Demonstrates triggers, user-defined functions, and ENUM types
require_once __DIR__ . '/db.php';

$pdo = getPDO();

// Check if the new schema exists
try {
    $check = $pdo->query("SHOW COLUMNS FROM Customers LIKE 'customer_tier'")->fetch();
    if (!$check) {
        header('Location: SETUP_INSTRUCTIONS.html');
        exit;
    }
} catch (Exception $e) {
    header('Location: SETUP_INSTRUCTIONS.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced SQL Features Demo - GreenCart</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'nav.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-green-800 mb-8">
            <i class="fas fa-code mr-3"></i>Advanced SQL Features Demo
        </h1>

        <!-- ENUM Data Types Demo -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-green-700 mb-4">
                <i class="fas fa-list-check mr-2"></i>1. User-Defined Data Types (ENUM)
            </h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Customer Tiers -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-3">Customer Tiers (ENUM)</h3>
                    <?php
                    $stmt = $pdo->query("
                        SELECT name, customer_tier, loyalty_points 
                        FROM Customers 
                        ORDER BY loyalty_points DESC
                    ");
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="p-2 text-left">Customer</th>
                                <th class="p-2 text-left">Tier</th>
                                <th class="p-2 text-right">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): ?>
                            <tr class="border-b">
                                <td class="p-2"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2">
                                    <span class="px-2 py-1 rounded text-xs font-bold
                                        <?= $row['customer_tier'] == 'Platinum' ? 'bg-purple-200 text-purple-800' : '' ?>
                                        <?= $row['customer_tier'] == 'Gold' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                                        <?= $row['customer_tier'] == 'Silver' ? 'bg-gray-200 text-gray-800' : '' ?>
                                        <?= $row['customer_tier'] == 'Bronze' ? 'bg-orange-200 text-orange-800' : '' ?>
                                    ">
                                        <?= $row['customer_tier'] ?>
                                    </span>
                                </td>
                                <td class="p-2 text-right"><?= $row['loyalty_points'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Product Stock Status -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-3">Product Stock Status (ENUM)</h3>
                    <?php
                    $stmt = $pdo->query("
                        SELECT name, stock_status, price 
                        FROM Products 
                        ORDER BY stock_status
                    ");
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="p-2 text-left">Product</th>
                                <th class="p-2 text-left">Status</th>
                                <th class="p-2 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): ?>
                            <tr class="border-b">
                                <td class="p-2"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2">
                                    <span class="px-2 py-1 rounded text-xs font-bold
                                        <?= $row['stock_status'] == 'In Stock' ? 'bg-green-200 text-green-800' : '' ?>
                                        <?= $row['stock_status'] == 'Low Stock' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                                        <?= $row['stock_status'] == 'Out of Stock' ? 'bg-red-200 text-red-800' : '' ?>
                                    ">
                                        <?= $row['stock_status'] ?>
                                    </span>
                                </td>
                                <td class="p-2 text-right">$<?= number_format($row['price'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User-Defined Functions Demo -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-green-700 mb-4">
                <i class="fas fa-function mr-2"></i>2. User-Defined Functions
            </h2>

            <div class="space-y-6">
                <!-- Function: get_customers_by_product -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-3">Function: get_customers_by_product(product_name)</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        <strong>Input:</strong> Product name → <strong>Returns:</strong> List of customers who bought it
                    </p>
                    <?php
                    $products = $pdo->query("SELECT DISTINCT name FROM Products ORDER BY name")->fetchAll();
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="p-2 text-left">Product Name (Input)</th>
                                <th class="p-2 text-left">Customers Who Bought It (Output)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $prod): ?>
                            <tr class="border-b">
                                <td class="p-2 font-semibold"><?= htmlspecialchars($prod['name']) ?></td>
                                <td class="p-2 text-blue-700">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT get_customers_by_product(?) as customers");
                                    $stmt->execute([$prod['name']]);
                                    $result = $stmt->fetch();
                                    echo htmlspecialchars($result['customers']);
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="bg-gray-900 text-green-400 p-3 rounded mt-3 text-xs">
                        <strong>SQL Usage:</strong><br>
                        <code>SELECT get_customers_by_product('Organic Apple');</code><br>
                        <span class="text-gray-400">-- Returns: "Alice Green, Frank Ocean"</span>
                    </div>
                </div>

                <!-- Function: get_product_info (returns multiple values as string) -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-3">Function: get_product_info(product_name) - Multi-Value Return</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        <strong>Input:</strong> Product name → <strong>Returns:</strong> Vendor name AND Category (as formatted string)
                    </p>
                    <table class="w-full text-sm">
                        <thead class="bg-purple-100">
                            <tr>
                                <th class="p-2 text-left">Product Name (Input)</th>
                                <th class="p-2 text-left">Vendor & Category (Multi-Value Output)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $prod): ?>
                            <tr class="border-b">
                                <td class="p-2 font-semibold"><?= htmlspecialchars($prod['name']) ?></td>
                                <td class="p-2 bg-purple-50 font-mono text-xs">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT get_product_info(?) as info");
                                    $stmt->execute([$prod['name']]);
                                    $result = $stmt->fetch();
                                    echo htmlspecialchars($result['info']);
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="bg-gray-900 text-green-400 p-3 rounded mt-3 text-xs">
                        <strong>SQL Usage:</strong><br>
                        <code>SELECT get_product_info('Kale');</code><br>
                        <span class="text-gray-400">-- Returns: "Vendor: Green Valley | Category: Vegetables"</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stored Procedure for Multi-Value Return (Better Approach) -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-green-700 mb-4">
                <i class="fas fa-code-branch mr-2"></i>3. Stored Procedure (TRUE Multi-Value Return)
            </h2>

            <div class="border border-green-200 rounded-lg p-4">
                <h3 class="font-bold text-lg mb-3">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">PROCEDURE</span>
                    get_product_details(IN product_name, OUT vendor_name, OUT category)
                </h3>
                <p class="text-sm text-gray-600 mb-3">
                    <strong>Input:</strong> Product name → <strong>Returns:</strong> 2 separate values (vendor name + category)
                </p>
                
                <?php
                // Test with different products
                $test_products = ['Organic Apple', 'Kale', 'Almond Milk', 'Brown Rice'];
                ?>
                
                <div class="space-y-3">
                    <?php foreach($test_products as $test_prod): ?>
                    <?php
                    $stmt = $pdo->prepare("CALL get_product_details(?, @vendor, @category)");
                    $stmt->execute([$test_prod]);
                    $result = $pdo->query("SELECT @vendor as vendor_name, @category as category")->fetch();
                    ?>
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 p-4 rounded">
                        <div class="text-sm font-bold mb-2">
                            Input: <span class="text-blue-600">"<?= htmlspecialchars($test_prod) ?>"</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white p-3 rounded shadow-sm">
                                <div class="text-xs text-gray-500 mb-1">OUT Parameter 1: Vendor Name</div>
                                <div class="font-bold text-green-600"><?= htmlspecialchars($result['vendor_name']) ?></div>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm">
                                <div class="text-xs text-gray-500 mb-1">OUT Parameter 2: Category</div>
                                <div class="font-bold text-purple-600"><?= htmlspecialchars($result['category']) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-gray-900 text-green-400 p-3 rounded mt-4 text-xs">
                    <strong>SQL Usage:</strong><br>
                    <code>CALL get_product_details('Kale', @vendor, @category);</code><br>
                    <code>SELECT @vendor, @category;</code><br>
                    <span class="text-gray-400">-- Returns 2 separate values: "Green Valley" and "Vegetables"</span>
                </div>
            </div>
        </div>

        <!-- Triggers Demo -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-green-700 mb-4">
                <i class="fas fa-bolt mr-2"></i>4. Triggers in Action
            </h2>

            <div class="space-y-6">
                <!-- Trigger 1: calculate_subtotal -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-2">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">BEFORE INSERT</span>
                        Trigger: calculate_subtotal
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">
                        Automatically calculates subtotal when order details are inserted (quantity × price)
                    </p>
                    <?php
                    $stmt = $pdo->query("
                        SELECT 
                            od.order_detail_id,
                            p.name as product_name,
                            p.price,
                            od.quantity,
                            od.subtotal
                        FROM Order_Details od
                        JOIN Products p ON od.product_id = p.product_id
                        LIMIT 5
                    ");
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="p-2 text-left">Product</th>
                                <th class="p-2 text-right">Price</th>
                                <th class="p-2 text-right">Qty</th>
                                <th class="p-2 text-right">Subtotal (Auto)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): ?>
                            <tr class="border-b">
                                <td class="p-2"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="p-2 text-right">$<?= number_format($row['price'], 2) ?></td>
                                <td class="p-2 text-right"><?= $row['quantity'] ?></td>
                                <td class="p-2 text-right font-bold text-green-600">
                                    $<?= number_format($row['subtotal'], 2) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Trigger 2: update_order_total -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-2">
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-sm">AFTER INSERT</span>
                        Trigger: update_order_total
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">
                        Automatically updates order total when order details are added
                    </p>
                    <?php
                    $stmt = $pdo->query("
                        SELECT 
                            o.order_id,
                            c.name as customer_name,
                            COUNT(od.order_detail_id) as items_count,
                            o.total_amount
                        FROM Orders o
                        JOIN Customers c ON o.customer_id = c.customer_id
                        LEFT JOIN Order_Details od ON o.order_id = od.order_id
                        GROUP BY o.order_id, c.name, o.total_amount
                    ");
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="p-2 text-left">Order ID</th>
                                <th class="p-2 text-left">Customer</th>
                                <th class="p-2 text-right">Items</th>
                                <th class="p-2 text-right">Total (Auto)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): ?>
                            <tr class="border-b">
                                <td class="p-2">#<?= $row['order_id'] ?></td>
                                <td class="p-2"><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td class="p-2 text-right"><?= $row['items_count'] ?></td>
                                <td class="p-2 text-right font-bold text-green-600">
                                    $<?= number_format($row['total_amount'], 2) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Trigger 3: update_loyalty_points -->
                <div class="border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-2">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">AFTER UPDATE</span>
                        Trigger: update_loyalty_points
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">
                        Awards loyalty points when order status changes to 'delivered'
                    </p>
                    <?php
                    $stmt = $pdo->query("
                        SELECT 
                            c.name,
                            c.loyalty_points,
                            COUNT(CASE WHEN o.status = 'delivered' THEN 1 END) as delivered_orders,
                            COALESCE(SUM(CASE WHEN o.status = 'delivered' THEN o.total_amount END), 0) as delivered_total
                        FROM Customers c
                        LEFT JOIN Orders o ON c.customer_id = o.customer_id
                        GROUP BY c.customer_id, c.name, c.loyalty_points
                        HAVING COUNT(o.order_id) > 0
                    ");
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="p-2 text-left">Customer</th>
                                <th class="p-2 text-right">Delivered Orders</th>
                                <th class="p-2 text-right">Total Delivered</th>
                                <th class="p-2 text-right">Loyalty Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): ?>
                            <tr class="border-b">
                                <td class="p-2"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2 text-right"><?= $row['delivered_orders'] ?></td>
                                <td class="p-2 text-right">$<?= number_format($row['delivered_total'], 2) ?></td>
                                <td class="p-2 text-right font-bold text-green-600">
                                    <?= $row['loyalty_points'] ?> pts
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SQL Code Examples -->
        <div class="bg-gray-800 text-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">
                <i class="fas fa-code mr-2"></i>SQL Code Examples
            </h2>

            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-green-400 mb-2">1. Function: Get Customers by Product Name</h3>
                    <pre class="bg-gray-900 p-3 rounded overflow-x-auto text-sm"><code>CREATE FUNCTION get_customers_by_product(product_name VARCHAR(150))
RETURNS TEXT
DETERMINISTIC
BEGIN
    DECLARE customer_list TEXT;
    
    SELECT GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ')
    INTO customer_list
    FROM Customers c
    JOIN Orders o ON c.customer_id = o.customer_id
    JOIN Order_Details od ON o.order_id = od.order_id
    JOIN Products p ON od.product_id = p.product_id
    WHERE p.name = product_name;
    
    RETURN COALESCE(customer_list, 'No customers found');
END

-- Usage:
SELECT get_customers_by_product('Organic Apple');
-- Returns: "Alice Green, Frank Ocean"</code></pre>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-green-400 mb-2">2. Function: Get Product Info (Multiple Values as String)</h3>
                    <pre class="bg-gray-900 p-3 rounded overflow-x-auto text-sm"><code>CREATE FUNCTION get_product_info(product_name VARCHAR(150))
RETURNS VARCHAR(500)
DETERMINISTIC
BEGIN
    DECLARE vendor_name VARCHAR(150);
    DECLARE product_category VARCHAR(100);
    DECLARE result VARCHAR(500);
    
    SELECT v.vendor_name, p.category
    INTO vendor_name, product_category
    FROM Products p
    LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
    WHERE p.name = product_name;
    
    SET result = CONCAT(
        'Vendor: ', COALESCE(vendor_name, 'Unknown'),
        ' | Category: ', COALESCE(product_category, 'Unknown')
    );
    
    RETURN result;
END

-- Usage:
SELECT get_product_info('Kale');
-- Returns: "Vendor: Green Valley | Category: Vegetables"</code></pre>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-green-400 mb-2">3. Stored Procedure: Get Product Details (TRUE Multiple Values)</h3>
                    <pre class="bg-gray-900 p-3 rounded overflow-x-auto text-sm"><code>CREATE PROCEDURE get_product_details(
    IN product_name VARCHAR(150),
    OUT vendor_name VARCHAR(150),
    OUT product_category VARCHAR(100)
)
BEGIN
    SELECT v.vendor_name, p.category
    INTO vendor_name, product_category
    FROM Products p
    LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
    WHERE p.name = product_name;
END

-- Usage:
CALL get_product_details('Kale', @vendor, @category);
SELECT @vendor, @category;
-- Returns 2 separate values: "Green Valley" and "Vegetables"</code></pre>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-green-400 mb-2">4. Trigger: Auto-Calculate Subtotal</h3>
                    <pre class="bg-gray-900 p-3 rounded overflow-x-auto text-sm"><code>CREATE TRIGGER calculate_subtotal
BEFORE INSERT ON Order_Details
FOR EACH ROW
BEGIN
    DECLARE product_price DECIMAL(10,2);
    SELECT price INTO product_price 
    FROM Products WHERE product_id = NEW.product_id;
    SET NEW.subtotal = product_price * NEW.quantity;
END</code></pre>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="index.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                <i class="fas fa-home mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
