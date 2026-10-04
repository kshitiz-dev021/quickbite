<?php
$pageTitle = 'Pending Approvals — QuickBite Admin';
$activeTab = 'approvals';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';

$db = get_db();

// Handle Approve / Reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $vendorId = (int)$_GET['id'];
    $act = $_GET['action'];

    if ($act === 'approve') {
        $stmt = $db->prepare("UPDATE vendors SET status = 'approved' WHERE id = ?");
        $stmt->execute([$vendorId]);
        set_flash('success', 'Vendor has been approved and is now live on the storefront!');
        header('Location: approvals.php');
        exit;
    } elseif ($act === 'reject') {
        $stmt = $db->prepare("UPDATE vendors SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$vendorId]);
        set_flash('info', 'Vendor registration has been rejected.');
        header('Location: approvals.php');
        exit;
    }
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Pending Vendor Approvals</h1>
  <p class="dash-panel__subtitle">Review and approve new restaurant registrations before they appear to customers.</p>

  <table class="data-table">
    <thead><tr><th>Owner Name</th><th>Email</th><th>Shop Name</th><th>Cuisine</th><th>Applied On</th><th>Action</th></tr></thead>
    <tbody>
      <?php if (empty($admin_approvals)): ?>
        <tr><td colspan="6" style="text-align: center; color: #6E6259; padding: 2.5rem;">🎉 No pending approvals. All vendors have been reviewed!</td></tr>
      <?php else: ?>
        <?php foreach ($admin_approvals as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['vendor']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><strong><?= htmlspecialchars($a['shop']) ?></strong></td>
            <td><?= htmlspecialchars($a['cuisine'] ?? '—') ?></td>
            <td><?= $a['joined'] ?></td>
            <td data-status-cell>
              <a href="approvals.php?action=approve&id=<?= $a['id'] ?>" class="table-action-btn table-action-btn--approve">Approve</a>
              <a href="approvals.php?action=reject&id=<?= $a['id'] ?>" class="table-action-btn table-action-btn--reject" onclick="return confirm('Reject this vendor registration?');">Reject</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
