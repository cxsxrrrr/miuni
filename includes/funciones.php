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
        $cache = $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'];
        if (is_array($cache)) {
            return $cache;
        }

        try {
            $stmt = $pdo->query('SHOW COLUMNS FROM ejercicios_usuario');
            $cols = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cols[$row['Field']] = true;
            }
        } catch (Throwable $e) {
            miuni_log_error('No se pudo leer columnas de ejercicios_usuario: ' . $e->getMessage());
            $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'] = ['uno' => 'sumando_uno', 'dos' => 'sumando_dos'];
            return $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'];
        }

        $uno = isset($cols['sumando_uno']) ? 'sumando_uno' : (isset($cols['operando_uno']) ? 'operando_uno' : 'sumando_uno');
        $dos = isset($cols['sumando_dos']) ? 'sumando_dos' : (isset($cols['operando_dos']) ? 'operando_dos' : 'sumando_dos');

        $result = ['uno' => $uno, 'dos' => $dos];
        $GLOBALS['MIUNI_EJERCICIOS_COLUMN_CACHE'] = $result;
        return $result;
    }
}

if (!function_exists('miuni_get_ejercicios_oper_column_names')) {
    function miuni_get_ejercicios_oper_column_names(PDO $pdo): array
    {
        try {
            $stmt = $pdo->query('SHOW COLUMNS FROM ejercicios_usuario');
            $cols = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cols[$row['Field']] = true;
            }
        } catch (Throwable $e) {
            miuni_log_error('No se pudo leer columnas legacy de ejercicios_usuario: ' . $e->getMessage());
            return ['uno' => null, 'dos' => null];
        }

        return [
            'uno' => isset($cols['operando_uno']) ? 'operando_uno' : null,
            'dos' => isset($cols['operando_dos']) ? 'operando_dos' : null,
        ];
    }
}

