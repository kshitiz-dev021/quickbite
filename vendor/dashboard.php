<?php
$pageTitle = 'Dashboard — QuickBite Vendor';
$activeTab = 'dashboard';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$db = get_db();

// Calculate live vendor stats
$vId = $currentVendorId;

// Total items
$totalItems = count($vendor_menu_items);

// Total orders
$totalOrders = count($vendor_orders);

// Total revenue
$revStmt = $db->prepare("SELECT COALESCE(SUM(total), 0) FROM orders WHERE vendor_id = ? AND status = 'completed'");
$revStmt->execute([$vId]);
$totalRevenue = (float)$revStmt->fetchColumn();

// If no completed orders yet, calculate from all orders for demonstration
if ($totalRevenue == 0) {
    $revStmt2 = $db->prepare("SELECT COALESCE(SUM(total), 0) FROM orders WHERE vendor_id = ?");
    $revStmt2->execute([$vId]);
    $totalRevenue = (float)$revStmt2->fetchColumn();
}

// Today's orders
$todayStmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE vendor_id = ? AND DATE(created_at) = CURDATE()");
$todayStmt->execute([$vId]);
$todayOrders = (int)$todayStmt->fetchColumn();

$vendorName = $currentUser['vendor_name'] ?? 'Vendor';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Welcome back, <?= htmlspecialchars($vendorName) ?></h1>
  <p class="dash-panel__subtitle">Here's what's happening with your shop today.</p>

  <div class="stat-row">
    <div class="stat-card"><span class="stat-card__value"><?= $totalItems ?></span><span class="stat-card__label">Total Items</span></div>
    <div class="stat-card"><span class="stat-card__value"><?= $totalOrders ?></span><span class="stat-card__label">Total Orders</span></div>
    <div class="stat-card"><span class="stat-card__value">Rs. <?= number_format($totalRevenue) ?></span><span class="stat-card__label">Total Revenue</span></div>
    <div class="stat-card"><span class="stat-card__value"><?= $todayOrders ?></span><span class="stat-card__label">Today's Orders</span></div>
  </div>

  <div class="panel-block">
    <div class="panel-block__header">
      <h2>Recent Orders</h2>
      <a href="orders.php">View All</a>
    </div>

    <?php if (empty($vendor_orders)): ?>
      <p style="padding: 1.5rem; color: #6E6259; text-align: center;">No orders received yet. New customer orders will show up here.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach (array_slice($vendor_orders, 0, 5) as $o): ?>
            <tr>
              <td>#ORD-<?= $o['id'] ?></td>
              <td><?= htmlspecialchars($o['customer']) ?></td>
              <td><?= $o['items'] ?> items</td>
              <td>Rs. <?= number_format($o['amount']) ?></td>
              <td><?= status_pill($o['status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
