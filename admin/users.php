<?php
$pageTitle = 'Users — QuickBite Admin';
$activeTab = 'users';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Registered Customers</h1>
  <p class="dash-panel__subtitle">All customer accounts registered on the platform.</p>

  <table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Total Orders</th><th>Status</th><th>Joined</th></tr></thead>
    <tbody>
      <?php if (empty($admin_users)): ?>
        <tr><td colspan="5" style="text-align: center; color: #6E6259; padding: 2.5rem;">No customer accounts registered yet.</td></tr>
      <?php else: ?>
        <?php foreach ($admin_users as $u): ?>
          <tr>
            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['orders'] ?> orders</td>
            <td><?= status_pill($u['status']) ?></td>
            <td><?= $u['joined'] ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
