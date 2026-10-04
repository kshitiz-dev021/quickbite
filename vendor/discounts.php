<?php
$pageTitle = 'Discounts — QuickBite Vendor';
$activeTab = 'discounts';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Manage Discounts</h1>
  <p class="dash-panel__subtitle">Create and manage discounts for your items.</p>

  <table class="data-table">
    <thead><tr><th>Item Name</th><th>Original Price</th><th>Discount (%)</th><th>Net Price</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($vendor_discounts as $d): ?>
        <tr>
          <td><?= htmlspecialchars($d['name']) ?></td>
          <td>Rs. <?= $d['original'] ?></td>
          <td><?= $d['pct'] ?></td>
          <td>Rs. <?= $d['net'] ?></td>
          <td><button class="table-action-btn">Edit</button></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
