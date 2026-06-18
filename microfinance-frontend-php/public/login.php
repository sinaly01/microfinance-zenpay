<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Connexion — ZEN-PAY';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-page">

  <!-- Panneau gauche décoratif -->
  <div class="login-left">
    <a href="index.php" class="logo" style="color:rgba(255,255,255,.9);margin-bottom:auto;">
      <img src="img/logo.svg" alt="ZEN-PAY" style="height:34px;width:auto;filter:brightness(0) invert(1);">
    </a>
    <div class="login-left-content">
      <h2>La microfinance digitale de la zone BCEAO</h2>
      <p>Gérez votre argent depuis votre smartphone, rechargez via Mobile Money et effectuez vos virements en toute sécurité.</p>
      <ul class="login-features">
        <li>Versements & retraits via Wave, Orange, MTN, Moov</li>
        <li>Virements instantanés entre clients ZEN-PAY</li>
        <li>Sécurité JWT + OTP sur chaque transaction</li>
        <li>Conformité KYC et réglementation BCEAO</li>
        <li>Tableau de bord et historique en temps réel</li>
      </ul>
    </div>
    <div style="margin-top:auto;padding-top:32px;font-size:.78rem;opacity:.5;">
      &copy; <?= APP_YEAR ?> ZEN-PAY · Projet académique
      <a href="superadmin-portal.php" style="color:inherit;text-decoration:none;margin-left:6px;opacity:.6;" title="">⬡</a>
    </div>
  </div>

  <!-- Panneau droit — formulaire -->
  <div class="login-right">
    <div class="login-form-wrap">
      <div class="login-logo" style="justify-content:center;">
        <img src="img/logo.svg" alt="ZEN-PAY" style="height:42px;width:auto;">
      </div>
      <h1 class="login-title">Bienvenue</h1>
      <p class="login-sub">Connectez-vous à votre espace ZEN-PAY</p>

      <!-- Étape 1 : Identifiants -->
      <div id="step-login">
        <form id="login-form">
          <div class="form-group">
            <label class="form-label" for="email">Adresse e-mail</label>
            <input class="form-control" type="email" id="email" required
                   autocomplete="username" placeholder="exemple@microfinance.local">
          </div>
          <div class="form-group">
            <label class="form-label" for="motDePasse">
              Mot de passe
              <a href="forgot-password.php" style="float:right;font-weight:400;color:var(--primary);font-size:.8rem;">Oublié ?</a>
            </label>
            <input class="form-control" type="password" id="motDePasse" required
                   autocomplete="current-password" placeholder="••••••••">
          </div>
          <button type="submit" class="btn btn-primary w-full" id="submit-btn" style="height:44px;margin-top:8px;">
            Se connecter
          </button>
        </form>
        <p style="margin-top:16px;text-align:center;font-size:.85rem;">
          Pas encore de compte ?
          <a href="register.php" style="color:var(--primary);font-weight:600;">S'inscrire</a>
        </p>
        <p style="margin-top:8px;text-align:center;font-size:.85rem;">
          <a href="index.php" style="color:var(--muted);">← Retour à l'accueil</a>
        </p>
      </div>

      <!-- Étape 2 : Code OTP (gestionnaire) -->
      <div id="step-otp" style="display:none;">
        <div style="text-align:center;margin-bottom:24px;">
          <div style="font-size:2.5rem;margin-bottom:12px;">📧</div>
          <p style="font-size:.9rem;color:var(--gray-600);">
            Un code de confirmation à 6 chiffres a été envoyé à<br>
            <strong id="otp-email-display" style="color:var(--primary);"></strong>
          </p>
        </div>
        <form id="otp-form">
          <div class="form-group">
            <label class="form-label">Code OTP</label>
            <input class="form-control" type="text" id="otp-code" required
                   maxlength="6" placeholder="• • • • • •"
                   style="font-size:1.4rem;letter-spacing:.4em;text-align:center;font-weight:700;">
          </div>
          <button type="submit" class="btn btn-primary w-full" id="otp-btn" style="height:44px;margin-top:8px;">
            Vérifier le code
          </button>
        </form>
        <div style="margin-top:16px;text-align:center;font-size:.83rem;color:var(--muted);">
          Code valable 10 minutes
          <div style="margin-top:10px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <button id="resend-btn" onclick="renvoyerOtp()" style="background:none;border:1px solid var(--primary);color:var(--primary);border-radius:6px;padding:6px 14px;cursor:pointer;font-size:.83rem;font-weight:600;">
              ↻ Renvoyer le code
            </button>
            <button onclick="retourLogin()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:.83rem;">
              Ressaisir mes identifiants
            </button>
          </div>
          <div id="resend-msg" style="margin-top:8px;font-size:.8rem;"></div>
        </div>
      </div>

      <!-- IP bloquée -->
      <div id="step-blocked" style="display:none;text-align:center;padding:16px 0;">
        <div style="font-size:2.5rem;margin-bottom:12px;">🚫</div>
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:8px;color:var(--danger);">Réseau non autorisé</h3>
        <p style="font-size:.85rem;color:var(--gray-600);margin-bottom:12px;">
          Votre adresse IP n'est pas dans la liste des réseaux autorisés.<br>
          Une demande d'accès temporaire a été transmise au Super Admin.
        </p>
        <div style="background:#f3f4f6;border-radius:8px;padding:10px;margin-bottom:16px;font-size:.82rem;">
          IP détectée : <code id="blocked-ip" style="font-weight:700;color:var(--danger);">—</code><br>
          <span style="font-size:.76rem;color:var(--muted);">Communiquez cette IP au Super Admin pour qu'il l'autorise.</span>
        </div>
        <button onclick="retourLogin()" class="btn btn-outline w-full">Réessayer</button>
      </div>
    </div>
  </div>

