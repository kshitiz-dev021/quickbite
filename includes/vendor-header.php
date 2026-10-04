<?php
/**
 * Vendor dashboard chrome. Expects $pageTitle and $activeTab (one of:
 * dashboard, items, add, discounts, orders, settings).
 * Vendor pages live one folder down, so assets are referenced via '../'.
 */
$basePath = '../';

function vendor_tab_classes(string $tab, string $active): string
{
    return $tab === $active ? 'dash-nav__link is-active' : 'dash-nav__link';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'QuickBite — Vendor') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,500&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $basePath ?>css/base.css">
<link rel="stylesheet" href="<?= $basePath ?>css/dashboard.css">
</head>
<body>

<div class="demo-bar">
  <span class="demo-bar__label">Prototype views</span>
  <a href="<?= $basePath ?>index.php" class="demo-bar__link">Customer</a>
  <a href="<?= $basePath ?>vendor/dashboard.php" class="demo-bar__link is-active">Vendor (Shop Owner)</a>
  <a href="<?= $basePath ?>admin/overview.php" class="demo-bar__link">Admin (System)</a>
</div>

<div class="dash-shell">
  <aside class="dash-sidebar">
    <a class="brand brand--dash" href="<?= $basePath ?>index.php">
      <img style="width: 200px" src="<?= $basePath ?>images/logo.png" alt="QuickBite" />
    </a>
    <nav class="dash-nav">
      <a href="dashboard.php"  class="<?= vendor_tab_classes('dashboard', $activeTab ?? '') ?>">Dashboard</a>
      <a href="menu-items.php" class="<?= vendor_tab_classes('items', $activeTab ?? '') ?>">Menu Items</a>
      <a href="add-item.php"   class="<?= vendor_tab_classes('add', $activeTab ?? '') ?>">Add Food Item</a>
      <a href="discounts.php"  class="<?= vendor_tab_classes('discounts', $activeTab ?? '') ?>">Discounts</a>
      <a href="orders.php"     class="<?= vendor_tab_classes('orders', $activeTab ?? '') ?>">Orders</a>
      <a href="settings.php"   class="<?= vendor_tab_classes('settings', $activeTab ?? '') ?>">Settings</a>
    </nav>
    <a href="<?= $basePath ?>index.php" class="dash-nav__link dash-nav__link--logout">Logout</a>
  </aside>

  <div class="dash-main">
