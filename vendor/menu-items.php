<?php
$pageTitle = 'Menu Items — QuickBite Vendor';
$activeTab = 'items';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$db = get_db();

// Handle status toggle action
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && !empty($_GET['id'])) {
    $itemId = (int)$_GET['id'];
    $stmt = $db->prepare("UPDATE menu_items SET is_active = IF(is_active = 1, 0, 1) WHERE id = ? AND vendor_id = ?");
    $stmt->execute([$itemId, $currentVendorId]);
    set_flash('success', 'Menu item status updated.');
    header('Location: menu-items.php');
    exit;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $itemId = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ? AND vendor_id = ?");
    $stmt->execute([$itemId, $currentVendorId]);
    set_flash('success', 'Menu item removed successfully.');
    header('Location: menu-items.php');
    exit;
}

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

  <?php if (empty($vendor_menu_items)): ?>
    <div style="padding: 2.5rem; text-align: center; color: #6E6259;">
      No menu items found. <a href="add-item.php" style="color: var(--color-brand); font-weight: 600;">Add your first dish</a>!
    </div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Item</th><th>Price</th><th>Discount</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($vendor_menu_items as $i): ?>
          <tr data-status="<?= $i['status'] ?>">
            <td>
              <strong><?= htmlspecialchars($i['name']) ?></strong>
              <?php if (!empty($i['category_name'])): ?>
                <span style="display:block; font-size: 0.75rem; color: #8C7B70;"><?= htmlspecialchars($i['category_name']) ?></span>
              <?php endif; ?>
            </td>
            <td>Rs. <?= number_format($i['price']) ?></td>
            <td><?= $i['discount'] ?></td>
            <td data-status-cell><?= status_pill($i['status']) ?></td>
            <td>
              <a href="menu-items.php?action=toggle&id=<?= $i['id'] ?>" class="table-action-btn" title="Toggle active status">
                <?= $i['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
              </a>
              <a href="menu-items.php?action=delete&id=<?= $i['id'] ?>" class="table-action-btn table-action-btn--reject" onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($i['name'])) ?>?');">
                Delete
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
