<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth_guard.php';
require_login();

// simple listado de ejercicios
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>MiUniKids | Ejercicios</title>
  <link rel="stylesheet" href="index.css">
  <style>
  .option-card {
    background-image: url('assets/games/option.svg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 0.75rem;
    box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #ffffff;
    padding: 2.5rem 2rem;
    text-decoration: none;
    height: 100%;
    width: min(520px, 100%);
  }
  .option-card h2 {
    font-weight: 800;
    font-size: 1.3rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  .option-card p {
    margin-top: 0.75rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #ffffff;
  }
  .carousel {
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
  }
  .carousel-track {
    display: flex;
    width: 100%;
    touch-action: pan-y;
  }
  .carousel-slide {
    min-width: 100%;
    display: flex;
    justify-content: center;
    align-items: stretch;
    box-sizing: border-box;
  }
  .carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    gap: 0.75rem;
    width: 100%;
    justify-content: space-between;
    pointer-events: none;
  }
  .carousel-button {
    pointer-events: all;
    background: rgba(255,255,255,0.85);
    border-radius: 9999px;
    padding: 0.35rem 0.7rem;
    font-weight: 700;
    color: #be123c;
    box-shadow: 0 10px 18px rgba(0,0,0,0.15);
    transition: transform 0.2s ease, background 0.2s ease;
  }
  .carousel-button:hover {
    transform: scale(1.05);
    background: rgba(255,255,255,1);
  }
  .carousel-indicators {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
    gap: 0.5rem;
  }
  .carousel-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 9999px;
    background: rgba(190, 18, 60, 0.35);
    transition: background 0.2s ease, transform 0.2s ease;
  }
  .carousel-indicators button[aria-current="true"] {
    background: #be123c;
    transform: scale(1.2);
  }
  </style>
</head>
<body class="font-sans min-h-screen bg-gradient-to-br from-pink-50 via-rose-50 to-white flex items-center justify-center p-6" style="background-image:url('assets/games/bgselector.png');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;">
  <main class="max-w-4xl w-full">
    <header class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-rose-800">Ejercicios · MiUniKids</h1>
      <a
        href="../index.php"
        class="inline-flex items-center gap-1.5 rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2"
      >
        Cerrar sesion
      </a>
    </header>

    <section class="carousel" aria-label="Ejercicios disponibles">
      <div class="carousel-track" id="exerciseTrack">
      <article class="carousel-slide">
        <a href="sumas.php" class="option-card block h-full p-8 transition transform">
        <h2>Suma: 5 digitos + 2 cifras</h2>
        <p>Practica sumas con numeros grandes, paso a paso.</p>
        </a>
      </article>
      <article class="carousel-slide">
        <a href="restas.php" class="option-card block h-full p-8 transition transform">
        <h2>Resta: 5 digitos - 2 cifras</h2>
        <p>Sustracciones con llevadas, con explicación visual.</p>
        </a>
      </article>
      <article class="carousel-slide">
        <a href="combinadas.php" class="option-card block h-full p-8 transition transform">
        <h2>Combinadas: 4 sumas y 4 restas</h2>
        <p>Alterna operaciones y fortalece ambas habilidades.</p>
        </a>
      </article>
      </div>
      <div class="carousel-nav" aria-hidden="true">
      <button type="button" class="carousel-button" data-direction="prev">&lt;</button>
      <button type="button" class="carousel-button" data-direction="next">&gt;</button>
      </div>
    </section>
    <div class="carousel-indicators" role="tablist" aria-label="Paginador de ejercicios">
      <button type="button" data-slide="0" aria-current="true" aria-label="Ver sumas"></button>
      <button type="button" data-slide="1" aria-label="Ver restas"></button>
      <button type="button" data-slide="2" aria-label="Ver combinadas"></button>
    </div>
  </main>

    <script>
    (function(){
      const track = document.getElementById('exerciseTrack');
      if (!track) { return; }
      const slides = Array.from(track.children);
      const container = track.parentElement;
      const buttons = document.querySelectorAll('.carousel-button');
      const indicators = Array.from(document.querySelectorAll('.carousel-indicators button'));
      let index = 0;

      const clampIndex = (value) => Math.max(0, Math.min(slides.length - 1, value));
      const getSlideWidth = () => (container ? container.getBoundingClientRect().width : track.getBoundingClientRect().width) || 1;

      const updateTransform = (immediate = false) => {
      const offset = -index * getSlideWidth();
      track.style.transition = immediate ? 'none' : 'transform 0.3s ease';
      track.style.transform = `translate3d(${offset}px, 0, 0)`;
      indicators.forEach((dot, dotIndex) => {
        dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
      });
      };

      const handleButton = (event) => {
      const direction = event.currentTarget.getAttribute('data-direction');
      if (!direction) { return; }
      index = clampIndex(index + (direction === 'next' ? 1 : -1));
      updateTransform();
      };

      const handleIndicator = (event) => {
      const slideTarget = Number.parseInt(event.currentTarget.getAttribute('data-slide'), 10);
      if (Number.isNaN(slideTarget)) { return; }
      index = clampIndex(slideTarget);
      updateTransform();
      };

      buttons.forEach((button) => button.addEventListener('click', handleButton));
      indicators.forEach((dot) => dot.addEventListener('click', handleIndicator));
      window.addEventListener('resize', () => updateTransform(true));

      updateTransform(true);
    })();
    </script>
</body>
</html>