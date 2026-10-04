<?php
/**
 * Customer site header.
 * Expects $pageTitle and $activeNav to be set by the including page.
 * $basePath should be '' for root pages.
 */
$basePath = $basePath ?? '';
require_once __DIR__ . '/auth.php';
$currentUser = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'QuickBite') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $basePath ?>css/base.css">
<link rel="stylesheet" href="<?= $basePath ?>css/customer.css">
</head>
<body>

<header class="site-header">
  <div class="site-header__inner">
    <a class="brand" href="<?= $basePath ?>index.php">
     <img class="brand__logo" src="<?= $basePath ?>images/logo.png" alt="QuickBite" />
    </a>
    
    <nav class="main-nav" id="mainNav">
      <a href="<?= $basePath ?>index.php" class="nav-link <?= ($activeNav ?? '') === 'home' ? 'is-active' : '' ?>">Home</a>
      <a href="<?= $basePath ?>vendors.php" class="nav-link <?= ($activeNav ?? '') === 'vendors' ? 'is-active' : '' ?>">Vendors</a>
      <a href="<?= $basePath ?>menu.php" class="nav-link <?= ($activeNav ?? '') === 'menu' ? 'is-active' : '' ?>">Menu</a>
      <a href="<?= $basePath ?>offers.php" class="nav-link <?= ($activeNav ?? '') === 'offers' ? 'is-active' : '' ?>">Offers</a>
      <a href="<?= $basePath ?>about.php" class="nav-link <?= ($activeNav ?? '') === 'about' ? 'is-active' : '' ?>">About Us</a>

      <?php if ($currentUser): ?>
        <div class="mobile-nav-user show-mobile">
          <div class="mobile-nav-user__info">
            Logged in as: <strong><?= htmlspecialchars($currentUser['name']) ?></strong>
          </div>
          <?php if ($currentUser['role'] === 'admin'): ?>
            <a href="<?= $basePath ?>admin/overview.php" class="nav-link nav-link--dash">Admin Dashboard</a>
          <?php elseif ($currentUser['role'] === 'vendor'): ?>
            <a href="<?= $basePath ?>vendor/dashboard.php" class="nav-link nav-link--dash">Vendor Dashboard</a>
          <?php else: ?>
            <a href="<?= $basePath ?>customer_orders.php" class="nav-link nav-link--orders">My Orders</a>
          <?php endif; ?>
          <a href="<?= $basePath ?>logout.php" class="nav-link nav-link--logout">Logout</a>
        </div>
      <?php else: ?>
        <div class="mobile-nav-auth show-mobile">
          <a href="<?= $basePath ?>login.php" class="nav-link">Login</a>
          <a href="<?= $basePath ?>signup.php" class="nav-link">Sign Up</a>
        </div>
      <?php endif; ?>
    </nav>
    
    <div class="site-header__actions">
      <button class="icon-btn" id="searchToggle" type="button" aria-label="Search">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M12.5 12.5L16 16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </button>

      <a class="icon-btn icon-btn--cart" href="<?= $basePath ?>cart.php" aria-label="Cart">
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none"><path d="M2 2h1.6l1.9 9.7a1.6 1.6 0 0 0 1.6 1.3h7.3a1.6 1.6 0 0 0 1.6-1.3L17.4 5H4.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="7.5" cy="16" r="1.1" fill="currentColor"/><circle cx="14" cy="16" r="1.1" fill="currentColor"/></svg>
        <span class="cart-badge" id="cartBadge">0</span>
      </a>

      <?php if ($currentUser): ?>
        <div class="header-user-menu hide-mobile-sm">
          <?php if ($currentUser['role'] === 'admin'): ?>
            <a href="<?= $basePath ?>admin/overview.php" class="btn btn--primary btn--sm">Admin</a>
          <?php elseif ($currentUser['role'] === 'vendor'): ?>
            <a href="<?= $basePath ?>vendor/dashboard.php" class="btn btn--primary btn--sm">Vendor</a>
          <?php else: ?>
            <a href="<?= $basePath ?>customer_orders.php" class="btn btn--outline btn--sm">Orders</a>
          <?php endif; ?>
          <span class="header-user-tag"><?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?></span>
          <a href="<?= $basePath ?>logout.php" class="btn btn--outline btn--sm" title="Log out">Logout</a>
        </div>
      <?php else: ?>
        <a href="<?= $basePath ?>login.php" class="btn btn--outline btn--sm hide-mobile-sm">Login</a>
        <a href="<?= $basePath ?>signup.php" class="btn btn--primary btn--sm hide-mobile-sm">Sign Up</a>
      <?php endif; ?>

      <button class="icon-btn mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Toggle navigation">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </div>
  <div class="search-panel" id="searchPanel">
    <input type="text" class="search-panel__input" placeholder="Search for vendors, dishes, cuisines…">
  </div>
</header>

<main class="page">
  <?= render_flash() ?>
