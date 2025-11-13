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
</head>
<body class="min-h-screen bg-gradient-to-br from-pink-50 via-rose-50 to-white flex items-center justify-center p-6" style="background-image:url('assets/games/bgselector.png');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;">
  <main class="max-w-4xl w-full">
    <header class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-rose-800">Ejercicios · MiUniKids</h1>
      <a href="../index.php" class="text-sm text-rose-600 hover:underline">Volver</a>
    </header>

    <section class="grid gap-6 grid-cols-1 sm:grid-cols-2">
      <a href="sumas.php" class="block rounded-xl p-6 bg-white shadow hover:scale-105 transition transform">
        <h2 class="text-xl font-bold text-sky-800">Suma: 5 dígitos + 2 cifras</h2>
        <p class="mt-2 text-sm text-gray-600">Practica sumas con números grandes, paso a paso.</p>
      </a>

  <a href="restas.php" class="block rounded-xl p-6 bg-white shadow hover:scale-105 transition transform">
        <h2 class="text-xl font-bold text-sky-800">Resta: 5 dígitos - 2 cifras</h2>
        <p class="mt-2 text-sm text-gray-600">Sustracciones con llevadas, con explicación visual.</p>
      </a>

      <a href="combinadas.php" class="block rounded-xl p-6 bg-white shadow hover:scale-105 transition transform">
        <h2 class="text-xl font-bold text-sky-800">Combinadas: 4 sumas y 4 restas</h2>
        <p class="mt-2 text-sm text-gray-600">Alterna operaciones y fortalece ambas habilidades.</p>
      </a>
    </section>
  </main>
</body>
</html>