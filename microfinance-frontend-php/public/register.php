<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Inscription — ZEN-PAY';
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
      <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;">Z</div>
      ZEN-PAY
    </a>
    <div class="login-left-content">
      <h2>Rejoignez la microfinance digitale BCEAO</h2>
      <p>Créez votre compte en quelques minutes et profitez de tous nos services de gestion d'argent mobile.</p>
      <ul class="login-features">
        <li>Compte ouvert gratuitement (Offre Standard)</li>
        <li>Versements & retraits via Mobile Money</li>
        <li>Virements instantanés entre clients ZEN-PAY</li>
        <li>Tableau de bord et historique en temps réel</li>
        <li>Support en ligne 24h/24</li>
      </ul>
    </div>
    <div style="margin-top:auto;padding-top:32px;font-size:.78rem;opacity:.5;">
      &copy; <?= APP_YEAR ?> ZEN-PAY · Projet académique
    </div>
  </div>

  <!-- Panneau droit — formulaire -->
  <div class="login-right" style="overflow-y:auto;">
    <div class="login-form-wrap" style="max-width:400px;">
      <div class="login-logo">
        <div class="login-logo-icon">Z</div>
        <span style="font-weight:800;font-size:1.2rem;color:var(--gray-900);">ZEN-PAY</span>
      </div>
      <h1 class="login-title">Créer mon compte</h1>
      <p class="login-sub">Remplissez les informations ci-dessous — c'est gratuit</p>

      <!-- Étape affichage -->
      <div id="step-1">
        <form id="register-form">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
              <label class="form-label">Prénom</label>
              <input class="form-control" type="text" id="prenom" placeholder="Jean" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nom</label>
              <input class="form-control" type="text" id="nom" placeholder="Dupont" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Téléphone Mobile Money</label>
            <input class="form-control" type="text" id="telephone" placeholder="+2250700000000" required>
          </div>
          <div class="form-group">
            <label class="form-label">Adresse e-mail</label>
            <input class="form-control" type="email" id="email" placeholder="jean@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Adresse de résidence</label>
            <input class="form-control" type="text" id="adresse" placeholder="Abidjan, Cocody" required>
          </div>
          <div class="form-group">
            <label class="form-label">N° CNI (optionnel)</label>
            <input class="form-control" type="text" id="numeroCni" placeholder="CI-AB-12345">
          </div>
          <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <input class="form-control" type="password" id="motDePasse" placeholder="••••••••" required minlength="8">
          </div>
          <div class="form-group">
            <label class="form-label">Confirmer le mot de passe</label>
            <input class="form-control" type="password" id="motDePasseConfirm" placeholder="••••••••" required minlength="8">
          </div>

          <div style="background:var(--g50);border:1px solid var(--g200);border-radius:var(--r);padding:12px;margin-bottom:16px;font-size:.8rem;color:var(--gray-600);">
            ℹ️ Votre compte nécessitera une validation de pièce d'identité (KYC) pour être pleinement activé.
          </div>

          <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:20px;padding:12px;background:#fefce8;border:1px solid #fde047;border-radius:var(--r);">
            <input type="checkbox" id="accepteCgu" required style="margin-top:3px;width:16px;height:16px;flex-shrink:0;cursor:pointer;">
            <label for="accepteCgu" style="font-size:.82rem;color:var(--gray-700);cursor:pointer;line-height:1.4;">
              J'ai lu et j'accepte les
              <a href="index.php#cgu" target="_blank" style="color:var(--primary);font-weight:700;text-decoration:underline;">Conditions Générales d'Utilisation</a>
              et la
              <a href="index.php#rgpd" target="_blank" style="color:var(--primary);font-weight:700;text-decoration:underline;">Politique de Protection des Données</a>
              de ZEN-PAY. <span style="color:var(--danger);">*</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary w-full" id="submit-btn" style="height:44px;">
            Créer mon compte
          </button>
        </form>

        <p style="margin-top:16px;text-align:center;font-size:.85rem;color:var(--muted);">
          Déjà inscrit ?
          <a href="login.php" style="color:var(--primary);font-weight:600;">Se connecter</a>
        </p>
        <p style="margin-top:8px;text-align:center;font-size:.82rem;">
          <a href="index.php" style="color:var(--muted);">← Retour à l'accueil</a>
        </p>
      </div>

      <!-- Succès -->
      <div id="step-success" style="display:none;text-align:center;padding:32px 0;">
        <div style="font-size:3rem;margin-bottom:16px;">🎉</div>
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--g700);margin-bottom:8px;">Compte créé !</h2>
        <p style="color:var(--muted);margin-bottom:12px;font-size:.9rem;">
          Votre compte ZEN-PAY a bien été créé.<br>
          Vous pouvez vous connecter et <strong>télécharger votre pièce d'identité</strong> (onglet Validation) pour activer votre compte.
        </p>
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px;margin-bottom:20px;font-size:.82rem;color:#166534;">
          📋 Préparez votre CNI (recto + verso) ou passeport en photo ou PDF.
        </div>
        <a href="login.php" class="btn btn-primary w-full">Se connecter et valider mon identité</a>
        <p style="margin-top:12px;font-size:.82rem;">
          <a href="index.php" style="color:var(--muted);">← Retour à l'accueil</a>
        </p>
      </div>

    </div>
  </div>

</div>

<script src="js/api.js"></script>
<script>
document.getElementById("register-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("submit-btn");
  const mdp = document.getElementById("motDePasse").value;
  const mdpC = document.getElementById("motDePasseConfirm").value;
  if (mdp !== mdpC) { flash("Les mots de passe ne correspondent pas", "error"); return; }
  if (mdp.length < 8) { flash("Le mot de passe doit comporter au moins 8 caractères", "error"); return; }

  const cgu = document.getElementById("accepteCgu");
  if (!cgu.checked) { flash("Vous devez accepter les Conditions Générales d'Utilisation.", "error"); return; }

  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';

  const body = {
    nom: document.getElementById("nom").value.trim(),
    prenom: document.getElementById("prenom").value.trim(),
    telephone: document.getElementById("telephone").value.trim(),
    email: document.getElementById("email").value.trim(),
    adresse: document.getElementById("adresse").value.trim(),
    numeroCni: document.getElementById("numeroCni").value.trim() || null,
    motDePasse: mdp
  };

  try {
    await api.post("/api/auth/register", body);
    document.getElementById("step-1").style.display = "none";
    document.getElementById("step-success").style.display = "";
  } catch(err) {
    flash("Erreur : " + err.message, "error");
    btn.disabled = false; btn.innerHTML = "Créer mon compte";
  }
});
</script>
</body>
</html>
