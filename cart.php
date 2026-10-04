<?php
$pageTitle = 'Your Cart — QuickBite';
$activeNav = '';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';

$deliveryFee = (int)get_setting('delivery_fee', 40);
?>

<div class="page-header">
  <h1 class="page-header__title">Your Cart <span class="page-header__count" id="cartCount">(0 items)</span></h1>
</div>

<div class="cart-layout">
  <div class="cart-table-wrap">
    <table class="cart-table">
      <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
      <tbody id="cartTableBody"></tbody>
    </table>
    <p class="cart-empty" id="cartEmptyMsg" style="display:none;">Your cart is empty. Head to the menu to add something tasty.</p>
  </div>
  <aside class="cart-summary">
    <h2 class="cart-summary__title">Order Summary</h2>
    <dl class="summary-list">
      <div class="summary-list__row"><dt>Subtotal</dt><dd id="sumSubtotal">Rs. 0</dd></div>
      <div class="summary-list__row"><dt>Discount</dt><dd id="sumDiscount">- Rs. 0</dd></div>
      <div class="summary-list__row"><dt>Delivery Fee</dt><dd id="sumDelivery">Rs. <?= $deliveryFee ?></dd></div>
      <div class="summary-list__row summary-list__row--total"><dt>Total</dt><dd id="sumTotal">Rs. <?= $deliveryFee ?></dd></div>
    </dl>
    <a href="menu.php" class="btn btn--outline btn--block">Continue Shopping</a>
    <a href="checkout.php" class="btn btn--primary btn--block">Proceed to Checkout</a>
  </aside>
</div>

<!-- Bridges the PHP data layer to the client-side cart renderer in customer.js -->
<script>window.PLATFORM_DELIVERY_FEE = <?= $deliveryFee ?>;</script>
<script type="application/json" id="menuItemsData"><?= json_encode($menu_items) ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
