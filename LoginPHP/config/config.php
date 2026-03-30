<?php
declare(strict_types=1);

/**
 * Configuración SMTP para PHPMailer.
 * Rellena estos valores con los de tu correo (Gmail/Outlook/hosting).
 *
 * IMPORTANTE:
 * - No subas contraseñas reales a internet.
 * - En Gmail normalmente necesitas "App Password" (no tu contraseña normal).
 */

// Gmail SMTP
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587; // 587 (STARTTLS) o 465 (SMTPS)
const SMTP_USER = 'riossergiogabriel03@gmail.com';
const SMTP_PASS = 'omicczrplowvfumh';
const SMTP_SECURE = 'tls'; // 'tls' o 'ssl'

const MAIL_FROM_EMAIL = 'riossergiogabriel03@gmail.com';
const MAIL_FROM_NAME  = 'Nexus';

// Base de datos (MySQL / MariaDB en XAMPP)
// Ajusta según tu phpMyAdmin (por defecto suele ser root sin contraseña)
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'loginphp';
const DB_USER = 'root';
const DB_PASS = '';

// OTP
const OTP_TTL_SECONDS = 300; // 5 minutos
const OTP_LENGTH = 6;        // 6 dígitos

// Debug (solo para desarrollo local)
const APP_DEBUG = true;

