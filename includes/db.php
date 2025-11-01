<?php
// includes/db.php
// Centraliza la conexión PDO a MySQL para que esté disponible como $pdo

declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'grup_miunikids';
$DB_USER = 'grup_admin';
$DB_PASS = 'miuni123';

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $DB_HOST, $DB_NAME);

$options = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
	// En producción, no exponer detalles. Registrar en logs y mostrar mensaje genérico.
	error_log('DB connection failed: ' . $e->getMessage());
	http_response_code(500);
	die('Database connection error.');
}

?>

