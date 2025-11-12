<?php /* Suma 5 dígitos + 2 cifras */ ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Suma · MiUniKids</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="index.css">
  <style>
    /* Página suma: correcciones de layout y estilo local */
  /* Remove white card so background shows through */
  #exercise{ max-width:1100px; margin:1rem auto; background: transparent; }
    #problem { display:block; }
    #problem .flex { align-items:flex-start; }
  /* Palette will be positioned over the board */
  #number-palette{ width:140px; }
  #number-palette .digit{ width:56px; height:56px; user-select:none; }
  #board{ /* max size for the visual board */ max-width:820px; height:450px; }
  #number-palette .grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
    .slot{ display:flex; align-items:center; justify-content:center; }
    .slot--over{ outline:3px solid rgba(255,255,255,0.65); transform:scale(1.03); }
    .digit.dragging{ opacity:.6; transform:scale(.95); }
    /* Ensure header stays at top of main */
  main > header{ margin-bottom:1rem; }
    @media (max-width:768px){
      #board{ width:100%; height:320px; }
      #number-palette{ width:120px; }
    }

    /* Remove page scroll */
    html, body { height: 100%; }
    body { overflow: hidden; }
    /* removed debug labels */
  </style>
</head>
<body class="min-h-screen flex items-start justify-center p-6" style="background-image:url('assets/games/bgjuegos.png');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;">
  <main class="w-full max-w-5xl mx-auto">
    <header class="mb-4 flex items-center justify-between">
      <a href="index.php" class="text-sm text-rose-600 hover:underline">← Volver</a>
      <div></div>
    </header>

  <section id="exercise" class="rounded-2xl p-6 text-center">

      <div id="problem" class="mx-auto w-full max-w-4xl">
        <div class="flex items-start gap-6">
          <!-- palette (left) -->
          <div id="number-palette" class="w-40 p-4 bg-transparent">
            <div class="grid grid-cols-3 gap-4">
              <img draggable="true" data-value="1" src="assets/games/uno.png" alt="1" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="2" src="assets/games/dos.png" alt="2" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="3" src="assets/games/tres.png" alt="3" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="4" src="assets/games/cuatro.png" alt="4" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="5" src="assets/games/cinco.png" alt="5" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="6" src="assets/games/seis.png" alt="6" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="7" src="assets/games/siete.png" alt="7" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="8" src="assets/games/ocho.png" alt="8" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="9" src="assets/games/nueve.png" alt="9" class="digit cursor-grab w-12 h-12 mx-auto">
              <img draggable="true" data-value="0" src="assets/games/cero.png" alt="0" class="digit cursor-grab w-12 h-12 mx-auto">
            </div>
            <div class="mt-2 text-sm text-gray-600 text-center">Arrastra los números a la pizarra</div>
          </div>

          <!-- board (right) -->
          <div id="board-wrap" class="flex-1 flex items-center justify-center" style="position:relative; overflow:visible;">
            <div id="board" class="relative w-full max-w-[720px] h-[420px] rounded-xl overflow-hidden">
              <!-- overlay slots -->
              <div class="slot" data-slot="t1" style="position:absolute;left:60%;top:10%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="t2" style="position:absolute;left:68%;top:10%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="t3" style="position:absolute;left:76%;top:10%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="t4" style="position:absolute;left:84%;top:10%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="t5" style="position:absolute;left:92%;top:10%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>

              <div class="slot" data-slot="m1" style="position:absolute;left:84%;top:36%;width:48px;height:64px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="m2" style="position:absolute;left:92%;top:36%;width:48px;height:64px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>

              <div class="slot" data-slot="b1" style="position:absolute;left:52%;top:64%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="b2" style="position:absolute;left:60%;top:64%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="b3" style="position:absolute;left:68%;top:64%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="b4" style="position:absolute;left:76%;top:64%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="b5" style="position:absolute;left:84%;top:64%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
              <div class="slot" data-slot="b6" style="position:absolute;left:92%;top:64%;width:48px;height:56px;border-radius:14px;background:rgba(63,99,47,0.38);transform:translateX(-50%);"></div>
            </div>
          </div>
        </div>
        </div>
      </div>

      <div class="flex justify-center gap-4 mt-2">
        <button id="checkBtn" class="px-5 py-2 rounded-md bg-emerald-500 text-white font-semibold">Check it</button>
      </div>

      <div id="toast" class="fixed right-6 top-6 z-50 max-w-xs hidden"></div>

      <div class="mt-6 text-sm text-gray-600">
        Progreso: <span id="progress-count">0</span> aciertos
      </div>
    </section>
  </main>

  <script src="js/suma_dragdrop.js"></script>
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