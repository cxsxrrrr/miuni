<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MiUni Kids Matemáticas | Login</title>
</head>
<body>
  <h1>Bienvenido a MiUni Kids Matemáticas</h1>
  <form action="juegos.php" method="post">
    <label for="username">Usuario:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Iniciar Sesión</button>
  </form>
  <h4>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></h4>
</body>
</html>