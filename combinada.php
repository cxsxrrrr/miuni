<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth_guard.php';
require_login();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/db.php';

$userId = (int)$_SESSION['user_id'];
$exerciseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$exerciseId) {
	header('Location: combinadas.php');
	exit;
}

try {
	$tipoSuma = miuni_get_or_create_tipo_id($pdo, 'combinada_suma');
	$tipoResta = miuni_get_or_create_tipo_id($pdo, 'combinada_resta');
	miuni_ensure_ejercicios_schema($pdo);
	$columns = miuni_get_ejercicios_sum_column_names($pdo);

	$exerciseStmt = $pdo->prepare(sprintf(
		'SELECT id, tipo_id, %1$s AS sumando_uno, %2$s AS sumando_dos, respuesta_usuario, resuelto, correcto
		 FROM ejercicios_usuario
		 WHERE id = :id AND usuario_id = :uid AND activo = 1
		 LIMIT 1',
		$columns['uno'],
		$columns['dos']
	));
	$exerciseStmt->execute([
		':id' => $exerciseId,
		':uid' => $userId
	]);
	$exercise = $exerciseStmt->fetch();

	if (!$exercise) {
		header('Location: combinadas.php');
		exit;
	}

	$tipoId = (int)$exercise['tipo_id'];
	if ($tipoId !== $tipoSuma && $tipoId !== $tipoResta) {
		header('Location: combinadas.php');
		exit;
	}

	$operation = $tipoId === $tipoResta ? 'resta' : 'suma';
	$top = (int)$exercise['sumando_uno'];
	$bottom = (int)$exercise['sumando_dos'];
	if ($operation === 'resta' && $bottom > $top) {
		$swap = $top;
		$top = $bottom;
		$bottom = $swap;
	}
	$result = $operation === 'resta' ? $top - $bottom : $top + $bottom;

	$status = 'pending';
	if ((int)$exercise['resuelto'] === 1) {
		$status = (int)$exercise['correcto'] === 1 ? 'correct' : 'incorrect';
	}

	$completedStmt = $pdo->prepare(
		'SELECT COUNT(*) FROM ejercicios_usuario
		 WHERE usuario_id = :uid AND tipo_id IN (:tipoSuma, :tipoResta) AND activo = 1 AND correcto = 1 AND resuelto = 1'
	);
	$completedStmt->execute([
		':uid' => $userId,
		':tipoSuma' => $tipoSuma,
		':tipoResta' => $tipoResta
	]);
	$completed = (int)$completedStmt->fetchColumn();

	$totalStmt = $pdo->prepare(
		'SELECT COUNT(*) FROM ejercicios_usuario
		 WHERE usuario_id = :uid AND tipo_id IN (:tipoSuma, :tipoResta) AND activo = 1'
	);
	$totalStmt->execute([
		':uid' => $userId,
		':tipoSuma' => $tipoSuma,
		':tipoResta' => $tipoResta
	]);
	$total = (int)$totalStmt->fetchColumn();

	if ($total === 0) {
		header('Location: combinadas.php');
		exit;
	}
} catch (Throwable $e) {
	$message = 'Error cargando ejercicio combinado: ' . $e->getMessage();
	error_log($message);
	miuni_log_error($message);
	http_response_code(500);
	echo 'No fue posible cargar el ejercicio.';
	exit;
}

$digitImages = [
	'0' => 'assets/games/cero.png',
	'1' => 'assets/games/uno.png',
	'2' => 'assets/games/dos.png',
	'3' => 'assets/games/tres.png',
	'4' => 'assets/games/cuatro.png',
	'5' => 'assets/games/cinco.png',
	'6' => 'assets/games/seis.png',
	'7' => 'assets/games/siete.png',
	'8' => 'assets/games/ocho.png',
	'9' => 'assets/games/nueve.png'
];

$topSlots = ['t1', 't2', 't3', 't4', 't5'];
$bottomSlots = ['m5', 'm4', 'm3', 'm1', 'm2'];
$answerSlots = ['b1', 'b2', 'b3', 'b4', 'b5', 'b6'];

$topDigits = str_split((string)$top);
$topValues = array_fill_keys($topSlots, null);
$ptr = count($topDigits) - 1;
for ($i = count($topSlots) - 1; $i >= 0; $i--) {
	$slotId = $topSlots[$i];
	$topValues[$slotId] = $ptr >= 0 ? $topDigits[$ptr] : null;
	$ptr--;
}

