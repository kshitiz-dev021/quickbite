<?php
/**
 * Database connection and configuration helper.
 */

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $db_name = 'quickbite';
    $db_user = 'root';
    $db_pass = '';
    $db_host = '127.0.0.1';
    $db_port = 3306;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Try TCP connection first
    try {
        $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);
        return $pdo;
    } catch (PDOException $e1) {
        // Fallback to socket paths if TCP fails
        $sockets = [
            '/tmp/mysql.sock',
            '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'
        ];
        foreach ($sockets as $sock) {
            if (file_exists($sock)) {
                try {
                    $dsn = "mysql:unix_socket={$sock};dbname={$db_name};charset=utf8mb4";
                    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
                    return $pdo;
                } catch (PDOException $e2) {
                    continue;
                }
            }
        }
        // If still failed, rethrow informative error
        throw new PDOException("Could not connect to database 'quickbite' on 127.0.0.1:3306 or sockets: " . $e1->getMessage());
    }
}

/**
 * Retrieve setting from database.
 */
function get_setting(string $key, $default = null): ?string
{
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false) ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Update or insert a setting in database.
 */
function set_setting(string $key, string $value): bool
{
    try {
        $db = get_db();
        $stmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        return $stmt->execute([$key, $value]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Fetch all platform settings as an associative key => value array.
 */
function get_all_settings(): array
{
    try {
        $db = get_db();
        $stmt = $db->query("SELECT `key`, `value` FROM settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['key']] = $r['value'];
        }
        return $settings;
    } catch (Exception $e) {
        return [];
    }
}
