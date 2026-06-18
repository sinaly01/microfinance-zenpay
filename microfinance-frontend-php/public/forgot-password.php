<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mot de passe oublié — ZEN-PAY';
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
      <h2>Réinitialisation sécurisée</h2>
      <p>Un code de vérification à 6 chiffres sera envoyé à votre adresse email enregistrée chez ZEN-PAY.</p>
      <ul class="login-features">
        <li>Code valable 15 minutes uniquement</li>
        <li>Envoi sécurisé sur votre email enregistré</li>
        <li>Chiffrement BCrypt de votre nouveau mot de passe</li>
      </ul>
    </div>
    <div style="margin-top:auto;padding-top:32px;font-size:.78rem;opacity:.5;">
      &copy; <?= APP_YEAR ?> ZEN-PAY · Projet académique
    </div>
  </div>

  <!-- Panneau droit -->
  <div class="login-right">
    <div class="login-form-wrap">
      <div class="login-logo" style="justify-content:center;">
        <img src="img/logo.svg" alt="ZEN-PAY" style="height:42px;width:auto;">
      </div>
      <h1 class="login-title">Mot de passe oublié</h1>

      <!-- Étape 1 : Email -->
      <div id="step-email">
        <p class="login-sub">Entrez votre adresse email pour recevoir un code de réinitialisation.</p>
        <form id="form-email">
          <div class="form-group">
            <label class="form-label">Adresse e-mail</label>
            <input class="form-control" type="email" id="email" required placeholder="exemple@domaine.com" autocomplete="email">
          </div>
          <button type="submit" class="btn btn-primary w-full" id="btn-email" style="height:44px;margin-top:8px;">
            Envoyer le code
          </button>
        </form>
        <p style="margin-top:20px;text-align:center;font-size:.85rem;">
          <a href="login.php" style="color:var(--muted);">← Retour à la connexion</a>
        </p>
      </div>

      <!-- Étape 2 : Code + Nouveau mot de passe -->
      <div id="step-code" style="display:none;">
        <div style="text-align:center;margin-bottom:20px;">
          <div style="font-size:2.5rem;margin-bottom:8px;">📧</div>
          <p style="font-size:.9rem;color:var(--gray-600);">
            Code envoyé à<br>
            <strong id="display-email" style="color:var(--primary);"></strong>
          </p>
          <p style="font-size:.78rem;color:var(--muted);margin-top:4px;">Vérifiez aussi vos spams · valable 15 minutes</p>
        </div>
        <form id="form-reset">
          <div class="form-group">
            <label class="form-label">Code à 6 chiffres</label>
            <input class="form-control" type="text" id="code" required maxlength="6"
                   placeholder="• • • • • •"
                   style="font-size:1.5rem;letter-spacing:.5em;text-align:center;font-weight:700;">
          </div>
          <div class="form-group">
            <label class="form-label">Nouveau mot de passe</label>
            <input class="form-control" type="password" id="nouveauMdp" required minlength="8" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label class="form-label">Confirmer le nouveau mot de passe</label>
            <input class="form-control" type="password" id="confirmMdp" required minlength="8" placeholder="••••••••">
          </div>
          <button type="submit" class="btn btn-primary w-full" id="btn-reset" style="height:44px;margin-top:4px;">
            Réinitialiser le mot de passe
          </button>
        </form>
        <div style="margin-top:16px;text-align:center;">
          <button onclick="retourEmail()" style="background:none;border:none;color:var(--muted);font-size:.83rem;cursor:pointer;">
            ← Ressaisir mon email
          </button>
          <span style="color:var(--g200);margin:0 8px;">|</span>
          <button id="btn-resend" onclick="renvoyerCode()" style="background:none;border:none;color:var(--primary);font-size:.83rem;cursor:pointer;font-weight:600;">
            ↻ Renvoyer le code
          </button>
        </div>
        <div id="resend-msg" style="text-align:center;font-size:.8rem;margin-top:6px;"></div>
      </div>

      <!-- Succès -->
      <div id="step-success" style="display:none;text-align:center;padding:24px 0;">
        <div style="font-size:3rem;margin-bottom:12px;">✅</div>
        <h2 style="font-size:1.3rem;font-weight:800;color:var(--g700);margin-bottom:8px;">Mot de passe réinitialisé !</h2>
        <p style="color:var(--muted);margin-bottom:20px;font-size:.9rem;">
          Votre mot de passe a été mis à jour avec succès.<br>
          Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.
        </p>
        <a href="login.php" class="btn btn-primary w-full">Se connecter</a>
      </div>

    </div>
  </div>

