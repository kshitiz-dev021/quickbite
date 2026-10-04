<?php
$pageTitle = 'Pending Approvals — QuickBite Admin';
$activeTab = 'approvals';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Pending Vendor Approvals</h1>
  <p class="dash-panel__subtitle">Review and approve new vendor registrations.</p>

  <table class="data-table">
    <thead><tr><th>Vendor</th><th>Email</th><th>Shop Name</th><th>Joined On</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($admin_approvals as $a): ?>
        <tr>
          <td><?= htmlspecialchars($a['vendor']) ?></td>
          <td><?= htmlspecialchars($a['email']) ?></td>
          <td><?= htmlspecialchars($a['shop']) ?></td>
          <td><?= $a['joined'] ?></td>
          <td data-status-cell>
            <button class="table-action-btn table-action-btn--approve">Approve</button>
            <button class="table-action-btn table-action-btn--reject">Reject</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
