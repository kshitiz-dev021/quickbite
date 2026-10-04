<?php
$pageTitle = 'Reports — QuickBite Admin';
$activeTab = 'reports';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';

$db = get_db();

$totalRev = (float)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = 'completed'")->fetchColumn();
if ($totalRev == 0) {
    $totalRev = (float)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders")->fetchColumn();
}
$totalOrders = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$newUsers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
$avgOrder = $totalOrders > 0 ? ($totalRev / $totalOrders) : 0;

// Top selling dishes
$topDishes = $db->query("
    SELECT oi.item_name, SUM(oi.quantity) as total_qty, SUM(oi.line_total) as total_sales
    FROM order_items oi
    GROUP BY oi.item_name
    ORDER BY total_qty DESC LIMIT 5
")->fetchAll();
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Reports &amp; Analytics</h1>
  <p class="dash-panel__subtitle">Platform sales, revenue metrics, and customer insights.</p>

  <div class="stat-row stat-row--compact">
    <div class="stat-card"><span class="stat-card__value">Rs. <?= number_format($totalRev) ?></span><span class="stat-card__label">Total Revenue</span></div>
    <div class="stat-card"><span class="stat-card__value"><?= $totalOrders ?></span><span class="stat-card__label">Total Orders</span></div>
    <div class="stat-card"><span class="stat-card__value"><?= $newUsers ?></span><span class="stat-card__label">New Customers (30d)</span></div>
    <div class="stat-card"><span class="stat-card__value">Rs. <?= number_format($avgOrder) ?></span><span class="stat-card__label">Avg. Order Value</span></div>
  </div>

  <div class="panel-block">
    <div class="panel-block__header"><h2>Top Selling Items</h2></div>
    <?php if (empty($topDishes)): ?>
      <p style="padding: 1.5rem; color: #6E6259; text-align: center;">No item sales data yet. Top dishes will appear once customer orders are placed.</p>
    <?php else: ?>
      <div class="table-responsive" style="border: none; box-shadow: none;">
        <table class="data-table">
          <thead><tr><th>Dish Name</th><th>Quantity Sold</th><th>Total Revenue</th></tr></thead>
          <tbody>
            <?php foreach ($topDishes as $td): ?>
              <tr>
                <td><strong><?= htmlspecialchars($td['item_name']) ?></strong></td>
                <td><?= $td['total_qty'] ?> units</td>
                <td>Rs. <?= number_format($td['total_sales']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
