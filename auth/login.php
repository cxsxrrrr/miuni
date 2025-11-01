<?php
// auth/login.php - procesa el login de usuarios

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/db.php'; // expone $pdo

// Helper para limpiar cadenas
function clean_str(string $v): string {
	return trim($v);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../login.php');
	exit;
}

$email = isset($_POST['email']) ? filter_var(clean_str($_POST['email']), FILTER_VALIDATE_EMAIL) : false;
$password = isset($_POST['password']) ? (string)$_POST['password'] : '';

if (!$email || $password === '') {
	header('Location: ../login.php?error=missing');
	exit;
}

try {
	$stmt = $pdo->prepare('SELECT usuario_id, nombre, apellido, email, password FROM usuarios WHERE email = :email LIMIT 1');
	$stmt->execute([':email' => $email]);
	$user = $stmt->fetch();

	if (!$user || !password_verify($password, $user['password'])) {
		// Credenciales inválidas
		header('Location: ../login.php?error=cred');
		exit;
	}

	// Opcional: reforzar hash si el algoritmo cambió
	if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
		$newHash = password_hash($password, PASSWORD_DEFAULT);
		$upd = $pdo->prepare('UPDATE usuarios SET password = :p WHERE usuario_id = :id');
		$upd->execute([':p' => $newHash, ':id' => $user['usuario_id']]);
	}

	// Autenticated: set session
	$_SESSION['user_id'] = (int)$user['usuario_id'];
	$_SESSION['user_name'] = (string)$user['nombre'];
	$_SESSION['user_email'] = (string)$user['email'];

	// Redirigir a la zona de juegos (o dashboard)
	header('Location: ../juegos.php');
	exit;
} catch (PDOException $e) {
	error_log('Login error: ' . $e->getMessage());
	header('Location: ../login.php?error=server');
	exit;
}
?>