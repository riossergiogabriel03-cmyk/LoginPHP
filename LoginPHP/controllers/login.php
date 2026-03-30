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
    json_response(['ok' => false, 'message' => 'Contraseña inválida.'], 400);
  }

  $user = user_find_by_email($email);
  if (!$user) {
    json_response(['ok' => false, 'message' => 'Credenciales incorrectas.'], 401);
  }
  if (empty($user['password_hash']) || !password_verify($password, (string) $user['password_hash'])) {
    json_response(['ok' => false, 'message' => 'Credenciales incorrectas.'], 401);
  }

  $otp = generate_otp();
  $_SESSION['otp_email'] = $email;
  $_SESSION['otp_hash'] = otp_hash($otp);
  $_SESSION['otp_expires_at'] = time() + OTP_TTL_SECONDS;
  $_SESSION['otp_verified'] = false;

  send_otp_email($email, $otp);

  json_response([
    'ok' => true,
    'message' => 'Te enviamos un código OTP a tu correo.',
    'ttlSeconds' => OTP_TTL_SECONDS,
  ]);
} catch (Throwable $e) {
  $msg = 'No se pudo enviar el OTP. Revisa SMTP en config.php.';
  if (defined('APP_DEBUG') && APP_DEBUG) {
    $msg .= ' Detalle: ' . $e->getMessage();
  }
  json_response(['ok' => false, 'message' => $msg], 500);
}

