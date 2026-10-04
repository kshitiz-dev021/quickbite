<?php
/**
 * Google SMTP Mailer and OTP Management for QuickBite.
 */

require_once __DIR__ . '/db.php';

class QuickBiteMailer
{
    /**
     * Send an email using SMTP (Google Gmail or any SMTP server).
     *
     * @param string $to Email address of recipient
     * @param string $subject Email subject
     * @param string $htmlBody HTML content
     * @param array $customConfig Optional overrides for SMTP configuration
     * @return array ['success' => bool, 'message' => string]
     */
    public static function send(string $to, string $subject, string $htmlBody, array $customConfig = []): array
    {
        $settings = get_all_settings();

        $host     = $customConfig['smtp_host']      ?? $settings['smtp_host']      ?? 'smtp.gmail.com';
        if (empty($host) || strpos($host, '@') !== false || substr(strtolower($host), -9) === 'gmail.com') {
            $host = 'smtp.gmail.com';
        }
        $port     = (int)($customConfig['smtp_port'] ?? $settings['smtp_port']      ?? 587);
        $secure   = $customConfig['smtp_secure']    ?? $settings['smtp_secure']    ?? 'tls';
        $user     = trim($customConfig['smtp_user'] ?? $settings['smtp_user'] ?? '');
        $pass     = str_replace(' ', '', trim($customConfig['smtp_pass'] ?? $settings['smtp_pass'] ?? ''));
        $fromName = $customConfig['smtp_from_name'] ?? $settings['smtp_from_name'] ?? 'QuickBite';
        $fromEmail = !empty($user) ? $user : 'no-reply@quickbite.com';

        // Check if credentials are provided
        if (empty($user) || empty($pass)) {
            return [
                'success' => false,
                'message' => 'Google SMTP credentials (email or app password) are not configured. You can configure them in Admin > Settings.'
            ];
        }

        $timeout = 15;
        $remoteHost = ($secure === 'ssl' || $port === 465) ? "ssl://{$host}" : "tcp://{$host}";

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client($remoteHost . ":{$port}", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            return [
                'success' => false,
                'message' => "Could not connect to SMTP server ({$remoteHost}:{$port}): {$errstr} ({$errno})"
            ];
        }

        stream_set_timeout($socket, $timeout);

        $readResponse = function() use ($socket) {
            $response = '';
            while ($line = fgets($socket, 512)) {
                $response .= $line;
                // SMTP response lines start with 3 digits. If the 4th char is space, it's the final line.
                if (preg_match('/^\d{3}\s/', $line)) {
                    break;
                }
            }
            return $response;
        };

        $sendCommand = function(string $cmd, string $expectedCode) use ($socket, $readResponse) {
            fputs($socket, $cmd . "\r\n");
            $res = $readResponse();
            $code = substr($res, 0, 3);
            if ($code !== $expectedCode) {
                throw new Exception("SMTP Error: Expected {$expectedCode} but got {$code}. Server said: {$res}");
            }
            return $res;
        };

        try {
            // Read initial greeting
            $greeting = $readResponse();
            if (substr($greeting, 0, 3) !== '220') {
                throw new Exception("Invalid greeting: {$greeting}");
            }

            // Send EHLO
            $sendCommand("EHLO " . (gethostname() ?: 'localhost'), '250');

            // Handle STARTTLS
            if ($secure === 'tls' || $port === 587) {
                $sendCommand("STARTTLS", '220');
                $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if (!$crypto) {
                    throw new Exception("TLS negotiation failed with {$host}");
                }
                $sendCommand("EHLO " . (gethostname() ?: 'localhost'), '250');
            }

            // Authenticate
            $sendCommand("AUTH LOGIN", '334');
            $sendCommand(base64_encode($user), '334');
            $sendCommand(base64_encode($pass), '235');

            // Sender and Recipient
            $sendCommand("MAIL FROM:<{$fromEmail}>", '250');
            $sendCommand("RCPT TO:<{$to}>", '250');

            // Send Email Data
            $sendCommand("DATA", '354');

            $date = date('r');
            $boundary = md5(uniqid(time()));
            $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
            $headers .= "To: <{$to}>\r\n";
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $headers .= "Date: {$date}\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";

            // Clean lines for dot-stuffing
            $body = str_replace("\r\n.", "\r\n..", $htmlBody);

            $fullPayload = $headers . "\r\n" . $body . "\r\n.";
            fputs($socket, $fullPayload . "\r\n");
            $dataResponse = $readResponse();
            if (substr($dataResponse, 0, 3) !== '250') {
                throw new Exception("Message body rejected: {$dataResponse}");
            }

            // Quit
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return ['success' => true, 'message' => 'Email sent successfully via Google SMTP.'];
        } catch (Exception $e) {
            if (is_resource($socket)) {
                @fclose($socket);
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

/**
 * Generate a 6-digit OTP and store it in the database.
 */
function generate_and_save_otp(string $email, string $purpose = 'register'): string
{
    $db = get_db();
    $otp = (string) random_int(100000, 999999);

    // Invalidate/delete any previous OTPs for this email and purpose
    $del = $db->prepare("DELETE FROM otps WHERE email = ? AND purpose = ?");
    $del->execute([$email, $purpose]);

    // Insert new OTP with 10 minutes expiry
    $stmt = $db->prepare("INSERT INTO otps (email, otp, purpose, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $stmt->execute([$email, $otp, $purpose]);

    return $otp;
}

/**
 * Verify OTP against the database.
 */
function verify_otp(string $email, string $otp, string $purpose): array
{
    $db = get_db();
    $stmt = $db->prepare("
        SELECT id, expires_at, (expires_at >= NOW()) as is_valid 
        FROM otps 
        WHERE email = ? AND otp = ? AND purpose = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$email, trim($otp), $purpose]);
    $record = $stmt->fetch();

    if (!$record) {
        return ['success' => false, 'message' => 'Invalid OTP code. Please check and try again.'];
    }

    if (!$record['is_valid']) {
        return ['success' => false, 'message' => 'The OTP code has expired. Please request a new one.'];
    }

    // OTP is valid - delete it so it cannot be reused
    $del = $db->prepare("DELETE FROM otps WHERE id = ?");
    $del->execute([$record['id']]);

    return ['success' => true, 'message' => 'OTP verified successfully.'];
}

/**
 * Send an OTP email with QuickBite styling.
 */
function send_otp_email(string $email, string $otp, string $purpose = 'register'): array
{
    if ($purpose === 'register') {
        $subject = 'Your QuickBite Email Verification Code';
        $title = 'Welcome to QuickBite!';
        $subtitle = 'Please use the verification code below to confirm your email and complete your registration.';
    } else {
        $subject = 'QuickBite Password Reset Code';
        $title = 'Password Reset Request';
        $subtitle = 'We received a request to reset your QuickBite account password. Use this code to proceed:';
    }

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #F8F5F0; margin: 0; padding: 20px; color: #2B2118; }
  .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(43,33,24,0.06); border: 1px solid #E6DEC8; }
  .email-logo { text-align: center; margin-bottom: 24px; }
  .email-logo h1 { color: #A8432B; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
  .email-title { font-size: 20px; font-weight: 700; color: #2B2118; margin-top: 0; text-align: center; }
  .email-subtitle { font-size: 14px; color: #6E6259; line-height: 1.5; text-align: center; margin-bottom: 28px; }
  .otp-box { background: #FFF4E5; border: 2px dashed #A8432B; border-radius: 8px; text-align: center; padding: 18px; margin-bottom: 28px; }
  .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #A8432B; margin: 0; }
  .otp-expiry { font-size: 12px; color: #8C7B70; margin-top: 6px; }
  .email-footer { font-size: 12px; color: #A69C8C; text-align: center; line-height: 1.5; border-top: 1px solid #EFEAE1; padding-top: 20px; margin-top: 20px; }
</style>
</head>
<body>
  <div class="email-container">
    <div class="email-logo">
      <h1>QuickBite</h1>
    </div>
    <h2 class="email-title">{$title}</h2>
    <p class="email-subtitle">{$subtitle}</p>
    <div class="otp-box">
      <div class="otp-code">{$otp}</div>
      <div class="otp-expiry">Valid for 10 minutes</div>
    </div>
    <div class="email-footer">
      If you did not request this verification code, you can safely ignore this email.<br>
      &copy; QuickBite. Local food, instant delight.
    </div>
  </div>
</body>
</html>
HTML;

    $result = QuickBiteMailer::send($email, $subject, $html);

    // Save debug OTP in session for local environments where SMTP credentials haven't been configured yet
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    $_SESSION['debug_last_otp'] = $otp;
    $_SESSION['debug_last_otp_email'] = $email;

    return $result;
}
