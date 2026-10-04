<?php
$pageTitle = 'Users — QuickBite Admin';
$activeTab = 'users';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Users</h1>
  <p class="dash-panel__subtitle">All registered customers on the platform.</p>

  <table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Joined</th></tr></thead>
    <tbody>
      <?php foreach ($admin_users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['orders'] ?></td>
          <td><?= $u['joined'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
