<?php
$pageTitle = 'Registered Vendors — QuickBite Admin';
$activeTab = 'vendors';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Registered Vendors</h1>
  <p class="dash-panel__subtitle">Manage and approve vendor accounts.</p>

  <div class="listing-toolbar">
    <input type="text" class="listing-toolbar__search" placeholder="Search vendors…">
    <select class="listing-toolbar__filter">
      <option>All Status</option>
      <option>Approved</option>
      <option>Pending</option>
    </select>
  </div>

  <table class="data-table">
    <thead><tr><th>Vendor</th><th>Email</th><th>Status</th><th>Joined On</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($admin_vendors as $v): ?>
        <tr>
          <td><?= htmlspecialchars($v['name']) ?></td>
          <td><?= htmlspecialchars($v['email']) ?></td>
          <td><?= status_pill($v['status']) ?></td>
          <td><?= $v['joined'] ?></td>
          <td><button class="table-action-btn">View</button></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
