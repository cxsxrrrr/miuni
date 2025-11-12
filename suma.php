<?php /* Suma 5 dígitos + 2 cifras */ ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Suma · MiUniKids</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../index.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-pink-50 to-white flex items-center justify-center p-6">
  <main class="w-full max-w-3xl">
    <header class="mb-4 flex items-center justify-between">
      <a href="index.php" class="text-sm text-rose-600 hover:underline">← Volver</a>
      <h1 class="text-2xl font-extrabold text-rose-800">Suma 5 dígitos + 2 cifras</h1>
      <div></div>
    </header>

    <section id="exercise" class="bg-white rounded-2xl p-6 shadow-lg text-center">
      <div class="mb-4 text-sm text-gray-600">Practica y guarda tu progreso localmente</div>

      <div id="problem" class="mx-auto w-full max-w-lg">
        <!-- visual del problema -->
        <div class="flex justify-center text-4xl font-extrabold text-sky-800 mb-2" id="top-number"></div>
        <div class="flex justify-center items-center text-4xl font-extrabold text-sky-800 mb-2">
          <div class="mr-3 text-4xl">+</div>
          <div id="bottom-number" class=""></div>
        </div>
        <div class="border-t border-dashed my-3"></div>

        <div id="answer-row" class="flex justify-center gap-2 mb-4"></div>
      </div>

      <div class="flex justify-center gap-4 mt-2">
        <button id="checkBtn" class="px-5 py-2 rounded-md bg-emerald-500 text-white font-semibold">Check it</button>
        <button id="newBtn" class="px-5 py-2 rounded-md bg-sky-400 text-white font-semibold">New game</button>
      </div>

      <div id="toast" class="fixed right-6 top-6 z-50 max-w-xs hidden"></div>

      <div class="mt-6 text-sm text-gray-600">
        Progreso: <span id="progress-count">0</span> aciertos
      </div>
    </section>
  </main>

  <script src="../js/exercises.js"></script>
  <script>
    // inicializar ejercicio tipo "suma_5d_2c"
    Exercises.init({
      key: 'suma_5d_2c',
      type: 'sum',
      topDigits: 5,
      bottomDigits: 2,
      elTop: '#top-number',
      elBottom: '#bottom-number',
      elAnswer: '#answer-row',
      progressEl: '#progress-count'
    });
  </script>
</body>
</html>