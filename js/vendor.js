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
