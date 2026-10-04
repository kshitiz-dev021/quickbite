<?php
$pageTitle = 'Discounts — QuickBite Vendor';
$activeTab = 'discounts';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$db = get_db();

// Handle setting/updating discount
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_discount'])) {
    $itemId = (int)$_POST['item_id'];
    $discountPct = max(0, min(90, (float)$_POST['discount_percent']));

    // Get current item
    $iStmt = $db->prepare("SELECT price, original_price FROM menu_items WHERE id = ? AND vendor_id = ?");
    $iStmt->execute([$itemId, $currentVendorId]);
    $item = $iStmt->fetch();

    if ($item) {
        $basePrice = $item['original_price'] ?: $item['price'];
        if ($discountPct > 0) {
            $newPrice = round($basePrice * (1 - ($discountPct / 100)));
            $upd = $db->prepare("UPDATE menu_items SET original_price = ?, price = ? WHERE id = ? AND vendor_id = ?");
            $upd->execute([$basePrice, $newPrice, $itemId, $currentVendorId]);
        } else {
            // Remove discount
            $upd = $db->prepare("UPDATE menu_items SET price = ?, original_price = NULL WHERE id = ? AND vendor_id = ?");
            $upd->execute([$basePrice, $itemId, $currentVendorId]);
        }
        set_flash('success', 'Discount updated successfully.');
        header('Location: discounts.php');
        exit;
    }
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Manage Discounts</h1>
  <p class="dash-panel__subtitle">Create and manage discounts for your items.</p>

  <?php if (empty($vendor_discounts)): ?>
    <div style="padding: 2rem; text-align: center; color: #6E6259;">
      No active discounts on your items right now. You can set discounts from the <a href="menu-items.php" style="color: var(--color-brand); font-weight: 600;">Menu Items</a> page or when adding a dish.
    </div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Item Name</th><th>Original Price</th><th>Discount (%)</th><th>Net Price</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($vendor_discounts as $d): ?>
          <tr>
            <td><strong><?= htmlspecialchars($d['name']) ?></strong></td>
            <td>Rs. <?= number_format($d['original']) ?></td>
            <td><?= $d['pct'] ?></td>
            <td>Rs. <?= number_format($d['net']) ?></td>
            <td>
              <form method="post" action="discounts.php" style="display: flex; gap: 0.4rem; align-items: center;">
                <input type="hidden" name="update_discount" value="1">
                <input type="hidden" name="item_id" value="<?= $d['id'] ?>">
                <input type="number" min="0" max="90" name="discount_percent" value="<?= (int)$d['pct'] ?>" class="field__input" style="width: 70px; padding: 0.25rem 0.4rem; font-size: 0.8rem;">
                <button type="submit" class="btn btn--outline btn--sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
