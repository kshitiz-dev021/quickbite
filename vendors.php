<?php
$pageTitle = 'All Vendors — QuickBite';
$activeNav = 'vendors';
$demoActive = 'customer';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <p class="breadcrumb">Home <span>/</span> Vendors</p>
  <h1 class="page-header__title">All Vendors</h1>
</div>

<div class="listing-toolbar">
  <input type="text" class="listing-toolbar__search" id="vendorSearch" placeholder="Search vendors…">
  <select class="listing-toolbar__filter" id="vendorFilter">
    <option value="all">All cuisines</option>
    <option value="American">American</option>
    <option value="Momos">Momos</option>
    <option value="Pizza">Pizza</option>
    <option value="Fast Food">Fast Food</option>
    <option value="Drinks">Drinks &amp; Juices</option>
  </select>
</div>

<ul class="vendor-list" id="vendorList">
  <?php foreach ($vendors as $v): ?>
    <li>
      <a class="vendor-row" href="menu.php?vendor=<?= urlencode($v['id']) ?>"
         data-name="<?= htmlspecialchars($v['name']) ?>" data-cuisine="<?= htmlspecialchars($v['cuisine']) ?>">
        <div class="vendor-row__thumb"><img class="thumb-img" src="<?= $v['img'] ?>" alt="<?= htmlspecialchars($v['name']) ?>"></div>
        <div>
          <p class="vendor-row__name"><?= htmlspecialchars($v['name']) ?></p>
          <p class="vendor-row__meta"><?= htmlspecialchars($v['cuisine']) ?></p>
        </div>
        <div class="vendor-row__right">
          <p class="vendor-row__rating">★ <?= number_format($v['rating'], 1) ?></p>
          <p><?= $v['time'] ?></p>
        </div>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<?php include __DIR__ . '/includes/footer.php'; ?>
