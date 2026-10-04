<?php
$pageTitle = 'QuickBite — Order Food Online in Kathmandu';
$activeNav = 'home';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';

// Featured dishes for the homepage
$featuredDishes = array_slice($menu_items, 0, 6);
?>

<?php if (isset($_GET['ordered'])): ?>
  <div class="alert alert--success" style="text-align: center; font-size: 1rem; padding: 1.25rem;">
    🎉 <strong>Your order has been placed successfully!</strong> QuickBite and the restaurant kitchen are preparing your food now.
    <div style="margin-top: 0.5rem;">
      <a href="customer_orders.php" class="btn btn--outline btn--sm">Track in My Orders</a>
    </div>
  </div>
<?php endif; ?>

<section class="hero">
  <div class="hero__copy">
    <h1 class="hero__title">Delicious food from your favorite restaurants</h1>
    <p class="hero__subtitle">From spicy Kathmandu momos and wood-fired pizzas to juicy burgers and organic coffees — delivered straight to your door in minutes.</p>
    <a href="vendors.php" class="btn btn--primary">Explore All Restaurants</a>
  </div>
  <div class="hero__art">
    <img class="thumb-img" src="<?= $photo['burger'] ?>" alt="Cheese burger with fries, ready for delivery">
  </div>
</section>

<!-- Pathao-style Promotional Vouchers Strip -->
<?php if (!empty($offers)): ?>
  <section class="section" style="padding-bottom: 0.5rem;">
    <div class="section__header">
      <h2 class="section__title">Today's Deals &amp; Vouchers</h2>
      <a href="offers.php" class="section__link">View All Offers</a>
    </div>
    <div class="promo-strip">
      <?php foreach ($offers as $off): ?>
        <div class="promo-banner-card">
          <div>
            <div class="promo-banner-card__title"><?= htmlspecialchars($off['title']) ?></div>
            <div class="promo-banner-card__desc"><?= htmlspecialchars($off['description']) ?></div>
          </div>
          <div class="promo-banner-card__footer">
            <span style="font-size: 0.72rem; color: #8C7B70;">
              <?= (float)$off['min_order'] > 0 ? ('Min. Rs. ' . number_format($off['min_order'])) : 'No min order' ?>
            </span>
            <span class="promo-banner-card__code" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($off['code']) ?>'); alert('Voucher code copied: <?= htmlspecialchars($off['code']) ?>');" title="Click to copy code">
              <?= htmlspecialchars($off['code']) ?> 📋
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<!-- Categories -->
<section class="section">
  <div class="section__header">
    <h2 class="section__title">Categories</h2>
    <a href="vendors.php" class="section__link">Browse All</a>
  </div>
  <div class="category-grid">
    <?php
    $catImages = [
      'burgers'   => $photo['burger'],
      'pizza'     => './images/pizza.png',
      'momos'     => './images/momo.png',
      'fast food' => $photo['fries'],
      'drinks'    => $photo['coffee'],
      'desserts'  => './images/dessert.png',
      'thali'     => './images/Thali.png',
      'pasta'     => 'https://images.unsplash.com/photo-1621996346565-e3d5d6281729?w=500&q=80',
    ];
    foreach ($menu_categories as $cat):
        $img = $catImages[strtolower($cat['name'])] ?? $photo['burger'];
    ?>
      <a class="category-card" href="vendors.php?cuisine=<?= urlencode($cat['name']) ?>">
        <span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="<?= $img ?>" alt="<?= htmlspecialchars($cat['name']) ?>"></span>
        <?= htmlspecialchars($cat['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Popular Vendors (Pathao style) -->
<section class="section">
  <div class="section__header">
    <h2 class="section__title">Popular Restaurants Near You</h2>
    <a href="vendors.php" class="section__link">View All (<?= count($vendors) ?>)</a>
  </div>
  <div class="vendor-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
    <?php foreach (array_slice($vendors, 0, 8) as $v): ?>
      <a class="vendor-card" href="menu.php?vendor=<?= urlencode($v['id']) ?>">
        <div class="vendor-card__thumb">
          <img class="thumb-img" src="<?= $v['img'] ?>" alt="<?= htmlspecialchars($v['name']) ?>">
          <?php if ((float)$v['rating'] >= 4.7): ?>
            <span class="vendor-card__badge">Top Rated</span>
          <?php endif; ?>
          <span class="vendor-card__time-badge">🕒 <?= htmlspecialchars($v['time']) ?></span>
        </div>
        <div class="vendor-card__body">
          <p class="vendor-card__name"><?= htmlspecialchars($v['name']) ?></p>
          <p class="vendor-card__meta"><?= htmlspecialchars($v['cuisine']) ?></p>
          <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 0.35rem;">
            <p class="vendor-card__rating">★ <?= number_format($v['rating'], 1) ?> (500+)</p>
            <span class="vendor-card__min">Min. Rs. <?= number_format($v['min_order']) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Popular Dishes -->
<section class="section">
  <div class="section__header">
    <h2 class="section__title">Popular Dishes</h2>
    <a href="vendors.php" class="section__link">More Dishes</a>
  </div>
  <ul class="menu-list" id="menuList">
    <?php foreach ($featuredDishes as $item): ?>
      <li class="menu-item" data-cat="<?= htmlspecialchars($item['cat']) ?>">
        <div class="menu-item__thumb"><img class="thumb-img" src="<?= $item['img'] ?>" alt="<?= htmlspecialchars($item['name']) ?>"></div>
        <div>
          <p class="menu-item__name"><?= htmlspecialchars($item['name']) ?></p>
          <p class="menu-item__desc"><?= htmlspecialchars($item['desc']) ?></p>
          <span style="font-size: 0.72rem; color: var(--color-brand); font-weight: 600;">By <?= htmlspecialchars($item['vendor_name']) ?></span>
        </div>
        <div class="menu-item__price-block">
          <p class="menu-item__price">
            <?php if (!empty($item['was'])): ?><span class="menu-item__price--was">Rs. <?= number_format($item['was']) ?></span><?php endif; ?>
            Rs. <?= number_format($item['price']) ?>
            <?php if (!empty($item['discount'])): ?><span class="menu-item__badge"><?= $item['discount'] ?>% OFF</span><?php endif; ?>
          </p>
          <button class="menu-item__add" data-add-item="<?= $item['id'] ?>">Add</button>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</section>

<!-- Floating Cart Indicator (Pathao-style) -->
<a href="cart.php" class="floating-cart-bar" id="floatingCartBar">
  <span>🛒 View Cart</span>
  <span class="floating-cart-bar__badge" id="floatingCartCount">0 items</span>
  <span class="floating-cart-bar__cta">Checkout &rarr;</span>
</a>

<script>
window.PLATFORM_DELIVERY_FEE = <?= (int)get_setting('delivery_fee', 40) ?>;
</script>
<script type="application/json" id="menuItemsData"><?= json_encode($menu_items) ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
