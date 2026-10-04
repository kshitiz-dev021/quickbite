document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.frontend-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      const password = form.querySelector('[name="password"]');
      const confirm = form.querySelector('[name="confirm_password"]');

      if (password && confirm && password.value !== confirm.value) {
        alert('Passwords do not match.');
        confirm.focus();
        return;
      }

      alert('This is a frontend prototype. Backend functionality will be connected later.');
    });
  });
});

function frontendMessage(message) {
  alert(message);
  return false;
}
