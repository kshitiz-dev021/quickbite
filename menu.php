<?php
$activeNav = 'menu';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';

$vendorId = $_GET['vendor'] ?? null;
$vendor = null;

if ($vendorId !== null) {
    foreach ($vendors as $v) {
        if ((string)$v['id'] === (string)$vendorId || strcasecmp($v['name'], $vendorId) === 0) {
            $vendor = $v;
            break;
        }
    }
}

if (!$vendor && !empty($vendors)) {
    $vendor = $vendors[0];
}

$items = array_values(array_filter($menu_items, fn($i) => (string)$i['vendor'] === (string)$vendor['id']));
$categories = array_values(array_unique(array_column($items, 'cat')));

$pageTitle = $vendor['name'] . ' — QuickBite';
include __DIR__ . '/includes/header.php';
?>

<div class="vendor-banner">
  <div class="vendor-banner__thumb">
    <img class="thumb-img" src="<?= $vendor['img'] ?>" alt="<?= htmlspecialchars($vendor['name']) ?>">
  </div>
  <div class="vendor-banner__info" style="flex: 1;">
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
      <h1 class="vendor-banner__name" style="margin-bottom: 0;"><?= htmlspecialchars($vendor['name']) ?></h1>
      <span class="status-pill status-pill--approved" style="font-size: 0.7rem;">Verified Partner</span>
    </div>
    <p class="vendor-banner__meta"><?= htmlspecialchars($vendor['cuisine']) ?></p>
    <p class="vendor-banner__stats">★ <?= number_format($vendor['rating'], 1) ?> (800+ reviews) &nbsp;·&nbsp; 🕒 <?= htmlspecialchars($vendor['time']) ?></p>
    <p class="vendor-banner__note">Minimum order: Rs. <?= number_format($vendor['min_order']) ?> &nbsp;·&nbsp; Standard delivery: Rs. <?= (int)get_setting('delivery_fee', 40) ?></p>
  </div>
</div>

<!-- In-Menu Dish Search (Pathao style) -->
<div class="menu-search-wrap" style="margin-top: 1.25rem;">
  <input type="text" class="menu-search-input" id="menuItemSearch" placeholder="Search dishes in <?= htmlspecialchars($vendor['name']) ?> (e.g. momo, burger, pizza, fries)…">
</div>

<!-- Category Tabs -->
<div class="menu-tabs" id="menuTabs">
  <button class="menu-tab is-active" data-cat="All">All Items (<?= count($items) ?>)</button>
  <?php foreach ($categories as $cat): 
      $catCount = count(array_filter($items, fn($i) => $i['cat'] === $cat));
  ?>
    <button class="menu-tab" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?> (<?= $catCount ?>)</button>
  <?php endforeach; ?>
</div>

<ul class="menu-list" id="menuList">
  <?php if (empty($items)): ?>
    <li style="padding: 3rem; text-align: center; color: #6E6259; grid-column: 1/-1;">
      This restaurant has no active menu items at the moment.
    </li>
  <?php else: ?>
    <?php foreach ($items as $item): ?>
      <li class="menu-item" data-cat="<?= htmlspecialchars($item['cat']) ?>" data-name="<?= htmlspecialchars(strtolower($item['name'])) ?>" data-desc="<?= htmlspecialchars(strtolower($item['desc'])) ?>">
        <div class="menu-item__thumb">
          <img class="thumb-img" src="<?= $item['img'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
        </div>
        <div style="flex: 1;">
          <p class="menu-item__name"><?= htmlspecialchars($item['name']) ?></p>
          <p class="menu-item__desc"><?= htmlspecialchars($item['desc']) ?></p>
        </div>
        <div class="menu-item__price-block">
          <p class="menu-item__price">
            <?php if (!empty($item['was']) && (float)$item['was'] > (float)$item['price']): ?>
              <span class="menu-item__price--was">Rs. <?= number_format($item['was']) ?></span>
            <?php endif; ?>
            Rs. <?= number_format($item['price']) ?>
            <?php if (!empty($item['discount'])): ?>
              <span class="menu-item__badge"><?= $item['discount'] ?>% OFF</span>
            <?php endif; ?>
          </p>
          <button class="menu-item__add" data-add-item="<?= $item['id'] ?>">+ Add</button>
        </div>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>

<!-- Floating Cart Indicator (Pathao-style) -->
<a href="cart.php" class="floating-cart-bar" id="floatingCartBar">
  <span>🛒 View Cart</span>
  <span class="floating-cart-bar__badge" id="floatingCartCount">0 items</span>
  <span class="floating-cart-bar__cta">Checkout &rarr;</span>
</a>

<script>window.PLATFORM_DELIVERY_FEE = <?= (int)get_setting('delivery_fee', 40) ?>;</script>
<script type="application/json" id="menuItemsData"><?= json_encode($menu_items) ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
