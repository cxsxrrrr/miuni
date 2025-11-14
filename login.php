<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <title>MiUniKids | Login</title>
    <style>
    :root { --font-sans: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
    body { font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        /* Scoped responsive layout for the login page without requiring a Tailwind rebuild */
    html { background-color: #ffffff; }
        .auth-wrapper { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; }
        .auth-card { width: 100%; max-width: 36rem; }
        html { background-color: #ffffff; }
        /* Rounded background only on desktop */
    body.hero-bg { background-clip: padding-box; }
    .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    .mascot-speech-trigger { position: absolute; border: 0; background: transparent; padding: 0; cursor: pointer; }
    .mascot-speech-trigger:focus { outline: none; }
    .mascot-speech-trigger:focus-visible { outline: 3px solid rgba(33, 150, 243, 0.65); border-radius: 1rem; }
        /* Glassmorphism card */
        .auth-card .card-inner {
            background: linear-gradient(135deg, rgba(255,255,255,0.38), rgba(255,255,255,0.22));
            border: 1px solid rgba(255,255,255,0.35);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(55,39,33,0.18), 0 8px 10px -6px rgba(55,39,33,0.08);
            padding: 3rem 2.5rem;
        }
        /* Inputs over glass background */
        .auth-card .card-inner input[type="email"],
        .auth-card .card-inner input[type="password"] {
            background: rgba(255,255,255,0.75);
            border-color: rgba(255,255,255,0.65);
            color: var(--english-walnut);
        }
        .auth-card .card-inner input::placeholder { color: rgba(55,39,33,0.6); }
        /* Fallback when backdrop-filter not supported */
        @supports not ((backdrop-filter: blur(1px))) {
            .auth-card .card-inner { background: #ffffff; border-color: #ffffff; }
        }
        /* Hide the decorative aside on small screens */
        .auth-aside { display: none; }
        @media (min-width: 1024px) {
            .auth-wrapper { flex-direction: row; align-items: stretch; padding: 0; }
            /* Left side: center the card vertically and horizontally */
            .auth-card { flex: 1 1 50%; display: flex; align-items: center; justify-content: center; max-width: none; }
            .auth-card .card-inner { max-width: 32rem; width: 100%; }
            /* Right side: decorative panel */
            .auth-aside {
                display: flex; align-items: center; justify-content: center; padding: 24px;
                flex: 1 1 50%; min-height: 100vh;
                /* Layered background: subtle translucent white + SVG */
                background-color: rgba(255,255,255,0.5);
                background-image: url('./assets/svg/sidebar-login.svg');
                background-repeat: no-repeat; background-position: left top; background-size: cover;
                /* Rounded outer edge only on desktop */
                border-top-right-radius: 2rem; border-bottom-right-radius: 2rem; overflow: hidden;
                /* Slight shadow to highlight depth */
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.06);
                /* Optional: blur the backdrop a bit for a glassy side panel without affecting the mascot */
                backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
            }
            .auth-aside .mascot-image { max-width: 90%; max-height: 90%; height: auto; width: auto; object-fit: contain; display: block; }
            .mascot-wrapper { position: relative; display: inline-block; }
            .mascot-speech-trigger { inset: 0; width: 100%; height: 100%; border-radius: 1rem; z-index: 5; }
            .speech-bubble { position: absolute; top: 14%; left: 65%; transform: translate(-50%, -60%); width: clamp(220px, 28vw, 320px); }
            .speech-bubble img { width: 100%; height: auto; display: block; }
            .speech-bubble__text { position: absolute; inset: 25% 20% 38% 12%; display: flex; align-items: center; justify-content: center; text-align: center; font-size: clamp(0.75rem, 1.1vw, 1rem); line-height: 1.4; color: #372721; font-weight: 500; }
        }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: #374151;
      text-decoration: none;
      margin-bottom: 0.75rem;
      font-size: 0.875rem;
    }
    .back-link svg { width: 1rem; height: 1rem; }
    .back-link:hover { text-decoration: none; }
    </style>
  </head>
  <body class="bg-background hero-bg min-h-screen">
    <div class="auth-wrapper">
        <!-- Card column -->
        <div class="auth-card">
            <div class="card-inner animate-pulse-fade-in">
               <!-- Back link colocado dentro de la tarjeta, arriba del titulo -->
               <a href="index.php" class="back-link text-sm text-black-200 hover:text-black mb-4" aria-label="Volver al inicio">
                   <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                       <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                   </svg>
                   Volver
               </a>
                <?php if (isset($_GET['error'])): ?>
                    <?php
                        $msg = '';
                        switch ($_GET['error']) {
                            case 'missing': $msg = 'Por favor ingresa tu correo y contraseña.'; break;
                            case 'cred': $msg = 'Correo o contraseña incorrectos.'; break;
                            case 'server': $msg = 'Hubo un problema con el servidor. Intenta de nuevo.'; break;
                            default: $msg = 'No se pudo iniciar sesión.'; break;
                        }
                    ?>
                    <div class="mb-4 px-4 py-3 rounded bg-valencia text-white">
                        <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
                <h2 class="text-2xl font-extrabold text-english-walnut mb-6">Iniciar sesión</h2>

                <form action="./auth/login.php" method="post" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full rounded border border-gray-200 px-3 py-2 shadow-sm" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" class="mt-1 block w-full rounded border border-gray-200 px-3 py-2 shadow-sm" />
                    </div>

                    <div>
                        <button type="submit" class="mt-2 w-full btn-primary">Iniciar Sesión</button>
                    </div>
                </form>

                <div class="mt-6 text-sm text-gray-600">
                    ¿No tienes cuenta? <a href="registro.php" class="text-gray-700 hover:underline">Regístrate aquí</a>
                </div>
            </div>
        </div>

        <div class="auth-aside" aria-hidden="true">
            <div class="mascot-wrapper">
                <img src="./assets/mascota_mirrored.png" alt="Mascota MiUni" class="animate-slide-in-top mascot-image">
                <button type="button" class="mascot-speech-trigger" aria-label="Reproducir mensaje de bienvenida">
                    <span class="sr-only">Reproducir mensaje de bienvenida</span>
                </button>
                <div class="speech-bubble animate-scale">
                    <img src="./assets/images/nube.png" alt="Mensaje de la mascota">
                    <div class="speech-bubble__text">¡Bienvenido a MiUni Kids Matemáticas!</div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/speech.js"></script>
</body>
</html>