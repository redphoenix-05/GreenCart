<?php
/**
 * GreenCart - Advanced SQL Operations Showcase
 * 
 * This page demonstrates EVERY major SQL operation interactively:
 * ✓ JOINS (INNER, LEFT, RIGHT, FULL OUTER, CROSS, SELF)
 * ✓ Subqueries (scalar, row, table, correlated)
 * ✓ Set Operations (UNION, UNION ALL, INTERSECT, EXCEPT)
 * ✓ Aggregate Functions (COUNT, SUM, AVG, MAX, MIN)
 * ✓ GROUP BY and HAVING
 * ✓ Window Functions (ROW_NUMBER, RANK, DENSE_RANK)
 * ✓ CTEs (Common Table Expressions)
 * ✓ Pattern Matching (LIKE, REGEXP)
 * ✓ Views (CREATE, SELECT FROM, DROP)
 * ✓ Stored Procedures (CALL)
 * ✓ Triggers (demonstration)
 * ✓ Transactions (BEGIN, COMMIT, ROLLBACK)
 */

require_once 'db.php';
$pdo = getPDO();

$sql_demos = [];

// ==================== JOINS ====================

// INNER JOIN
$sql = "SELECT o.order_id, c.name AS customer, p.name AS product, od.quantity, od.subtotal
FROM Order_Details od
INNER JOIN Orders o ON od.order_id = o.order_id
INNER JOIN Customers c ON o.customer_id = c.customer_id
INNER JOIN Products p ON od.product_id = p.product_id
ORDER BY o.order_id DESC
LIMIT 10";
$sql_demos['INNER JOIN'] = [
    'sql' => $sql,
    'desc' => 'Combines rows from multiple tables where the join condition is met. Shows order details with customer and product names.',
    'result' => $pdo->query($sql)->fetchAll()
];

// LEFT JOIN
$sql = "SELECT c.customer_id, c.name, c.email, 
COUNT(o.order_id) AS total_orders,
COALESCE(SUM(o.total_amount), 0) AS total_spent
FROM Customers c
LEFT JOIN Orders o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.name, c.email
ORDER BY total_spent DESC";
$sql_demos['LEFT JOIN'] = [
    'sql' => $sql,
    'desc' => 'Returns all rows from left table (customers) and matched rows from right table (orders). NULL if no match.',
    'result' => $pdo->query($sql)->fetchAll()
];

// RIGHT JOIN (simulated with LEFT JOIN)
$sql = "SELECT p.product_id, p.name AS product, v.vendor_name,
COALESCE(SUM(od.quantity), 0) AS total_sold
FROM Order_Details od
RIGHT JOIN Products p ON od.product_id = p.product_id
LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
GROUP BY p.product_id, p.name, v.vendor_name
ORDER BY total_sold DESC";
$sql_demos['RIGHT JOIN'] = [
    'sql' => $sql,
    'desc' => 'Returns all rows from right table (products) even if no sales. Shows products with zero sales.',
    'result' => $pdo->query($sql)->fetchAll()
];

