/**
 * customer.js
 * Shared across vendors.php, menu.php, cart.php and checkout.php.
 * Each init function bails out early if its page's markup isn't present,
 * so it's safe to load this one file on all four pages.
 */

const DELIVERY_FEE = typeof window.PLATFORM_DELIVERY_FEE !== 'undefined' ? window.PLATFORM_DELIVERY_FEE : 40;

/* -----------------------------------------------------------
   Vendor listing search + cuisine filter (vendors.php)
----------------------------------------------------------- */
(function initVendorFilter() {
  const searchInput = document.getElementById('vendorSearch');
  const cuisineSelect = document.getElementById('vendorFilter');
  const filterChips = document.querySelectorAll('.filter-chip');
  const rows = document.querySelectorAll('.vendor-row');
  if (!rows.length) return;

  let activeChipFilter = 'all';

  function applyFilter() {
    const text = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const selectedCuisine = cuisineSelect ? cuisineSelect.value.toLowerCase() : 'all';

    rows.forEach(row => {
      const name = (row.dataset.name || '').toLowerCase();
      const cuisine = (row.dataset.cuisine || '').toLowerCase();
      const badge = (row.dataset.badge || '').toLowerCase();
      const rating = parseFloat(row.dataset.rating || '0');
      const time = (row.dataset.time || '').toLowerCase();

      // Text query match
      const matchesText = !text || name.includes(text) || cuisine.includes(text) || badge.includes(text);

      // Dropdown cuisine filter match
      const matchesSelect = selectedCuisine === 'all' || cuisine.includes(selectedCuisine);

      // Quick Chip filter match
      let matchesChip = true;
      if (activeChipFilter === 'popular') {
        matchesChip = rating >= 4.7 || badge.includes('popular');
      } else if (activeChipFilter === 'top-rated') {
        matchesChip = rating >= 4.8 || badge.includes('top rated');
      } else if (activeChipFilter === 'fastest') {
        matchesChip = time.includes('15-') || time.includes('20-') || badge.includes('express');
      } else if (activeChipFilter !== 'all') {
        matchesChip = cuisine.includes(activeChipFilter.toLowerCase());
      }

      row.hidden = !(matchesText && matchesSelect && matchesChip);
    });
  }

  if (searchInput) searchInput.addEventListener('input', applyFilter);
  if (cuisineSelect) cuisineSelect.addEventListener('change', applyFilter);

  filterChips.forEach(chip => {
    chip.addEventListener('click', () => {
      filterChips.forEach(c => c.classList.remove('is-active'));
      chip.classList.add('is-active');
      activeChipFilter = chip.dataset.filter || 'all';
      if (cuisineSelect && activeChipFilter !== 'all' && !['popular', 'top-rated', 'fastest'].includes(activeChipFilter)) {
        // Sync dropdown if matching cuisine
        Array.from(cuisineSelect.options).forEach(opt => {
          if (opt.value.toLowerCase() === activeChipFilter.toLowerCase()) {
            cuisineSelect.value = opt.value;
          }
        });
      }
      applyFilter();
    });
  });
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
    return MENU_ITEMS.find(i => String(i.id) === String(id));
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
      const item = MENU_ITEMS.find(i => String(i.id) === String(id));
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
    
    // Set hidden cart data field and submit form to backend
    let cartInput = document.getElementById('cartDataInput');
    if (!cartInput) {
      cartInput = document.createElement('input');
      cartInput.type = 'hidden';
      cartInput.name = 'cart_data';
      cartInput.id = 'cartDataInput';
      form.appendChild(cartInput);
    }
    cartInput.value = JSON.stringify(cart);

    // Clear cart in localStorage
    writeCart({});
    
    form.submit();
  });

  renderSummary();
})();

/* -----------------------------------------------------------
   In-Menu Dish Search (menu.php)
----------------------------------------------------------- */
(function initMenuSearch() {
  const searchInput = document.getElementById('menuItemSearch');
  const items = document.querySelectorAll('.menu-item');
  if (!searchInput || !items.length) return;

  searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim().toLowerCase();
    items.forEach(item => {
      const name = item.querySelector('.menu-item__name')?.textContent.toLowerCase() || '';
      const desc = item.querySelector('.menu-item__desc')?.textContent.toLowerCase() || '';
      const matches = name.includes(query) || desc.includes(query);
      item.hidden = !matches;
    });
  });
})();

