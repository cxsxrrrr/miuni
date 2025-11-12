<?php
declare(strict_types=1);

if (!function_exists('miuni_log_error')) {
	/**
	 * Append an error message to the storage/logs directory within the project.
	 */
	function miuni_log_error(string $message, string $filename = 'miuni-error.log'): void
	{
		$projectRoot = dirname(__DIR__);
		$logDir = $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';

		if (!is_dir($logDir)) {
			@mkdir($logDir, 0775, true);
		}

		$logFile = $logDir . DIRECTORY_SEPARATOR . $filename;
		$entry = sprintf('[%s] %s%s', date('c'), $message, PHP_EOL);

		if (@file_put_contents($logFile, $entry, FILE_APPEND) === false) {
			error_log('MiUni log fallback: ' . $message);
		}
	}
}

if (!function_exists('miuni_random_int')) {
	function miuni_random_int(int $min, int $max): int
	{
		return function_exists('random_int') ? random_int($min, $max) : mt_rand($min, $max);
	}
}

if (!function_exists('miuni_get_or_create_tipo_id')) {
	function miuni_get_or_create_tipo_id(PDO $pdo, string $nombre): int
	{
		$tipoStmt = $pdo->prepare('SELECT tipo_id FROM tipos_operacion WHERE nombre = :nombre LIMIT 1');
		$tipoStmt->execute([':nombre' => $nombre]);
		$tipoId = $tipoStmt->fetchColumn();

		if ($tipoId !== false) {
			return (int)$tipoId;
		}

		try {
			$insertTipo = $pdo->prepare('INSERT INTO tipos_operacion (nombre) VALUES (:nombre)');
			$insertTipo->execute([':nombre' => $nombre]);
			return (int)$pdo->lastInsertId();
		} catch (PDOException $insertException) {
			if ((int)($insertException->errorInfo[1] ?? 0) === 1062) {
				$tipoStmt->execute([':nombre' => $nombre]);
				$tipoId = $tipoStmt->fetchColumn();
				if ($tipoId !== false) {
					return (int)$tipoId;
				}
			}

			throw $insertException;
		}
	}
}

if (!function_exists('miuni_ensure_ejercicios_schema')) {
	function miuni_ensure_ejercicios_schema(PDO $pdo): void
	{
		static $checked = false;
		if ($checked) {
			return;
		}

		try {
			$columns = [];
			$stmt = $pdo->query('SHOW COLUMNS FROM ejercicios_usuario');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$columns[$row['Field']] = true;
			}

			$alter = [];
			if (!isset($columns['sumando_uno'])) {
				$alter[] = 'ADD COLUMN sumando_uno INT NOT NULL DEFAULT 0 AFTER tipo_id';
			}
			if (!isset($columns['sumando_dos'])) {
				$alter[] = 'ADD COLUMN sumando_dos INT NOT NULL DEFAULT 0 AFTER sumando_uno';
			}
			if (!isset($columns['respuesta_usuario'])) {
				$alter[] = 'ADD COLUMN respuesta_usuario INT DEFAULT NULL AFTER sumando_dos';
			}
			if (!isset($columns['correcto'])) {
				$alter[] = 'ADD COLUMN correcto BOOLEAN DEFAULT FALSE AFTER respuesta_usuario';
			}
			if (!isset($columns['resuelto'])) {
				$alter[] = 'ADD COLUMN resuelto BOOLEAN DEFAULT FALSE AFTER correcto';
			}
			if (!isset($columns['activo'])) {
				$alter[] = 'ADD COLUMN activo BOOLEAN DEFAULT TRUE AFTER resuelto';
			}
			if (!isset($columns['fecha_creacion'])) {
				$alter[] = 'ADD COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP AFTER activo';
			}

			if ($alter) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ' . implode(', ', $alter));
			}
		} catch (Throwable $e) {
			miuni_log_error('No se pudo validar/ajustar la tabla ejercicios_usuario: ' . $e->getMessage());
		}

		$checked = true;
	}
}

if (!function_exists('miuni_fetch_user_exercises')) {
	function miuni_fetch_user_exercises(PDO $pdo, int $userId, int $tipoId): array
	{
		miuni_ensure_ejercicios_schema($pdo);
		$stmt = $pdo->prepare(
			'SELECT id, sumando_uno, sumando_dos, respuesta_usuario, correcto, resuelto, fecha_creacion
			 FROM ejercicios_usuario
			 WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1
			 ORDER BY fecha_creacion ASC, id ASC'
		);
		$stmt->execute([':uid' => $userId, ':tid' => $tipoId]);
		$rows = $stmt->fetchAll();
		return $rows ?: [];
	}
}

if (!function_exists('miuni_ensure_user_exercises')) {
	function miuni_ensure_user_exercises(PDO $pdo, int $userId, int $tipoId, int $target = 8): array
	{
		miuni_ensure_ejercicios_schema($pdo);
		$exercises = miuni_fetch_user_exercises($pdo, $userId, $tipoId);
		$needed = $target - count($exercises);

		if ($needed > 0) {
			$insertStmt = $pdo->prepare(
				'INSERT INTO ejercicios_usuario (usuario_id, tipo_id, sumando_uno, sumando_dos)
				 VALUES (:uid, :tid, :uno, :dos)'
			);

			for ($i = 0; $i < $needed; $i++) {
				$top = miuni_random_int(10000, 99999);
				$bottom = miuni_random_int(10, 99);
				$insertStmt->execute([
					':uid' => $userId,
					':tid' => $tipoId,
					':uno' => $top,
					':dos' => $bottom
				]);
			}

			$exercises = miuni_fetch_user_exercises($pdo, $userId, $tipoId);
		}

		return $exercises;
	}
}

if (!function_exists('miuni_count_completed_exercises')) {
	function miuni_count_completed_exercises(array $exercises): int
	{
		$completed = 0;
		foreach ($exercises as $exercise) {
			if ((int)($exercise['resuelto'] ?? 0) === 1 && (int)($exercise['correcto'] ?? 0) === 1) {
				$completed++;
			}
		}
		return $completed;
	}
}
