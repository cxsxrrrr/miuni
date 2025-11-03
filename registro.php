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

    /* Raining signs placed al lado de las tarjetas */
    .rain-side {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      height: 60%;
      right: -170px;
      width: 160px;
      pointer-events: none;
      overflow: visible;
      z-index: 0;
      display: block;
    }

    /* variante para el lado izquierdo */
    .rain-side.left {
      left: -170px;
      right: auto;
    }

    .math-sign {
      position: absolute;
      top: -2.5rem;
      color: rgba(255,255,255,0.95);
      font-weight: 800;
      text-shadow: 0 4px 18px rgba(0,0,0,0.45);
      will-change: transform, opacity, left;
      opacity: 0.95;
      display: inline-block;
      user-select: none;
      -webkit-user-select: none;
      mix-blend-mode: normal; /* normal para evitar desaparición sobre fondos claros */
      animation: fall var(--dur,6s) linear var(--delay,0s) forwards;
    }

    @keyframes fall {
      0%   { transform: translateY(-10%) translateX(0); opacity: 0.0; }
      8%   { opacity: 1; }
      100% { transform: translateY(110vh) translateX(var(--drift,0px)); opacity: 0.95; }
    }

    /* size presets */
    .math-sign.size-sm { font-size: 0.9rem; }
    .math-sign.size-md { font-size: 1.25rem; }
    .math-sign.size-lg { font-size: 1.9rem; }

    /* subtle color variants */
    .math-sign.plus { color: rgba(56, 189, 248, 0.98); }    /* cyan */
    .math-sign.minus { color: rgba(253, 224, 71, 0.98); }   /* yellow */
    .math-sign.times { color: rgba(252, 165, 165, 0.98); }  /* red */
    .math-sign.divide { color: rgba(163, 230, 53, 0.98); }  /* green */
    .math-sign.equal { color: rgba(99, 102, 241, 0.98); }   /* indigo */

    /* smaller screens: fewer/ smaller signs */
    @media (max-width: 640px) {
      .math-sign { font-size: 1.0rem; }
    }
  </style>