if (!function_exists('miuni_ensure_ejercicios_schema')) {
    function miuni_ensure_ejercicios_schema(PDO $pdo): void
    {
        // Intencion: migrar/asegurar columnas necesarias en ejercicios_usuario.
        try {
            $fetch = function () use ($pdo) {
                $stmt = $pdo->query('SHOW COLUMNS FROM ejercicios_usuario');
                $cols = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $cols[$row['Field']] = $row;
                }
                return $cols;
            };

            $columns = $fetch();
        } catch (Throwable $e) {
            miuni_log_error('No se pudo validar la tabla ejercicios_usuario: ' . $e->getMessage());
            return;
        }

        try {
            // Cambios mínimos razonables: renombrar operando_* a sumando_* si están presentes
            if (isset($columns['operando_uno']) && !isset($columns['sumando_uno'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario CHANGE COLUMN operando_uno sumando_uno INT NOT NULL');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (isset($columns['operando_dos']) && !isset($columns['sumando_dos'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario CHANGE COLUMN operando_dos sumando_dos INT NOT NULL');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }

            // Asegurar columnas básicas
            if (!isset($columns['sumando_uno'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN sumando_uno INT NOT NULL DEFAULT 0 AFTER tipo_id');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (!isset($columns['sumando_dos'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN sumando_dos INT NOT NULL DEFAULT 0 AFTER sumando_uno');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (!isset($columns['respuesta_usuario'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN respuesta_usuario INT DEFAULT NULL AFTER sumando_dos');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (!isset($columns['correcto'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN correcto BOOLEAN NOT NULL DEFAULT FALSE AFTER respuesta_usuario');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (!isset($columns['resuelto'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN resuelto BOOLEAN NOT NULL DEFAULT FALSE AFTER correcto');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (!isset($columns['activo'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN activo BOOLEAN NOT NULL DEFAULT TRUE AFTER resuelto');
                miuni_reset_ejercicios_column_cache();
                $columns = $fetch();
            }
            if (!isset($columns['fecha_creacion'])) {
                $pdo->exec('ALTER TABLE ejercicios_usuario ADD COLUMN fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER activo');
                miuni_reset_ejercicios_column_cache();
            }
        } catch (Throwable $alterException) {
            miuni_log_error('No se pudo ajustar la tabla ejercicios_usuario: ' . $alterException->getMessage());
        }
    }
}

if (!function_exists('miuni_randomize_unresolved_exercises')) {
    /**
     * Re-assign random values to unresolved (resuelto = 0) active exercises for a user and tipo.
     */
    function miuni_randomize_unresolved_exercises(PDO $pdo, int $userId, int $tipoId, string $operation = 'suma'): void
    {
        miuni_ensure_ejercicios_schema($pdo);
        $columns = miuni_get_ejercicios_sum_column_names($pdo);
        $legacy = miuni_get_ejercicios_oper_column_names($pdo);

        $select = $pdo->prepare('SELECT id FROM ejercicios_usuario WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1 AND resuelto = 0 ORDER BY fecha_creacion ASC, id ASC');
        $select->execute([':uid' => $userId, ':tid' => $tipoId]);
        $ids = $select->fetchAll(PDO::FETCH_COLUMN);
        if (empty($ids)) {
            return;
        }

        $setParts = [];
        $order = [];
        $setParts[] = sprintf('%s = ?', $columns['uno']);
        $order[] = 'uno';
        $setParts[] = sprintf('%s = ?', $columns['dos']);
        $order[] = 'dos';

        if ($legacy['uno'] && $legacy['uno'] !== $columns['uno']) {
            $setParts[] = sprintf('%s = ?', $legacy['uno']);
            $order[] = 'uno';
        }
        if ($legacy['dos'] && $legacy['dos'] !== $columns['dos']) {
            $setParts[] = sprintf('%s = ?', $legacy['dos']);
            $order[] = 'dos';
        }

        $updateSql = sprintf('UPDATE ejercicios_usuario SET %s WHERE id = ? AND usuario_id = ?', implode(', ', $setParts));
        $updateStmt = $pdo->prepare($updateSql);

        foreach ($ids as $id) {
            $top = miuni_random_int(10000, 99999);
            $bottom = miuni_random_int(10000, 99999);
            if ($operation === 'resta' && $bottom > $top) {
                $swap = $top;
                $top = $bottom;
                $bottom = $swap;
            }

            $params = [];
            foreach ($order as $key) {
                $params[] = $key === 'uno' ? $top : $bottom;
            }
            $params[] = (int)$id;
            $params[] = $userId;

            try {
                $updateStmt->execute($params);
            } catch (Throwable $e) {
                miuni_log_error('Error randomizando ejercicio '.$id.': '.$e->getMessage());
            }
        }
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
    function miuni_ensure_user_exercises(PDO $pdo, int $userId, int $tipoId, int $target = 8, string $operation = 'suma'): array
    {
        miuni_ensure_ejercicios_schema($pdo);
        $exercises = miuni_fetch_user_exercises($pdo, $userId, $tipoId);
        $needed = $target - count($exercises);

        if ($needed > 0) {
            $columns = miuni_get_ejercicios_sum_column_names($pdo);
            $legacyColumns = miuni_get_ejercicios_oper_column_names($pdo);
            $fields = ['usuario_id', 'tipo_id', $columns['uno'], $columns['dos']];
            $placeholders = ['?', '?', '?', '?'];

            $hasLegacyUno = $legacyColumns['uno'] !== null && $legacyColumns['uno'] !== $columns['uno'];
            $hasLegacyDos = $legacyColumns['dos'] !== null && $legacyColumns['dos'] !== $columns['dos'];
            if ($hasLegacyUno) {
                $fields[] = $legacyColumns['uno'];
                $placeholders[] = '?';
            }
            if ($hasLegacyDos) {
                $fields[] = $legacyColumns['dos'];
                $placeholders[] = '?';
            }

            $insertSql = sprintf('INSERT INTO ejercicios_usuario (%s) VALUES (%s)', implode(', ', $fields), implode(', ', $placeholders));
            $insertStmt = $pdo->prepare($insertSql);

            for ($i = 0; $i < $needed; $i++) {
                $top = miuni_random_int(10000, 99999);
                $bottom = miuni_random_int(10000, 99999);
                if ($operation === 'resta' && $bottom > $top) {
                    $swap = $top;
                    $top = $bottom;
                    $bottom = $swap;
                }
                $params = [$userId, $tipoId, $top, $bottom];
                if ($hasLegacyUno) {
                    $params[] = $top;
                }
                if ($hasLegacyDos) {
                    $params[] = $bottom;
                }
                $insertStmt->execute($params);
            }

            $exercises = miuni_fetch_user_exercises($pdo, $userId, $tipoId);
        }

        return $exercises;
    }
}

if (!function_exists('miuni_reset_user_exercises')) {
    function miuni_reset_user_exercises(PDO $pdo, int $userId, int $tipoId, int $target = 8, string $operation = 'suma'): void
    {
        miuni_ensure_ejercicios_schema($pdo);
        $columns = miuni_get_ejercicios_sum_column_names($pdo);
        $legacy = miuni_get_ejercicios_oper_column_names($pdo);

        $manageTransaction = !$pdo->inTransaction();
        if ($manageTransaction) {
            $pdo->beginTransaction();
        }

        $needEnsureAfter = false;

        try {
            $selectActive = $pdo->prepare('SELECT id FROM ejercicios_usuario WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1 ORDER BY fecha_creacion ASC, id ASC');
            $selectActive->execute([':uid' => $userId, ':tid' => $tipoId]);
            $activeIds = $selectActive->fetchAll(PDO::FETCH_COLUMN);
            $activeIds = array_map('intval', $activeIds ?: []);
            $idsToReset = array_slice($activeIds, 0, $target);

            if (empty($idsToReset)) {
                $needEnsureAfter = true;
                if ($manageTransaction && $pdo->inTransaction()) {
                    $pdo->commit();
                }
            } else {
                $setParts = [];
                $order = [];

                $setParts[] = sprintf('%s = ?', $columns['uno']);
                $order[] = 'uno';
                $setParts[] = sprintf('%s = ?', $columns['dos']);
                $order[] = 'dos';
                $setParts[] = 'respuesta_usuario = NULL';
                $setParts[] = 'resuelto = 0';
                $setParts[] = 'correcto = 0';
                $setParts[] = 'activo = 1';
                $setParts[] = 'fecha_creacion = NOW()';

                if ($legacy['uno'] && $legacy['uno'] !== $columns['uno']) {
                    $setParts[] = sprintf('%s = ?', $legacy['uno']);
                    $order[] = 'uno';
                }
                if ($legacy['dos'] && $legacy['dos'] !== $columns['dos']) {
                    $setParts[] = sprintf('%s = ?', $legacy['dos']);
                    $order[] = 'dos';
                }

                $updateSql = sprintf('UPDATE ejercicios_usuario SET %s WHERE id = ? AND usuario_id = ?', implode(', ', $setParts));
                $updateStmt = $pdo->prepare($updateSql);

                foreach ($idsToReset as $exerciseId) {
                    $top = miuni_random_int(10000, 99999);
                    $bottom = miuni_random_int(10000, 99999);
                    if ($operation === 'resta' && $bottom > $top) {
                        $swap = $top;
                        $top = $bottom;
                        $bottom = $swap;
                    }
                    $params = [];
                    foreach ($order as $key) {
                        $params[] = $key === 'uno' ? $top : $bottom;
                    }
                    $params[] = (int)$exerciseId;
                    $params[] = $userId;
                    $updateStmt->execute($params);
                }

                $extraIds = array_values(array_diff($activeIds, $idsToReset));
                if (!empty($extraIds)) {
                    $placeholders = implode(', ', array_fill(0, count($extraIds), '?'));
                    $deactivateSql = sprintf(
                        'UPDATE ejercicios_usuario SET activo = 0, respuesta_usuario = NULL, resuelto = 0, correcto = 0 WHERE usuario_id = ? AND tipo_id = ? AND id IN (%s)',
                        $placeholders
                    );
                    $params = array_merge([$userId, $tipoId], array_map('intval', $extraIds));
                    $deactivateStmt = $pdo->prepare($deactivateSql);
                    $deactivateStmt->execute($params);
                }

                if (count($idsToReset) < $target) {
                    $needEnsureAfter = true;
                }

                if ($manageTransaction && $pdo->inTransaction()) {
                    $pdo->commit();
                }
            }
        } catch (Throwable $e) {
            if ($manageTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        if ($needEnsureAfter) {
            miuni_ensure_user_exercises($pdo, $userId, $tipoId, $target, $operation);
        }
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
