<?php
declare(strict_types=1);
// Temporary debug endpoint to inspect socket detection and DSN used by the app.
// Safe: does not print DB password. Remove after debugging.

ini_set('display_errors', '1');
header('Content-Type: application/json');

$DB_NAME = getenv('DB_NAME') ?: 'grup_miunikids';
$DB_USER = getenv('DB_USER') ?: 'grup_admin';
$envSocket = getenv('DB_SOCKET') ?: '';
$commonSockets = [$envSocket, '/run/mysqld/mysqld.sock', '/var/run/mysqld/mysqld.sock', '/tmp/mysql.sock'];

$results = [];
$chosen = '';
foreach ($commonSockets as $s) {
    if (!$s) continue;
    $exists = file_exists($s);
    $readable = $exists ? is_readable($s) : false;
    $perms = $exists ? sprintf('%04o', fileperms($s) & 07777) : null;
    $owner = null;
    if ($exists && function_exists('posix_getpwuid')) {
        $pw = @posix_getpwuid(@fileowner($s));
        $owner = $pw['name'] ?? @fileowner($s);
    }
    $results[] = ['path' => $s, 'exists' => $exists, 'readable' => $readable, 'perms' => $perms, 'owner' => $owner];
    if ($exists && !$chosen) $chosen = $s;
}

$info = ['sockets' => $results, 'chosen_socket' => $chosen];

if ($chosen) {
    $dsn = sprintf('mysql:unix_socket=%s;dbname=%s;charset=utf8mb4', $chosen, $DB_NAME);
} else {
    $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
    $DB_PORT = (int)(getenv('DB_PORT') ?: 3306);
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $DB_HOST, $DB_PORT, $DB_NAME);
}

$info['dsn'] = $dsn;
$info['php_user'] = function_exists('posix_getpwuid') ? @posix_getpwuid(posix_geteuid())['name'] ?? null : null;

// Try to connect (will use DB_PASS from env or default). We intentionally
// do not echo the password.
$DB_PASS = getenv('DB_PASS') ?: 'miuni123';
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $info['connection'] = 'ok';
} catch (Throwable $e) {
    $info['connection'] = 'error';
    $info['error'] = $e->getMessage();
}

echo json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