</div>

<script src="js/api.js"></script>
<script>
const API_URL = localStorage.getItem("mf_api_base") || "http://localhost:8080";
let pendingEmail = "";

function show(step) {
  ["step-login","step-otp","step-blocked"].forEach(id => {
    document.getElementById(id).style.display = "none";
  });
  document.getElementById("step-" + step).style.display = "";
}

function retourLogin() { show("login"); }

/* ── Étape 1 : Connexion ────────────────────────────────── */
document.getElementById("login-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("submit-btn");
  const email = document.getElementById("email").value.trim();
  const motDePasse = document.getElementById("motDePasse").value;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';

  try {
    const res = await fetch(API_URL + "/api/auth/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, motDePasse })
    });
    const data = await res.json().catch(() => ({}));

    if (res.status === 200) {
      saveSession(data);
      flash("Connexion réussie !", "success");
      const role = data.role || "";
      setTimeout(() => {
        window.location.href = role.includes("CLIENT") ? "client-dashboard.php" : "dashboard.php";
      }, 500);
      return;
    }

    if (res.status === 202 && data.step === "OTP_REQUIRED") {
      pendingEmail = data.email || email;
      document.getElementById("otp-email-display").textContent = pendingEmail;
      show("otp");
      btn.disabled = false;
      btn.innerHTML = "Se connecter";
      return;
    }

    if (res.status === 403 && data.step === "IP_BLOCKED") {
      // Récupérer l'IP détectée pour l'afficher
      try {
        const ipRes = await fetch(API_URL + "/api/auth/my-ip");
        const ipData = await ipRes.json().catch(() => ({}));
        const el = document.getElementById("blocked-ip");
        if (el) el.textContent = ipData.ip || ipData.ipRaw || "inconnue";
      } catch(e) {}
      show("blocked");
      btn.disabled = false;
      btn.innerHTML = "Se connecter";
      return;
    }

    flash("Échec : " + (data.error || data.message || "Identifiants incorrects"), "error");
  } catch (err) {
    flash("Erreur réseau : " + err.message, "error");
  }
  btn.disabled = false;
  btn.innerHTML = "Se connecter";
});

/* ── Renvoi OTP ─────────────────────────────────────────── */
let resendCooldown = 0;
async function renvoyerOtp() {
  if (resendCooldown > 0) return;
  const btn = document.getElementById("resend-btn");
  const msg = document.getElementById("resend-msg");
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';
  try {
    const res = await fetch(API_URL + "/api/auth/resend-otp", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: pendingEmail })
    });
    const data = await res.json().catch(() => ({}));
    if (res.ok) {
      msg.style.color = "var(--success, #16a34a)";
      msg.textContent = "Code renvoyé ! Vérifiez votre boîte mail (et les spams).";
      resendCooldown = 60;
      const interval = setInterval(() => {
        resendCooldown--;
        if (resendCooldown <= 0) {
          clearInterval(interval);
          btn.disabled = false;
          btn.innerHTML = "↻ Renvoyer le code";
          msg.textContent = "";
        } else {
          btn.innerHTML = `↻ Renvoyer (${resendCooldown}s)`;
        }
      }, 1000);
    } else {
      msg.style.color = "var(--danger)";
      msg.textContent = data.error || "Erreur lors du renvoi.";
      btn.disabled = false;
      btn.innerHTML = "↻ Renvoyer le code";
    }
  } catch(err) {
    msg.style.color = "var(--danger)";
    msg.textContent = "Erreur réseau.";
    btn.disabled = false;
    btn.innerHTML = "↻ Renvoyer le code";
  }
}

/* ── Étape 2 : Vérification OTP ─────────────────────────── */
document.getElementById("otp-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("otp-btn");
  const code = document.getElementById("otp-code").value.trim();
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';

  try {
    const res = await fetch(API_URL + "/api/auth/verify-otp", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: pendingEmail, code })
    });
    const data = await res.json().catch(() => ({}));

    if (res.ok) {
      saveSession(data);
      flash("Connexion réussie !", "success");
      setTimeout(() => { window.location.href = "dashboard.php"; }, 500);
      return;
    }
    flash("Code incorrect ou expiré : " + (data.error || ""), "error");
  } catch (err) {
    flash("Erreur réseau : " + err.message, "error");
  }
  btn.disabled = false;
  btn.innerHTML = "Vérifier le code";
});
</script>


</body>
</html>
