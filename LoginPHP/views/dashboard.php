<?php
declare(strict_types=1);

session_start();

$ok = !empty($_SESSION['logged_in']) && !empty($_SESSION['otp_verified']) && !empty($_SESSION['user_email']);
if (!$ok) {
  header('Location: ./login.php');
  exit;
}

$email = (string) $_SESSION['user_email'];
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../public/styles.css" />
  </head>
  <body>
    <div class="login-layout" style="grid-template-columns: 1fr">
      <main class="login-form-panel">
        <div class="login-card">
          <header class="login-card__header">
            <h1 class="login-card__title">Bienvenido</h1>
            <p class="login-card__subtitle">
              Has iniciado sesión como <strong><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></strong>.
            </p>
          </header>

          <a class="btn-secondary" href="./login.php">Ir al login</a>
        </div>
      </main>
    </div>
  </body>
</html>