</head>
<body class="min-h-screen font-sans antialiased bg-gradient-to-br from-quicksand/20 to-danube/8">
  <div class="relative min-h-screen overflow-hidden">
    <!-- SVG background -->
    <div class="absolute inset-0 -z-10">
      <img src="./assets/svg/bg-login.svg" alt="fondo decorativo" class="w-full h-full object-cover opacity-60" />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/5 pointer-events-none"></div>
    </div>

    <main class="max-w-6xl mx-auto py-12 px-6">
      <div class="relative grid grid-cols-1 md:grid-cols-2 gap-10 items-stretch">
        <!-- Decorative accents -->
        <div class="hidden md:block absolute right-0 bottom-0 w-72 h-72 rounded-full bg-danube/20 blur-3xl transform -translate-x-12 pointer-events-none"></div>

        <!-- contenedor derecho e izquierdo para lluvia lateral -->
        <div id="rain-side-left" class="rain-side left" aria-hidden="true"></div>
        <div id="rain-side" class="rain-side" aria-hidden="true"></div>

        <!-- Hero -->
        <div class="flex items-center justify-center z-20 h-full">
          <div class="relative w-full max-w-md rounded-2xl overflow-hidden shadow-2xl h-full">
            <img src="./assets/unnamed.png" alt="Ilustración Matemáticas"
                 class="w-full h-full object-cover bg-white/40" />

            <div class="absolute inset-0 flex items-end z-10">
              <div class="w-full bg-gradient-to-t from-black/50 to-transparent p-6">
                <h2 class="text-3xl font-extrabold text-white leading-tight">Matemáticas</h2>
                <p class="mt-2 text-sm text-white/90">
                  Lecciones divertidas y didácticas para niños — aprende jugando.
                </p>
              </div>
            </div>
           </div>
         </div>

        <section class="relative rounded-3xl p-10 md:p-12 shadow-2xl overflow-hidden h-full z-20"
                 aria-labelledby="registro-title">
          <div class="absolute inset-0 rounded-3xl glass-bg border border-white/20 backdrop-blur-support -z-10"></div>

          <h1 id="registro-title" class="relative text-2xl font-bold text-english-walnut mb-4">Registro de Usuario</h1>

          <form action="/miuni/auth/register.php" method="post" id="registro-form" class="relative space-y-4">
            <div>
              <label for="first_name" class="block text-sm font-medium text-english-walnut">Nombre</label>
              <input id="first_name" name="first_name" required
                class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-danube focus:bg-white/20 transition" />
            </div>

            <div>
              <label for="last_name" class="block text-sm font-medium text-english-walnut">Apellido</label>
              <input id="last_name" name="last_name" required
                class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-danube focus:bg-white/20 transition" />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-english-walnut">Correo Electrónico</label>
              <input id="email" type="email" name="email" required
                class="mt-1 block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-danube focus:bg-white/20 transition" />
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-english-walnut">Contraseña</label>
              <div class="relative mt-1">
                <input id="password" type="password" name="password" required
                  class="block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-valencia focus:bg-white/20 transition" />
                <button type="button" data-target="password" aria-label="Mostrar contraseña"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-200 p-1 rounded-md hover:bg-white/10 focus:outline-none">
                  <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>

            <div>
              <label for="confirm_password" class="block text-sm font-medium text-english-walnut">Confirmar Contraseña</label>
              <div class="relative mt-1">
                <input id="confirm_password" type="password" name="confirm_password" required
                  class="block w-full rounded-xl border border-white/20 bg-white/6 text-english-walnut placeholder-gray-200 px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-valencia focus:bg-white/20 transition" />
                <button type="button" data-target="confirm_password" aria-label="Mostrar contraseña"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-200 p-1 rounded-md hover:bg-white/10 focus:outline-none">
                  <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>

            <p id="form-error" class="text-sm text-red-400 hidden"></p>

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

    <script>
    // Toggle show/hide password for inputs with data-target attribute on buttons
    document.querySelectorAll('button[data-target]').forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        btn.classList.toggle('text-valencia', isPassword);
      });
    });

    // passwords must match and meet minimal length
    document.getElementById('registro-form').addEventListener('submit', function (e) {
      const pwd = document.getElementById('password').value || '';
      const cpwd = document.getElementById('confirm_password').value || '';
      const errorEl = document.getElementById('form-error');

      if (pwd.length < 6) {
        e.preventDefault();
        errorEl.textContent = 'La contraseña debe tener al menos 6 caracteres.';
        errorEl.classList.remove('hidden');
        return false;
      }

      if (pwd !== cpwd) {
        e.preventDefault();
        errorEl.textContent = 'Las contraseñas no coinciden.';
        errorEl.classList.remove('hidden');
        return false;
      }

      errorEl.classList.add('hidden');
      return true;
    });

    // Raining math signs
    (function createRainingSigns() {
      const containers = Array.from(document.querySelectorAll('.rain-side'));
      if (!containers.length) return;

       const SIGNS = ['+', '−', '×', '÷', '=', '%', '±'];
       const VARIANTS = ['plus','minus','times','divide','equal'];
       const COUNT = window.innerWidth < 640 ? 8 : 20; // menos en pantallas pequeñas

      function rand(min, max) { return Math.random() * (max - min) + min; }

      for (let i = 0; i < COUNT; i++) {
        const span = document.createElement('span');
        const sign = SIGNS[Math.floor(Math.random() * SIGNS.length)];
        span.textContent = sign;

        // size class
        const size = Math.random() < 0.5 ? 'size-sm' : (Math.random() < 0.6 ? 'size-md' : 'size-lg');
        span.className = `math-sign ${size}`;

        // color variant
        const variant = VARIANTS[Math.floor(Math.random() * VARIANTS.length)];
        span.classList.add(variant);

        // random horizontal position (use left so we can animate translateY only)
        const leftPos = rand(2, 96);
        span.style.left = `${leftPos}%`;

        // random animation duration and short delay so aparecen pronto
        const dur = rand(4.0, 7.0).toFixed(2) + 's';
        const delay = rand(0, 0.9).toFixed(2) + 's'; // delay corto
        span.style.setProperty('--dur', dur);
        span.style.setProperty('--delay', delay);

        // random horizontal drift applied in transform at the end
        const drift = `${rand(-40, 40).toFixed(1)}px`;
        span.style.setProperty('--drift', drift);

        // slight static rotation for variety (no animation conflict)
        span.style.transform = `rotate(${rand(-18,18).toFixed(1)}deg)`;

        // elegir contenedor (izquierda/derecha) de forma alterna para balancear
        const target = containers[i % containers.length];
        target.appendChild(span);

        // remove element after animation ends to keep DOM light
        span.addEventListener('animationend', () => {
          if (span.parentNode) span.parentNode.removeChild(span);
        });
       }

      // spawn pequeños refuerzos para mantener el efecto
      let burstTimer = setInterval(() => {
        if (!containers.length) { clearInterval(burstTimer); return; }
        const span = document.createElement('span');
        span.textContent = SIGNS[Math.floor(Math.random() * SIGNS.length)];
        span.className = 'math-sign size-sm ' + VARIANTS[Math.floor(Math.random() * VARIANTS.length)];
        span.style.left = `${rand(5, 92)}%`;
        span.style.setProperty('--dur', `${rand(4.0, 6.5).toFixed(2)}s`);
        span.style.setProperty('--delay', '0s');
        span.style.setProperty('--drift', `${rand(-30,30).toFixed(1)}px`);
        span.style.transform = `rotate(${rand(-12,12).toFixed(1)}deg)`;
        // añadir a un contenedor aleatorio entre los disponibles
        const c = containers[Math.floor(Math.random() * containers.length)];
        c.appendChild(span);
        span.addEventListener('animationend', () => { if (span.parentNode) span.parentNode.removeChild(span); });
      }, 600);
     })();
    </script>
</body>
</html>