<?php
$pageTitle = 'My Orders — QuickBite';
$activeNav = '';
$basePath = '';
require_once __DIR__ . '/includes/auth.php';
require_login('login.php');
include __DIR__ . '/includes/data.php';

$currentUser = current_user();
$myOrders = [];
$orderItemsMap = [];

try {
    $db = get_db();
    $stmt = $db->prepare("
        SELECT o.id, o.vendor_id, v.name as vendor_name, o.customer_name, 
               o.address, o.phone, o.subtotal, o.discount, o.delivery_fee, o.total,
               o.status, DATE_FORMAT(o.created_at, '%d %b %Y, %h:%i %p') as order_time
        FROM orders o
        JOIN vendors v ON o.vendor_id = v.id
        WHERE o.user_id = ?
        ORDER BY o.id DESC
    ");
    $stmt->execute([$currentUser['id']]);
    $myOrders = $stmt->fetchAll();

    if (!empty($myOrders)) {
        $orderIds = array_column($myOrders, 'id');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $iStmt = $db->prepare("SELECT order_id, item_name, unit_price, quantity, line_total FROM order_items WHERE order_id IN ({$placeholders})");
        $iStmt->execute($orderIds);
        $items = $iStmt->fetchAll();
        foreach ($items as $item) {
            $orderItemsMap[$item['order_id']][] = $item;
        }
    }
} catch (Exception $e) {
    // Session fallback for demo orders when DB offline
    $myOrders = $_SESSION['recent_orders'] ?? [
        [
            'id' => 101,
            'vendor_id' => 1,
            'vendor_name' => 'Dalle',
            'customer_name' => $currentUser['name'] ?? 'Customer',
            'address' => 'Thamel, Kathmandu',
            'phone' => '9800000000',
            'subtotal' => 500,
            'discount' => 50,
            'delivery_fee' => 40,
            'total' => 490,
            'status' => 'preparing',
            'order_time' => 'Today, Just Now'
        ]
    ];
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <p class="breadcrumb"><a href="index.php" style="color:inherit; text-decoration:none;">Home</a> <span>/</span> My Orders</p>
  <h1 class="page-header__title">My Orders</h1>
</div>

<section style="max-width: 900px; margin: 0 auto; padding: 1rem 0;">
  <?php if (empty($myOrders)): ?>
    <div style="text-align: center; padding: 4rem 1rem; background: var(--color-surface); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
      <p style="font-size: 1.25rem; color: var(--color-ink-soft); margin-bottom: 1.25rem;">You haven't placed any orders yet.</p>
      <a href="vendors.php" class="btn btn--primary">Browse Restaurants</a>
    </div>
  <?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
      <?php foreach ($myOrders as $o): ?>
        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
            <div>
              <span style="font-weight: 700; color: var(--color-brand); font-size: 1.05rem;">Order #ORD-<?= $o['id'] ?></span>
              <span style="color: #8C7B70; font-size: 0.82rem; margin-left: 0.5rem;"><?= $o['order_time'] ?></span>
              <div style="font-weight: 600; color: var(--color-ink); margin-top: 0.25rem;">From: <?= htmlspecialchars($o['vendor_name']) ?></div>
            </div>
            <div>
              <?= status_pill($o['status']) ?>
            </div>
          </div>

          <div style="margin-bottom: 0.75rem;">
            <strong style="font-size: 0.85rem; color: var(--color-ink-soft);">Ordered Items:</strong>
            <ul style="list-style: none; padding-left: 0; margin-top: 0.35rem; font-size: 0.9rem;">
              <?php foreach ($orderItemsMap[$o['id']] ?? [] as $line): ?>
                <li style="display: flex; justify-content: space-between; padding: 0.2rem 0; color: var(--color-ink);">
                  <span><?= htmlspecialchars($line['item_name']) ?> &times; <?= $line['quantity'] ?></span>
                  <span>Rs. <?= number_format($line['line_total']) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed var(--color-border); padding-top: 0.75rem; font-size: 0.88rem;">
            <div style="color: #6E6259;">
              Delivering to: <strong style="color: var(--color-ink);"><?= htmlspecialchars($o['address']) ?></strong>
            </div>
            <div style="font-size: 1.05rem; font-weight: 700; color: var(--color-brand);">
              Total: Rs. <?= number_format($o['total']) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
