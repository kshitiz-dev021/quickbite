/**
 * customer.js
 * Shared across vendors.php, menu.php, cart.php and checkout.php.
 * Each init function bails out early if its page's markup isn't present,
 * so it's safe to load this one file on all four pages.
 */

const DELIVERY_FEE = 40;

/* -----------------------------------------------------------
   Vendor listing search + cuisine filter (vendors.php)
----------------------------------------------------------- */
(function initVendorFilter() {
  const searchInput = document.getElementById('vendorSearch');
  const cuisineSelect = document.getElementById('vendorFilter');
  const rows = document.querySelectorAll('.vendor-row');
  if (!searchInput || !rows.length) return;

  function applyFilter() {
    const text = searchInput.value.trim().toLowerCase();
    const cuisine = cuisineSelect.value;
    rows.forEach(row => {
      const matchesText = row.dataset.name.toLowerCase().includes(text);
      const matchesCuisine = cuisine === 'all' || row.dataset.cuisine.includes(cuisine);
      row.hidden = !(matchesText && matchesCuisine);
    });
  }

  searchInput.addEventListener('input', applyFilter);
  cuisineSelect.addEventListener('change', applyFilter);
})();

/* -----------------------------------------------------------
   Food menu: category tabs + add to cart (menu.php)
----------------------------------------------------------- */
(function initMenuPage() {
  const tabs = document.querySelectorAll('.menu-tab');
  const items = document.querySelectorAll('.menu-item');
  if (!items.length) return;

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('is-active'));
      tab.classList.add('is-active');
      const cat = tab.dataset.cat;
      items.forEach(item => {
        item.hidden = cat !== 'All' && item.dataset.cat !== cat;
      });
    });
  });

  const existingCart = readCart();

  items.forEach(item => {
    const btn = item.querySelector('[data-add-item]');
    if (!btn) return;
    const id = btn.dataset.addItem;

    if (existingCart[id]) {
      btn.textContent = `In cart · ${existingCart[id]}`;
      btn.classList.add('is-added');
    }

    btn.addEventListener('click', () => {
      const cart = readCart();
      cart[id] = (cart[id] || 0) + 1;
      writeCart(cart);
      btn.textContent = `In cart · ${cart[id]}`;
      btn.classList.add('is-added');
    });
  });
})();

/* -----------------------------------------------------------
   Cart page (cart.php)
----------------------------------------------------------- */
(function initCartPage() {
  const body = document.getElementById('cartTableBody');
  const dataTag = document.getElementById('menuItemsData');
  if (!body || !dataTag) return;

  const MENU_ITEMS = JSON.parse(dataTag.textContent);

  function findItem(id) {
    return MENU_ITEMS.find(i => i.id === id);
  }

  function totals(cart) {
    let subtotal = 0;
    let discount = 0;
    Object.entries(cart).forEach(([id, qty]) => {
      const item = findItem(id);
      if (!item) return;
      subtotal += item.price * qty;
      if (item.was) discount += (item.was - item.price) * qty;
    });
    const total = Object.keys(cart).length ? subtotal + DELIVERY_FEE : 0;
    return { subtotal, discount, total };
  }

  function render() {
    const cart = readCart();
    const entries = Object.entries(cart);
    document.getElementById('cartCount').textContent =
      `(${cartItemCount(cart)} items)`;

    const emptyMsg = document.getElementById('cartEmptyMsg');

    if (!entries.length) {
      body.innerHTML = '';
      emptyMsg.style.display = 'block';
    } else {
      emptyMsg.style.display = 'none';
      body.innerHTML = entries.map(([id, qty]) => {
        const item = findItem(id);
        if (!item) return '';
        return `
        <tr>
          <td>
            <p class="cart-item__name">${item.name}</p>
            <p class="cart-item__vendor">${item.vendor_name}</p>
          </td>
          <td>Rs. ${item.price}</td>
          <td>
            <span class="qty-stepper">
              <button type="button" data-qty-down="${id}">&minus;</button>
              <span>${qty}</span>
              <button type="button" data-qty-up="${id}">+</button>
            </span>
          </td>
          <td>Rs. ${item.price * qty}</td>
          <td><button class="cart-remove-btn" data-remove="${id}" aria-label="Remove item">&times;</button></td>
        </tr>`;
      }).join('');

      body.querySelectorAll('[data-qty-up]').forEach(b =>
        b.addEventListener('click', () => changeQty(b.dataset.qtyUp, 1)));
      body.querySelectorAll('[data-qty-down]').forEach(b =>
        b.addEventListener('click', () => changeQty(b.dataset.qtyDown, -1)));
      body.querySelectorAll('[data-remove]').forEach(b =>
        b.addEventListener('click', () => removeItem(b.dataset.remove)));
    }

    const { subtotal, discount, total } = totals(cart);
    document.getElementById('sumSubtotal').textContent = `Rs. ${subtotal}`;
    document.getElementById('sumDiscount').textContent = `- Rs. ${discount}`;
    document.getElementById('sumDelivery').textContent = entries.length ? `Rs. ${DELIVERY_FEE}` : 'Rs. 0';
    document.getElementById('sumTotal').textContent = `Rs. ${total}`;
  }

  function changeQty(id, delta) {
    const cart = readCart();
    if (!cart[id]) return;
    cart[id] += delta;
    if (cart[id] <= 0) delete cart[id];
    writeCart(cart);
    render();
  }

  function removeItem(id) {
    const cart = readCart();
    delete cart[id];
    writeCart(cart);
    render();
  }

  render();
})();

/* -----------------------------------------------------------
   Checkout page (checkout.php)
----------------------------------------------------------- */
(function initCheckoutPage() {
  const placeOrderBtn = document.getElementById('placeOrderBtn');
  const dataTag = document.getElementById('menuItemsData');
  if (!placeOrderBtn || !dataTag) return;

  const MENU_ITEMS = JSON.parse(dataTag.textContent);

  function totals() {
    const cart = readCart();
    let subtotal = 0;
    let discount = 0;
    Object.entries(cart).forEach(([id, qty]) => {
      const item = MENU_ITEMS.find(i => i.id === id);
      if (!item) return;
      subtotal += item.price * qty;
      if (item.was) discount += (item.was - item.price) * qty;
    });
    const total = Object.keys(cart).length ? subtotal + DELIVERY_FEE : 0;
    return { cart, subtotal, discount, total };
  }

  function renderSummary() {
    const { cart, discount, total } = totals();
    document.getElementById('checkoutItemCount').textContent = cartItemCount(cart);
    document.getElementById('checkoutDiscount').textContent = `- Rs. ${discount}`;
    document.getElementById('checkoutDelivery').textContent = Object.keys(cart).length ? `Rs. ${DELIVERY_FEE}` : 'Rs. 0';
    document.getElementById('checkoutTotal').textContent = `Rs. ${total}`;
  }

  placeOrderBtn.addEventListener('click', () => {
    const form = document.getElementById('checkoutForm');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    const { cart } = totals();
    if (!Object.keys(cart).length) {
      alert('Your cart is empty — add something from the menu first.');
      return;
    }
    // In the real build this is where we'd POST the order to the backend.
    writeCart({});
    window.location.href = 'index.php?ordered=1';
  });

  renderSummary();
})();
