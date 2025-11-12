<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'No autorizado']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input) || !isset($input['exerciseId'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Solicitud incompleta']);
  exit;
}

$exerciseId = filter_var($input['exerciseId'], FILTER_VALIDATE_INT);
if (!$exerciseId) {
  http_response_code(400);
  echo json_encode(['error' => 'Identificador inválido']);
  exit;
}

$status = $input['status'] ?? 'pending';
$allowedStatuses = ['pending', 'correct', 'incorrect'];
if (!in_array($status, $allowedStatuses, true)) {
  $status = 'pending';
}

$answer = null;
if (isset($input['answer'])) {
  $answerDigits = preg_replace('/[^0-9]/', '', (string)$input['answer']);
  if ($answerDigits !== '') {
    $answer = $answerDigits;
  }
}

require_once __DIR__ . '/../includes/db.php';

$userId = (int)$_SESSION['user_id'];

try {
  $exerciseStmt = $pdo->prepare(
    'SELECT id, tipo_id FROM ejercicios_usuario
     WHERE id = :id AND usuario_id = :uid AND activo = 1
     LIMIT 1'
  );
  $exerciseStmt->execute([
    ':id' => $exerciseId,
    ':uid' => $userId
  ]);

  $exercise = $exerciseStmt->fetch();
  if (!$exercise) {
    http_response_code(404);
    echo json_encode(['error' => 'Ejercicio no encontrado']);
    exit;
  }

  $resuelto = 0;
  $correcto = 0;
  $respuesta = $answer;

  switch ($status) {
    case 'correct':
      $resuelto = 1;
      $correcto = 1;
      break;
    case 'incorrect':
      $resuelto = 1;
      $correcto = 0;
      break;
    default:
      $resuelto = 0;
      $correcto = 0;
      $respuesta = null;
      break;
  }

  $updateStmt = $pdo->prepare(
    'UPDATE ejercicios_usuario
     SET respuesta_usuario = :respuesta, resuelto = :resuelto, correcto = :correcto
     WHERE id = :id AND usuario_id = :uid'
  );

  if ($respuesta === null) {
    $updateStmt->bindValue(':respuesta', null, PDO::PARAM_NULL);
  } else {
    $updateStmt->bindValue(':respuesta', $respuesta, PDO::PARAM_STR);
  }
  $updateStmt->bindValue(':resuelto', $resuelto, PDO::PARAM_INT);
  $updateStmt->bindValue(':correcto', $correcto, PDO::PARAM_INT);
  $updateStmt->bindValue(':id', $exerciseId, PDO::PARAM_INT);
  $updateStmt->bindValue(':uid', $userId, PDO::PARAM_INT);
  $updateStmt->execute();

  $completedStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM ejercicios_usuario
     WHERE usuario_id = :uid AND tipo_id = :tid AND activo = 1 AND correcto = 1 AND resuelto = 1'
  );
  $completedStmt->execute([
    ':uid' => $userId,
    ':tid' => (int)$exercise['tipo_id']
  ]);

  $completed = (int)$completedStmt->fetchColumn();

  echo json_encode([
    'ok' => true,
    'status' => $status,
    'completed' => $completed,
    'resuelto' => $resuelto,
    'correcto' => $correcto
  ]);
} catch (Throwable $exception) {
  error_log('Error actualizando resultado: ' . $exception->getMessage());
  http_response_code(500);
  echo json_encode(['error' => 'No fue posible guardar el resultado']);
}
