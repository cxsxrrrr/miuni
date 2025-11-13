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
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
  .option-card {
    background-image: url('assets/games/option.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    overflow: hidden;
  }
  .option-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(9, 64, 44, 0.55);
    transition: background 0.2s ease-in-out;
  }
  .option-card:hover::before {
    background: rgba(9, 64, 44, 0.45);
  }
  .option-card > * {
    position: relative;
  }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-pink-50 via-rose-50 to-white flex items-center justify-center p-6" style="background-image:url('assets/games/bgselector.png');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;">
  <main class="max-w-4xl w-full">
    <header class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-rose-800">Ejercicios · MiUniKids</h1>
      <a href="../index.php" class="text-sm text-rose-600 hover:underline">Volver</a>
    </header>

    <section class="grid gap-6 grid-cols-1 sm:grid-cols-2">
      <a href="sumas.php" class="option-card block rounded-xl p-6 shadow hover:scale-105 transition transform text-white">
        <h2 class="text-xl font-bold drop-shadow">Suma: 5 dígitos + 2 cifras</h2>
        <p class="mt-2 text-sm text-emerald-100 drop-shadow">Practica sumas con números grandes, paso a paso.</p>
      </a>

	  <a href="restas.php" class="option-card block rounded-xl p-6 shadow hover:scale-105 transition transform text-white">
        <h2 class="text-xl font-bold drop-shadow">Resta: 5 dígitos - 2 cifras</h2>
        <p class="mt-2 text-sm text-emerald-100 drop-shadow">Sustracciones con llevadas, con explicación visual.</p>
      </a>

      <a href="combinadas.php" class="option-card block rounded-xl p-6 shadow hover:scale-105 transition transform text-white">
        <h2 class="text-xl font-bold drop-shadow">Combinadas: 4 sumas y 4 restas</h2>
        <p class="mt-2 text-sm text-emerald-100 drop-shadow">Alterna operaciones y fortalece ambas habilidades.</p>
      </a>
    </section>
  </main>
</body>
</html>