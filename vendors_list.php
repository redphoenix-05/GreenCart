<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();
$sql = 'SELECT * FROM Vendors ORDER BY vendor_id DESC';
$vendors = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Vendors - GreenCart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
          <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-truck text-purple-600 mr-2"></i>Vendors</h1>
          <p class="text-gray-600 mt-2">Manage vendor records with CRUD operations</p>
        </div>
        <div class="flex gap-3">
          <button onclick="toggleSQL('sql-vendors')" class="toggle-sql px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-code mr-2"></i>View SQL <i class="fas fa-chevron-down ml-1"></i>
          </button>
          <a class="sql-tooltip px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" href="vendors_form.php">
            <i class="fas fa-plus mr-2"></i>Add Vendor
            <span class="sql-tooltip-text">INSERT INTO Vendors 
(vendor_name, contact_email, phone, location) 
VALUES (?, ?, ?, ?)</span>
          </a>
        </div>
      </div>
      <div id="sql-vendors" class="sql-code-box"><?= htmlspecialchars($sql) ?></div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-800 text-white">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Contact Email</th>
              <th class="px-4 py-3 text-left">Phone</th>
              <th class="px-4 py-3 text-left">Location</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($vendors as $v): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 font-mono"><?=htmlspecialchars($v['vendor_id'])?></td>
              <td class="px-4 py-3 font-semibold text-purple-600"><?=htmlspecialchars($v['vendor_name'])?></td>
              <td class="px-4 py-3 text-blue-600"><?=htmlspecialchars($v['contact_email'])?></td>
              <td class="px-4 py-3"><?=htmlspecialchars($v['phone'])?></td>
              <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm"><?=htmlspecialchars($v['location'])?></span></td>
              <td class="px-4 py-3 text-center">
                <a href="vendors_form.php?id=<?=urlencode($v['vendor_id'])?>" class="sql-tooltip text-blue-600 hover:text-blue-800 mr-3">
                  <i class="fas fa-edit"></i> Edit
                  <span class="sql-tooltip-text">UPDATE Vendors SET 
vendor_name=?, contact_email=?, 
phone=?, location=? 
WHERE vendor_id=<?=$v['vendor_id']?></span>
                </a>
                <a class="sql-tooltip text-red-600 hover:text-red-800" href="vendors_delete.php?id=<?=urlencode($v['vendor_id'])?>" onclick="return confirm('Delete this vendor?')">
                  <i class="fas fa-trash"></i> Delete
                  <span class="sql-tooltip-text">DELETE FROM Vendors 
WHERE vendor_id=<?=$v['vendor_id']?></span>
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
