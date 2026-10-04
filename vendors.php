<?php
$pageTitle = 'All Restaurants — QuickBite';
$activeNav = 'vendors';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';

$selectedCuisine = $_GET['cuisine'] ?? 'all';
?>

<div class="page-header">
  <p class="breadcrumb"><a href="index.php" style="color:inherit; text-decoration:none;">Home</a> <span>/</span> Restaurants</p>
  <h1 class="page-header__title">All Restaurants <span class="page-header__count">(<?= count($vendors) ?> places)</span></h1>
</div>

<div class="listing-toolbar">
  <input type="text" class="listing-toolbar__search" id="vendorSearch" placeholder="Search by restaurant name, cuisine, or location (e.g. Thamel, Momos, Pizza)…" autofocus>
  <select class="listing-toolbar__filter" id="vendorFilter">
    <option value="all" <?= $selectedCuisine === 'all' ? 'selected' : '' ?>>All Cuisines</option>
    <?php foreach ($menu_categories as $cat): ?>
      <option value="<?= htmlspecialchars($cat['name']) ?>" <?= strcasecmp($selectedCuisine, $cat['name']) === 0 ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<ul class="vendor-list" id="vendorList">
  <?php if (empty($vendors)): ?>
    <li style="text-align: center; color: #6E6259; padding: 3rem;">No restaurants currently found.</li>
  <?php else: ?>
    <?php foreach ($vendors as $v): ?>
      <li>
        <a class="vendor-row" href="menu.php?vendor=<?= urlencode($v['id']) ?>"
           data-name="<?= htmlspecialchars($v['name']) ?>" data-cuisine="<?= htmlspecialchars($v['cuisine']) ?>">
          <div class="vendor-row__thumb">
            <img class="thumb-img" src="<?= $v['img'] ?>" alt="<?= htmlspecialchars($v['name']) ?>">
          </div>
          <div style="flex: 1;">
            <p class="vendor-row__name"><?= htmlspecialchars($v['name']) ?></p>
            <p class="vendor-row__meta"><?= htmlspecialchars($v['cuisine']) ?></p>
            <span style="font-size: 0.72rem; color: #8C7B70;">Min. Order: Rs. <?= number_format($v['min_order']) ?></span>
          </div>
          <div class="vendor-row__right">
            <p class="vendor-row__rating">★ <?= number_format($v['rating'], 1) ?> (500+)</p>
            <p style="font-weight: 600; color: var(--color-ink-soft); margin-top: 0.2rem;">🕒 <?= htmlspecialchars($v['time']) ?></p>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>

<!-- Floating Cart Indicator -->
<a href="cart.php" class="floating-cart-bar" id="floatingCartBar">
  <span>🛒 View Cart</span>
  <span class="floating-cart-bar__badge" id="floatingCartCount">0 items</span>
  <span class="floating-cart-bar__cta">Checkout &rarr;</span>
</a>

<script>window.PLATFORM_DELIVERY_FEE = <?= (int)get_setting('delivery_fee', 40) ?>;</script>
<script type="application/json" id="menuItemsData"><?= json_encode($menu_items) ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
