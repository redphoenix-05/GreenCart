<?php
// customers_list.php - Read & entry points for Customers table (CRUD)
require_once __DIR__ . '/db.php';
$pdo = getPDO();

// Fetch all customers
$sql = 'SELECT * FROM Customers ORDER BY customer_id DESC';
$stmt = $pdo->query($sql);
$customers = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Customers - GreenCart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    .sql-code-box{display:none;background:#1e293b;color:#e2e8f0;padding:16px;border-radius:8px;margin-top:12px;font-family:monospace;font-size:13px;white-space:pre-wrap;border:2px solid #3b82f6}.sql-code-box.active{display:block}.toggle-sql{transition:all 0.3s}.toggle-sql i{transition:transform 0.3s}.toggle-sql.active i{transform:rotate(180deg)}
    .sql-tooltip{position:relative;cursor:pointer}.sql-tooltip:hover .sql-tooltip-text{visibility:visible;opacity:1}.sql-tooltip-text{visibility:hidden;opacity:0;position:absolute;z-index:1000;background:#1e293b;color:#e2e8f0;padding:12px 16px;border-radius:8px;font-family:monospace;font-size:11px;white-space:pre;border:2px solid #3b82f6;transition:opacity 0.3s;bottom:100%;left:50%;transform:translateX(-50%);margin-bottom:8px;min-width:300px;max-width:500px;box-shadow:0 4px 20px rgba(0,0,0,0.3)}.sql-tooltip-text::after{content:"";position:absolute;top:100%;left:50%;margin-left:-8px;border-width:8px;border-style:solid;border-color:#3b82f6 transparent transparent transparent}
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
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-users text-blue-600 mr-2"></i>Customers</h1>
          <p class="text-gray-600 mt-2">Manage customer records with CRUD operations</p>
        </div>
        <div class="flex gap-3">
          <button onclick="toggleSQL('sql-customers')" class="toggle-sql px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-code mr-2"></i>View SQL <i class="fas fa-chevron-down ml-1"></i>
          </button>
          <a class="sql-tooltip px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" href="customers_form.php">
            <i class="fas fa-plus mr-2"></i>Add Customer
            <span class="sql-tooltip-text">INSERT INTO Customers 
(name, email, phone, address, preferences) 
VALUES (?, ?, ?, ?, ?)</span>
          </a>
        </div>
      </div>
      <div id="sql-customers" class="sql-code-box"><?= htmlspecialchars($sql) ?></div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-800 text-white">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-left">Phone</th>
              <th class="px-4 py-3 text-left">Address</th>
              <th class="px-4 py-3 text-left">Preferences</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($customers as $c): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 font-mono"><?=htmlspecialchars($c['customer_id'])?></td>
              <td class="px-4 py-3 font-semibold"><?=htmlspecialchars($c['name'])?></td>
              <td class="px-4 py-3 text-blue-600"><?=htmlspecialchars($c['email'])?></td>
              <td class="px-4 py-3"><?=htmlspecialchars($c['phone'])?></td>
              <td class="px-4 py-3 text-sm"><?=nl2br(htmlspecialchars($c['address']))?></td>
              <td class="px-4 py-3 text-sm text-gray-600"><?=nl2br(htmlspecialchars($c['preferences']))?></td>
              <td class="px-4 py-3 text-center">
                <a href="customers_form.php?id=<?=urlencode($c['customer_id'])?>" class="sql-tooltip text-blue-600 hover:text-blue-800 mr-3">
                  <i class="fas fa-edit"></i> Edit
                  <span class="sql-tooltip-text">UPDATE Customers SET 
name=?, email=?, phone=?, 
address=?, preferences=? 
WHERE customer_id=<?=$c['customer_id']?></span>
                </a>
                <a class="sql-tooltip text-red-600 hover:text-red-800" href="customers_delete.php?id=<?=urlencode($c['customer_id'])?>" onclick="return confirm('Delete this customer?')">
                  <i class="fas fa-trash"></i> Delete
                  <span class="sql-tooltip-text">DELETE FROM Customers 
WHERE customer_id=<?=$c['customer_id']?></span>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
