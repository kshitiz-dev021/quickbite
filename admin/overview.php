<?php
$pageTitle = 'System Overview — QuickBite Admin';
$activeTab = 'overview';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">System Overview</h1>
  <p class="dash-panel__subtitle">Monitor your platform's performance.</p>

  <div class="stat-row">
    <div class="stat-card"><span class="stat-card__value">24</span><span class="stat-card__label">Total Vendors</span></div>
    <div class="stat-card"><span class="stat-card__value">120</span><span class="stat-card__label">Total Users</span></div>
    <div class="stat-card"><span class="stat-card__value">356</span><span class="stat-card__label">Total Orders</span></div>
    <div class="stat-card"><span class="stat-card__value">Rs. 78,450</span><span class="stat-card__label">Total Revenue</span></div>
  </div>

  <div class="panel-block">
    <div class="panel-block__header"><h2>Recent Activities</h2></div>
    <ul class="activity-list">
      <?php foreach ($admin_activity as $a): ?>
        <li><?= htmlspecialchars($a['text']) ?><time><?= $a['time'] ?></time></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
