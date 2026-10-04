<?php
$pageTitle = 'System Overview — QuickBite Admin';
$activeTab = 'overview';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';

$totalVendors = count($vendors);
$totalUsers   = count($admin_users);
$totalOrders  = 12;
$totalRev     = 18400;

try {
    $db = get_db();
    $totalVendors = (int)$db->query("SELECT COUNT(*) FROM vendors WHERE status = 'approved'")->fetchColumn();
    $totalUsers   = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    $totalOrders  = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRev     = (float)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = 'completed'")->fetchColumn();
    if ($totalRev == 0) {
        $totalRev = (float)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders")->fetchColumn();
    }
} catch (Exception $e) {
    // Database connection fallback
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">System Overview</h1>
  <p class="dash-panel__subtitle">Monitor your platform's live performance and operations.</p>

  <div class="stat-row">
    <div class="stat-card"><span class="stat-card__value"><?= $totalVendors ?></span><span class="stat-card__label">Active Vendors</span></div>
    <div class="stat-card"><span class="stat-card__value"><?= $totalUsers ?></span><span class="stat-card__label">Registered Customers</span></div>
    <div class="stat-card"><span class="stat-card__value"><?= $totalOrders ?></span><span class="stat-card__label">Total Orders</span></div>
    <div class="stat-card"><span class="stat-card__value">Rs. <?= number_format($totalRev) ?></span><span class="stat-card__label">Total Revenue</span></div>
  </div>

  <div class="panel-block">
    <div class="panel-block__header"><h2>Recent Platform Activities</h2></div>
    <ul class="activity-list">
      <?php foreach ($admin_activity as $a): ?>
        <li><?= htmlspecialchars($a['text']) ?><time><?= htmlspecialchars($a['time']) ?></time></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
