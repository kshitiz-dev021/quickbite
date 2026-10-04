/**
 * main.js
 * Runs on every page. Handles the bits of chrome that are shared across
 * the whole site: the cart badge in the header, and the search panel
 * toggle. Cart contents live in localStorage (key: CART_KEY) so they
 * survive a normal page navigation between menu.php -> cart.php ->
 * checkout.php. Once there's a real backend this should move to a
 * session/DB-backed cart instead.
 */

const CART_KEY = 'quickbite_cart';

function readCart() {
  try {
    return JSON.parse(localStorage.getItem(CART_KEY)) || {};
  } catch (err) {
    return {};
  }
}

function writeCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartBadge();
}

function cartItemCount(cart) {
  return Object.values(cart).reduce((sum, qty) => sum + qty, 0);
}

function updateCartBadge() {
  const cart = readCart();
  const count = cartItemCount(cart);

  const badge = document.getElementById('cartBadge');
  if (badge) {
    badge.textContent = count;
  }

  const floatingBar = document.getElementById('floatingCartBar');
  const floatingCount = document.getElementById('floatingCartCount');
  if (floatingBar && floatingCount) {
    floatingCount.textContent = `${count} item${count === 1 ? '' : 's'}`;
    if (count > 0) {
      floatingBar.classList.add('is-visible');
    } else {
      floatingBar.classList.remove('is-visible');
    }
  }
}

/* Search panel toggle (only present on customer pages) */
const searchToggle = document.getElementById('searchToggle');
const searchPanel = document.getElementById('searchPanel');
if (searchToggle && searchPanel) {
  searchToggle.addEventListener('click', () => {
    searchPanel.classList.toggle('is-open');
    if (searchPanel.classList.contains('is-open')) {
      searchPanel.querySelector('input').focus();
    }
  });
}

updateCartBadge();
