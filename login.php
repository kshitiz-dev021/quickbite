<?php
$pageTitle = 'Login — QuickBite';
$demoActive = 'customer';
$basePath = '';
include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="auth-card">
    <img class="auth-card__brand" src="images/logo.png" alt="QuickBite">

    <h1 class="auth-card__title">Welcome back</h1>
    <p class="auth-card__subtitle">Log in to continue to QuickBite.</p>

    <form class="frontend-form" action="#" method="post">
      <label class="field">
        <span class="field__label">Email address</span>
        <input class="field__input" type="email" name="email" placeholder="you@example.com" required>
      </label>

      <label class="field">
        <span class="field__label">Password</span>
        <input class="field__input" type="password" name="password" placeholder="Enter your password" required>
      </label>

      <div class="auth-options">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a class="auth-link" href="#" onclick="return frontendMessage('Password recovery will be connected later.')">Forgot password?</a>
      </div>

      <button class="btn btn--primary btn--block" type="submit">Login</button>
    </form>

    <div class="auth-divider">or</div>

    <p class="auth-switch">
      Don't have an account?
      <a class="auth-link" href="signup.php">Create an account</a>
    </p>
  </div>
</section>

<?php
$pageScripts = ['js/auth.js'];
include __DIR__ . '/includes/footer.php';
?>
