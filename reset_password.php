<?php
$pageTitle = 'Set New Password — QuickBite';
$activeNav = '';
$basePath = '';
require_once __DIR__ . '/includes/auth.php';

$email = $_SESSION['otp_verified_email'] ?? '';
if (empty($email)) {
    set_flash('error', 'Please verify your OTP code first.');
    header('Location: forgot_password.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm)) {
        $error = 'Please fill in both password fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $db = get_db();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);

        unset($_SESSION['otp_verified_email']);
        set_flash('success', 'Your password has been reset successfully! Please log in with your new password.');
        header('Location: login.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="auth-card" style="max-width: 440px;">
    <img class="auth-card__brand" src="images/logo.png" alt="QuickBite">

    <h1 class="auth-card__title">Create New Password</h1>
    <p class="auth-card__subtitle">Choose a new password for <strong><?= htmlspecialchars($email) ?></strong></p>

    <?php if (!empty($error)): ?>
      <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="frontend-form" action="reset_password.php" method="post">
      <label class="field">
        <span class="field__label">New Password</span>
        <input class="field__input" type="password" name="password" placeholder="At least 6 characters" required autofocus>
      </label>

      <label class="field">
        <span class="field__label">Confirm New Password</span>
        <input class="field__input" type="password" name="confirm_password" placeholder="Repeat new password" required>
      </label>

      <button class="btn btn--primary btn--block" type="submit">Update Password</button>
    </form>
  </div>
</section>

<?php
$pageScripts = ['js/auth.js'];
include __DIR__ . '/includes/footer.php';
?>
