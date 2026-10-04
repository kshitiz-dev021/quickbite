<?php
$pageTitle = 'Orders — QuickBite Vendor';
$activeTab = 'orders';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Orders</h1>
  <p class="dash-panel__subtitle">Track and manage customer orders.</p>

  <div class="chip-row">
    <button class="chip is-active" data-filter="all">All</button>
    <button class="chip" data-filter="pending">Pending</button>
    <button class="chip" data-filter="preparing">Preparing</button>
    <button class="chip" data-filter="completed">Completed</button>
    <button class="chip" data-filter="cancelled">Cancelled</button>
  </div>

  <table class="data-table">
    <thead><tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($vendor_orders as $o): ?>
        <tr data-status="<?= $o['status'] ?>">
          <td><?= $o['id'] ?></td>
          <td><?= htmlspecialchars($o['customer']) ?></td>
          <td><?= $o['items'] ?> items</td>
          <td>Rs. <?= $o['amount'] ?></td>
          <td><?= status_pill($o['status']) ?></td>
          <td><button class="table-action-btn">View</button></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
