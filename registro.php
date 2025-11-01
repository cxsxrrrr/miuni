<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>MiUni Kids Matemáticas | Registro</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* ensure backdrop-filter works on some browsers when using CDN */
    .backdrop-blur-support { -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); }
    /* fallback background color for older browsers */
    .glass-bg { background: rgba(255,255,255,0.06); }
  </style>
</head>
<body class="min-h-screen font-sans antialiased bg-gradient-to-br from-quicksand/20 to-danube/8">
  <div class="relative min-h-screen overflow-hidden">
    <!-- SVG background (placed behind content) -->
    <div class="absolute inset-0 -z-10">
      <img src="./assets/svg/bg-login.svg" alt="fondo decorativo" class="w-full h-full object-cover opacity-60" />
      <!-- subtle overlay to tint -->
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/5 pointer-events-none"></div>
    </div>

    <main class="max-w-6xl mx-auto py-12 px-6">
      <div class="relative grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <!-- Decorative blobs (accent) -->
        <div class="hidden md:block absolute -left-20 -top-10 w-56 h-56 rounded-full bg-valencia/20 blur-3xl transform rotate-12 pointer-events-none"></div>
        <div class="hidden md:block absolute right-0 bottom-0 w-72 h-72 rounded-full bg-danube/20 blur-3xl transform -translate-x-12 pointer-events-none"></div>

        <!-- Hero / ilustración -->
        <div class="flex items-center justify-center">
          <div class="max-w-md text-center md:text-left">
            <h2 class="mt-2 text-3xl font-extrabold text-english-walnut">Matemáticas</h2>
            <p class="mt-2 text-sm text-gray-700">
              Lecciones divertidas y didácticas para niños — aprende jugando.
            </p>
            <img src="./assets/unnamed.png" alt="Ilustración Matemáticas" class="w-full rounded-2xl shadow-2xl object-cover bg-white/40 p-6 mb-6"/>

          </div>
        </div>

        <!-- Card de registro con glassmorphism -->
        <section class="relative rounded-3xl p-8 shadow-2xl overflow-hidden"
                 aria-labelledby="registro-title">
          <!-- Glass pane -->
          <div class="absolute inset-0 rounded-3xl glass-bg border border-white/20 backdrop-blur-support -z-10"></div>

          <h1 id="registro-title" class="relative text-2xl font-bold text-english-walnut mb-4">Registro de Usuario</h1>

          <form action="/miuni/auth/register.php" method="post" class="relative space-y-4">
            <div>
              <label for="username" class="block text-sm font-medium text-english-walnut">Usuario</label>
              <input id="username" name="username" required
                class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-danube focus:bg-white/20 transition" />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-english-walnut">Correo Electrónico</label>
              <input id="email" type="email" name="email" required
                class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-danube focus:bg-white/20 transition" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="password" class="block text-sm font-medium text-english-walnut">Contraseña</label>
                <input id="password" type="password" name="password" required
                  class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-valencia focus:bg-white/20 transition" />
              </div>
              <div>
                <label for="confirm_password" class="block text-sm font-medium text-english-walnut">Confirmar Contraseña</label>
                <input id="confirm_password" type="password" name="confirm_password" required
                  class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-valencia focus:bg-white/20 transition" />
              </div>
            </div>

            <div class="pt-2">
              <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-valencia to-alizarin-crimson text-white px-4 py-2 font-semibold shadow-lg hover:scale-105 transition-transform">
                Registrarse
              </button>
            </div>

            <p class="text-center text-sm text-gray-200">
              ¿Ya tienes una cuenta?
              <a href="index.php" class="text-valencia font-medium hover:underline"> Inicia sesión aquí</a>
            </p>
          </form>
        </section>
      </div>
    </main>
  </div>
</body>
</html>