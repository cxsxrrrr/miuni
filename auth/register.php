<?php
// register.php - procesa el registro de usuarios
// Espera POST: nombre, apellido, email, password, confirm_password

require_once __DIR__ . '/../includes/db.php';

// Helper simple para limpiar entrada
function clean($v) {
    return trim(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? clean($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? clean($_POST['apellido']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : false;
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validaciones básicas
    if (!$nombre) {
        $errors[] = 'El nombre es obligatorio.';
    }
    if (!$email) {
        $errors[] = 'Correo electrónico inválido.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        try {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare('SELECT usuario_id FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'El correo ya está registrado. Intenta iniciar sesión o usar otro correo.';
            } else {
                // Insertar nuevo usuario
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, password) VALUES (:nombre, :apellido, :email, :password)');
                $insert->execute([
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':email' => $email,
                    ':password' => $hash
                ]);

                // Redirigir a pantalla de login o página principal
                header('Location: ../index.php?registered=1');
                exit;
            }
        } catch (PDOException $e) {
            // En producción no mostrar detalles sensibles
            $errors[] = 'Error al registrar el usuario: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Mostrar formulario de registro y errores (si los hay)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registro - MiUni Kids</title>
    <style>
        body{font-family: 'Arial Rounded MT Bold', Arial, sans-serif; padding:20px;}
        .error{color:#b00020;margin-bottom:1rem}
        .success{color:green}
        form{max-width:420px;background:#f3f4f6;padding:16px;border-radius:8px}
        label{display:block;margin-top:8px}
        input[type="text"],input[type="email"],input[type="password"]{width:100%;padding:8px;margin-top:4px;border:1px solid #ccc;border-radius:4px}
        button{margin-top:12px;padding:10px 16px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer}
    </style>
</head>
<body>
    <h1>Registro de Usuario</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo $err; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="post" novalidate>
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : '';?>" required>

        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo isset($apellido) ? htmlspecialchars($apellido) : '';?>">

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : '';?>" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirmar contraseña</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Registrarme</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="../index.php">Inicia sesión</a></p>
</body>
</html>
