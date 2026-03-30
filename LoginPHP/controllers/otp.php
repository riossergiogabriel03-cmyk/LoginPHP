<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../services/EmailService.php';

$data = read_json_body();
$otp = isset($data['otp']) ? preg_replace('/\D+/', '', (string) $data['otp']) : '';

if (!isset($_SESSION['otp_hash'], $_SESSION['otp_expires_at'], $_SESSION['otp_email'])) {
  json_response(['ok' => false, 'message' => 'No hay un OTP pendiente.'], 400);
}

if (!is_int($_SESSION['otp_expires_at']) || time() > $_SESSION['otp_expires_at']) {
  unset($_SESSION['otp_hash'], $_SESSION['otp_expires_at'], $_SESSION['otp_email'], $_SESSION['otp_verified']);
  json_response(['ok' => false, 'message' => 'El OTP expiró. Solicita uno nuevo.'], 400);
}

if ($otp === '' || strlen($otp) !== OTP_LENGTH) {
  json_response(['ok' => false, 'message' => 'OTP inválido.'], 400);
}

$hash = otp_hash($otp);
if (!hash_equals((string) $_SESSION['otp_hash'], $hash)) {
  json_response(['ok' => false, 'message' => 'OTP incorrecto.'], 401);
}

$_SESSION['otp_verified'] = true;
$_SESSION['logged_in'] = true;
$_SESSION['user_email'] = (string) $_SESSION['otp_email'];

unset($_SESSION['otp_hash'], $_SESSION['otp_expires_at']);

json_response(['ok' => true, 'message' => 'Sesión verificada con OTP.']);

