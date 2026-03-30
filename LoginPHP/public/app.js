/* global lucide */

// Inicialización de iconos Lucide
lucide.createIcons();

// Sistema de toasts
function showToast(message, type = "success") {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = `toast toast--${type}`;

  const iconName = type === "success" ? "check-circle" : "x-circle";
  toast.innerHTML = `
    <i data-lucide="${iconName}" class="toast__icon"></i>
    <span>${message}</span>
  `;

  container.appendChild(toast);
  lucide.createIcons({ nodes: [toast] });

  setTimeout(() => {
    toast.classList.add("toast--exit");
    toast.addEventListener("animationend", () => toast.remove(), { once: true });
  }, 4000);
}

// Validación de formulario
const form = document.getElementById("loginForm");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const otpInput = document.getElementById("otp");
const emailGroup = document.getElementById("emailGroup");
const passwordGroup = document.getElementById("passwordGroup");
const otpGroup = document.getElementById("otpGroup");
const submitBtn = document.getElementById("submitBtn");
const otpHint = document.getElementById("otpHint");
const resendOtpBtn = document.getElementById("resendOtpBtn");

function setFieldError(group, errorEl, message) {
  if (!group || !errorEl) return;
  group.classList.add("form-group--error");
  const span = errorEl.querySelector("span");
  if (span) span.textContent = message;
}

function clearFieldError(group) {
  if (!group) return;
  group.classList.remove("form-group--error");
}

function validateEmail(value) {
  if (!value.trim()) return "El correo electronico es obligatorio";
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!pattern.test(value)) return "Ingresa un correo electronico valido";
  return "";
}

function validatePassword(value) {
  if (!value) return "La contrasena es obligatoria";
  if (value.length < 6) return "Minimo 6 caracteres";
  return "";
}

function validateOtp(value) {
  const digits = (value || "").replace(/\D+/g, "");
  if (!digits) return "El OTP es obligatorio";
  if (digits.length !== 6) return "El OTP debe tener 6 digitos";
  return "";
}

async function postJson(url, body) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({}));
  return { ok: res.ok && data && data.ok !== false, status: res.status, data };
}

function appBasePath() {
  const p = window.location.pathname || "";
  const markers = ["/views/", "/public/"];
  for (const m of markers) {
    const idx = p.indexOf(m);
    if (idx >= 0) return p.slice(0, idx); // ej: /LoginPHP
  }
  return p.replace(/\/[^/]*$/, "");
}

const BASE_PATH = appBasePath();

let otpStep = false;
let otpTtl = 0;
let otpStartedAt = 0;
let otpTimerId = null;

function setOtpStep(enabled) {
  otpStep = enabled;
  if (otpGroup) otpGroup.classList.toggle("is-hidden", !enabled);

  if (emailInput) emailInput.readOnly = enabled;
  if (passwordInput) passwordInput.readOnly = enabled;

  const btnText = submitBtn?.querySelector(".btn-primary__text");
  if (btnText) btnText.textContent = enabled ? "Verificar OTP" : "Enviar OTP";

  if (enabled && otpInput) otpInput.focus();
}

function startOtpCountdown(ttlSeconds) {
  otpTtl = Number(ttlSeconds) || 0;
  otpStartedAt = Date.now();
  if (otpTimerId) window.clearInterval(otpTimerId);

  const tick = () => {
    if (!otpHint) return;
    const elapsed = Math.floor((Date.now() - otpStartedAt) / 1000);
    const left = Math.max(0, otpTtl - elapsed);
    const mm = String(Math.floor(left / 60)).padStart(2, "0");
    const ss = String(left % 60).padStart(2, "0");
    otpHint.textContent = left > 0 ? `Expira en ${mm}:${ss}` : "El código expiró";
  };

  tick();
  otpTimerId = window.setInterval(tick, 1000);
}

if (emailInput && emailGroup) {
  emailInput.addEventListener("blur", () => {
    const error = validateEmail(emailInput.value);
    if (error) setFieldError(emailGroup, document.getElementById("emailError"), error);
    else clearFieldError(emailGroup);
  });

  emailInput.addEventListener("input", () => {
    if (emailGroup.classList.contains("form-group--error")) {
      const error = validateEmail(emailInput.value);
      if (!error) clearFieldError(emailGroup);
    }
  });
}

if (passwordInput && passwordGroup) {
  passwordInput.addEventListener("blur", () => {
    const error = validatePassword(passwordInput.value);
    if (error) setFieldError(passwordGroup, document.getElementById("passwordError"), error);
    else clearFieldError(passwordGroup);
  });

  passwordInput.addEventListener("input", () => {
    if (passwordGroup.classList.contains("form-group--error")) {
      const error = validatePassword(passwordInput.value);
      if (!error) clearFieldError(passwordGroup);
    }
  });
}

