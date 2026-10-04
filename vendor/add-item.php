<?php
$pageTitle = 'Add Food Item — QuickBite Vendor';
$activeTab = 'add';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/vendor-header.php';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Add New Food Item</h1>

  <!-- action/method point at a backend endpoint that doesn't exist yet —
       swap this in once the item-create route is built. -->
  <form class="item-form" method="post" action="add-item.php">
    <div class="item-form__grid">
      <div class="item-form__fields">
        <label class="field"><span class="field__label">Item Name</span><input class="field__input" name="name" placeholder="Enter item name"></label>
        <label class="field"><span class="field__label">Description</span><input class="field__input" name="description" placeholder="Enter description"></label>
        <label class="field">
          <span class="field__label">Category</span>
          <select class="field__input" name="category">
            <option value="">Select category</option>
            <option>Burgers</option>
            <option>Pasta</option>
            <option>Drinks</option>
            <option>Desserts</option>
          </select>
        </label>
        <div class="field-pair">
          <label class="field"><span class="field__label">Original Price (Rs.)</span><input class="field__input" name="price" placeholder="Enter price"></label>
          <label class="field"><span class="field__label">Discount (%)</span><input class="field__input" name="discount" placeholder="Enter discount"></label>
        </div>
      </div>
      <label class="upload-box">
        <span class="upload-box__icon">🖼️</span>
        <span class="upload-box__text">Click to upload image<br><small>or drag and drop</small></span>
        <small class="upload-box__hint">PNG, JPG up to 2MB</small>
        <input type="file" name="image" accept="image/png, image/jpeg" hidden>
      </label>
    </div>
    <div class="item-form__actions">
      <a href="menu-items.php" class="btn btn--outline">Cancel</a>
      <button type="button" class="btn btn--primary" id="saveItemBtn">Save Item</button>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../includes/vendor-footer.php'; ?>
