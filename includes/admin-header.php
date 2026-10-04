<?php
/**
 * Admin dashboard chrome. Expects $pageTitle and $activeTab (one of:
 * overview, vendors, approvals, reports, users, settings).
 */
$basePath = '../';

function admin_tab_classes(string $tab, string $active): string
{
    return $tab === $active ? 'dash-nav__link is-active' : 'dash-nav__link';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'QuickBite — Admin') ?></title>
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
  <a href="<?= $basePath ?>vendor/dashboard.php" class="demo-bar__link">Vendor (Shop Owner)</a>
  <a href="<?= $basePath ?>admin/overview.php" class="demo-bar__link is-active">Admin (System)</a>
</div>

<div class="dash-shell">
  <aside class="dash-sidebar dash-sidebar--admin">
    <a class="brand brand--dash" href="<?= $basePath ?>index.php">
      <img style="width: 200px" src="<?= $basePath ?>images/logo.png" alt="QuickBite" />
    </a>
    <nav class="dash-nav">
      <a href="overview.php"  class="<?= admin_tab_classes('overview', $activeTab ?? '') ?>">Dashboard</a>
      <a href="vendors.php"   class="<?= admin_tab_classes('vendors', $activeTab ?? '') ?>">Vendors</a>
      <a href="approvals.php" class="<?= admin_tab_classes('approvals', $activeTab ?? '') ?>">Pending Approvals</a>
      <a href="reports.php"   class="<?= admin_tab_classes('reports', $activeTab ?? '') ?>">Reports</a>
      <a href="users.php"     class="<?= admin_tab_classes('users', $activeTab ?? '') ?>">Users</a>
      <a href="settings.php"  class="<?= admin_tab_classes('settings', $activeTab ?? '') ?>">Settings</a>
    </nav>
    <a href="<?= $basePath ?>index.php" class="dash-nav__link dash-nav__link--logout">Logout</a>
  </aside>

  <div class="dash-main">