if (form && submitBtn && emailInput && passwordInput) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    let hasError = false;

    if (!otpStep) {
      const emailError = validateEmail(emailInput.value);
      const passError = validatePassword(passwordInput.value);

      if (emailError) {
        setFieldError(emailGroup, document.getElementById("emailError"), emailError);
        hasError = true;
      } else {
        clearFieldError(emailGroup);
      }

      if (passError) {
        setFieldError(passwordGroup, document.getElementById("passwordError"), passError);
        hasError = true;
      } else {
        clearFieldError(passwordGroup);
      }

      if (hasError) {
        const firstError = form.querySelector(".form-group--error .form-group__input");
        if (firstError instanceof HTMLElement) firstError.focus();
        return;
      }

      submitBtn.classList.add("btn-primary--loading");
      const resp = await postJson(`${BASE_PATH}/controllers/login.php`, {
        email: emailInput.value,
        password: passwordInput.value,
      });
      submitBtn.classList.remove("btn-primary--loading");

      if (!resp.ok) {
        showToast(resp.data?.message || "No se pudo enviar el OTP", "error");
        return;
      }

      showToast(resp.data?.message || "OTP enviado", "success");
      setOtpStep(true);
      startOtpCountdown(resp.data?.ttlSeconds || 300);
      return;
    }

    const otpError = validateOtp(otpInput?.value || "");
    if (otpError) {
      setFieldError(otpGroup, document.getElementById("otpError"), otpError);
      hasError = true;
    } else {
      clearFieldError(otpGroup);
    }

    if (hasError) return;

    submitBtn.classList.add("btn-primary--loading");
    const resp = await postJson(`${BASE_PATH}/controllers/otp.php`, { otp: otpInput?.value || "" });
    submitBtn.classList.remove("btn-primary--loading");

    if (!resp.ok) {
      setFieldError(otpGroup, document.getElementById("otpError"), resp.data?.message || "OTP incorrecto");
      showToast(resp.data?.message || "OTP incorrecto", "error");
      return;
    }

    showToast(resp.data?.message || "Sesión iniciada", "success");
    window.location.href = `${BASE_PATH}/views/dashboard.php`;
  });
}

if (resendOtpBtn && emailInput && passwordInput && submitBtn) {
  resendOtpBtn.addEventListener("click", async () => {
    submitBtn.classList.add("btn-primary--loading");
    const resp = await postJson(`${BASE_PATH}/controllers/login.php`, {
      email: emailInput.value,
      password: passwordInput.value,
    });
    submitBtn.classList.remove("btn-primary--loading");

    if (!resp.ok) {
      showToast(resp.data?.message || "No se pudo reenviar el OTP", "error");
      return;
    }
    showToast("OTP reenviado", "success");
    startOtpCountdown(resp.data?.ttlSeconds || 300);
  });
}

const toggleBtn = document.getElementById("togglePassword");
let passwordVisible = false;

if (toggleBtn && passwordInput) {
  toggleBtn.addEventListener("click", () => {
    passwordVisible = !passwordVisible;
    passwordInput.type = passwordVisible ? "text" : "password";
    toggleBtn.setAttribute("aria-label", passwordVisible ? "Ocultar contrasena" : "Mostrar contrasena");

    const icon = toggleBtn.querySelector("i");
    if (icon) icon.setAttribute("data-lucide", passwordVisible ? "eye-off" : "eye");
    lucide.createIcons({ nodes: [toggleBtn] });
  });
}

const forgotLink = document.getElementById("forgotLink");
const signupLink = document.getElementById("signupLink");
const googleBtn = document.getElementById("googleBtn");
const githubBtn = document.getElementById("githubBtn");

if (forgotLink) {
  forgotLink.addEventListener("click", (e) => {
    e.preventDefault();
    showToast("Se ha enviado un enlace de recuperacion a tu correo", "success");
  });
}

if (signupLink) {
  signupLink.addEventListener("click", (e) => {
    const href = signupLink.getAttribute("href") || "";
    if (href === "#" || href.trim() === "") {
      e.preventDefault();
      showToast("Redirigiendo al registro...", "success");
    }
  });
}

if (googleBtn) googleBtn.addEventListener("click", () => showToast("Conectando con Google...", "success"));
if (githubBtn) githubBtn.addEventListener("click", () => showToast("Conectando con GitHub...", "success"));

