<?php
$pageTitle = 'Checkout — QuickBite';
$activeNav = '';
$basePath = '';
$pageScripts = ['js/customer.js'];
require_once __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/data.php';

$currentUser = current_user();
$deliveryFee = (int)get_setting('delivery_fee', 40);
$error = '';

// Handle Order Placement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = 'checkout.php';
        set_flash('error', 'Please log in to complete your order.');
        header('Location: login.php');
        exit;
    }

    $customerName = trim($_POST['customer_name'] ?? $currentUser['name'] ?? '');
    $phone        = trim($_POST['phone'] ?? $currentUser['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $cartDataJson = $_POST['cart_data'] ?? '{}';
    $cartData     = json_decode($cartDataJson, true) ?: [];

    if (empty($customerName) || empty($phone) || empty($address)) {
        $error = 'Please fill in your name, phone number, and delivery address.';
    } elseif (empty($cartData)) {
        $error = 'Your cart is empty. Please add items before checking out.';
    } else {
        $db = get_db();
        // Lookup items from database
        $itemIds = array_keys($cartData);
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $stmt = $db->prepare("SELECT id, vendor_id, name, price, original_price FROM menu_items WHERE id IN ({$placeholders})");
        $stmt->execute($itemIds);
        $dbItems = $stmt->fetchAll();

        if (empty($dbItems)) {
            $error = 'The items in your cart could not be found. Please refresh your cart.';
        } else {
            $subtotal = 0;
            $discount = 0;
            $vendorId = null;
            $orderItemsToInsert = [];

            foreach ($dbItems as $item) {
                $qty = (int)($cartData[$item['id']] ?? 0);
                if ($qty <= 0) continue;

                $lineSubtotal = (float)$item['price'] * $qty;
                $subtotal += $lineSubtotal;

                if (!empty($item['original_price']) && (float)$item['original_price'] > (float)$item['price']) {
                    $discount += ((float)$item['original_price'] - (float)$item['price']) * $qty;
                }

                if ($vendorId === null) {
                    $vendorId = (int)$item['vendor_id'];
                }

                $orderItemsToInsert[] = [
                    'menu_item_id' => (int)$item['id'],
                    'item_name'    => $item['name'],
                    'unit_price'   => (float)$item['price'],
                    'quantity'     => $qty,
                    'line_total'   => $lineSubtotal,
                ];
            }

            $total = $subtotal + $deliveryFee;

            if ($vendorId === null) {
                $vendorId = 1;
            }

            // Insert into orders table
            $db->beginTransaction();
            try {
                $oStmt = $db->prepare("
                    INSERT INTO orders (user_id, vendor_id, customer_name, phone, address, subtotal, discount, delivery_fee, total, payment_method, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'cod', 'pending')
                ");
                $oStmt->execute([
                    (int)$currentUser['id'],
                    $vendorId,
                    $customerName,
                    $phone,
                    $address,
                    $subtotal,
                    $discount,
                    $deliveryFee,
                    $total
                ]);
                $orderId = (int)$db->lastInsertId();

                // Insert line items
                $oiStmt = $db->prepare("
                    INSERT INTO order_items (order_id, menu_item_id, item_name, unit_price, quantity, line_total)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                foreach ($orderItemsToInsert as $oi) {
                    $oiStmt->execute([
                        $orderId,
                        $oi['menu_item_id'],
                        $oi['item_name'],
                        $oi['unit_price'],
                        $oi['quantity'],
                        $oi['line_total']
                    ]);
                }

                $db->commit();

                // Order placed successfully!
                header('Location: index.php?ordered=1&order_id=' . $orderId);
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Failed to save order: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="checkout-steps">
  <span class="checkout-step is-active">Cart</span>
  <span class="checkout-step__arrow">&mdash;</span>
  <span class="checkout-step is-active">Checkout</span>
  <span class="checkout-step__arrow">&mdash;</span>
  <span class="checkout-step">Confirm</span>
</div>

<?php if (!$currentUser): ?>
  <div class="alert alert--info" style="margin-bottom: 1.5rem;">
    💡 <strong>Quick Note:</strong> You need an account to place and track your order. 
    <a href="login.php" style="font-weight: 700; text-decoration: underline;">Log in here</a> or 
    <a href="signup.php" style="font-weight: 700; text-decoration: underline;">Register in 30 seconds</a>. Your cart will be saved!
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="checkout-layout">
  <form class="checkout-form" id="checkoutForm" method="post" action="checkout.php">
    <h2 class="checkout-form__title">Delivery Details</h2>
    <label class="field">
      <span class="field__label">Full Name</span>
      <input type="text" class="field__input" name="customer_name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" placeholder="Enter full name" required>
    </label>
    <label class="field">
      <span class="field__label">Phone Number</span>
      <input type="tel" class="field__input" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" placeholder="Enter phone number" required>
    </label>
    <label class="field">
      <span class="field__label">Delivery Address</span>
      <input type="text" class="field__input" name="address" placeholder="Street name, landmark, area" required autofocus>
    </label>

    <h2 class="checkout-form__title">Payment Method</h2>
    <label class="radio-row">
      <input type="radio" name="payment" value="cod" checked>
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
    <div class="summary-list__row"><dt>Delivery Fee</dt><dd id="checkoutDelivery">Rs. <?= $deliveryFee ?></dd></div>
    <div class="summary-list__row summary-list__row--total"><dt>Total</dt><dd id="checkoutTotal">Rs. <?= $deliveryFee ?></dd></div>
    <button class="btn btn--primary btn--block" id="placeOrderBtn" type="button">Place Order</button>
  </aside>
</div>

<script>window.PLATFORM_DELIVERY_FEE = <?= $deliveryFee ?>;</script>
<script type="application/json" id="menuItemsData"><?= json_encode($menu_items) ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
