<?php
$pageTitle = 'Offers — QuickBite';
$activeNav = 'offers';
$basePath = '';
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';
?>

<section class="content-page">
  <div class="content-hero">
    <span class="content-hero__eyebrow">QuickBite Offers</span>
    <h1 class="content-hero__title">Good food, better deals.</h1>
    <p class="content-hero__text">
      Enjoy exclusive discounts and promo codes on your favorite local restaurants.
    </p>
  </div>

  <div class="offer-grid">
    <?php if (empty($offers)): ?>
      <p style="grid-column: 1 / -1; text-align: center; color: #6E6259; padding: 2rem;">No active promotional offers at the moment. Check back soon!</p>
    <?php else: ?>
      <?php foreach ($offers as $offer): ?>
        <article class="offer-card">
          <span class="offer-card__badge">
            <?= (float)$offer['discount_percent'] > 0 ? ((int)$offer['discount_percent'] . '% OFF') : 'SPECIAL DEAL' ?>
          </span>
          <h2 class="offer-card__title"><?= htmlspecialchars($offer['title']) ?></h2>
          <p class="offer-card__text"><?= htmlspecialchars($offer['description']) ?></p>
          <?php if ((float)$offer['min_order'] > 0): ?>
            <p style="font-size: 0.8rem; color: #8C7B70; margin-bottom: 0.5rem;">Min. order: Rs. <?= number_format($offer['min_order']) ?></p>
          <?php endif; ?>
          <span class="offer-card__code" title="Click to copy" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($offer['code']) ?>'); alert('Copied code: <?= htmlspecialchars($offer['code']) ?>');">
            <?= htmlspecialchars($offer['code']) ?>
          </span>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
