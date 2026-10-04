<?php
/**
 * Authentication and authorization middleware.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']['id']);
}

function has_role($roles): bool
{
    if (!is_logged_in()) {
        return false;
    }
    $userRole = $_SESSION['user']['role'] ?? '';
    if (is_array($roles)) {
        return in_array($userRole, $roles, true);
    }
    return $userRole === $roles;
}

function require_login(string $redirect = 'login.php'): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        header('Location: ' . $redirect);
        exit;
    }
}

function require_role($roles, string $redirect = 'login.php'): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to access this area.');
        header('Location: ' . $redirect);
        exit;
    }

    if (!has_role($roles)) {
        set_flash('error', 'You do not have permission to access that area.');
        // Redirect according to their actual role
        $role = $_SESSION['user']['role'] ?? 'customer';
        if ($role === 'admin') {
            header('Location: /quickbite/admin/overview.php');
        } elseif ($role === 'vendor') {
            header('Location: /quickbite/vendor/dashboard.php');
        } else {
            header('Location: /quickbite/index.php');
        }
        exit;
    }
}

function login_user(array $user): void
{
    $_SESSION['user'] = [
        'id'    => (int)$user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? '',
        'role'  => $user['role'],
    ];

    // If vendor, also cache vendor profile id and shop name
    if ($user['role'] === 'vendor') {
        $vendor = get_vendor_for_user((int)$user['id']);
        if ($vendor) {
            $_SESSION['user']['vendor_id'] = (int)$vendor['id'];
            $_SESSION['user']['vendor_name'] = $vendor['name'];
            $_SESSION['user']['vendor_status'] = $vendor['status'];
        }
    }
}

function logout_user(): void
{
    $_SESSION['user'] = null;
    unset($_SESSION['user']);
    session_destroy();
}

function get_vendor_for_user(int $userId): ?array
{
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM vendors WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $vendor = $stmt->fetch();
    return $vendor ?: null;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type, // 'error', 'success', 'info'
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function render_flash(): string
{
    $flash = get_flash();
    if (!$flash) {
        return '';
    }
    $type = htmlspecialchars($flash['type']);
    $msg  = htmlspecialchars($flash['message']);
    return "<div class=\"alert alert--{$type}\">{$msg}</div>";
}
