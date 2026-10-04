<?php
$pageTitle = 'Sign Up — QuickBite';
$demoActive = 'customer';
$basePath = '';
include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="auth-card">
    <img class="auth-card__brand" src="images/logo.png" alt="QuickBite">

    <h1 class="auth-card__title">Create your account</h1>
    <p class="auth-card__subtitle">Join QuickBite and start exploring local food.</p>

    <form class="frontend-form" action="#" method="post">
      <label class="field">
        <span class="field__label">Full name</span>
        <input class="field__input" type="text" name="name" placeholder="Your full name" required>
      </label>

      <label class="field">
        <span class="field__label">Email address</span>
        <input class="field__input" type="email" name="email" placeholder="you@example.com" required>
      </label>

      <label class="field">
        <span class="field__label">Phone number</span>
        <input class="field__input" type="tel" name="phone" placeholder="98XXXXXXXX" required>
      </label>

      <label class="field">
        <span class="field__label">Password</span>
        <input class="field__input" type="password" name="password" placeholder="Create a password" required>
      </label>

      <label class="field">
        <span class="field__label">Confirm password</span>
        <input class="field__input" type="password" name="confirm_password" placeholder="Confirm your password" required>
      </label>

      <button class="btn btn--primary btn--block" type="submit">Create Account</button>
    </form>

    <div class="auth-divider">already registered?</div>

    <p class="auth-switch">
      <a class="auth-link" href="login.php">Back to Login</a>
    </p>
  </div>
</section>

<?php
$pageScripts = ['js/auth.js'];
include __DIR__ . '/includes/footer.php';
?>
