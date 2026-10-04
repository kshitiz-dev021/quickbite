<?php
$pageTitle = 'Offers — QuickBite';
$activeNav = 'offers';
$demoActive = 'customer';
$basePath = '';
include __DIR__ . '/includes/header.php';
?>

<section class="content-page">
  <div class="content-hero">
    <span class="content-hero__eyebrow">QuickBite Offers</span>
    <h1 class="content-hero__title">Good food, better deals.</h1>
    <p class="content-hero__text">
      Explore the current promotional offers available in our frontend prototype.
      These offers are static for now and can later be connected to the backend and database.
    </p>
  </div>

  <div class="offer-grid">
    <article class="offer-card">
      <span class="offer-card__badge">20% OFF</span>
      <h2 class="offer-card__title">First Order</h2>
      <p class="offer-card__text">Get 20% off your first QuickBite order.</p>
      <span class="offer-card__code">WELCOME20</span>
    </article>

    <article class="offer-card">
      <span class="offer-card__badge">FREE DELIVERY</span>
      <h2 class="offer-card__title">Lunch Break</h2>
      <p class="offer-card__text">Enjoy free delivery on selected lunch orders.</p>
      <span class="offer-card__code">LUNCHFREE</span>
    </article>

    <article class="offer-card">
      <span class="offer-card__badge">10% OFF</span>
      <h2 class="offer-card__title">Weekend Treat</h2>
      <p class="offer-card__text">Save on selected dishes during the weekend.</p>
      <span class="offer-card__code">WEEKEND10</span>
    </article>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
