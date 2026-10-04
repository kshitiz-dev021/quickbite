<?php
/**
 * Vendor dashboard chrome. Expects $pageTitle and $activeTab (one of:
 * dashboard, items, add, discounts, orders, settings).
 * Vendor pages live one folder down, so assets are referenced via '../'.
 */
$basePath = '../';
require_once __DIR__ . '/auth.php';
require_role('vendor', '../login.php');

$currentUser = current_user();

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

<div class="dash-shell">
  <aside class="dash-sidebar">
    <a class="brand brand--dash" href="<?= $basePath ?>index.php">
      <img style="width: 200px" src="<?= $basePath ?>images/logo.png" alt="QuickBite" />
    </a>
    <div style="padding: 0 1rem 0.75rem; font-size: 0.8rem; color: #8C7B70;">
      Logged in as: <strong style="color: #2B2118;"><?= htmlspecialchars($currentUser['vendor_name'] ?? $currentUser['name']) ?></strong>
    </div>
    <nav class="dash-nav">
      <a href="dashboard.php"  class="<?= vendor_tab_classes('dashboard', $activeTab ?? '') ?>">Dashboard</a>
      <a href="menu-items.php" class="<?= vendor_tab_classes('items', $activeTab ?? '') ?>">Menu Items</a>
      <a href="add-item.php"   class="<?= vendor_tab_classes('add', $activeTab ?? '') ?>">Add Food Item</a>
      <a href="discounts.php"  class="<?= vendor_tab_classes('discounts', $activeTab ?? '') ?>">Discounts</a>
      <a href="orders.php"     class="<?= vendor_tab_classes('orders', $activeTab ?? '') ?>">Orders</a>
      <a href="settings.php"   class="<?= vendor_tab_classes('settings', $activeTab ?? '') ?>">Settings</a>
      <a href="<?= $basePath ?>index.php" class="dash-nav__link">View Storefront</a>
    </nav>
    <a href="<?= $basePath ?>logout.php" class="dash-nav__link dash-nav__link--logout">Logout</a>
  </aside>

  <div class="dash-main">
    <?= render_flash() ?>
