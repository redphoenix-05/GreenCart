<?php
/**
 * GreenCart - Professional Admin Dashboard
 * SQL OPERATIONS: SELECT with JOINs, GROUP BY, aggregate functions (COUNT, SUM, AVG)
 */
require_once 'db.php';
$pdo = getPDO();

// Collect all SQL queries for display
$sql_log = [];

// Dashboard statistics with SQL logging
function execSQL($label, $sql, $pdo) {
    global $sql_log;
    $sql_log[$label] = $sql;
    return $pdo->query($sql);
}

$stats = [
    'customers' => execSQL('Total Customers', 'SELECT COUNT(*) as total FROM Customers', $pdo)->fetch()['total'],
    'products' => execSQL('Total Products', 'SELECT COUNT(*) as total FROM Products', $pdo)->fetch()['total'],
    'orders' => execSQL('Total Orders', 'SELECT COUNT(*) as total FROM Orders', $pdo)->fetch()['total'],
    'revenue' => execSQL('Total Revenue', "SELECT COALESCE(SUM(total_amount), 0) as total FROM Orders WHERE status != 'cancelled'", $pdo)->fetch()['total'],
    'vendors' => execSQL('Active Vendors', 'SELECT COUNT(*) as total FROM Vendors', $pdo)->fetch()['total'],
    'pending' => execSQL('Pending Orders', "SELECT COUNT(*) as total FROM Orders WHERE status IN ('pending', 'processing')", $pdo)->fetch()['total'],
    'avg_order' => execSQL('Average Order', "SELECT COALESCE(AVG(total_amount), 0) as avg FROM Orders WHERE status != 'cancelled'", $pdo)->fetch()['avg'],
    'low_stock' => execSQL('Low Stock', 'SELECT COUNT(*) as total FROM Products WHERE price >= 0', $pdo)->fetch()['total']
];

$sql = "SELECT p.product_id, p.name, p.category, p.price, 
        COALESCE(SUM(od.quantity), 0) as total_sold,
        COALESCE(SUM(od.subtotal), 0) as revenue
        FROM Products p
        LEFT JOIN Order_Details od ON p.product_id = od.product_id
        GROUP BY p.product_id
        ORDER BY total_sold DESC LIMIT 5";
$top_products = execSQL('Top Products (LEFT JOIN + GROUP BY)', $sql, $pdo)->fetchAll();

$sql = "SELECT o.order_id, o.order_date, o.status, o.total_amount,
        c.name as customer_name, c.email,
        COUNT(od.order_detail_id) as items
        FROM Orders o
        INNER JOIN Customers c ON o.customer_id = c.customer_id
        LEFT JOIN Order_Details od ON o.order_id = od.order_id
        GROUP BY o.order_id
        ORDER BY o.order_date DESC LIMIT 5";
$recent_orders = execSQL('Recent Orders (INNER JOIN)', $sql, $pdo)->fetchAll();

$sql = "SELECT v.vendor_name, v.location,
        COUNT(DISTINCT p.product_id) as products,
        COALESCE(SUM(od.subtotal), 0) as sales
        FROM Vendors v
        LEFT JOIN Products p ON v.vendor_id = p.vendor_id
        LEFT JOIN Order_Details od ON p.product_id = od.product_id
        GROUP BY v.vendor_id
        ORDER BY sales DESC LIMIT 5";
