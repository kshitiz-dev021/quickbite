<?php
$pageTitle = 'Forgot Password — QuickBite';
$activeNav = '';
$basePath = '';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mailer.php';

$error = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $emailValue = $email;

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $db = get_db();
        $stmt = $db->prepare("SELECT id, name, status FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'No account found with this email address. Please check and try again.';
        } elseif ($user['status'] === 'blocked') {
            $error = 'This account has been suspended. Please contact QuickBite support.';
        } else {
            // Generate OTP for password reset
            $otp = generate_and_save_otp($email, 'forgot_password');
            $mailRes = send_otp_email($email, $otp, 'forgot_password');

            $_SESSION['reset_email'] = $email;

            $flashMsg = "A 6-digit password reset OTP has been sent to {$email}.";
            if (!$mailRes['success']) {
                $flashMsg .= " (Dev Note: {$mailRes['message']} — Your reset OTP is: <strong>{$otp}</strong>)";
            }
            set_flash('info', $flashMsg);

            header('Location: verify_otp.php?purpose=forgot_password');
            exit;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="auth-card" style="max-width: 440px;">
    <img class="auth-card__brand" src="images/logo.png" alt="QuickBite">

    <h1 class="auth-card__title">Reset Password</h1>
    <p class="auth-card__subtitle">Enter your registered email and we'll send you an OTP to reset your password.</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="frontend-form" action="forgot_password.php" method="post">
      <label class="field">
        <span class="field__label">Email address</span>
        <input class="field__input" type="email" name="email" value="<?= htmlspecialchars($emailValue) ?>" placeholder="you@example.com" required autofocus>
      </label>

      <button class="btn btn--primary btn--block" type="submit">Send Reset OTP</button>
    </form>

    <div class="auth-divider">or</div>

    <p class="auth-switch">
      Remembered your password?
      <a class="auth-link" href="login.php">Back to Login</a>
    </p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