$bottomDigits = str_split(str_pad((string)$bottom, 5, '0', STR_PAD_LEFT));
$bottomValues = array_fill_keys($bottomSlots, null);
$ptr = count($bottomDigits) - 1;
for ($i = count($bottomSlots) - 1; $i >= 0; $i--) {
	$slotId = $bottomSlots[$i];
	$bottomValues[$slotId] = $ptr >= 0 ? $bottomDigits[$ptr] : null;
	$ptr--;
}

$slotDefinitions = [
	't1' => ['style' => 'left:60%;top:10%;width:48px;height:56px;', 'type' => 'top'],
	't2' => ['style' => 'left:68%;top:10%;width:48px;height:56px;', 'type' => 'top'],
	't3' => ['style' => 'left:76%;top:10%;width:48px;height:56px;', 'type' => 'top'],
	't4' => ['style' => 'left:84%;top:10%;width:48px;height:56px;', 'type' => 'top'],
	't5' => ['style' => 'left:92%;top:10%;width:48px;height:56px;', 'type' => 'top'],
	'm1' => ['style' => 'left:84%;top:36%;width:48px;height:64px;', 'type' => 'bottom'],
	'm2' => ['style' => 'left:92%;top:36%;width:48px;height:64px;', 'type' => 'bottom'],
	'm3' => ['style' => 'left:76%;top:36%;width:48px;height:64px;', 'type' => 'bottom'],
	'm4' => ['style' => 'left:68%;top:36%;width:48px;height:64px;', 'type' => 'bottom'],
	'm5' => ['style' => 'left:60%;top:36%;width	:48px;height:64px;', 'type' => 'bottom'],
	'b1' => ['style' => 'left:52%;top:64%;width:48px;height:56px;', 'type' => 'answer'],
	'b2' => ['style' => 'left:60%;top:64%;width:48px;height:56px;', 'type' => 'answer'],
	'b3' => ['style' => 'left:68%;top:64%;width:48px;height:56px;', 'type' => 'answer'],
	'b4' => ['style' => 'left:76%;top:64%;width:48px;height:56px;', 'type' => 'answer'],
	'b5' => ['style' => 'left:84%;top:64%;width:48px;height:56px;', 'type' => 'answer'],
	'b6' => ['style' => 'left:92%;top:64%;width:48px;height:56px;', 'type' => 'answer']
];

$payload = [
	'id' => (int)$exercise['id'],
	'operation' => $operation,
	'top' => $top,
	'bottom' => $bottom,
	'result' => $result,
	'status' => $status,
	'resuelto' => (int)$exercise['resuelto'] === 1,
	'answer' => $exercise['respuesta_usuario'] ?? null,
	'completed' => $completed,
	'total' => $total,
	'slots' => [
		'answer' => $answerSlots
	]
];

