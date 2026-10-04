<?php
$pageTitle = 'Settings — QuickBite Vendor';
$activeTab = 'settings';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Settings</h1>
  <p class="dash-panel__subtitle">Shop profile and account preferences.</p>

  <form class="item-form">
    <label class="field"><span class="field__label">Shop Name</span><input class="field__input" value="Café ABC"></label>
    <label class="field"><span class="field__label">Contact Email</span><input class="field__input" value="cafeabc@mail.com"></label>
    <label class="field"><span class="field__label">Opening Hours</span><input class="field__input" value="9:00 AM – 9:00 PM"></label>
    <div class="item-form__actions">
      <button type="button" class="btn btn--primary">Save Changes</button>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
