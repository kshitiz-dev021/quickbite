<?php
$pageTitle = 'Sign Up — QuickBite';
$activeNav = '';
$basePath = '';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mailer.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$name = '';
$email = '';
$phone = '';
$role = 'customer';
$shop_name = '';
$cuisine = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role      = in_array($_POST['role'] ?? '', ['customer', 'vendor']) ? $_POST['role'] : 'customer';
    $name      = trim($_POST['name'] ?? '');
    $email     = trim(strtolower($_POST['email'] ?? ''));
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $shop_name = trim($_POST['shop_name'] ?? '');
    $cuisine   = trim($_POST['cuisine'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif ($role === 'vendor' && (empty($shop_name) || empty($cuisine))) {
        $error = 'Shop Name and Cuisine are required for vendor registration.';
    } else {
        $db = get_db();
        // Check if an active account with this email already exists
        $stmt = $db->prepare("SELECT id, status FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if ($existing && $existing['status'] === 'active') {
            $error = 'An active account already exists with this email address. Please log in.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            if ($existing && $existing['status'] === 'pending') {
                // Update pending record
                $userId = $existing['id'];
                $upd = $db->prepare("UPDATE users SET name = ?, phone = ?, password_hash = ?, role = ? WHERE id = ?");
                $upd->execute([$name, $phone, $passwordHash, $role, $userId]);
            } else {
                // Insert new pending user
                $ins = $db->prepare("INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $ins->execute([$name, $email, $phone, $passwordHash, $role]);
                $userId = (int)$db->lastInsertId();
            }

            // If vendor, update or insert vendors table
            if ($role === 'vendor') {
                $vCheck = $db->prepare("SELECT id FROM vendors WHERE user_id = ?");
                $vCheck->execute([$userId]);
                if ($vCheck->fetch()) {
                    $vUpd = $db->prepare("UPDATE vendors SET name = ?, cuisine = ?, status = 'pending' WHERE user_id = ?");
                    $vUpd->execute([$shop_name, $cuisine, $userId]);
                } else {
                    $vIns = $db->prepare("INSERT INTO vendors (user_id, name, cuisine, status) VALUES (?, ?, ?, 'pending')");
                    $vIns->execute([$userId, $shop_name, $cuisine]);
                }
            }

            // Generate OTP and send via Google SMTP
            $otp = generate_and_save_otp($email, 'register');
            $mailRes = send_otp_email($email, $otp, 'register');

            $_SESSION['pending_otp_email'] = $email;

            $flashMsg = "Registration initiated! We've sent a 6-digit verification OTP to {$email}.";
            if (!$mailRes['success']) {
                $flashMsg .= " (Dev Note: {$mailRes['message']} — Your OTP is: <strong>{$otp}</strong>)";
            }
            set_flash('info', $flashMsg);

            header('Location: verify_otp.php?purpose=register');
            exit;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="auth-card" style="max-width: 480px;">
    <img class="auth-card__brand" src="images/logo.png" alt="QuickBite">

    <h1 class="auth-card__title">Create your account</h1>
    <p class="auth-card__subtitle">Join QuickBite and start exploring or selling local food.</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="frontend-form" action="signup.php" method="post">
      <label class="field">
        <span class="field__label">I want to register as:</span>
        <select class="field__input" name="role" id="roleSelect" onchange="toggleVendorFields(this.value)">
          <option value="customer" <?= $role === 'customer' ? 'selected' : '' ?>>Customer (Food Lover)</option>
          <option value="vendor" <?= $role === 'vendor' ? 'selected' : '' ?>>Vendor (Shop / Restaurant Owner)</option>
        </select>
      </label>

      <label class="field">
        <span class="field__label">Full name</span>
        <input class="field__input" type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Your full name" required>
      </label>

      <div id="vendorFields" style="<?= $role === 'vendor' ? '' : 'display: none;' ?>">
        <label class="field">
          <span class="field__label">Restaurant / Shop Name</span>
          <input class="field__input" type="text" name="shop_name" id="shopNameInput" value="<?= htmlspecialchars($shop_name) ?>" placeholder="e.g. Kathmandu Burger Hub">
        </label>
        <label class="field">
          <span class="field__label">Cuisine Type</span>
          <input class="field__input" type="text" name="cuisine" id="cuisineInput" value="<?= htmlspecialchars($cuisine) ?>" placeholder="e.g. Burgers, Fast Food, Beverages">
        </label>
      </div>

      <label class="field">
        <span class="field__label">Email address</span>
        <input class="field__input" type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="you@example.com" required>
      </label>

      <label class="field">
        <span class="field__label">Phone number</span>
        <input class="field__input" type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="98XXXXXXXX" required>
      </label>

      <div class="field-pair">
        <label class="field">
          <span class="field__label">Password</span>
          <input class="field__input" type="password" name="password" placeholder="At least 6 characters" required>
        </label>

        <label class="field">
          <span class="field__label">Confirm password</span>
          <input class="field__input" type="password" name="confirm_password" placeholder="Repeat password" required>
        </label>
      </div>

      <button class="btn btn--primary btn--block" type="submit">Create Account &amp; Send OTP</button>
    </form>

    <div class="auth-divider">already registered?</div>

    <p class="auth-switch">
      <a class="auth-link" href="login.php">Back to Login</a>
    </p>
  </div>
</section>

<script>
function toggleVendorFields(role) {
  var vFields = document.getElementById('vendorFields');
  var shopName = document.getElementById('shopNameInput');
  var cuisine = document.getElementById('cuisineInput');
  if (role === 'vendor') {
    vFields.style.display = 'block';
    shopName.required = true;
    cuisine.required = true;
  } else {
    vFields.style.display = 'none';
    shopName.required = false;
    cuisine.required = false;
  }
}
</script>

<?php
$pageScripts = ['js/auth.js'];
include __DIR__ . '/includes/footer.php';
?>
