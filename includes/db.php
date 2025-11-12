<?php
declare(strict_types=1);
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
// Allow overriding the port via environment variable (useful for different envs / tunnels).
// Default to 3306 which is the standard MySQL port.
$DB_PORT = (int) (getenv('DB_PORT') ?: 3306);
$DB_NAME = 'grup_miunikids';
$DB_USER = 'grup_admin';
$DB_PASS = 'miuni123';
$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $DB_HOST, $DB_PORT, $DB_NAME);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection error.');
}
?>

