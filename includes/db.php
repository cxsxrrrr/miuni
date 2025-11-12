<?php
declare(strict_types=1);
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = (int) (getenv('DB_PORT') ?: 3306);
$DB_SOCKET = getenv('DB_SOCKET') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'grup_miunikids';
$DB_USER = getenv('DB_USER') ?: 'grup_admin';
$DB_PASS = getenv('DB_PASS') ?: 'miuni123';

if ($DB_SOCKET) {
    $dsn = sprintf('mysql:unix_socket=%s;dbname=%s;charset=utf8mb4', $DB_SOCKET, $DB_NAME);
} else {
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
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection error.');
}
?>
<?php
declare(strict_types=1);
$DB_HOST = '127.0.0.1';
$DB_NAME = 'grup_miunikids';
$DB_USER = 'grup_admin';
$DB_PASS = 'miuni123';

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $DB_HOST, 3306, $DB_NAME);
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

