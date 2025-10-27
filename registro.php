<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiUni Kids Matemáticas | Registro</title>
</head>
<body>
    <h1>Registro de Usuario</h1>
    <form action="./auth/register.php" method="post">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirm_password">Confirmar Contraseña:</label>
        <br>

        <button type="submit">Registrarse</button>
    </form>
    <h4>¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a></h4>
</body>
</html>