<?php
$pageTitle = 'QuickBite — Local Food, Instant Delight';
$activeNav = 'home';
$demoActive = 'customer';
$basePath = '';
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['ordered'])): ?>
  <div class="order-confirmed">Your order has been placed — QuickBite will have it at your door soon.</div>
<?php endif; ?>

<section class="hero">
  <div class="hero__copy">
    <h1 class="hero__title">Discover delicious food from local vendors</h1>
    <p class="hero__subtitle">Fresh meals, great taste, best service — ordered in a couple of taps and on your table in under an hour.</p>
    <a href="vendors.php" class="btn btn--primary">Browse Food</a>
  </div>
  <div class="hero__art">
    <img class="thumb-img" src="<?= $photo['burger'] ?>" alt="Cheese burger with fries, ready for delivery">
  </div>
</section>

<section class="section">
  <div class="section__header">
    <h2 class="section__title">Categories</h2>
    <a href="vendors.php" class="section__link">View All</a>
  </div>
  <div class="category-grid">
    <a class="category-card" href="vendors.php?cuisine=Burgers">
      <span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="<?= $photo['burger'] ?>" alt=""></span>
      Burger
    </a>
    <a class="category-card" href="vendors.php?cuisine=Pizza">
      <span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="./images/pizza.png" alt=""></span>
      Pizza
    </a>
    <a class="category-card" href="vendors.php?cuisine=Momos">
      <span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="./images/momo.png" alt=""></span>
      Momo
    </a>
    <a class="category-card" href="vendors.php?cuisine=Fast+Food">
      <span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="<?= $photo['fries'] ?>" alt=""></span>
      Snacks
    </a>
    <a class="category-card" href="vendors.php?cuisine=Drinks">
      <span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="<?= $photo['coffee'] ?>" alt=""></span>
      Drinks
    </a>
    <a class="category-card" href="vendors.php"><span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="./images/Thali.png" alt=""></span>Thali</a>
    <a class="category-card" href="vendors.php"><span class="category-card__icon category-card__icon--photo"><img class="thumb-img" src="./images/dessert.png" alt=""></span>Desserts</a>
    <a class="category-card" href="vendors.php"><span class="category-card__icon">⋯</span>More</a>
  </div>
</section>

<section class="section">
  <div class="section__header">
    <h2 class="section__title">Popular Vendors</h2>
    <a href="vendors.php" class="section__link">View All</a>
  </div>
  <div class="vendor-grid">
    <?php foreach (array_slice($vendors, 0, 4) as $v): ?>
      <a class="vendor-card" href="menu.php?vendor=<?= urlencode($v['id']) ?>">
        <div class="vendor-card__thumb"><img class="thumb-img" src="<?= $v['img'] ?>" alt="<?= htmlspecialchars($v['name']) ?>"></div>
        <div class="vendor-card__body">
          <p class="vendor-card__name"><?= htmlspecialchars($v['name']) ?></p>
          <p class="vendor-card__meta"><?= htmlspecialchars($v['cuisine']) ?></p>
          <p class="vendor-card__rating">★ <?= number_format($v['rating'], 1) ?> · <?= $v['time'] ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