$operatorSymbol = $operation === 'resta' ? '-' : '+';
$pageTitle = $operation === 'resta' ? 'Resta' : 'Suma';
$successMessage = $operation === 'resta' ? '¡Excelente! Has resuelto la resta correctamente.' : '¡Excelente! Has resuelto la suma correctamente.';
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1"/>
	<title><?php echo $pageTitle; ?> combinada · MiUniKids</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="index.css">
	<style>
		#exercise{ max-width:1100px; margin:1rem auto; background: transparent; }
		#problem { display:block; }
		#problem .flex { align-items:flex-start; }
		#number-palette{ width:140px; }
		#number-palette .digit{ width:56px; height:56px; user-select:none; }
		#board{ max-width:820px; height:450px; }
		#number-palette .grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
		.slot{ display:flex; align-items:center; justify-content:center; border-radius:14px; background:rgba(63,99,47,0.38); transform:translateX(-50%); }
		.slot--answer{ cursor:pointer; }
		.slot--static{ pointer-events:none; }
		.slot--static img{ width:44px; height:44px; object-fit:contain; user-select:none; }
		.slot--over{ outline:3px solid rgba(255,255,255,0.65); transform:translateX(-50%) scale(1.03); }
		.digit.dragging{ opacity:.6; transform:scale(.95); }
		main > header{ margin-bottom:1rem; }
		@media (max-width:768px){
			#board{ width:100%; height:320px; }
			#number-palette{ width:120px; }
		}
		html, body { height: 100%; }
		body { overflow: hidden; }
		.board-operator{ position:absolute; left:76%; top:28%; font-size:2rem; color:#ffe4ec; font-weight:700; transform:translateX(-50%); pointer-events:none; }
	</style>
</head>
<body class="min-h-screen flex items-start justify-center p-6" style="background-image:url('assets/games/bgjuegos.png');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;">
	<main class="w-full max-w-5xl mx-auto">
		<header class="mb-4 flex flex-wrap items-center justify-between gap-3 text-white drop-shadow">
			<a href="combinadas.php" class="text-sm bg-sky-900/80 hover:bg-sky-900 px-3 py-1 rounded-lg shadow">← Volver a la lista</a>
			<div class="flex items-center gap-3 text-xs uppercase tracking-wide">
				<span class="inline-block px-3 py-1 rounded-lg bg-sky-900/70">Completados <span id="progress-count"><?php echo $completed; ?></span> / <?php echo $total; ?></span>
			</div>
		</header>

		<section id="exercise" class="rounded-2xl p-6 text-center">
			<div id="problem" class="mx-auto w-full max-w-4xl">
				<div class="flex items-start gap-6">
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
						<div class="mt-2 inline-block px-3 py-1 rounded-lg bg-emerald-900/80 text-sm text-white text-center drop-shadow">Arrastra los números a la pizarra</div>
					</div>

					<div id="board-wrap" class="flex-1 flex items-center justify-center" style="position:relative; overflow:visible;">
						<div id="board" class="relative w-full max-w-[720px] h-[420px] rounded-xl overflow-hidden">
							<div class="board-operator"><?php echo $operatorSymbol; ?></div>
							<?php foreach ($slotDefinitions as $slotId => $definition): ?>
								<?php
									$classes = ['slot'];
									if ($definition['type'] !== 'answer') {
										$classes[] = 'slot--static';
									} else {
										$classes[] = 'slot--answer';
									}
									$value = null;
									if ($definition['type'] === 'top') {
										$value = $topValues[$slotId] ?? null;
									} elseif ($definition['type'] === 'bottom') {
										$value = $bottomValues[$slotId] ?? null;
									}
								?>
								<div
									class="<?php echo implode(' ', $classes); ?>"
									data-slot="<?php echo $slotId; ?>"
									style="position:absolute;<?php echo $definition['style']; ?>"
								>
									<?php if ($value !== null): ?>
										<?php $imgSrc = $digitImages[$value] ?? null; ?>
										<?php if ($imgSrc): ?>
											<img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>">
										<?php else: ?>
											<span class="text-white text-2xl font-bold drop-shadow"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></span>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>

			<div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-6">
				<button type="button" id="checkBtn" class="px-5 py-3 rounded-xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 transition">Verificar resultado</button>
				<button type="button" id="resetSlots" class="px-5 py-3 rounded-xl bg-sky-900/80 text-white font-semibold shadow hover:bg-sky-900 transition">Vaciar respuesta</button>
				<button type="button" id="skipBtn" class="px-5 py-3 rounded-xl bg-rose-600 text-white font-semibold shadow hover:bg-rose-700 transition">Volver a la lista</button>
			</div>

			<div id="toast" class="fixed right-6 top-6 z-50 max-w-xs hidden"></div>
			<div id="congratsModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-6">
				<div class="relative w-full max-w-md rounded-3xl bg-white text-slate-800 shadow-2xl overflow-hidden">
					<button type="button" class="absolute right-4 top-4 text-xl text-slate-500 hover:text-slate-700" data-congrats-close aria-label="Cerrar">×</button>
					<div class="px-8 pt-12 pb-8 text-center space-y-4">
						<div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 text-sky-600 text-2xl font-bold shadow-inner">8/8</div>
						<h2 class="text-2xl font-bold">¡Felicitaciones!</h2>
						<p class="text-sm text-slate-600">Completaste los 8 ejercicios de esta categoría. ¡Sigue practicando para mantenerte al día!</p>
						<button type="button" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 transition" data-congrats-close>Continuar</button>
					</div>
				</div>
			</div>
		</section>
	</main>

	<script>
		window.currentExercise = <?php echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
		window.combinadaMessages = {
			success: <?php echo json_encode($successMessage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
		};
	</script>
	<script src="js/combinada_dragdrop.js"></script>
	<script src="js/combinada_page.js"></script>
</body>
</html>
