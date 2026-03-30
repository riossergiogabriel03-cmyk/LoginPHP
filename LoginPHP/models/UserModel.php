<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function user_find_by_email(string $email): ?array {
  $email = strtolower(trim($email));
  $stmt = db()->prepare('SELECT id, email, password_hash, created_at FROM users WHERE email = ? LIMIT 1');
  $stmt->execute([$email]);
  $u = $stmt->fetch();
  return is_array($u) ? $u : null;
}

function user_create(string $email, string $passwordHash): int {
  $email = strtolower(trim($email));
  $stmt = db()->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
  $stmt->execute([$email, $passwordHash]);
  return (int) db()->lastInsertId();
}

