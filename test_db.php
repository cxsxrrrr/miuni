<?php
declare(strict_types=1);
// tools/db_check.php
// Small, temporary script to test DB connection using the same includes/db.php config.
// Usage: upload to your server under the project and request it once via browser,
// then check the generated tools/db_check.log for full details. Remove after use.

ini_set('display_errors', '0');
$logFile = __DIR__ . '/db_check.log';

try {
    // Attempt to reuse the project's DB bootstrap
    require_once __DIR__ . '/../includes/db.php';

    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new RuntimeException('PDO instance not available from includes/db.php');
    }

    // quick test query
    $stmt = $pdo->query('SELECT 1');
    $val = $stmt ? $stmt->fetchColumn() : null;

    file_put_contents($logFile, date('c') . " - OK: test query result: " . var_export($val, true) . PHP_EOL, FILE_APPEND);
    echo 'DB_OK';
    exit;

} catch (Throwable $e) {
    // Log full error details for debugging (not shown to browser)
    $msg = date('c') . ' - ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    @file_put_contents($logFile, $msg, FILE_APPEND);

    http_response_code(500);
    echo 'DB_ERROR (details logged to tools/db_check.log)';
    exit;
}
