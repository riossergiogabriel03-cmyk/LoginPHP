<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

// Carga manual de PHPMailer (sin Composer)
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

function mailer_new(): PHPMailer {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = SMTP_HOST;
  $mail->SMTPAuth = true;
  $mail->Username = SMTP_USER;
  $mail->Password = SMTP_PASS;
  $mail->Port = SMTP_PORT;

  if (SMTP_SECURE === 'ssl') {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  } else {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  }

  $mail->CharSet = 'UTF-8';
  $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
  return $mail;
}

function json_response(array $data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function read_json_body(): array {
  $raw = file_get_contents('php://input') ?: '';
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function send_otp_email(string $toEmail, string $otp): void {
  $mail = mailer_new();
  $mail->addAddress($toEmail);

  $mail->isHTML(true);
  $mail->Subject = 'Tu código de verificación (OTP)';
  $mail->Body =
    '<div style="font-family:Arial,sans-serif;line-height:1.6">' .
    '<h2 style="margin:0 0 12px">Verificación de inicio de sesión</h2>' .
    '<p>Tu código OTP es:</p>' .
    '<p style="font-size:28px;font-weight:700;letter-spacing:6px;margin:12px 0">' . htmlspecialchars($otp) . '</p>' .
    '<p style="color:#666">Este código expira en ' . (int) floor(OTP_TTL_SECONDS / 60) . ' minutos.</p>' .
    '</div>';
  $mail->AltBody = "Tu código OTP es: {$otp}. Expira en " . OTP_TTL_SECONDS . " segundos.";

  $mail->send();
}

function generate_otp(): string {
  $max = (10 ** OTP_LENGTH) - 1;
  $min = 10 ** (OTP_LENGTH - 1);
  return (string) random_int($min, $max);
}

function otp_hash(string $otp): string {
  $salt = $_SESSION['otp_salt'] ?? '';
  if (!is_string($salt) || $salt === '') {
    $salt = bin2hex(random_bytes(16));
    $_SESSION['otp_salt'] = $salt;
  }
  return hash('sha256', $salt . '|' . $otp);
}