$top_vendors = execSQL('Top Vendors (Multiple LEFT JOINs)', $sql, $pdo)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenCart - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sql-display{position:relative;cursor:help}.sql-display:hover .sql-box{display:block}.sql-box{display:none;position:absolute;z-index:50;background:#1e293b;color:#e2e8f0;padding:12px;border-radius:8px;font-family:monospace;font-size:11px;white-space:pre-wrap;min-width:400px;max-width:600px;bottom:120%;left:50%;transform:translateX(-50%);box-shadow:0 8px 16px rgba(0,0,0,0.3);border:2px solid #3b82f6}.stat-card{transition:transform 0.2s}.stat-card:hover{transform:translateY(-4px)}
        .sql-code-box{display:none;background:#1e293b;color:#e2e8f0;padding:16px;border-radius:8px;margin-top:12px;font-family:monospace;font-size:13px;white-space:pre-wrap;border:2px solid #3b82f6}.sql-code-box.active{display:block}.toggle-sql{transition:all 0.3s}.toggle-sql i{transition:transform 0.3s}.toggle-sql.active i{transform:rotate(180deg)}
    </style>
    <script>
        function toggleSQL(id) {
            const box = document.getElementById(id);
            const btn = event.currentTarget;
            box.classList.toggle('active');
            btn.classList.toggle('active');
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include 'nav.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-tachometer-alt text-blue-600"></i> Admin Dashboard
            </h1>
            <p class="text-gray-600">Professional Grocery Management - Hover over cards to see SQL queries</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card sql-display bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div><p class="text-blue-100 text-sm">Revenue</p><p class="text-3xl font-bold">$<?= number_format($stats['revenue'], 2) ?></p></div>
                    <i class="fas fa-dollar-sign text-5xl opacity-50"></i>
                </div>
                <div class="sql-box">/* SQL: <?= $sql_log['Total Revenue'] ?> */</div>
            </div>
            
            <div class="stat-card sql-display bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div><p class="text-green-100 text-sm">Orders</p><p class="text-3xl font-bold"><?= $stats['orders'] ?></p></div>
                    <i class="fas fa-shopping-cart text-5xl opacity-50"></i>
                </div>
                <div class="sql-box">/* SQL: <?= $sql_log['Total Orders'] ?> */</div>
            </div>
            
            <div class="stat-card sql-display bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div><p class="text-purple-100 text-sm">Customers</p><p class="text-3xl font-bold"><?= $stats['customers'] ?></p></div>
                    <i class="fas fa-users text-5xl opacity-50"></i>
                </div>
                <div class="sql-box">/* SQL: <?= $sql_log['Total Customers'] ?> */</div>
            </div>
            
            <div class="stat-card sql-display bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div><p class="text-orange-100 text-sm">Avg Order</p><p class="text-3xl font-bold">$<?= number_format($stats['avg_order'], 2) ?></p></div>
                    <i class="fas fa-chart-line text-5xl opacity-50"></i>
                </div>
                <div class="sql-box">/* SQL: <?= $sql_log['Average Order'] ?> */</div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold"><i class="fas fa-fire text-orange-500 mr-2"></i>Top Products</h2>
                <button onclick="toggleSQL('sql-top-products')" class="toggle-sql px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-code mr-2"></i>View SQL <i class="fas fa-chevron-down ml-1"></i>
                </button>
            </div>
            <div id="sql-top-products" class="sql-code-box"><?= htmlspecialchars($sql_log['Top Products (LEFT JOIN + GROUP BY)']) ?></div>
            <div class="space-y-3">
                <?php foreach($top_products as $p): ?>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div><h3 class="font-semibold"><?= htmlspecialchars($p['name']) ?></h3>
                    <p class="text-sm text-gray-500"><?= $p['category'] ?> • $<?= $p['price'] ?></p></div>
                    <div class="text-right"><p class="font-bold"><?= $p['total_sold'] ?> units</p><p class="text-sm text-green-600">$<?= number_format($p['revenue'], 2) ?></p></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold"><i class="fas fa-clock text-blue-500 mr-2"></i>Recent Orders</h2>
                <button onclick="toggleSQL('sql-recent-orders')" class="toggle-sql px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-code mr-2"></i>View SQL <i class="fas fa-chevron-down ml-1"></i>
                </button>
            </div>
            <div id="sql-recent-orders" class="sql-code-box"><?= htmlspecialchars($sql_log['Recent Orders (INNER JOIN)']) ?></div>
            <table class="w-full">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left">Order</th><th class="px-4 py-2 text-left">Customer</th><th class="px-4 py-2 text-left">Date</th><th class="px-4 py-2 text-left">Amount</th><th class="px-4 py-2 text-left">Status</th></tr></thead>
                <tbody>
                    <?php foreach($recent_orders as $o): 
                        $color = ['delivered'=>'green','shipped'=>'blue','processing'=>'yellow','pending'=>'gray','cancelled'=>'red'][$o['status']] ?? 'gray';
                    ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono">#<?= $o['order_id'] ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= date('M d, Y', strtotime($o['order_date'])) ?></td>
                        <td class="px-4 py-3 font-semibold">$<?= number_format($o['total_amount'], 2) ?></td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800"><?= ucfirst($o['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>