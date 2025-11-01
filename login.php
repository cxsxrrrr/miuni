<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>MiUniKids | Login</title>
</head>
<body>
    
    <!-- Registration card -->
    <div class="min-h-screen flex items-center justify-center"> 
        <form action="./auth/login.php" method="post" class="max-w-md mx-auto p-6">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Iniciar Sesión</button>
</body>
</html>