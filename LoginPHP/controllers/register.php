<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../models/UserModel.php';

try {
  $data = read_json_body();

  $email = isset($data['email']) ? trim((string) $data['email']) : '';
  $password = isset($data['password']) ? (string) $data['password'] : '';

  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['ok' => false, 'message' => 'Correo inválido.'], 400);
  }
  if ($password === '' || strlen($password) < 6) {
    json_response(['ok' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.'], 400);
  }

  $existing = user_find_by_email($email);
  if ($existing) {
    json_response(['ok' => false, 'message' => 'Ese correo ya está registrado.'], 409);
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);
  user_create($email, $hash);

  json_response(['ok' => true, 'message' => 'Cuenta creada correctamente. Ya puedes iniciar sesión.']);
} catch (Throwable $e) {
  $msg = 'No se pudo crear la cuenta. Revisa la BD.';
  if (defined('APP_DEBUG') && APP_DEBUG) $msg .= ' Detalle: ' . $e->getMessage();
  json_response(['ok' => false, 'message' => $msg], 500);
}

