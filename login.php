<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css?v=2">
    <title>MiUniKids | Login</title>
    <style>
        /* Scoped responsive layout for the login page without requiring a Tailwind rebuild */
        .auth-wrapper { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; }
        .auth-card { width: 100%; max-width: 36rem; }
        .auth-card .card-inner { background: #fff; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1); padding: 3rem 2.5rem; }
        /* Hide the decorative aside on small screens */
        .auth-aside { display: none; }
        @media (min-width: 1024px) {
            .auth-wrapper { flex-direction: row; align-items: stretch; padding: 0; }
            /* Left side: center the card vertically and horizontally */
            .auth-card { flex: 1 1 50%; display: flex; align-items: center; justify-content: center; max-width: none; }
            .auth-card .card-inner { max-width: 32rem; width: 100%; }
            /* Right side: decorative panel */
            .auth-aside { display: block; flex: 1 1 50%; min-height: 100vh; background: #fff; border-top-right-radius: 2rem; border-bottom-right-radius: 2rem; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1); background-image: url('./assets/svg/sidebar-login.svg'); background-repeat: no-repeat; background-position: left top; background-size: cover; }
        }
    </style>
</head>
<body class="bg-background hero-bg min-h-screen">
    <div class="auth-wrapper">
        <!-- Card column -->
        <div class="auth-card">
            <div class="card-inner">
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

        <!-- Decorative aside column (only on desktop) -->
        <div class="auth-aside" aria-hidden="true"></div>
    </div>


</body>
</html>