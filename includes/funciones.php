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

if (!array_key_exists('MIUNI_EJERCICIOS_COLUMN_CACHE', $GLOBALS)) {
	$GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'] = null;
}

if (!function_exists('miuni_reset_ejercicios_column_cache')) {
	function miuni_reset_ejercicios_column_cache(): void
	{
		$GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'] = null;
	}
}

if (!function_exists('miuni_get_ejercicios_sum_column_names')) {
	function miuni_get_ejercicios_sum_column_names(PDO $pdo): array
	{
		if (is_array($GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'])) {
			return $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'];
		}

		try {
			$columns = [];
			$stmt = $pdo->query('SHOW COLUMNS FROM ejercicios_usuario');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$columns[$row['Field']] = true;
			}
		} catch (Throwable $e) {
			miuni_log_error('No se pudo leer columnas de ejercicios_usuario: ' . $e->getMessage());
			$GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'] = ['uno' => 'sumando_uno', 'dos' => 'sumando_dos'];
			return $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'];
		}

		$sumUno = isset($columns['sumando_uno']) ? 'sumando_uno' : (isset($columns['operando_uno']) ? 'operando_uno' : 'sumando_uno');
		$sumDos = isset($columns['sumando_dos']) ? 'sumando_dos' : (isset($columns['operando_dos']) ? 'operando_dos' : 'sumando_dos');

		$GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'] = ['uno' => $sumUno, 'dos' => $sumDos];
		return $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'];
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
			$fetchColumns = function () use ($pdo) {
				$cols = [];
				$stmt = $pdo->query('SHOW COLUMNS FROM ejercicios_usuario');
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$cols[$row['Field']] = $row;
				}
				return $cols;
			};

			$columns = $fetchColumns();
		} catch (Throwable $e) {
			miuni_log_error('No se pudo validar la tabla ejercicios_usuario: ' . $e->getMessage());
			return;
		}

		try {
			if (isset($columns['operando_uno']) && !isset($columns['sumando_uno'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario CHANGE COLUMN operando_uno sumando_uno INT NOT NULL');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			}
			if (isset($columns['operando_dos']) && !isset($columns['sumando_dos'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario CHANGE COLUMN operando_dos sumando_dos INT NOT NULL');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			}

			if (!isset($columns['sumando_uno'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN sumando_uno INT NOT NULL DEFAULT 0 AFTER tipo_id');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			} else {
				$pdo->exec('ALTER TABLE ejercicios_usuario MODIFY COLUMN sumando_uno INT NOT NULL');
			}

			if (!isset($columns['sumando_dos'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN sumando_dos INT NOT NULL DEFAULT 0 AFTER sumando_uno');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			} else {
				$pdo->exec('ALTER TABLE ejercicios_usuario MODIFY COLUMN sumando_dos INT NOT NULL');
			}

			if (!isset($columns['respuesta_usuario'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN respuesta_usuario INT DEFAULT NULL AFTER sumando_dos');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			}

			if (!isset($columns['correcto'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN correcto BOOLEAN NOT NULL DEFAULT FALSE AFTER respuesta_usuario');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			} else {
				$pdo->exec('ALTER TABLE ejercicios_usuario MODIFY COLUMN correcto BOOLEAN NOT NULL DEFAULT FALSE');
			}

			if (!isset($columns['resuelto'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN resuelto BOOLEAN NOT NULL DEFAULT FALSE AFTER correcto');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			} else {
				$pdo->exec('ALTER TABLE ejercicios_usuario MODIFY COLUMN resuelto BOOLEAN NOT NULL DEFAULT FALSE');
			}

			if (!isset($columns['activo'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN activo BOOLEAN NOT NULL DEFAULT TRUE AFTER resuelto');
				miuni_reset_ejercicios_column_cache();
				$columns = $fetchColumns();
			} else {
				$pdo->exec('ALTER TABLE ejercicios_usuario MODIFY COLUMN activo BOOLEAN NOT NULL DEFAULT TRUE');
			}

			if (!isset($columns['fecha_creacion'])) {
				$pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER activo');
				miuni_reset_ejercicios_column_cache();
			}
		} catch (Throwable $alterException) {
			miuni_log_error('No se pudo ajustar la tabla ejercicios_usuario: ' . $alterException->getMessage());
		}

		$checked = true;
	}
}

if (!function_exists('miuni_fetch_user_exercises')) {
	function miuni_fetch_user_exercises(PDO $pdo, int $userId, int $tipoId): array
	{
		miuni_ensure_ejercicios_schema($pdo);
		$columns = miuni_get_ejercicios_sum_column_names($pdo);
		$sql = sprintf(
			'SELECT id, %1$s AS sumando_uno, %2$s AS sumando_dos, respuesta_usuario, correcto, resuelto, fecha_creacion
			 FROM ejercicios_usuario
			 WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1
			 ORDER BY fecha_creacion ASC, id ASC',
			$columns['uno'],
			$columns['dos']
		);
		$stmt = $pdo->prepare($sql);
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
			$columns = miuni_get_ejercicios_sum_column_names($pdo);
			$insertSql = sprintf(
				'INSERT INTO ejercicios_usuario (usuario_id, tipo_id, %1$s, %2$s)
				 VALUES (:uid, :tid, :uno, :dos)',
				$columns['uno'],
				$columns['dos']
			);
			$insertStmt = $pdo->prepare($insertSql);

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
