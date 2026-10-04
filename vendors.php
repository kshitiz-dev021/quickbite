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
  <input type="text" class="listing-toolbar__search" id="vendorSearch" placeholder="Search by restaurant name, cuisine, or location (e.g. Dalle, Momos, Thamel, KKFC, Pizza)…" autofocus>
  <select class="listing-toolbar__filter" id="vendorFilter">
    <option value="all" <?= $selectedCuisine === 'all' ? 'selected' : '' ?>>All Cuisines</option>
    <?php foreach ($menu_categories as $cat): ?>
      <option value="<?= htmlspecialchars($cat['name']) ?>" <?= strcasecmp($selectedCuisine, $cat['name']) === 0 ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<!-- Pathao-style Filter Chips -->
<div class="filter-chips">
  <button type="button" class="filter-chip <?= $selectedCuisine === 'all' ? 'is-active' : '' ?>" data-filter="all">All Places</button>
  <button type="button" class="filter-chip" data-filter="popular">🔥 Popular</button>
  <button type="button" class="filter-chip" data-filter="top-rated">★ Top Rated</button>
  <button type="button" class="filter-chip" data-filter="fastest">⚡ Express (&lt;25m)</button>
  <button type="button" class="filter-chip <?= strcasecmp($selectedCuisine, 'Momos') === 0 ? 'is-active' : '' ?>" data-filter="Momos">🥟 Momos</button>
  <button type="button" class="filter-chip <?= strcasecmp($selectedCuisine, 'Burgers') === 0 ? 'is-active' : '' ?>" data-filter="Burgers">🍔 Burgers</button>
  <button type="button" class="filter-chip <?= strcasecmp($selectedCuisine, 'Pizza') === 0 ? 'is-active' : '' ?>" data-filter="Pizza">🍕 Pizza</button>
  <button type="button" class="filter-chip <?= strcasecmp($selectedCuisine, 'Coffee & Bakery') === 0 ? 'is-active' : '' ?>" data-filter="Coffee">☕ Coffee &amp; Bakery</button>
  <button type="button" class="filter-chip <?= strcasecmp($selectedCuisine, 'Korean & Asian') === 0 ? 'is-active' : '' ?>" data-filter="Korean">🇰🇷 Korean</button>
  <button type="button" class="filter-chip <?= strcasecmp($selectedCuisine, 'Pasta & Italian') === 0 ? 'is-active' : '' ?>" data-filter="Pasta">🍝 Pasta &amp; Italian</button>
</div>

<ul class="vendor-list" id="vendorList">
  <?php if (empty($vendors)): ?>
    <li style="text-align: center; color: #6E6259; padding: 3rem;">No restaurants currently found.</li>
  <?php else: ?>
    <?php foreach ($vendors as $v): 
      $badge = $v['badge'] ?? ((float)$v['rating'] >= 4.8 ? 'Top Rated' : 'Popular');
    ?>
      <li>
        <a class="vendor-row" href="menu.php?vendor=<?= urlencode($v['id']) ?>"
           data-name="<?= htmlspecialchars($v['name']) ?>"
           data-cuisine="<?= htmlspecialchars($v['cuisine']) ?>"
           data-rating="<?= htmlspecialchars($v['rating']) ?>"
           data-time="<?= htmlspecialchars($v['time']) ?>"
           data-badge="<?= htmlspecialchars($badge) ?>">
          <div class="vendor-row__thumb">
            <img class="thumb-img" src="<?= $v['img'] ?>" alt="<?= htmlspecialchars($v['name']) ?>">
          </div>
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
              <p class="vendor-row__name" style="margin-bottom: 0;"><?= htmlspecialchars($v['name']) ?></p>
              <?php if (!empty($badge)): ?>
                <span class="status-pill status-pill--approved" style="font-size: 0.65rem; padding: 0.15rem 0.5rem; background: var(--color-brand-tint); color: var(--color-brand); font-weight: 700;">
                  <?= htmlspecialchars($badge) ?>
                </span>
              <?php endif; ?>
            </div>
            <p class="vendor-row__meta"><?= htmlspecialchars($v['cuisine']) ?></p>
            <span style="font-size: 0.72rem; color: #8C7B70;">Min. Order: Rs. <?= number_format($v['min_order']) ?> &nbsp;·&nbsp; Delivery: Rs. <?= (int)get_setting('delivery_fee', 40) ?></span>
          </div>
          <div class="vendor-row__right">
            <p class="vendor-row__rating">★ <?= number_format($v['rating'], 1) ?> (1k+ reviews)</p>
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
