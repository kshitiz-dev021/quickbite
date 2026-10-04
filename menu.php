<?php
$activeNav = 'menu';
$demoActive = 'customer';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';

$vendorId = $_GET['vendor'] ?? 'cafe-abc';
$vendor = null;
foreach ($vendors as $v) {
    if ($v['id'] === $vendorId) { $vendor = $v; break; }
}
if (!$vendor) { $vendor = $vendors[0]; }

$items = array_values(array_filter($menu_items, fn($i) => $i['vendor'] === $vendor['id']));
$categories = array_values(array_unique(array_column($items, 'cat')));

$pageTitle = $vendor['name'] . ' — QuickBite';
include __DIR__ . '/includes/header.php';
?>

<div class="vendor-banner">
  <div class="vendor-banner__thumb"><img class="thumb-img" src="<?= $vendor['img'] ?>" alt="<?= htmlspecialchars($vendor['name']) ?>"></div>
  <div class="vendor-banner__info">
    <h1 class="vendor-banner__name"><?= htmlspecialchars($vendor['name']) ?></h1>
    <p class="vendor-banner__meta"><?= htmlspecialchars($vendor['cuisine']) ?></p>
    <p class="vendor-banner__stats">★ <?= number_format($vendor['rating'], 1) ?> &nbsp;·&nbsp; <?= $vendor['time'] ?></p>
    <p class="vendor-banner__note">Minimum order: Rs. <?= $vendor['min_order'] ?></p>
  </div>
</div>

<div class="menu-tabs" id="menuTabs">
  <button class="menu-tab is-active" data-cat="All">All Items</button>
  <?php foreach ($categories as $cat): ?>
    <button class="menu-tab" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
  <?php endforeach; ?>
</div>

<ul class="menu-list" id="menuList">
  <?php foreach ($items as $item): ?>
    <li class="menu-item" data-cat="<?= htmlspecialchars($item['cat']) ?>">
      <div class="menu-item__thumb"><img class="thumb-img" src="<?= $item['img'] ?>" alt="<?= htmlspecialchars($item['name']) ?>"></div>
      <div>
        <p class="menu-item__name"><?= htmlspecialchars($item['name']) ?></p>
        <p class="menu-item__desc"><?= htmlspecialchars($item['desc']) ?></p>
      </div>
      <div class="menu-item__price-block">
        <p class="menu-item__price">
          <?php if ($item['was']): ?><span class="menu-item__price--was">Rs. <?= $item['was'] ?></span><?php endif; ?>
          Rs. <?= $item['price'] ?>
          <?php if ($item['discount']): ?><span class="menu-item__badge"><?= $item['discount'] ?>% OFF</span><?php endif; ?>
        </p>
        <button class="menu-item__add" data-add-item="<?= $item['id'] ?>">Add</button>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php include __DIR__ . '/includes/footer.php'; ?>
