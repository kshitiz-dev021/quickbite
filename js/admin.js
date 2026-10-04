/**
 * admin.js — interactions and mobile navigation on admin & vendor dashboards.
 */

document.addEventListener('DOMContentLoaded', () => {
  const dashNavToggle = document.getElementById('dashNavToggle');
  const dashSidebar = document.getElementById('dashSidebar');

  if (dashNavToggle && dashSidebar) {
    dashNavToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      dashSidebar.classList.toggle('is-open');
    });

    // Close menu when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (dashSidebar.classList.contains('is-open') && !dashSidebar.contains(e.target)) {
        dashSidebar.classList.remove('is-open');
      }
    });
  }
});

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
