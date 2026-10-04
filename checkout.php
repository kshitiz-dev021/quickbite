<?php
$pageTitle = 'Checkout — QuickBite';
$activeNav = '';
$demoActive = 'customer';
$basePath = '';
$pageScripts = ['js/customer.js'];
include __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';
?>

<div class="checkout-steps">
  <span class="checkout-step is-active">Cart</span>
  <span class="checkout-step__arrow">&mdash;</span>
  <span class="checkout-step is-active">Checkout</span>
  <span class="checkout-step__arrow">&mdash;</span>
  <span class="checkout-step">Confirm</span>
</div>

<div class="checkout-layout">
  <form class="checkout-form" id="checkoutForm">
    <h2 class="checkout-form__title">Delivery Details</h2>
    <label class="field">
      <span class="field__label">Full Name</span>
      <input type="text" class="field__input" placeholder="Enter full name" required>
    </label>
    <label class="field">
      <span class="field__label">Phone Number</span>
      <input type="tel" class="field__input" placeholder="Enter phone number" required>
    </label>
    <label class="field">
      <span class="field__label">Delivery Address</span>
      <input type="text" class="field__input" placeholder="Enter delivery address" required>
    </label>

    <h2 class="checkout-form__title">Payment Method</h2>
    <label class="radio-row">
      <input type="radio" name="payment" checked>
      <span>Cash on Delivery</span>
    </label>
    <label class="radio-row radio-row--disabled">
      <input type="radio" name="payment" disabled>
      <span>eSewa / Khalti <em>(Coming Soon)</em></span>
    </label>
  </form>

  <aside class="order-summary-card">
    <h2 class="order-summary-card__title">Order Summary</h2>
    <div class="summary-list__row"><dt>Items</dt><dd id="checkoutItemCount">0</dd></div>
    <div class="summary-list__row"><dt>Discount</dt><dd id="checkoutDiscount">- Rs. 0</dd></div>
    <div class="summary-list__row"><dt>Delivery Fee</dt><dd id="checkoutDelivery">Rs. 40</dd></div>
    <div class="summary-list__row summary-list__row--total"><dt>Total</dt><dd id="checkoutTotal">Rs. 40</dd></div>
    <button class="btn btn--primary btn--block" id="placeOrderBtn" type="button">Place Order</button>
  </aside>
</div>

<script type="application/json" id="menuItemsData"><?= json_encode($menu_items) ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
