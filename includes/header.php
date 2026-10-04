<?php
/**
 * Customer site header.
 * Expects $pageTitle and $activeNav to be set by the including page.
 * $basePath should be '' for root pages.
 */
$basePath = $basePath ?? '';
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

<div class="demo-bar">
  <span class="demo-bar__label">Prototype views</span>
  <a href="<?= $basePath ?>index.php" class="demo-bar__link <?= ($demoActive ?? '') === 'customer' ? 'is-active' : '' ?>">Customer</a>
  <a href="<?= $basePath ?>vendor/dashboard.php" class="demo-bar__link">Vendor (Shop Owner)</a>
  <a href="<?= $basePath ?>admin/overview.php" class="demo-bar__link">Admin (System)</a>
</div>

<header class="site-header">
  <div class="site-header__inner">
    <a class="brand" href="<?= $basePath ?>index.php">
     <img style="width: 200px" src="<?= $basePath ?>images/logo.png" alt="QuickBite" />
    </a>
    <nav class="main-nav">
      <a href="<?= $basePath ?>index.php" class="nav-link <?= ($activeNav ?? '') === 'home' ? 'is-active' : '' ?>">Home</a>
      <a href="<?= $basePath ?>vendors.php" class="nav-link <?= ($activeNav ?? '') === 'vendors' ? 'is-active' : '' ?>">Vendors</a>
      <a href="<?= $basePath ?>menu.php" class="nav-link <?= ($activeNav ?? '') === 'menu' ? 'is-active' : '' ?>">Menu</a>
      <a href="<?= $basePath ?>offers.php" class="nav-link <?= ($activeNav ?? '') === 'offers' ? 'is-active' : '' ?>">Offers</a>
      <a href="<?= $basePath ?>about.php" class="nav-link <?= ($activeNav ?? '') === 'about' ? 'is-active' : '' ?>">About Us</a>
    </nav>
    <div class="site-header__actions">
      <button class="icon-btn" id="searchToggle" type="button" aria-label="Search">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M12.5 12.5L16 16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </button>
      <a class="icon-btn icon-btn--cart" href="<?= $basePath ?>cart.php" aria-label="Cart">
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none"><path d="M2 2h1.6l1.9 9.7a1.6 1.6 0 0 0 1.6 1.3h7.3a1.6 1.6 0 0 0 1.6-1.3L17.4 5H4.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="7.5" cy="16" r="1.1" fill="currentColor"/><circle cx="14" cy="16" r="1.1" fill="currentColor"/></svg>
        <span class="cart-badge" id="cartBadge">0</span>
      </a>
      <a href="<?= $basePath ?>login.php" class="btn btn--outline btn--sm">Login</a>
    </div>
  </div>
  <div class="search-panel" id="searchPanel">
    <input type="text" class="search-panel__input" placeholder="Search for vendors, dishes, cuisines…">
  </div>
</header>

<main class="page">
