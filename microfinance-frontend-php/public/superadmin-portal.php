<?php
$page_title = 'Administration — ZEN-PAY';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, sans-serif;
      background: linear-gradient(160deg, #f0fdf4 0%, #dcfce7 100%);
      color: #1a2e1a;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
    }

    /* ── Logo ── */
    .zp-logo {
      text-align: center;
      margin-bottom: 28px;
    }
    .zp-logo img {
      height: 52px;
      width: auto;
      display: block;
      margin: 0 auto 10px;
      filter: drop-shadow(0 2px 6px rgba(22,163,74,.25));
    }
    .zp-logo-name {
      font-size: .72rem;
      font-weight: 700;
      color: #166534;
      letter-spacing: .15em;
      text-transform: uppercase;
      opacity: .85;
    }

    /* ── Card ── */
    .zp-card {
      background: #fff;
      border: 1px solid #bbf7d0;
      border-top: 3px solid #16a34a;
      border-radius: 8px;
      padding: 32px 30px 28px;
      width: 100%;
      max-width: 360px;
      box-shadow: 0 4px 24px rgba(22,163,74,.10), 0 1px 4px rgba(0,0,0,.06);
    }
    .zp-card h1 {
      font-size: 1rem;
      font-weight: 700;
      color: #14532d;
      text-align: center;
      margin-bottom: 4px;
    }
    .zp-card .step-hint {
      font-size: .74rem;
      color: #4d7c60;
      text-align: center;
      margin-bottom: 24px;
    }

    /* ── Stepper ── */
    .stepper {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 22px;
    }
    .step-dot {
      width: 28px; height: 28px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: .72rem; font-weight: 700;
    }
    .step-dot.active  { background: #16a34a; color: #fff; }
    .step-dot.done    { background: #86efac; color: #14532d; }
    .step-dot.pending { background: #f0fdf4; color: #86efac; border: 2px solid #bbf7d0; }
    .step-line { flex: 1; height: 2px; background: #bbf7d0; max-width: 32px; }

    /* ── Formulaire ── */
    .zp-form-group { margin-bottom: 18px; }
    .zp-form-group label {
      display: block;
      font-size: .81rem;
      font-weight: 600;
      color: #166534;
      margin-bottom: 5px;
    }
    .zp-input {
      width: 100%;
      padding: 9px 12px;
      border: 1.5px solid #d1fae5;
      border-radius: 6px;
      font-size: .88rem;
      color: #1a2e1a;
      background: #fff;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .zp-input:focus {
      border-color: #16a34a;
      box-shadow: 0 0 0 3px rgba(22,163,74,.12);
    }
    .zp-input[readonly] {
      background: #f0fdf4;
      color: #4d7c60;
      cursor: not-allowed;
      border-color: #bbf7d0;
    }

    /* ── Bouton ── */
    .zp-btn {
      width: 100%;
      padding: 10px 14px;
      background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: .9rem;
      font-weight: 700;
      cursor: pointer;
      height: 42px;
      transition: opacity .15s, transform .1s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      letter-spacing: .02em;
      box-shadow: 0 2px 8px rgba(22,163,74,.3);
    }
    .zp-btn:hover:not(:disabled) { opacity: .92; transform: translateY(-1px); }
    .zp-btn:active:not(:disabled){ transform: translateY(0); }
    .zp-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    /* ── Erreur ── */
    .zp-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 6px;
      padding: 9px 12px;
      font-size: .82rem;
      color: #991b1b;
      margin-bottom: 16px;
      display: none;
    }

    /* ── Séparateur ── */
    .zp-divider {
      border: none;
      border-top: 1px solid #dcfce7;
      margin: 20px 0 16px;
    }

    /* ── Liens ── */
    .zp-links {
      text-align: center;
      margin-top: 16px;
      font-size: .78rem;
    }
    .zp-links a { color: #4d7c60; text-decoration: none; }
    .zp-links a:hover { color: #16a34a; text-decoration: underline; }

    /* ── Spinner ── */
    .spinner {
      display: inline-block;
      width: 15px; height: 15px;
      border: 2px solid rgba(255,255,255,.4);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .65s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Pied de page ── */
    footer {
      margin-top: 28px;
      font-size: .72rem;
      color: #4d7c60;
      text-align: center;
      opacity: .8;
    }
  </style>
</head>
<body>

  <div class="zp-logo">
    <img src="img/logo.svg" alt="ZEN-PAY">
    <div class="zp-logo-name">Portail Administrateur</div>
  </div>

  <!-- ÉTAPE 1 : Clé secrète -->
  <div class="zp-card" id="step-key">
    <h1>Accès sécurisé</h1>
    <p class="step-hint">Authentification en deux étapes</p>

    <div class="stepper">
      <div class="step-dot active">1</div>
      <div class="step-line"></div>
      <div class="step-dot pending">2</div>
    </div>

    <div class="zp-error" id="err-key"></div>

    <form id="form-key" autocomplete="off">
      <div class="zp-form-group">
        <label for="key-email">Adresse e-mail</label>
        <input class="zp-input" type="email" id="key-email" required
               placeholder="superadmin@exemple.com" autocomplete="username">
      </div>
      <div class="zp-form-group">
        <label for="key-secret">Mot clé secret</label>
        <input class="zp-input" type="password" id="key-secret" required
               placeholder="••••••••" autocomplete="new-password">
      </div>
      <button type="submit" class="zp-btn" id="btn-key">
        Continuer
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </form>
  </div>

  <!-- ÉTAPE 2 : Mot de passe -->
  <div class="zp-card" id="step-pass" style="display:none;">
    <h1>Connexion</h1>
    <p class="step-hint">Saisissez votre mot de passe</p>

    <div class="stepper">
      <div class="step-dot done">✓</div>
      <div class="step-line" style="background:#16a34a;"></div>
      <div class="step-dot active">2</div>
    </div>

    <div class="zp-error" id="err-pass"></div>

    <form id="form-pass">
      <div class="zp-form-group">
        <label>Compte vérifié</label>
        <input class="zp-input" type="email" id="pass-email" readonly>
      </div>
      <hr class="zp-divider">
      <div class="zp-form-group">
        <label for="pass-mdp">Mot de passe</label>
        <input class="zp-input" type="password" id="pass-mdp" required
               placeholder="••••••••" autocomplete="current-password">
      </div>
      <button type="submit" class="zp-btn" id="btn-pass">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        Se connecter
      </button>
    </form>

    <div class="zp-links">
      <a href="#" onclick="retourEtape1(); return false;">← Retour</a>
    </div>
  </div>

  <div class="zp-links" id="lien-retour" style="margin-top:18px;">
    <a href="login.php">← Retour à la connexion</a>
  </div>

  <footer>&copy; <?= date('Y') ?> ZEN-PAY · Accès restreint — Personnel autorisé uniquement</footer>

<script>
const API_BASE = localStorage.getItem("mf_api_base") || (
  (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
    ? "http://localhost:8080"
    : window.location.protocol + "//" + window.location.hostname + ":8080"
);

let emailVerifie = "";

function afficherErreur(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.style.display = "block";
}
function cacherErreur(id) {
  document.getElementById(id).style.display = "none";
}

document.getElementById("form-key").addEventListener("submit", async (e) => {
  e.preventDefault();
  cacherErreur("err-key");
  const btn = document.getElementById("btn-key");
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Vérification…';

  const email = document.getElementById("key-email").value.trim();
  const cleSecrete = document.getElementById("key-secret").value;

  try {
    const res = await fetch(API_BASE + "/api/auth/superadmin/verify-key", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, cleSecrete })
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || "Accès refusé.");

    emailVerifie = email;
    document.getElementById("pass-email").value = email;
    document.getElementById("step-key").style.display = "none";
    document.getElementById("step-pass").style.display = "block";
    document.getElementById("lien-retour").style.display = "none";
    document.getElementById("pass-mdp").focus();
  } catch (err) {
    afficherErreur("err-key", err.message);
  } finally {
    btn.disabled = false;
    btn.innerHTML = 'Continuer <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
  }
});

document.getElementById("form-pass").addEventListener("submit", async (e) => {
  e.preventDefault();
  cacherErreur("err-pass");
  const btn = document.getElementById("btn-pass");
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Connexion…';

  const motDePasse = document.getElementById("pass-mdp").value;

  try {
    const res = await fetch(API_BASE + "/api/auth/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: emailVerifie, motDePasse })
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || "Identifiants incorrects.");
    if (!data.token) throw new Error("Réponse inattendue du serveur.");

    localStorage.setItem("mf_jwt_token", data.token);
    localStorage.setItem("mf_user", JSON.stringify({ email: data.email, role: data.role }));
    window.location.href = "dashboard.php";
  } catch (err) {
    afficherErreur("err-pass", err.message);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Se connecter';
  }
});

function retourEtape1() {
  document.getElementById("step-pass").style.display = "none";
  document.getElementById("step-key").style.display = "block";
  document.getElementById("lien-retour").style.display = "block";
  document.getElementById("key-secret").value = "";
  document.getElementById("pass-mdp").value = "";
  cacherErreur("err-key");
  cacherErreur("err-pass");
}
</script>
</body>
</html>
