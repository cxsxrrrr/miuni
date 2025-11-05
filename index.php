<!DOCTYPE html>
<html lang="es" class="overflow-x-hidden">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MiUni Kids Matemáticas | Portal</title>
  <link rel="stylesheet" href="index.css">
</head>
<body class="font-sans index-bg min-h-screen text-white antialiased overflow-x-hidden">
  <div class="min-h-screen flex flex-col bg-gradient-to-r from-[#f5e9df]/60 via-[#c48a63]/70 to-[#885a44]/80">
    <header class="max-w-6xl w-full mx-auto px-4 md:px-6 pt-6 md:pt-8">
      <nav class="flex items-center justify-between gap-4">
        <a href="index.php" class="inline-flex items-center flex-shrink-0 text-white" aria-label="MiUni Kids">
          <span class="text-lg md:text-xl font-semibold tracking-wide">MiUniKids</span>
        </a>
        <ul class="hidden md:flex items-center gap-6">
          <li><button type="button" class="nav-link nav-link--active" data-section="home">Home</button></li>
          <li><button type="button" class="nav-link" data-section="pricing">Pricing</button></li>
          <li><button type="button" class="nav-link" data-section="services">Services</button></li>
        </ul>
        <div class="hidden md:flex items-center gap-4 text-sm font-medium">
          <a href="login.php" class="px-5 py-2 rounded-full border border-white/40 text-white/80 hover:text-white transition">Login</a>
          <a href="registro.php" class="px-6 py-2 rounded-full bg-harvest-gold text-english-walnut font-semibold shadow hover:brightness-110 transition">Sign Up</a>
        </div>
        <button type="button" class="md:hidden inline-flex items-center justify-center rounded-full bg-white/15 p-2 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70 transition" data-nav-toggle aria-label="Menú" aria-expanded="false">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
          </svg>
        </button>
      </nav>
  <div id="mobileNav" class="md:hidden hidden flex-col gap-4 mt-4 rounded-2xl bg-white/10 px-4 py-5 backdrop-filter text-sm" aria-label="Navegación móvil">
        <button type="button" class="nav-link nav-link--active w-full justify-between bg-white/20 px-3 py-2 rounded-lg" data-section="home">Home</button>
        <button type="button" class="nav-link w-full justify-between bg-white/10 px-3 py-2 rounded-lg" data-section="pricing">Pricing</button>
        <button type="button" class="nav-link w-full justify-between bg-white/10 px-3 py-2 rounded-lg" data-section="services">Services</button>
        <div class="flex flex-col gap-3 pt-2 text-sm font-medium">
          <a href="login.php" class="inline-flex w-full items-center justify-center rounded-lg border border-white/30 px-3 py-2 text-white/90 hover:text-white transition">Login</a>
          <a href="registro.php" class="inline-flex w-full items-center justify-center rounded-lg bg-harvest-gold px-3 py-2 font-semibold text-english-walnut shadow hover:brightness-110 transition">Sign Up</a>
        </div>
      </div>
    </header>

    <main class="flex-1 flex items-center justify-center">
      <div class="relative max-w-6xl w-full mx-auto px-4 md:px-6 py-12 md:py-16 overflow-hidden">
        <div class="pointer-events-none absolute -z-10 left-1/2 top-1/2 h-[420px] w-[420px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/10 blur-3xl sm:h-[520px] sm:w-[520px]"></div>
        <div class="grid w-full place-items-center md:place-items-stretch md:items-center gap-12 md:gap-14 md:grid-cols-2">
          <div class="order-last md:order-first flex justify-center md:justify-start">
            <img src="./assets/images/teacher.png" alt="Profesor explicando matemática" class="max-w-[320px] sm:max-w-[380px] md:max-w-[420px] w-full h-auto drop-shadow-xl" loading="lazy">

          </div>

          <div id="heroContent" class="w-full max-w-xl space-y-8 text-white/90 text-center md:text-left mx-auto md:mx-0 overflow-hidden">
            <div class="space-y-5">
              <p id="heroEyebrow" class="uppercase tracking-[0.4em] text-white/70 text-xs sm:text-sm">Aprende</p>
              <h1 id="heroTitle" class="text-5xl sm:text-6xl md:text-7xl font-extrabold leading-tight">MATEMÁTICAS</h1>
              <h2 id="heroSubtitle" class="text-lg sm:text-2xl md:text-3xl font-semibold text-english-walnut">Ejercicios interactivos</h2>
              <p id="heroDescription" class="text-white/85 max-w-2xl md:max-w-xl mx-auto md:mx-0">Potencia el pensamiento lógico con retos diarios, material manipulativo y actividades guiadas paso a paso pensadas para primaria.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center sm:items-end md:items-center gap-6 sm:gap-8">
              <a id="heroCta" href="#" class="px-8 py-3 rounded-full bg-gradient-to-r from-[#3fb886] to-[#208765] text-white font-semibold shadow-xl hover:scale-105 transition-transform">Comenzar ahora</a>
              <div class="carousel-dots justify-center md:justify-start" role="tablist" aria-label="Secciones del carrusel">
                <span class="carousel-dot carousel-dot--active" data-dot="home" role="tab" aria-selected="true" aria-label="Home" tabindex="0"></span>
                <span class="carousel-dot" data-dot="pricing" role="tab" aria-selected="false" aria-label="Pricing" tabindex="0"></span>
                <span class="carousel-dot" data-dot="services" role="tab" aria-selected="false" aria-label="Services" tabindex="0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="px-4 md:px-6 pb-10">
      <div class="max-w-6xl mx-auto flex justify-end">
        <button type="button" class="relative inline-flex items-center justify-center w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/20 border border-white/25 text-english-walnut shadow-lg backdrop-filter" aria-label="Ayuda">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10a4 4 0 0 1 8 0c0 1.657-1.333 3-3 3v1m0 4h.01M21 12c0 4.418-3.582 8-8 8a8.06 8.06 0 0 1-3-.6L5 21l1.6-4.8A8 8 0 1 1 21 12Z" />
          </svg>
        </button>
      </div>
    </footer>
  </div>
  <script type="module" src="./js/index.js"></script>
</body>
</html>