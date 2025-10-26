<?php
/**
 * GreenCart - Grocery Management System
 * Database Connection Helper with SQL Display Functionality
 * 
 * This file demonstrates:
 * - PDO connection with proper error handling
 * - SQL query display for educational purposes
 * - Prepared statements for security
 */

// Update the DB_USER and DB_PASS constants if your MySQL has a password for root
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'greencart_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Get PDO connection
function getPDO($withDb = true) {
    $host = DB_HOST;
    $db = $withDb ? DB_NAME : null;
    $dsn = 'mysql:host=' . $host . ($db ? ';dbname=' . $db : '') . ';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        echo "Database connection failed: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

/**
 * Display SQL query in a formatted box for educational purposes
 * Shows what SQL operation is being performed
 */
function displaySQL($query, $description = "", $type = "info") {
    $colors = [
        'info' => 'bg-gray-800 text-green-400 border-green-500',
        'success' => 'bg-green-900 text-green-300 border-green-400',
        'warning' => 'bg-yellow-900 text-yellow-300 border-yellow-400',
        'error' => 'bg-red-900 text-red-300 border-red-400'
    ];
    
    $colorClass = $colors[$type] ?? $colors['info'];
    
    $icons = [
        'SELECT' => '🔍',
        'INSERT' => '➕',
        'UPDATE' => '✏️',
        'DELETE' => '🗑️',
        'CREATE' => '🔨',
        'ALTER' => '🔧',
        'DROP' => '💥'
    ];
    
    $icon = '';
    foreach ($icons as $keyword => $emoji) {
        if (stripos($query, $keyword) !== false) {
            $icon = $emoji . ' ';
            break;
        }
    }
    
    return "<div class='sql-display border-l-4 {$colorClass} p-4 rounded-lg my-3 shadow-lg'>
                <div class='flex items-center gap-2 mb-2'>
                    <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                        <path d='M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z'></path>
                    </svg>
                    <span class='font-bold text-yellow-300'>{$icon}" . ($description ?: "SQL Query") . "</span>
                </div>
                <pre class='whitespace-pre-wrap break-all text-sm font-mono bg-black bg-opacity-30 p-3 rounded'>" . htmlspecialchars($query) . "</pre>
            </div>";
}

/**
 * Execute query and return result with SQL display
 * Useful for showing what SQL is being executed
 */
function executeAndDisplay($pdo, $query, $params = [], $description = "") {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        // Build display query with parameters substituted (for display only)
        $displayQuery = $query;
        foreach ($params as $key => $value) {
            $displayValue = is_string($value) ? "'{$value}'" : $value;
            $displayQuery = str_replace($key, $displayValue, $displayQuery);
        }
        
        $sqlDisplay = displaySQL($displayQuery, $description, 'success');
        return ['stmt' => $stmt, 'sql' => $sqlDisplay];
    } catch (PDOException $e) {
        $errorDisplay = displaySQL($query, "ERROR: " . $e->getMessage(), 'error');
        return ['stmt' => null, 'sql' => $errorDisplay, 'error' => $e->getMessage()];
    }
}

/**
 * Get common HTML header with Tailwind CSS
 */
function getHeader($title = "GreenCart") {
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . ' - GreenCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hover-sql { position: relative; }
        .hover-sql:hover .sql-tooltip {
            display: block;
            position: absolute;
            z-index: 50;
            background: #1f2937;
            color: #10b981;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-family: monospace;
            white-space: pre-wrap;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            top: 100%;
            left: 0;
            margin-top: 0.5rem;
        }
        .sql-tooltip { display: none; }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-100">';
}

/**
 * Get navigation menu
 */
function getNavigation() {
    return '<nav class="gradient-bg shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shopping-cart text-white text-2xl"></i>
                    <span class="text-white text-2xl font-bold">GreenCart</span>
                    <span class="text-green-300 text-sm">Grocery Management</span>
                </div>
                <div class="flex space-x-1">
                    <a href="index.php" class="text-white hover:bg-white hover:bg-opacity-20 px-4 py-2 rounded transition">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                    <div class="relative group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-4 py-2 rounded transition">
                            <i class="fas fa-database mr-1"></i>Manage <i class="fas fa-caret-down ml-1"></i>
                        </button>
                        <div class="hidden group-hover:block absolute bg-white shadow-lg rounded mt-1 py-2 w-48 z-50">
                            <a href="customers_list.php" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-users mr-2 text-blue-500"></i>Customers</a>
                            <a href="vendors_list.php" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-truck mr-2 text-green-500"></i>Vendors</a>
                            <a href="products_list.php" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-box mr-2 text-yellow-500"></i>Products</a>
                            <a href="orders_list.php" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-shopping-bag mr-2 text-purple-500"></i>Orders</a>
                            <a href="order_details_list.php" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-list mr-2 text-indigo-500"></i>Order Details</a>
                            <a href="delivery_list.php" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-shipping-fast mr-2 text-red-500"></i>Delivery</a>
                        </div>
                    </div>
                    <a href="sql_ops.php" class="text-white hover:bg-white hover:bg-opacity-20 px-4 py-2 rounded transition">
                        <i class="fas fa-code mr-1"></i>SQL Operations
                    </a>
                </div>
            </div>
        </div>
    </nav>';
}

/**
 * Get footer
 */
function getFooter() {
    return '<footer class="bg-gray-800 text-white mt-12 py-6">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 GreenCart - Database Systems Course Project</p>
            <p class="text-sm text-gray-400 mt-2">Demonstrating SQL: JOINs, Subqueries, Views, Triggers, Constraints, Aggregations & More</p>
        </div>
    </footer>
</body>
</html>';
}

?>