// Partículas (Canvas)
(function initParticles() {
  const canvas = document.getElementById("particlesCanvas");
  if (!(canvas instanceof HTMLCanvasElement)) return;

  const ctx = canvas.getContext("2d");
  if (!ctx) return;

  let width = 0;
  let height = 0;
  let particles = [];
  const PARTICLE_COUNT = 50;
  const CONNECTION_DIST = 120;
  let mouseX = -1000;
  let mouseY = -1000;

  function resize() {
    const parent = canvas.parentElement;
    if (!parent) return;
    const rect = parent.getBoundingClientRect();
    width = canvas.width = rect.width;
    height = canvas.height = rect.height;
  }

  function createParticle() {
    return {
      x: Math.random() * width,
      y: Math.random() * height,
      vx: (Math.random() - 0.5) * 0.4,
      vy: (Math.random() - 0.5) * 0.4,
      radius: Math.random() * 1.5 + 0.5,
      opacity: Math.random() * 0.4 + 0.1,
    };
  }

  function init() {
    resize();
    particles = [];
    for (let i = 0; i < PARTICLE_COUNT; i++) particles.push(createParticle());
  }

  function drawParticle(p) {
    ctx.beginPath();
    ctx.arc(p.x, p.y, Math.max(0.1, p.radius), 0, Math.PI * 2);
    ctx.fillStyle = `rgba(212, 160, 83, ${p.opacity})`;
    ctx.fill();
  }

  function drawConnections() {
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const dx = particles[i].x - particles[j].x;
        const dy = particles[i].y - particles[j].y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist >= CONNECTION_DIST) continue;

        const opacity = (1 - dist / CONNECTION_DIST) * 0.08;
        ctx.beginPath();
        ctx.moveTo(particles[i].x, particles[i].y);
        ctx.lineTo(particles[j].x, particles[j].y);
        ctx.strokeStyle = `rgba(212, 160, 83, ${opacity})`;
        ctx.lineWidth = 0.5;
        ctx.stroke();
      }
    }
  }

  function update() {
    for (const p of particles) {
      const dx = p.x - mouseX;
      const dy = p.y - mouseY;
      const dist = Math.sqrt(dx * dx + dy * dy);
      if (dist < 150 && dist > 0) {
        const force = ((150 - dist) / 150) * 0.02;
        p.vx += (dx / dist) * force;
        p.vy += (dy / dist) * force;
      }

      p.x += p.vx;
      p.y += p.vy;
      p.vx *= 0.99;
      p.vy *= 0.99;

      if (p.x < 0 || p.x > width) p.vx *= -1;
      if (p.y < 0 || p.y > height) p.vy *= -1;
      p.x = Math.max(0, Math.min(width, p.x));
      p.y = Math.max(0, Math.min(height, p.y));
    }
  }

  function animate() {
    ctx.clearRect(0, 0, width, height);
    update();
    drawConnections();
    for (const p of particles) drawParticle(p);
    requestAnimationFrame(animate);
  }

  const parent = canvas.parentElement;
  if (parent) {
    parent.addEventListener("mousemove", (e) => {
      const rect = canvas.getBoundingClientRect();
      mouseX = e.clientX - rect.left;
      mouseY = e.clientY - rect.top;
    });

    parent.addEventListener("mouseleave", () => {
      mouseX = -1000;
      mouseY = -1000;
    });
  }

  window.addEventListener("resize", () => {
    resize();
    for (const p of particles) {
      if (p.x > width) p.x = width * Math.random();
      if (p.y > height) p.y = height * Math.random();
    }
  });

  init();
  animate();
})();

// Registro
const registerForm = document.getElementById("registerForm");
const regEmail = document.getElementById("regEmail");
const regPassword = document.getElementById("regPassword");
const regEmailGroup = document.getElementById("regEmailGroup");
const regPasswordGroup = document.getElementById("regPasswordGroup");
const registerBtn = document.getElementById("registerBtn");
const afterRegister = document.getElementById("afterRegister");

if (registerForm && regEmail && regPassword && registerBtn) {
  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const emailError = validateEmail(regEmail.value);
    const passError = validatePassword(regPassword.value);
    let hasError = false;

    if (emailError) {
      setFieldError(regEmailGroup, document.getElementById("regEmailError"), emailError);
      hasError = true;
    } else {
      clearFieldError(regEmailGroup);
    }

    if (passError) {
      setFieldError(regPasswordGroup, document.getElementById("regPasswordError"), passError);
      hasError = true;
    } else {
      clearFieldError(regPasswordGroup);
    }

    if (hasError) return;

    registerBtn.classList.add("btn-primary--loading");
    const resp = await postJson(`${BASE_PATH}/controllers/register.php`, {
      email: regEmail.value,
      password: regPassword.value,
    });
    registerBtn.classList.remove("btn-primary--loading");

    if (!resp.ok) {
      showToast(resp.data?.message || "No se pudo registrar", "error");
      return;
    }

    showToast(resp.data?.message || "Cuenta creada correctamente.", "success");
    if (afterRegister) afterRegister.classList.remove("is-hidden");
  });
}

