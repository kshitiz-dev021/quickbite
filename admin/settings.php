<?php
$pageTitle = 'Settings — QuickBite Admin';
$activeTab = 'settings';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Platform Settings</h1>
  <p class="dash-panel__subtitle">Delivery fees, commission rates, and general config.</p>

  <form class="item-form">
    <label class="field"><span class="field__label">Platform Commission (%)</span><input class="field__input" value="10"></label>
    <label class="field"><span class="field__label">Default Delivery Fee (Rs.)</span><input class="field__input" value="40"></label>
    <div class="item-form__actions">
      <button type="button" class="btn btn--primary">Save Changes</button>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
