<?php
$pageTitle = 'Reports — QuickBite Admin';
$activeTab = 'reports';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Reports</h1>
  <p class="dash-panel__subtitle">View detailed platform reports.</p>

  <div class="stat-row stat-row--compact">
    <div class="stat-card"><span class="stat-card__value">Rs. 78,450</span><span class="stat-card__label">Total Revenue <em>+12.5%</em></span></div>
    <div class="stat-card"><span class="stat-card__value">356</span><span class="stat-card__label">Total Orders <em>+8.2%</em></span></div>
    <div class="stat-card"><span class="stat-card__value">35</span><span class="stat-card__label">New Users <em>+15.8%</em></span></div>
  </div>

  <div class="panel-block">
    <div class="panel-block__header"><h2>Revenue Over Time</h2></div>
    <svg class="report-chart" viewBox="0 0 600 160" preserveAspectRatio="none">
      <polyline fill="none" stroke="#A8432B" stroke-width="2.5"
        points="0,120 75,100 150,60 225,90 300,40 375,70 450,50 525,65 600,30" />
    </svg>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
