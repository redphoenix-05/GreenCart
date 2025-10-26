<?php
$current_page = basename($_SERVER['PHP_SELF']);
function isActive($page) {
    global $current_page;
    return ($current_page === $page) ? 'bg-white bg-opacity-30 font-semibold' : 'hover:bg-white hover:bg-opacity-20';
}
?>
<nav class="bg-gradient-to-r from-green-600 to-emerald-700 text-white shadow-lg mb-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <i class="fas fa-leaf text-2xl mr-3"></i>
                <span class="text-2xl font-bold">GreenCart Admin</span>
            </div>
            <div class="flex space-x-1">
                <a href="index.php" class="px-4 py-2 rounded-lg transition <?= isActive('index.php') ?>"><i class="fas fa-home mr-2"></i>Dashboard</a>
                <a href="customers_list.php" class="px-4 py-2 rounded-lg transition <?= isActive('customers_list.php') ?>"><i class="fas fa-users mr-2"></i>Customers</a>
                <a href="vendors_list.php" class="px-4 py-2 rounded-lg transition <?= isActive('vendors_list.php') ?>"><i class="fas fa-truck mr-2"></i>Vendors</a>
                <a href="products_list.php" class="px-4 py-2 rounded-lg transition <?= isActive('products_list.php') ?>"><i class="fas fa-box mr-2"></i>Products</a>
                <a href="orders_list.php" class="px-4 py-2 rounded-lg transition <?= isActive('orders_list.php') ?>"><i class="fas fa-shopping-cart mr-2"></i>Orders</a>
                <a href="delivery_list.php" class="px-4 py-2 rounded-lg transition <?= isActive('delivery_list.php') ?>"><i class="fas fa-shipping-fast mr-2"></i>Delivery</a>
                <a href="sql_ops.php" class="px-4 py-2 rounded-lg transition <?= $current_page === 'sql_ops.php' ? 'bg-yellow-500 font-semibold' : 'bg-yellow-400 hover:bg-yellow-500' ?>"><i class="fas fa-code mr-2"></i>SQL Ops</a>
            </div>
        </div>
    </div>
</nav>