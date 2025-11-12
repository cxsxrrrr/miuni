<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=grup_miunikids;charset=utf8mb4', 'grup_admin', 'miuni123', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo 'Conexión remota vía túnel OK';
} catch (PDOException $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}