<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth_guard.php';
require_login();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/db.php';

$userId = (int)$_SESSION['user_id'];

try {
  $tipoId = miuni_get_or_create_tipo_id($pdo, 'suma');

  if (isset($_GET['reset'])) {
	miuni_reset_user_exercises($pdo, $userId, $tipoId, 8, 'suma');
    header('Location: sumas.php');
    exit;
  }

  $exercises = miuni_ensure_user_exercises($pdo, $userId, $tipoId, 8, 'suma');
  $completed = miuni_count_completed_exercises($exercises);
  $total = count($exercises);
} catch (Throwable $e) {
  $message = 'Error cargando ejercicios: ' . $e->getMessage();
  error_log($message);
  miuni_log_error($message);
  http_response_code(500);
  echo 'No fue posible cargar tus ejercicios en este momento.';
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>MiUniKids | Sumas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Arial Rounded MT Bold', 'Helvetica Rounded', Arial, sans-serif; }
    .card-disabled { pointer-events: none; opacity: 0.55; }
    .exercise-card { background-image: url('assets/games/pizarra.png'); background-size: cover; background-position: center; background-repeat: no-repeat; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-white p-6" style="background-image:url('assets/games/bgselector.png');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;">
  <main class="max-w-5xl mx-auto">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-rose-900">Ejercicios de Suma</h1>
        <p class="text-sm text-rose-600">Selecciona un ejercicio para resolverlo mediante arrastre de números.</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-semibold">Completados: <?php echo $completed; ?>/<?php echo $total; ?></span>
        <a href="?reset=1" class="px-3 py-1 text-sm rounded-lg bg-white border border-rose-200 text-rose-700 hover:bg-rose-100 transition">Reiniciar</a>
      </div>
    </header>

    <nav class="mb-6">
      <a href="juegos.php" class="inline-flex items-center text-sm text-rose-600 hover:underline">← Volver a ejercicios</a>
    </nav>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($exercises as $index => $exercise): ?>
        <?php
          $top = (int)$exercise['sumando_uno'];
          $bottom = (int)$exercise['sumando_dos'];
          $status = 'pending';
          if ((int)$exercise['resuelto'] === 1) {
            $status = (int)$exercise['correcto'] === 1 ? 'correct' : 'incorrect';
          }

          $statusText = [
            'pending' => 'Pendiente',
            'correct' => 'Correcto',
            'incorrect' => 'Intenta de nuevo'
          ][$status] ?? 'Pendiente';

          $statusClasses = [
            'pending' => 'text-slate-500 bg-slate-100',
            'correct' => 'text-emerald-600 bg-emerald-100',
            'incorrect' => 'text-rose-600 bg-rose-100'
          ][$status] ?? 'text-slate-500 bg-slate-100';

          $statusIcon = [
            'pending' => '⌛',
            'correct' => '✔',
            'incorrect' => '⚠'
          ][$status] ?? '⌛';

          $disabled = $status === 'correct';
        ?>
        <a
          href="suma.php?id=<?php echo (int)$exercise['id']; ?>"
          class="exercise-card block rounded-2xl shadow hover:shadow-lg transition transform hover:-translate-y-1 p-6 <?php echo $disabled ? 'card-disabled' : ''; ?>"
        >
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-500">Ejercicio <?php echo $index + 1; ?></p>
              <p class="text-2xl text-rose-900 mt-2 text-right">
                <span class="block leading-tight"><?php echo number_format($top, 0, '', ' '); ?></span>
                <span class="block leading-tight">+ <?php echo str_pad((string)$bottom, 2, '0', STR_PAD_LEFT); ?></span>
              </p>
            </div>
            <span class="inline-flex items-center justify-center rounded-full w-10 h-10 text-lg <?php echo $statusClasses; ?>"><?php echo $statusIcon; ?></span>
          </div>
          <p class="mt-4 text-sm text-slate-500">Estado: <?php echo $statusText; ?></p>
          <?php if ($disabled): ?>
            <p class="mt-2 text-xs text-emerald-600">¡Bien hecho! Puedes reiniciar para practicar nuevamente.</p>
          <?php elseif ($status === 'incorrect'): ?>
            <p class="mt-2 text-xs text-rose-600">Vuelve a intentarlo, aún puedes lograrlo.</p>
          <?php else: ?>
            <p class="mt-2 text-xs text-slate-400">Haz clic para comenzar.</p>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>
