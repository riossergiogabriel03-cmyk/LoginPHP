<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear cuenta</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,200..900;1,9..40,200..900&family=Playfair+Display:wght@400;600;700;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../public/styles.css">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body>
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <div class="login-layout">
    <aside class="login-panel" aria-hidden="true">
      <div class="login-panel__bg"></div>
      <div class="login-panel__pattern"></div>
      <div class="login-panel__grid"></div>
      <div class="login-panel__orb login-panel__orb--primary"></div>
      <div class="login-panel__orb login-panel__orb--secondary"></div>
      <canvas class="login-panel__particles" id="particlesCanvas"></canvas>

      <div class="login-panel__content">
        <div class="login-panel__brand">
          <div class="login-panel__logo">
            <i data-lucide="hexagon" style="width:22px;height:22px;color:var(--color-bg)"></i>
          </div>
          <span class="login-panel__logo-text">Malvados y Asociados</span>
        </div>

        <h1 class="login-panel__headline">
          Crea tu<br>cuenta con<br><span>seguridad</span>
        </h1>
        <p class="login-panel__description">
          Crea tu cuenta con correo y contraseña.
          Luego podrás iniciar sesión y confirmar con OTP.
        </p>
      </div>
    </aside>

    <main class="login-form-panel">
      <div class="login-card">
        <header class="login-card__header">
          <h2 class="login-card__title">Crear cuenta</h2>
          <p class="login-card__subtitle">
            ¿Ya tienes cuenta? <a href="./login.php">Iniciar sesión</a>
          </p>
        </header>

        <form id="registerForm" novalidate autocomplete="on">
          <div class="form-group" id="regEmailGroup">
            <label class="form-group__label" for="regEmail">Correo electronico</label>
            <div class="form-group__input-wrapper">
              <i data-lucide="mail" class="form-group__icon"></i>
              <input class="form-group__input" type="email" id="regEmail" name="email" placeholder="tu@correo.com" autocomplete="email" required>
            </div>
            <div class="form-group__error" id="regEmailError" role="alert">
              <i data-lucide="alert-circle"></i><span></span>
            </div>
          </div>

          <div class="form-group" id="regPasswordGroup">
            <label class="form-group__label" for="regPassword">Contrasena</label>
            <div class="form-group__input-wrapper">
              <i data-lucide="lock" class="form-group__icon"></i>
              <input class="form-group__input" type="password" id="regPassword" name="password" placeholder="Minimo 6 caracteres" autocomplete="new-password" required>
            </div>
            <div class="form-group__error" id="regPasswordError" role="alert">
              <i data-lucide="alert-circle"></i><span></span>
            </div>
          </div>

          <button type="submit" class="btn-primary" id="registerBtn">
            <span class="btn-primary__content">
              <span class="btn-primary__text">Crear cuenta</span>
              <span class="btn-primary__spinner"></span>
            </span>
          </button>
        </form>

        <div class="after-register is-hidden" id="afterRegister">
          <p class="after-register__text">
            Cuenta creada. Ya puedes volver e iniciar sesión.
          </p>
          <a class="btn-secondary" href="./login.php" id="backToLoginBtn">Volver al login</a>
        </div>
      </div>
    </main>
  </div>

  <script src="../public/app.js"></script>
</body>
</html>

