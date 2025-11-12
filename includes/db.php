<?php

declare(strict_types=1);

// Database credentials (override with environment variables in production)
$DB_NAME = getenv('DB_NAME') ?: 'grup_miunikids';
$DB_USER = getenv('DB_USER') ?: 'grup_admin';
$DB_PASS = getenv('DB_PASS') ?: 'miuni123';

// Prefer a unix socket connection when available. Allow override with DB_SOCKET.
$envSocket = getenv('DB_SOCKET') ?: '';
$commonSockets = [
    $envSocket,
    '/run/mysqld/mysqld.sock',
    '/var/run/mysqld/mysqld.sock',
    '/tmp/mysql.sock',
];
$dbSocket = '';
foreach ($commonSockets as $s) {
    if (!$s) continue;
    if (file_exists($s)) { $dbSocket = $s; break; }
}

if ($dbSocket) {
    // connect using unix socket
    $dsn = sprintf('mysql:unix_socket=%s;dbname=%s;charset=utf8mb4', $dbSocket, $DB_NAME);
} else {
    // fallback to TCP (allow overriding host/port via env vars)
    $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
    $DB_PORT = (int) (getenv('DB_PORT') ?: 3306);
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $DB_HOST, $DB_PORT, $DB_NAME);
}

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Log detailed error server-side, but show generic message to users
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection error.');
}

?>

