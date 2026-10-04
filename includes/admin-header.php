<?php
/**
 * Admin dashboard chrome. Expects $pageTitle and $activeTab (one of:
 * overview, vendors, approvals, reports, users, settings).
 */
$basePath = '../';
require_once __DIR__ . '/auth.php';
require_role('admin', '../login.php');

$currentUser = current_user();

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

<div class="dash-shell">
  <aside class="dash-sidebar dash-sidebar--admin" id="dashSidebar">
    <div class="dash-sidebar__top">
      <a class="brand brand--dash" href="<?= $basePath ?>index.php">
        <img class="brand__logo" src="<?= $basePath ?>images/logo.png" alt="QuickBite" />
      </a>
      <button type="button" class="dash-nav-toggle" id="dashNavToggle" aria-label="Toggle Navigation">
        <span class="dash-nav-toggle__bar"></span>
        <span class="dash-nav-toggle__bar"></span>
        <span class="dash-nav-toggle__bar"></span>
      </button>
    </div>

    <div class="dash-sidebar__content" id="dashSidebarContent">
      <div class="dash-user-card">
        <span class="dash-user-card__label">Logged in as:</span>
        <strong class="dash-user-card__name"><?= htmlspecialchars($currentUser['name']) ?></strong>
        <span class="dash-user-card__badge">Admin</span>
      </div>

      <nav class="dash-nav">
        <a href="overview.php" class="<?= admin_tab_classes('overview', $activeTab ?? '') ?>">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>
        <a href="vendors.php" class="<?= admin_tab_classes('vendors', $activeTab ?? '') ?>">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Vendors
        </a>
        <a href="approvals.php" class="<?= admin_tab_classes('approvals', $activeTab ?? '') ?>">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Pending Approvals
        </a>
        <a href="reports.php" class="<?= admin_tab_classes('reports', $activeTab ?? '') ?>">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Reports
        </a>
        <a href="users.php" class="<?= admin_tab_classes('users', $activeTab ?? '') ?>">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Users
        </a>
        <a href="settings.php" class="<?= admin_tab_classes('settings', $activeTab ?? '') ?>">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          Settings
        </a>
        <a href="<?= $basePath ?>index.php" class="dash-nav__link dash-nav__link--storefront">
          <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          View Storefront
        </a>
      </nav>

      <a href="<?= $basePath ?>logout.php" class="dash-nav__link dash-nav__link--logout">
        <svg class="dash-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </a>
    </div>
  </aside>

  <div class="dash-main">
    <?= render_flash() ?>
