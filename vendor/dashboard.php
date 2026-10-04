<?php
$pageTitle = 'Dashboard — QuickBite Vendor';
$activeTab = 'dashboard';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Welcome back, Café ABC</h1>
  <p class="dash-panel__subtitle">Here's what's happening with your shop today.</p>

  <div class="stat-row">
    <div class="stat-card"><span class="stat-card__value">12</span><span class="stat-card__label">Total Items</span></div>
    <div class="stat-card"><span class="stat-card__value">25</span><span class="stat-card__label">Total Orders</span></div>
    <div class="stat-card"><span class="stat-card__value">Rs. 12,450</span><span class="stat-card__label">Total Revenue</span></div>
    <div class="stat-card"><span class="stat-card__value">8</span><span class="stat-card__label">Today's Orders</span></div>
  </div>

  <div class="panel-block">
    <div class="panel-block__header"><h2>Recent Orders</h2><a href="orders.php">View All</a></div>
    <table class="data-table">
      <thead><tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($vendor_orders as $o): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer']) ?></td>
            <td><?= $o['items'] ?> items</td>
            <td>Rs. <?= $o['amount'] ?></td>
            <td><?= status_pill($o['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
