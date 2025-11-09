<?php
// register.php - procesa el registro de usuarios (endpoint)
// Espera POST: nombre, apellido, email, password, confirm_password

declare(strict_types=1);
session_start();

require_once __DIR__ . '/../includes/db.php';

function clean(string $v): string {
    return trim($v);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registro.php');
    exit;
}

$errors = [];
$old = [];

$old['nombre'] = clean($_POST['nombre'] ?? '');
$old['apellido'] = clean($_POST['apellido'] ?? '');
$old['email'] = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm_password = (string)($_POST['confirm_password'] ?? '');

// Validaciones server-side
if ($old['nombre'] === '') {
    $errors[] = 'El nombre es obligatorio.';
}
if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Correo electrónico obligatorio o inválido.';
} else {
    $old['email'] = strtolower($old['email']);
}
if (strlen($password) < 6) {
    $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
}
if ($password !== $confirm_password) {
    $errors[] = 'Las contraseñas no coinciden.';
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = $old;
    header('Location: ../registro.php');
    exit;
}

try {
    // comprobar existencia de email
    $stmt = $pdo->prepare('SELECT usuario_id FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $old['email']]);
    if ($stmt->fetch()) {
        $_SESSION['register_errors'] = ['El correo ya está registrado. Intenta iniciar sesión o usa otro correo.'];
        $_SESSION['register_old'] = $old;
        header('Location: ../registro.php');
        exit;
    }

    // insertar usuario
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, password) VALUES (:nombre, :apellido, :email, :password)');
    $insert->execute([
        ':nombre' => $old['nombre'],
        ':apellido' => $old['apellido'],
        ':email' => $old['email'],
        ':password' => $hash
    ]);

    // éxito
    $_SESSION['registered'] = true;
    header('Location: ../index.php?registered=1');
    exit;
} catch (PDOException $e) {
    error_log('Register error: ' . $e->getMessage());
    $_SESSION['register_errors'] = ['Error al registrar el usuario. Intente de nuevo más tarde.'];
    $_SESSION['register_old'] = $old;
    header('Location: ../registro.php');
    exit;
}