</div>

<script src="js/api.js"></script>
<script>
const API_URL = localStorage.getItem("mf_api_base") || (
  (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
    ? "http://localhost:8080" : ""
);
let pendingEmail = "";
let resendCooldown = 0;

function show(step) {
  ["step-email","step-code","step-success"].forEach(id => {
    document.getElementById(id).style.display = "none";
  });
  document.getElementById("step-" + step).style.display = "";
}

function retourEmail() { show("email"); }

/* ── Étape 1 : Envoi du code ── */
document.getElementById("form-email").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("btn-email");
  const email = document.getElementById("email").value.trim();
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';

  try {
    const res = await fetch(API_URL + "/api/auth/forgot-password", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email })
    });
    const data = await res.json().catch(() => ({}));
    if (res.ok) {
      pendingEmail = email;
      document.getElementById("display-email").textContent = email;
      show("code");
    } else {
      flash(data.error || "Erreur lors de l'envoi.", "error");
    }
  } catch(err) {
    flash("Erreur réseau : " + err.message, "error");
  }
  btn.disabled = false;
  btn.innerHTML = "Envoyer le code";
});

/* ── Renvoi du code ── */
async function renvoyerCode() {
  if (resendCooldown > 0) return;
  const btn = document.getElementById("btn-resend");
  const msg = document.getElementById("resend-msg");
  btn.disabled = true;

  try {
    const res = await fetch(API_URL + "/api/auth/forgot-password", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: pendingEmail })
    });
    if (res.ok) {
      msg.style.color = "var(--success, #16a34a)";
      msg.textContent = "Nouveau code envoyé !";
      resendCooldown = 60;
      const iv = setInterval(() => {
        resendCooldown--;
        if (resendCooldown <= 0) {
          clearInterval(iv);
          btn.disabled = false;
          btn.textContent = "↻ Renvoyer le code";
          msg.textContent = "";
        } else {
          btn.textContent = "↻ Renvoyer (" + resendCooldown + "s)";
        }
      }, 1000);
    } else {
      msg.style.color = "var(--danger)";
      msg.textContent = "Erreur lors du renvoi.";
      btn.disabled = false;
    }
  } catch(err) {
    msg.style.color = "var(--danger)";
    msg.textContent = "Erreur réseau.";
    btn.disabled = false;
  }
}

/* ── Étape 2 : Réinitialisation ── */
document.getElementById("form-reset").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("btn-reset");
  const code = document.getElementById("code").value.trim();
  const mdp = document.getElementById("nouveauMdp").value;
  const confirm = document.getElementById("confirmMdp").value;

  if (mdp !== confirm) { flash("Les mots de passe ne correspondent pas.", "error"); return; }
  if (mdp.length < 8)  { flash("Le mot de passe doit contenir au moins 8 caractères.", "error"); return; }
  if (code.length !== 6) { flash("Le code doit contenir exactement 6 chiffres.", "error"); return; }

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';

  try {
    const res = await fetch(API_URL + "/api/auth/reset-password", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: pendingEmail, code, nouveauMotDePasse: mdp })
    });
    const data = await res.json().catch(() => ({}));
    if (res.ok) {
      show("success");
    } else {
      flash(data.error || "Code invalide ou expiré.", "error");
      btn.disabled = false;
      btn.innerHTML = "Réinitialiser le mot de passe";
    }
  } catch(err) {
    flash("Erreur réseau : " + err.message, "error");
    btn.disabled = false;
    btn.innerHTML = "Réinitialiser le mot de passe";
  }
});
</script>
</body>
</html>
