<?php
$pageTitle = 'Menu Items — QuickBite Vendor';
$activeTab = 'items';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$active_count = count(array_filter($vendor_menu_items, fn($i) => $i['status'] === 'active'));
$inactive_count = count($vendor_menu_items) - $active_count;
?>

<section class="dash-panel">
  <div class="dash-panel__row">
    <div>
      <h1 class="dash-panel__title">Menu Items</h1>
      <p class="dash-panel__subtitle">Manage your food items and availability.</p>
    </div>
    <a href="add-item.php" class="btn btn--primary btn--sm">+ Add New Item</a>
  </div>

  <div class="chip-row">
    <button class="chip is-active" data-filter="all">All (<?= count($vendor_menu_items) ?>)</button>
    <button class="chip" data-filter="active">Active (<?= $active_count ?>)</button>
    <button class="chip" data-filter="inactive">Inactive (<?= $inactive_count ?>)</button>
  </div>

  <table class="data-table">
    <thead><tr><th>Item</th><th>Price</th><th>Discount</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($vendor_menu_items as $i): ?>
        <tr data-status="<?= $i['status'] ?>">
          <td><?= htmlspecialchars($i['name']) ?></td>
          <td>Rs. <?= $i['price'] ?></td>
          <td><?= $i['discount'] ?></td>
          <td data-status-cell><?= status_pill($i['status']) ?></td>
          <td>
            <button class="table-action-btn">Edit</button>
            <button class="table-action-btn table-action-btn--reject">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
