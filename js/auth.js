document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.frontend-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      const password = form.querySelector('[name="password"]');
      const confirm = form.querySelector('[name="confirm_password"]');

      if (password && confirm && password.value !== confirm.value) {
        event.preventDefault();
        alert('Passwords do not match. Please verify your passwords.');
        confirm.focus();
        return false;
      }
    });
  });
});

function frontendMessage(message) {
  alert(message);
  return false;
}
