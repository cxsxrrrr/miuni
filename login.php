<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css?v=2">
    <title>MiUniKids | Login</title>
</head>
<body class="bg-background hero-bg min-h-screen">
    <div class="min-h-screen flex">

            <div class="w-1/2 min-h-screen bg-white flex items-center justify-center shadow-lg" style="border-top-right-radius:2rem; border-bottom-right-radius:2rem; overflow:hidden; background-image: url('./assets/svg/sidebar-login.svg'); background-repeat: no-repeat; background-position: left top; background-size: cover;">
                <!-- Inner login card: occupy only necessary width, keep comfortable padding -->
                <div class="bg-white rounded-2xl shadow-lg px-10 py-12 w-full max-w-xl md:max-w-3xl">

                    <h2 class="text-2xl font-extrabold text-english-walnut mb-6">Iniciar sesión</h2>

                    <form action="./auth/login.php" method="post" class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required class="mt-1 block w-full rounded border border-gray-200 px-3 py-2 shadow-sm" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" id="password" name="password" required class="mt-1 block w-full rounded border border-gray-200 px-3 py-2 shadow-sm" />
                        </div>

                        <div>
                            <button type="submit" class="mt-2 w-full btn-primary">Iniciar Sesión</button>
                        </div>
                    </form>

                    <div class="mt-6 text-sm text-gray-600">
                        ¿No tienes cuenta? <a href="registro.php" class="text-sky-600 hover:underline">Regístrate aquí</a>
                    </div>
                </div>
            </div>

        <div class="w-1/2"></div>
    </div>


</body>
</html>