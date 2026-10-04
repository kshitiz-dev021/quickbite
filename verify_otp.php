<?php
$pageTitle = 'Verify OTP — QuickBite';
$activeNav = '';
$basePath = '';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mailer.php';

$purpose = in_array($_GET['purpose'] ?? '', ['register', 'forgot_password']) ? $_GET['purpose'] : 'register';

if ($purpose === 'register') {
    $email = $_SESSION['pending_otp_email'] ?? trim($_GET['email'] ?? '');
} else {
    $email = $_SESSION['reset_email'] ?? trim($_GET['email'] ?? '');
}

if (empty($email)) {
    set_flash('error', 'No pending verification found. Please start the process again.');
    header('Location: ' . ($purpose === 'register' ? 'signup.php' : 'forgot_password.php'));
    exit;
}

$error = '';
$success = '';

// Handle Resend Request
if (isset($_POST['action']) && $_POST['action'] === 'resend') {
    $newOtp = generate_and_save_otp($email, $purpose);
    $res = send_otp_email($email, $newOtp, $purpose);
    if ($res['success']) {
        $success = "A fresh 6-digit OTP code has been sent to {$email}.";
    } else {
        $success = "A new OTP was generated. (Dev Note: {$res['message']} — Your code is: {$newOtp})";
    }
}

// Handle Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $enteredOtp = trim($_POST['otp'] ?? '');
    if (empty($enteredOtp)) {
        $error = 'Please enter the 6-digit verification code.';
    } else {
        $res = verify_otp($email, $enteredOtp, $purpose);
        if ($res['success']) {
            $db = get_db();
            if ($purpose === 'register') {
                // Activate user
                $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE email = ?");
                $stmt->execute([$email]);

                // Check role
                $uStmt = $db->prepare("SELECT role, name FROM users WHERE email = ?");
                $uStmt->execute([$email]);
                $u = $uStmt->fetch();

                if ($u && $u['role'] === 'vendor') {
                    set_flash('success', 'Email verified successfully! Your restaurant profile is under review by QuickBite Admin. You can now log in.');
                } else {
                    set_flash('success', 'Email verified successfully! Your account is now active. Please log in.');
                }
                unset($_SESSION['pending_otp_email']);
                header('Location: login.php');
                exit;
            } elseif ($purpose === 'forgot_password') {
                $_SESSION['otp_verified_email'] = $email;
                unset($_SESSION['reset_email']);
                header('Location: reset_password.php');
                exit;
            }
        } else {
            $error = $res['message'];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="auth-card" style="max-width: 440px;">
    <img class="auth-card__brand" src="images/logo.png" alt="QuickBite">

    <h1 class="auth-card__title">Verify OTP Code</h1>
    <p class="auth-card__subtitle">
      We sent a 6-digit verification code to<br>
      <strong><?= htmlspecialchars($email) ?></strong>
    </p>

    <?php if (!empty($error)): ?>
      <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert--success"><?= $success ?></div>
    <?php endif; ?>

    <form action="verify_otp.php?purpose=<?= urlencode($purpose) ?>" method="post">
      <label class="field">
        <span class="field__label">Enter 6-Digit OTP</span>
        <input class="field__input" type="text" name="otp" maxlength="6" pattern="\d{6}" 
               placeholder="123456" style="font-size: 1.5rem; letter-spacing: 6px; text-align: center; font-weight: 700;" required autofocus autocomplete="one-time-code">
      </label>

      <button class="btn btn--primary btn--block" type="submit">Verify &amp; Continue</button>
    </form>

    <div class="auth-divider">didn't receive the code?</div>

    <form action="verify_otp.php?purpose=<?= urlencode($purpose) ?>" method="post" style="text-align: center;">
      <input type="hidden" name="action" value="resend">
      <button type="submit" class="btn btn--outline btn--sm" style="margin: 0 auto;">Resend OTP</button>
    </form>

    <p class="auth-switch" style="margin-top: 1.5rem;">
      <a class="auth-link" href="<?= $purpose === 'register' ? 'signup.php' : 'login.php' ?>">Start Over</a>
    </p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
