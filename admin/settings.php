<?php
$pageTitle = 'Settings — QuickBite Admin';
$activeTab = 'settings';
include __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/admin-header.php';

$db = get_db();

// Handle Platform Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $commission = max(0, min(100, (float)($_POST['commission_percent'] ?? 10)));
    $delivery   = max(0, (float)($_POST['delivery_fee'] ?? 40));

    set_setting('commission_percent', (string)$commission);
    set_setting('delivery_fee', (string)$delivery);

    set_flash('success', 'Platform settings saved successfully.');
    header('Location: settings.php');
    exit;
}

// Handle Google SMTP Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_smtp'])) {
    $host     = trim($_POST['smtp_host'] ?? 'smtp.gmail.com');
    if (empty($host) || strpos($host, '@') !== false) {
        $host = 'smtp.gmail.com';
    }
    $port     = trim($_POST['smtp_port'] ?? '587');
    $secure   = trim($_POST['smtp_secure'] ?? 'tls');
    $user     = trim($_POST['smtp_user'] ?? '');
    $pass     = trim($_POST['smtp_pass'] ?? '');
    $fromName = trim($_POST['smtp_from_name'] ?? 'QuickBite');

    set_setting('smtp_host', $host);
    set_setting('smtp_port', $port);
    set_setting('smtp_secure', $secure);
    set_setting('smtp_user', $user);
    if (!empty($pass) && strpos($pass, '•') === false) {
        set_setting('smtp_pass', str_replace(' ', '', $pass));
    }
    set_setting('smtp_from_name', $fromName);

    set_flash('success', 'Google SMTP credentials and settings updated.');
    header('Location: settings.php');
    exit;
}

$settings = get_all_settings();
$commission = $settings['commission_percent'] ?? '10';
$delivery   = $settings['delivery_fee'] ?? '40';
$smtpHost   = $settings['smtp_host'] ?? 'smtp.gmail.com';
$smtpPort   = $settings['smtp_port'] ?? '587';
$smtpSecure = $settings['smtp_secure'] ?? 'tls';
$smtpUser   = $settings['smtp_user'] ?? '';
$smtpPass   = $settings['smtp_pass'] ?? '';
$fromName   = $settings['smtp_from_name'] ?? 'QuickBite';
?>

<section class="dash-panel">
  <h1 class="dash-panel__title">Platform Settings</h1>
  <p class="dash-panel__subtitle">Delivery fees, commission rates, and Google SMTP email configuration.</p>

  <div class="settings-grid">
    <!-- Platform Settings Card -->
    <div class="settings-card">
      <h2 class="settings-card__title">Platform Fees</h2>
      <p class="settings-card__desc">Adjust default delivery charge and restaurant commission rate.</p>

      <form class="item-form item-form--card" method="post" action="settings.php">
        <input type="hidden" name="save_settings" value="1">
        <label class="field">
          <span class="field__label">Platform Commission (%)</span>
          <input class="field__input" type="number" step="0.5" name="commission_percent" value="<?= htmlspecialchars($commission) ?>" required>
        </label>
        <label class="field">
          <span class="field__label">Default Delivery Fee (Rs.)</span>
          <input class="field__input" type="number" step="1" name="delivery_fee" value="<?= htmlspecialchars($delivery) ?>" required>
        </label>
        <div class="item-form__actions">
          <button type="submit" class="btn btn--primary">Save Fee Settings</button>
        </div>
      </form>
    </div>

    <!-- Google SMTP Settings Card -->
    <div class="settings-card">
      <h2 class="settings-card__title">Google SMTP Configuration</h2>
      <p class="settings-card__desc">
        Used to send 6-digit OTP codes for account registration and password resets.
      </p>

      <form class="item-form item-form--card" method="post" action="settings.php">
        <input type="hidden" name="save_smtp" value="1">
        
        <div class="field-pair">
          <label class="field">
            <span class="field__label">SMTP Host</span>
            <input class="field__input" name="smtp_host" value="<?= htmlspecialchars($smtpHost) ?>" required>
          </label>
          <label class="field">
            <span class="field__label">Port</span>
            <input class="field__input" name="smtp_port" value="<?= htmlspecialchars($smtpPort) ?>" required>
          </label>
        </div>

        <div class="field-pair">
          <label class="field">
            <span class="field__label">Encryption</span>
            <select class="field__input" name="smtp_secure">
              <option value="tls" <?= $smtpSecure === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS - 587)</option>
              <option value="ssl" <?= $smtpSecure === 'ssl' ? 'selected' : '' ?>>SSL (Direct - 465)</option>
            </select>
          </label>
          <label class="field">
            <span class="field__label">Sender Name</span>
            <input class="field__input" name="smtp_from_name" value="<?= htmlspecialchars($fromName) ?>" required>
          </label>
        </div>

        <label class="field">
          <span class="field__label">Google / Gmail Address (Username)</span>
          <input class="field__input" type="email" name="smtp_user" value="<?= htmlspecialchars($smtpUser) ?>" placeholder="your-email@gmail.com" required>
        </label>

        <label class="field">
          <span class="field__label">Google App Password (16 characters)</span>
          <input class="field__input" type="password" name="smtp_pass" value="<?= !empty($smtpPass) ? '••••••••••••••••' : '' ?>" placeholder="e.g. abcd efgh ijkl mnop">
          <small style="font-size: 0.75rem; color: #8C7B70; display: block; margin-top: 0.25rem;">
            Generate an App Password at <a href="https://myaccount.google.com/apppasswords" target="_blank" style="color: var(--color-brand); text-decoration: underline;">myaccount.google.com/apppasswords</a>.
          </small>
        </label>

        <div class="item-form__actions">
          <button type="submit" class="btn btn--primary">Save SMTP Settings</button>
        </div>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
