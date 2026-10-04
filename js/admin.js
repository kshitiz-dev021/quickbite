/**
 * admin.js — small interactions on the admin dashboard pages.
 */

document.addEventListener('click', (e) => {
  // If button was clicked (not a normal link)
  if (e.target.tagName === 'BUTTON') {
    if (e.target.classList.contains('table-action-btn--approve')) {
      const cell = e.target.closest('[data-status-cell]');
      if (cell) cell.innerHTML = status_pill_html('approved');
    }
    if (e.target.classList.contains('table-action-btn--reject')) {
      const cell = e.target.closest('[data-status-cell]');
      if (cell) cell.innerHTML = '<span class="status-pill status-pill--cancelled">Rejected</span>';
      const row = e.target.closest('tr');
      if (row) row.style.opacity = '0.55';
    }
  }
});

function status_pill_html(status) {
  const label = status.charAt(0).toUpperCase() + status.slice(1);
  return `<span class="status-pill status-pill--${status}">${label}</span>`;
}
