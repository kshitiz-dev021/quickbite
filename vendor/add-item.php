<?php
$pageTitle = 'Add Food Item — QuickBite Vendor';
$activeTab = 'add';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';

$error = '';
$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $rawPrice    = (float)($_POST['price'] ?? 0);
    $discountPct = (float)($_POST['discount'] ?? 0);

    if (empty($name)) {
        $error = 'Please enter an item name.';
    } elseif ($rawPrice <= 0) {
        $error = 'Please enter a valid price.';
    } else {
        // Calculate selling price and original price
        if ($discountPct > 0 && $discountPct < 100) {
            $originalPrice = $rawPrice;
            $price = round($originalPrice * (1 - ($discountPct / 100)));
        } else {
            $price = $rawPrice;
            $originalPrice = null;
        }

        // Handle Image Upload
        $imagePath = './images/pizza.png'; // default fallback
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../images/uploads/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                $filename = 'dish_' . time() . '_' . random_int(100, 999) . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = './images/uploads/' . $filename;
                }
            }
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO menu_items (vendor_id, category_id, name, description, price, original_price, image, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $currentVendorId,
                $categoryId,
                $name,
                $description,
                $price,
                $originalPrice,
                $imagePath
            ]);

            set_flash('success', "Food item '{$name}' added to your menu!");
            header('Location: menu-items.php');
            exit;
        } catch (Exception $e) {
            $error = 'Failed to save menu item: ' . $e->getMessage();
        }
    }
}
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Add New Food Item</h1>
  <p class="dash-panel__subtitle">Create a new dish for your shop menu.</p>

  <?php if (!empty($error)): ?>
    <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form class="item-form" method="post" action="add-item.php" enctype="multipart/form-data">
    <div class="item-form__grid">
      <div class="item-form__fields">
        <label class="field">
          <span class="field__label">Item Name *</span>
          <input class="field__input" name="name" placeholder="e.g. Crispy Chicken Burger" required autofocus>
        </label>
        
        <label class="field">
          <span class="field__label">Description</span>
          <input class="field__input" name="description" placeholder="Short description of ingredients, flavor, or portion">
        </label>
        
        <label class="field">
          <span class="field__label">Category</span>
          <select class="field__input" name="category_id">
            <option value="">Select category</option>
            <?php foreach ($menu_categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        
        <div class="field-pair">
          <label class="field">
            <span class="field__label">Regular Price (Rs.) *</span>
            <input class="field__input" type="number" step="1" name="price" placeholder="e.g. 250" required>
          </label>
          <label class="field">
            <span class="field__label">Discount (%) (Optional)</span>
            <input class="field__input" type="number" min="0" max="90" name="discount" placeholder="e.g. 10">
          </label>
        </div>
      </div>

      <label class="upload-box">
        <span class="upload-box__icon">🖼️</span>
        <span class="upload-box__text">Click to upload image<br><small>or drag and drop</small></span>
        <small class="upload-box__hint">PNG, JPG up to 2MB</small>
        <input type="file" name="image" accept="image/png, image/jpeg, image/webp" hidden>
      </label>
    </div>

    <div class="item-form__actions">
      <a href="menu-items.php" class="btn btn--outline">Cancel</a>
      <button type="submit" class="btn btn--primary" id="saveItemBtn">Save Food Item</button>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
