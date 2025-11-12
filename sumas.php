<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth_guard.php';
require_login();

require_once __DIR__ . '/includes/db.php';

$userId = (int)$_SESSION['user_id'];

try {
  $tipoStmt = $pdo->prepare('SELECT tipo_id FROM tipos_operacion WHERE nombre = :nombre LIMIT 1');
  $tipoStmt->execute([':nombre' => 'suma']);
  $tipoId = $tipoStmt->fetchColumn();

  if ($tipoId === false) {
    $startedTransaction = !$pdo->inTransaction();
    if ($startedTransaction) {
      $pdo->beginTransaction();
    }

    try {
      $insertTipo = $pdo->prepare('INSERT INTO tipos_operacion (nombre) VALUES (:nombre)');
      $insertTipo->execute([':nombre' => 'suma']);
      $tipoId = (int)$pdo->lastInsertId();
      if ($startedTransaction) {
        $pdo->commit();
      }
    } catch (PDOException $insertException) {
      if ($startedTransaction && $pdo->inTransaction()) {
        $pdo->rollBack();
      }
      // Si otro proceso ya lo creó, volvemos a consultarlo.
      if ((int)($insertException->errorInfo[1] ?? 0) === 1062) {
        $tipoStmt->execute([':nombre' => 'suma']);
        $tipoId = $tipoStmt->fetchColumn();
      } else {
        throw $insertException;
      }
    }

    if ($tipoId === false) {
      throw new RuntimeException('No se pudo registrar el tipo de operación "suma".');
    }
  }

  $tipoId = (int)$tipoId;

  if (isset($_GET['reset'])) {
    $resetStmt = $pdo->prepare('UPDATE ejercicios_usuario SET activo = 0 WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1');
    $resetStmt->execute([':uid' => $userId, ':tid' => $tipoId]);
    header('Location: sumas.php');
    exit;
  }

  $fetchExercises = function () use ($pdo, $userId, $tipoId) {
    $stmt = $pdo->prepare(
      'SELECT id, sumando_uno, sumando_dos, respuesta_usuario, correcto, resuelto, fecha_creacion
       FROM ejercicios_usuario
       WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1
       ORDER BY fecha_creacion ASC, id ASC'
    );
    $stmt->execute([':uid' => $userId, ':tid' => $tipoId]);
    return $stmt->fetchAll() ?: [];
  };

  $exercises = $fetchExercises();

  $needed = 8 - count($exercises);
  if ($needed > 0) {
    $insertStmt = $pdo->prepare(
      'INSERT INTO ejercicios_usuario (usuario_id, tipo_id, sumando_uno, sumando_dos)
       VALUES (:uid, :tid, :uno, :dos)'
    );

    for ($i = 0; $i < $needed; $i++) {
      $top = random_int(10000, 99999);
      $bottom = random_int(10, 99);
      $insertStmt->execute([
        ':uid' => $userId,
        ':tid' => $tipoId,
        ':uno' => $top,
        ':dos' => $bottom
      ]);
    }

    $exercises = $fetchExercises();
  }

  $completed = 0;
  foreach ($exercises as $exercise) {
    if ((int)$exercise['resuelto'] === 1 && (int)$exercise['correcto'] === 1) {
      $completed++;
    }
  }

  $total = count($exercises);
} catch (Throwable $e) {
  error_log('Error cargando ejercicios: ' . $e->getMessage());
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
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-white p-6">
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
          class="block rounded-2xl bg-white/80 shadow hover:shadow-lg transition transform hover:-translate-y-1 p-6 border border-white/60 <?php echo $disabled ? 'card-disabled' : ''; ?>"
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