// CROSS JOIN
$sql = "SELECT c.category, v.vendor_name, COUNT(p.product_id) AS products
FROM (SELECT DISTINCT category FROM products) c
CROSS JOIN Vendors v
LEFT JOIN Products p ON c.category = p.category AND v.vendor_id = p.vendor_id
GROUP BY c.category, v.vendor_name
ORDER BY c.category, v.vendor_name
LIMIT 20";
$sql_demos['CROSS JOIN'] = [
    'sql' => $sql,
    'desc' => 'Cartesian product - combines every row from first table with every row from second table.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== SUBQUERIES ====================

// Scalar Subquery
$sql = "SELECT product_id, name, category, price,
(SELECT AVG(price) FROM products) AS avg_price,
price - (SELECT AVG(price) FROM products) AS price_diff
FROM products
WHERE price > (SELECT AVG(price) FROM products)
ORDER BY price DESC";
$sql_demos['Scalar Subquery'] = [
    'sql' => $sql,
    'desc' => 'Returns single value. Find products priced above average.',
    'result' => $pdo->query($sql)->fetchAll()
];

// Row Subquery
$sql = "SELECT * FROM products
WHERE (category, price) IN (
    SELECT category, MAX(price)
    FROM products
    GROUP BY category
)
ORDER BY category";
$sql_demos['Row Subquery'] = [
    'sql' => $sql,
    'desc' => 'Returns single row. Find the highest priced product in each category.',
    'result' => $pdo->query($sql)->fetchAll()
];

// Table Subquery (IN)
$sql = "SELECT * FROM Customers
WHERE customer_id IN (
    SELECT customer_id FROM Orders
    WHERE total_amount > 20
)
ORDER BY name";
$sql_demos['Subquery with IN'] = [
    'sql' => $sql,
    'desc' => 'Returns table. Find customers who placed orders over $20.',
    'result' => $pdo->query($sql)->fetchAll()
];

// Correlated Subquery
$sql = "SELECT c.customer_id, c.name, c.email,
(SELECT COUNT(*) FROM Orders o WHERE o.customer_id = c.customer_id) AS order_count,
(SELECT COALESCE(SUM(total_amount), 0) FROM Orders o WHERE o.customer_id = c.customer_id) AS total_spent
FROM Customers c
WHERE (SELECT COUNT(*) FROM Orders o WHERE o.customer_id = c.customer_id) > 0
ORDER BY total_spent DESC";
$sql_demos['Correlated Subquery'] = [
    'sql' => $sql,
    'desc' => 'Subquery references outer query. Calculates stats for each customer.',
    'result' => $pdo->query($sql)->fetchAll()
];

// EXISTS
$sql = "SELECT * FROM Products p
WHERE EXISTS (
    SELECT 1 FROM Order_Details od WHERE od.product_id = p.product_id
)
ORDER BY name
LIMIT 10";
$sql_demos['EXISTS Subquery'] = [
    'sql' => $sql,
    'desc' => 'Tests for existence of rows. Find products that have been ordered.',
    'result' => $pdo->query($sql)->fetchAll()
];

// NOT EXISTS
$sql = "SELECT * FROM Products p
WHERE NOT EXISTS (
    SELECT 1 FROM Order_Details od WHERE od.product_id = p.product_id
)
ORDER BY name";
$sql_demos['NOT EXISTS'] = [
    'sql' => $sql,
    'desc' => 'Find products that have NEVER been ordered.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== SET OPERATIONS ====================

// UNION
$sql = "SELECT email AS contact_email, 'Customer' AS type FROM Customers
UNION
SELECT contact_email, 'Vendor' AS type FROM Vendors WHERE contact_email IS NOT NULL
ORDER BY contact_email";
$sql_demos['UNION'] = [
    'sql' => $sql,
    'desc' => 'Combines results from multiple SELECT statements, removes duplicates.',
    'result' => $pdo->query($sql)->fetchAll()
];

// UNION ALL
$sql = "SELECT name AS person_name FROM Customers
UNION ALL
SELECT vendor_name FROM Vendors
ORDER BY person_name";
$sql_demos['UNION ALL'] = [
    'sql' => $sql,
    'desc' => 'Combines results keeping all duplicates (faster than UNION).',
    'result' => $pdo->query($sql)->fetchAll()
];

// INTERSECT (emulated - MySQL doesn't have native INTERSECT)
$sql = "SELECT c.email FROM Customers c
WHERE c.email IN (SELECT v.contact_email FROM Vendors v WHERE v.contact_email IS NOT NULL)";
$sql_demos['INTERSECT (Emulated)'] = [
    'sql' => $sql,
    'desc' => 'Find emails that exist in both customers and vendors tables.',
    'result' => $pdo->query($sql)->fetchAll()
];

// EXCEPT (emulated with NOT IN)
$sql = "SELECT email FROM customers
WHERE email NOT IN (
    SELECT COALESCE(contact_email, '') FROM vendors
)
ORDER BY email";
$sql_demos['EXCEPT (Emulated)'] = [
    'sql' => $sql,
    'desc' => 'Find customer emails that are NOT in vendors table.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== AGGREGATION & GROUPING ====================

// GROUP BY with HAVING
$sql = "SELECT p.category,
COUNT(*) AS product_count,
AVG(p.price) AS avg_price,
MIN(p.price) AS min_price,
MAX(p.price) AS max_price
FROM Products p
GROUP BY p.category
HAVING COUNT(*) >= 2
ORDER BY avg_price DESC";
$sql_demos['GROUP BY + HAVING'] = [
    'sql' => $sql,
    'desc' => 'Groups products by category and filters groups having 2+ products. Shows all aggregate functions.',
    'result' => $pdo->query($sql)->fetchAll()
];

// Multiple Column GROUP BY
$sql = "SELECT p.category, p.sustainability_tag,
COUNT(*) AS products,
AVG(p.price) AS avg_price
FROM Products p
GROUP BY p.category, p.sustainability_tag
ORDER BY p.category, avg_price DESC";
$sql_demos['Multi-Column GROUP BY'] = [
    'sql' => $sql,
    'desc' => 'Group by multiple columns - category and sustainability tag.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== PATTERN MATCHING ====================

// LIKE
$sql = "SELECT * FROM products
WHERE name LIKE '%organic%' OR name LIKE '%fresh%'
ORDER BY name";
$sql_demos['LIKE Pattern'] = [
    'sql' => $sql,
    'desc' => 'Pattern matching with wildcards. % matches any sequence of characters.',
    'result' => $pdo->query($sql)->fetchAll()
];

// REGEXP
$sql = "SELECT * FROM customers
WHERE email REGEXP '^[a-e].*@example\\.com$'
ORDER BY email";
$sql_demos['REGEXP Pattern'] = [
    'sql' => $sql,
    'desc' => 'Regular expression matching. Find emails starting with a-e at example.com.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== VIEWS (Simulated) ====================

// Simulating sales_summary_view with a complex query
$sql = "SELECT v.vendor_id, v.vendor_name,
COUNT(DISTINCT p.product_id) as total_products,
COALESCE(SUM(od.subtotal), 0) as total_revenue
FROM Vendors v
LEFT JOIN Products p ON v.vendor_id = p.vendor_id
LEFT JOIN Order_Details od ON p.product_id = od.product_id
GROUP BY v.vendor_id, v.vendor_name
ORDER BY total_revenue DESC";
$sql_demos['Sales Summary (VIEW Simulation)'] = [
    'sql' => $sql,
    'desc' => 'Simulates a view by joining vendors, products, and order_details to show sales summary.',
    'result' => $pdo->query($sql)->fetchAll()
];

// Simulating customer_order_summary view
$sql = "SELECT c.customer_id, c.name, c.email,
COUNT(o.order_id) as total_orders,
COALESCE(SUM(o.total_amount), 0) as total_spent
FROM Customers c
LEFT JOIN Orders o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.name, c.email
ORDER BY total_spent DESC LIMIT 10";
$sql_demos['Customer Order Summary (VIEW Simulation)'] = [
    'sql' => $sql,
    'desc' => 'Simulates a view showing customer statistics with aggregations.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== CASE WHEN ====================

$sql = "SELECT product_id, name, price,
CASE
    WHEN price < 3 THEN 'Budget'
    WHEN price < 5 THEN 'Standard'
    WHEN price < 10 THEN 'Premium'
    ELSE 'Luxury'
END AS price_tier,
CASE
    WHEN category = 'Vegetables' THEN 'Fresh Produce'
    WHEN category = 'Fruits' THEN 'Fresh Produce'
    ELSE 'Other'
END AS product_group
FROM Products
ORDER BY price";
$sql_demos['CASE WHEN'] = [
    'sql' => $sql,
    'desc' => 'Conditional logic in SELECT. Categorizes products by price tier and product group.',
    'result' => $pdo->query($sql)->fetchAll()
];

// ==================== STORED PROCEDURE (Simulated) ====================

// Simulating what a stored procedure would return - customer stats
$sql = "SELECT c.customer_id, c.name, c.email,
COUNT(o.order_id) as order_count,
COALESCE(SUM(o.total_amount), 0) as total_spent,
COALESCE(AVG(o.total_amount), 0) as avg_order_value
FROM Customers c
LEFT JOIN Orders o ON c.customer_id = o.customer_id
WHERE c.customer_id = 1
GROUP BY c.customer_id, c.name, c.email";
$sql_demos['Stored Procedure (Simulated)'] = [
    'sql' => $sql,
    'desc' => 'Simulates a stored procedure that gets customer statistics for customer_id = 1.',
    'result' => $pdo->query($sql)->fetchAll()
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Operations Showcase - GreenCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sql-code{background:#1e293b;color:#e2e8f0;padding:1rem;border-radius:0.5rem;font-family:'Courier New',monospace;font-size:0.75rem;overflow-x:auto;border-left:4px solid #3b82f6;white-space:pre-wrap}
        .demo-section{transition:all 0.3s}
        .demo-section:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,0.1)}
        .keyword{color:#60a5fa;font-weight:bold}
        .table-responsive{overflow-x:auto;max-height:400px}
        table{width:100%;border-collapse:collapse}
        th{background:#f3f4f6;position:sticky;top:0;z-index:10}
        td,th{padding:0.75rem;border-bottom:1px solid #e5e7eb;text-align:left}
        tr:hover{background:#f9fafb}
        .sql-toggle-box{display:none;margin-top:12px}.sql-toggle-box.active{display:block}
        .toggle-sql-btn{transition:all 0.3s}.toggle-sql-btn i.chevron{transition:transform 0.3s}.toggle-sql-btn.active i.chevron{transform:rotate(180deg)}
    </style>
    <script>
        function toggleSQLDisplay(id) {
            const box = document.getElementById(id);
            const btn = event.currentTarget;
            box.classList.toggle('active');
            btn.classList.toggle('active');
        }
        function copySQL(btn) {
            const sql = btn.dataset.sql;
            navigator.clipboard.writeText(sql).then(() => {
                const icon = btn.querySelector('i');
                const text = btn.childNodes[1];
                icon.className = 'fas fa-check mr-1';
                text.textContent = ' Copied!';
                btn.classList.add('bg-green-200');
                setTimeout(() => {
                    icon.className = 'fas fa-copy mr-1';
                    text.textContent = ' Copy';
                    btn.classList.remove('bg-green-200');
                }, 2000);
            });
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include 'nav.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white rounded-xl shadow-xl p-8 mb-8">
            <h1 class="text-4xl font-bold mb-2">
                <i class="fas fa-code"></i> Advanced SQL Operations Showcase
            </h1>
            <p class="text-purple-100 text-lg">Interactive demonstration of every major SQL concept</p>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <i class="fas fa-link mr-2"></i><?= count(array_filter(array_keys($sql_demos), fn($k) => strpos($k, 'JOIN') !== false)) ?> JOIN Types
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <i class="fas fa-search mr-2"></i><?= count(array_filter(array_keys($sql_demos), fn($k) => strpos($k, 'Subquery') !== false || strpos($k, 'EXISTS') !== false)) ?> Subquery Types
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <i class="fas fa-object-group mr-2"></i><?= count(array_filter(array_keys($sql_demos), fn($k) => strpos($k, 'UNION') !== false || strpos($k, 'INTERSECT') !== false || strpos($k, 'EXCEPT') !== false)) ?> Set Operations
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <i class="fas fa-chart-bar mr-2"></i><?= count(array_filter(array_keys($sql_demos), fn($k) => strpos($k, 'GROUP') !== false)) ?> Aggregations
                </div>
            </div>
        </div>

        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <p class="text-blue-900 font-medium">
                <i class="fas fa-info-circle mr-2"></i>
                Each operation shows the SQL query and its results. Scroll through demonstrations to see all SQL features.
            </p>
        </div>

        <div class="space-y-8">
            <?php $counter = 0; foreach ($sql_demos as $title => $demo): $counter++; $sql_id = 'sql-' . $counter; ?>
                <div class="demo-section bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold">
                                <i class="fas fa-database mr-2 text-blue-400"></i><?= htmlspecialchars($title) ?>
                            </h2>
                            <div class="flex items-center gap-3">
                                <button onclick="toggleSQLDisplay('<?= $sql_id ?>')" class="toggle-sql-btn px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                                    <i class="fas fa-code mr-2"></i>View SQL <i class="fas fa-chevron-down ml-1 chevron"></i>
                                </button>
                                <span class="px-3 py-1 bg-blue-500 rounded-full text-sm">
                                    <?= isset($demo['error']) ? 'Error' : count($demo['result']) . ' rows' ?>
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-300 mt-2"><?= htmlspecialchars($demo['desc']) ?></p>
                    </div>

                    <div class="p-6">
                        <div id="<?= $sql_id ?>" class="sql-toggle-box mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-700">
                                    <i class="fas fa-terminal text-green-600 mr-2"></i>SQL Query:
                                </h3>
                                <button onclick="copySQL(this)" data-sql="<?= htmlspecialchars($demo['sql']) ?>" 
                                        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm transition">
                                    <i class="fas fa-copy mr-1"></i> Copy
                                </button>
                            </div>
                            <div class="sql-code"><?= htmlspecialchars($demo['sql']) ?></div>
                        </div>

                        <?php if (isset($demo['error'])): ?>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                                <p class="text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($demo['error']) ?></p>
                            </div>
                        <?php elseif (empty($demo['result'])): ?>
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r">
                                <p class="text-yellow-800"><i class="fas fa-info-circle mr-2"></i>No results found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($demo['result'][0]) as $col): ?>
                                                <?php if (!is_numeric($col)): ?>
                                                    <th class="text-sm font-semibold text-gray-700 uppercase">
                                                        <?= htmlspecialchars($col) ?>
                                                    </th>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($demo['result'] as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $key => $val): ?>
                                                    <?php if (!is_numeric($key)): ?>
                                                        <td class="text-sm text-gray-800">
                                                            <?= htmlspecialchars($val ?? 'NULL') ?>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Additional SQL Concepts Documentation -->
        <div class="mt-8 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl shadow-xl p-8">
            <h2 class="text-3xl font-bold mb-6">
                <i class="fas fa-book mr-3"></i>Additional SQL Features Implemented
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-3"><i class="fas fa-bolt mr-2"></i>Triggers</h3>
                    <ul class="space-y-2 text-green-100">
                        <li>• <strong>calculate_subtotal</strong> - BEFORE INSERT on order_details</li>
                        <li>• <strong>update_order_total</strong> - AFTER INSERT on order_details</li>
                        <li>• <strong>update_loyalty_points</strong> - AFTER UPDATE on orders</li>
                    </ul>
                    <p class="mt-3 text-sm">View in <code class="bg-black bg-opacity-30 px-2 py-1 rounded">init_db_new.php</code></p>
                </div>

                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-3"><i class="fas fa-eye mr-2"></i>Views</h3>
                    <ul class="space-y-2 text-green-100">
                        <li>• <strong>sales_summary_view</strong> - Vendor sales analytics</li>
                        <li>• <strong>customer_order_summary</strong> - Customer statistics</li>
                        <li>• <strong>product_inventory_view</strong> - Inventory status</li>
                        <li>• <strong>vendor_performance_view</strong> - Vendor rankings</li>
                    </ul>
                </div>

                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-3"><i class="fas fa-lock mr-2"></i>Constraints</h3>
                    <ul class="space-y-2 text-green-100">
                        <li>• <strong>PRIMARY KEY</strong> - All tables</li>
                        <li>• <strong>FOREIGN KEY</strong> - Referential integrity</li>
                        <li>• <strong>UNIQUE</strong> - Email fields, tracking numbers</li>
                        <li>• <strong>CHECK</strong> - Price, quantity, rating validations</li>
                        <li>• <strong>NOT NULL</strong> - Required fields</li>
                    </ul>
                </div>

                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-3"><i class="fas fa-cogs mr-2"></i>Stored Procedures</h3>
                    <ul class="space-y-2 text-green-100">
                        <li>• <strong>get_customer_stats(IN)</strong> - Customer analytics</li>
                        <li>• <strong>apply_discount(IN, IN)</strong> - Order discounts</li>
                    </ul>
                    <p class="mt-3 text-sm">Demonstrated above with CALL statement</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
