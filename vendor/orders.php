<?php
$pageTitle = 'Orders — QuickBite Vendor';
$activeTab = 'orders';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$db = get_db();

// Handle status change
if (isset($_POST['update_status']) && !empty($_POST['order_id']) && !empty($_POST['new_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];
    if (in_array($newStatus, ['pending', 'preparing', 'completed', 'cancelled'])) {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ? AND vendor_id = ?");
        $stmt->execute([$newStatus, $orderId, $currentVendorId]);
        set_flash('success', "Order #ORD-{$orderId} status updated to " . ucfirst($newStatus) . ".");
        header('Location: orders.php');
        exit;
    }
}

// Fetch order items breakdown for all orders
$orderItemsMap = [];
if (!empty($vendor_orders)) {
    $orderIds = array_column($vendor_orders, 'id');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $iStmt = $db->prepare("SELECT order_id, item_name, unit_price, quantity, line_total FROM order_items WHERE order_id IN ({$placeholders})");
    $iStmt->execute($orderIds);
    $items = $iStmt->fetchAll();
    foreach ($items as $item) {
        $orderItemsMap[$item['order_id']][] = $item;
    }
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Orders</h1>
  <p class="dash-panel__subtitle">Track and manage customer orders for your restaurant.</p>

  <div class="chip-row">
    <button class="chip is-active" data-filter="all">All (<?= count($vendor_orders) ?>)</button>
    <button class="chip" data-filter="pending">Pending</button>
    <button class="chip" data-filter="preparing">Preparing</button>
    <button class="chip" data-filter="completed">Completed</button>
    <button class="chip" data-filter="cancelled">Cancelled</button>
  </div>

  <?php if (empty($vendor_orders)): ?>
    <div style="padding: 2.5rem; text-align: center; color: #6E6259;">
      No orders found. When customers place orders from your menu, they will appear here in real-time.
    </div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Order ID</th><th>Customer &amp; Address</th><th>Items</th><th>Amount</th><th>Status</th><th>Update Status</th></tr></thead>
      <tbody>
        <?php foreach ($vendor_orders as $o): ?>
          <tr data-status="<?= $o['status'] ?>">
            <td><strong>#ORD-<?= $o['id'] ?></strong></td>
            <td>
              <strong><?= htmlspecialchars($o['customer']) ?></strong>
              <div style="font-size: 0.78rem; color: #6E6259;">📞 <?= htmlspecialchars($o['phone'] ?? '') ?></div>
              <div style="font-size: 0.78rem; color: #8C7B70;">📍 <?= htmlspecialchars($o['address'] ?? '') ?></div>
            </td>
            <td>
              <strong><?= $o['items'] ?> items</strong>
              <ul style="margin: 0.25rem 0 0; padding-left: 1rem; font-size: 0.75rem; color: #6E6259;">
                <?php foreach ($orderItemsMap[$o['id']] ?? [] as $line): ?>
                  <li><?= htmlspecialchars($line['item_name']) ?> &times; <?= $line['quantity'] ?></li>
                <?php endforeach; ?>
              </ul>
            </td>
            <td>Rs. <?= number_format($o['amount']) ?></td>
            <td data-status-cell><?= status_pill($o['status']) ?></td>
            <td>
              <form method="post" action="orders.php" style="display: flex; gap: 0.4rem; align-items: center;">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="new_status" class="field__input" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; width: auto;">
                  <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="preparing" <?= $o['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                  <option value="completed" <?= $o['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                  <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn--outline btn--sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
