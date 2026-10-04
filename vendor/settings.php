<?php
$pageTitle = 'Settings — QuickBite Vendor';
$activeTab = 'settings';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$db = get_db();

// Fetch vendor profile
$vStmt = $db->prepare("SELECT * FROM vendors WHERE id = ?");
$vStmt->execute([$currentVendorId]);
$vProfile = $vStmt->fetch();

if (!$vProfile) {
    $vProfile = [
        'name'          => 'Café ABC',
        'cuisine'       => 'American, Beverages',
        'description'   => '',
        'delivery_time' => '30-40 mins',
        'min_order'     => 200,
    ];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shopName     = trim($_POST['name'] ?? '');
    $cuisine      = trim($_POST['cuisine'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $deliveryTime = trim($_POST['delivery_time'] ?? '30-40 mins');
    $minOrder     = (float)($_POST['min_order'] ?? 0);

    if (empty($shopName) || empty($cuisine)) {
        $error = 'Restaurant name and cuisine are required.';
    } else {
        $upd = $db->prepare("
            UPDATE vendors 
            SET name = ?, cuisine = ?, description = ?, delivery_time = ?, min_order = ?
            WHERE id = ?
        ");
        $upd->execute([$shopName, $cuisine, $description, $deliveryTime, $minOrder, $currentVendorId]);

        $_SESSION['user']['vendor_name'] = $shopName;
        set_flash('success', 'Shop settings updated successfully.');
        header('Location: settings.php');
        exit;
    }
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Settings</h1>
  <p class="dash-panel__subtitle">Shop profile and delivery preferences.</p>

  <?php if (!empty($error)): ?>
    <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form class="item-form" method="post" action="settings.php">
    <label class="field">
      <span class="field__label">Shop / Restaurant Name</span>
      <input class="field__input" name="name" value="<?= htmlspecialchars($vProfile['name']) ?>" required>
    </label>

    <label class="field">
      <span class="field__label">Cuisine Specialties</span>
      <input class="field__input" name="cuisine" value="<?= htmlspecialchars($vProfile['cuisine']) ?>" required>
    </label>

    <label class="field">
      <span class="field__label">Shop Description</span>
      <input class="field__input" name="description" value="<?= htmlspecialchars($vProfile['description'] ?? '') ?>" placeholder="Brief description of your shop">
    </label>

    <div class="field-pair">
      <label class="field">
        <span class="field__label">Estimated Delivery Time</span>
        <input class="field__input" name="delivery_time" value="<?= htmlspecialchars($vProfile['delivery_time'] ?? '30-40 mins') ?>" required>
      </label>

      <label class="field">
        <span class="field__label">Minimum Order (Rs.)</span>
        <input class="field__input" type="number" name="min_order" value="<?= (int)($vProfile['min_order'] ?? 0) ?>" required>
      </label>
    </div>

    <div class="item-form__actions">
      <button type="submit" class="btn btn--primary">Save Changes</button>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
