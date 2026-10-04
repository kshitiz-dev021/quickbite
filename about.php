<?php
$pageTitle = 'About Us — QuickBite';
$activeNav = 'about';
$demoActive = 'customer';
$basePath = '';
include __DIR__ . '/includes/header.php';
?>

<section class="content-page">
  <div class="content-hero">
    <span class="content-hero__eyebrow">About QuickBite</span>
    <h1 class="content-hero__title">Local food, made easier.</h1>
    <p class="content-hero__text">
      QuickBite is a food ordering platform concept designed to make discovering
      local vendors and ordering favourite meals simple and convenient.
    </p>
  </div>

  <div class="about-grid">
    <div>
      <article class="about-card">
        <h2 class="about-card__title">Our Story</h2>
        <p>
          QuickBite brings customers and local food vendors together in one simple
          experience. Customers can browse vendors, explore menus, add items to a
          cart and continue to checkout from one place.
        </p>
      </article>

      <article class="about-card">
        <h2 class="about-card__title">Built for Local Food</h2>
        <p>
          The platform focuses on helping customers discover everyday meals while
          giving vendors a straightforward digital storefront.
        </p>
      </article>
    </div>

    <aside class="about-card">
      <h2 class="about-card__title">What We Value</h2>
      <div class="about-values">
        <div class="about-value">
          <span class="about-value__number">01</span>
          <div><strong>Simple</strong><p>Clear browsing and ordering.</p></div>
        </div>
        <div class="about-value">
          <span class="about-value__number">02</span>
          <div><strong>Local</strong><p>Built around local vendors and food.</p></div>
        </div>
        <div class="about-value">
          <span class="about-value__number">03</span>
          <div><strong>Convenient</strong><p>A smoother way to find your next meal.</p></div>
        </div>
      </div>
    </aside>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
