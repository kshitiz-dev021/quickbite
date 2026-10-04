<?php
$pageTitle = 'Registered Vendors — QuickBite Admin';
$activeTab = 'vendors';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';

try {
    $db = get_db();
    // Handle status change
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $vId = (int)$_GET['id'];
        $act = $_GET['action'];
        if (in_array($act, ['approve', 'block', 'activate'])) {
            $newStatus = $act === 'block' ? 'blocked' : 'approved';
            $stmt = $db->prepare("UPDATE vendors SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $vId]);
            set_flash('success', "Vendor account status updated to {$newStatus}.");
            header('Location: vendors.php');
            exit;
        }
    }
} catch (Exception $e) {
    // Database connection fallback
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Registered Vendors</h1>
  <p class="dash-panel__subtitle">Manage restaurant accounts, approvals, and platform status.</p>

  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>Vendor / Shop</th><th>Owner Email</th><th>Cuisine</th><th>Status</th><th>Joined On</th><th>Action</th></tr></thead>
      <tbody>
        <?php if (empty($admin_vendors)): ?>
          <tr><td colspan="6" style="text-align: center; color: #6E6259; padding: 2rem;">No vendors registered yet.</td></tr>
        <?php else: ?>
          <?php foreach ($admin_vendors as $v): ?>
            <tr>
              <td><strong><?= htmlspecialchars($v['name']) ?></strong></td>
              <td><?= htmlspecialchars($v['email']) ?></td>
              <td><?= htmlspecialchars($v['cuisine'] ?? '—') ?></td>
              <td><?= status_pill($v['status']) ?></td>
              <td><?= $v['joined'] ?></td>
              <td>
                <?php if ($v['status'] === 'pending'): ?>
                  <a href="approvals.php" class="table-action-btn table-action-btn--approve">Review</a>
                <?php elseif ($v['status'] === 'approved'): ?>
                  <a href="vendors.php?action=block&id=<?= $v['id'] ?>" class="table-action-btn table-action-btn--reject" onclick="return confirm('Suspend this vendor account?');">Block</a>
                <?php elseif ($v['status'] === 'blocked'): ?>
                  <a href="vendors.php?action=activate&id=<?= $v['id'] ?>" class="table-action-btn table-action-btn--approve">Unblock</a>
                <?php else: ?>
                  <a href="vendors.php?action=approve&id=<?= $v['id'] ?>" class="table-action-btn table-action-btn--approve">Approve</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
