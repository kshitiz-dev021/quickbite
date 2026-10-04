/**
 * vendor.js — small interactions on the vendor dashboard pages.
 */

/* Chip-style filters above a data table (menu-items.php, orders.php) */
document.querySelectorAll('.chip-row').forEach(row => {
  const chips = row.querySelectorAll('.chip');
  const table = row.nextElementSibling;
  if (!table || !table.classList.contains('data-table')) return;

  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      chips.forEach(c => c.classList.remove('is-active'));
      chip.classList.add('is-active');
      const filter = chip.dataset.filter;
      table.querySelectorAll('tbody tr').forEach(tr => {
        tr.hidden = filter && filter !== 'all' && tr.dataset.status !== filter;
      });
    });
  });
});

/* Upload dropzone label swap once a file is chosen */
const uploadInput = document.querySelector('.upload-box input[type="file"]');
if (uploadInput) {
  uploadInput.addEventListener('change', () => {
    const box = uploadInput.closest('.upload-box');
    const label = box.querySelector('.upload-box__text');
    if (label && uploadInput.files.length) {
      label.textContent = uploadInput.files[0].name;
    }
  });
}

/* Cancel/Save buttons on the add-item form are visual-only in this
   prototype — wire them up once the backend endpoint exists. */
const saveItemBtn = document.getElementById('saveItemBtn');
if (saveItemBtn) {
  saveItemBtn.addEventListener('click', () => {
    alert('Item saved (demo only — hook this up to the backend when it is ready).');
  });
}
