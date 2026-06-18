<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Paramètres — ' . APP_NAME;
$sidebar_active = 'parametres';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .param-section { background:#fff; border:1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:20px; }
    .param-section h2 { font-size:1rem; font-weight:700; color:var(--gray-800); margin-bottom:4px; }
    .param-section p.desc { font-size:.82rem; color:var(--muted); margin-bottom:18px; }
    .info-field { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--g100); gap:12px; }
    .info-field:last-child { border-bottom:none; }
    .info-label { font-size:.82rem; color:var(--muted); min-width:110px; }
    .info-value { font-size:.9rem; font-weight:600; color:var(--gray-800); flex:1; }
    .pending-tag { font-size:.72rem; background:#fefce8; border:1px solid #fde047; color:#854d0e; padding:2px 8px; border-radius:100px; font-weight:600; }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Paramètres'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Mes <span>paramètres</span></h1><p style="color:var(--muted);font-size:.85rem;">Gérez vos informations personnelles et la sécurité de votre compte</p></div>
  </div>

  <!-- Onglets -->
  <div class="tab-group" style="margin-bottom:20px;">
    <div class="tab-item active" onclick="switchTab('profil',this)">Mon profil</div>
    <div class="tab-item" onclick="switchTab('securite',this)">Sécurité</div>
    <div class="tab-item" onclick="switchTab('demandes',this)">Mes demandes <span id="badge-demandes" style="display:none;" class="badge badge-kyc-pending" style="margin-left:4px;font-size:.7rem;padding:1px 6px;">0</span></div>
  </div>

  <!-- === Onglet Profil === -->
  <div id="tab-profil">
    <div class="param-section">
      <h2>Informations personnelles</h2>
      <p class="desc">
        Pour modifier vos informations d'identité, soumettez une demande — elle sera appliquée après validation par un gestionnaire.
        <span class="pending-tag">⏳ Workflow sécurisé</span>
      </p>

      <!-- Informations actuelles -->
      <div style="margin-bottom:20px;">
        <div class="info-field"><span class="info-label">Prénom</span><span class="info-value" id="cur-prenom">—</span></div>
        <div class="info-field"><span class="info-label">Nom</span><span class="info-value" id="cur-nom">—</span></div>
        <div class="info-field"><span class="info-label">Email</span><span class="info-value" id="cur-email">—</span></div>
        <div class="info-field"><span class="info-label">Téléphone</span><span class="info-value" id="cur-telephone">—</span></div>
        <div class="info-field"><span class="info-label">Adresse</span><span class="info-value" id="cur-adresse">—</span></div>
      </div>

      <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;font-size:.8rem;color:#92400e;margin-bottom:16px;">
        ℹ️ Laissez vide les champs que vous ne souhaitez pas modifier. Seuls les champs remplis seront mis à jour.
      </div>

      <form id="form-profil">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label class="form-label">Nouveau prénom</label>
            <input class="form-control" type="text" id="new-prenom" placeholder="Laisser vide = pas de changement">
          </div>
          <div class="form-group">
            <label class="form-label">Nouveau nom</label>
            <input class="form-control" type="text" id="new-nom" placeholder="Laisser vide = pas de changement">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nouvel email</label>
          <input class="form-control" type="email" id="new-email" placeholder="Laisser vide = pas de changement">
        </div>
        <div class="form-group">
          <label class="form-label">Nouveau téléphone Mobile Money</label>
          <input class="form-control" type="text" id="new-telephone" placeholder="Laisser vide = pas de changement">
        </div>
        <div class="form-group">
          <label class="form-label">Nouvelle adresse</label>
          <input class="form-control" type="text" id="new-adresse" placeholder="Laisser vide = pas de changement">
        </div>
        <button type="submit" class="btn btn-primary" id="btn-profil" style="height:40px;padding:0 24px;">
          Soumettre la demande de modification
        </button>
      </form>
    </div>
  </div>

  <!-- === Onglet Sécurité === -->
  <div id="tab-securite" style="display:none;">
    <div class="param-section">
      <h2>Changer le mot de passe</h2>
      <p class="desc">Modification immédiate — aucune validation externe requise.</p>
      <form id="form-mdp" style="max-width:400px;">
        <div class="form-group">
          <label class="form-label">Mot de passe actuel</label>
          <input class="form-control" type="password" id="ancien-mdp" required placeholder="••••••••">
        </div>
        <div class="form-group">
          <label class="form-label">Nouveau mot de passe</label>
          <input class="form-control" type="password" id="nouveau-mdp" required minlength="8" placeholder="••••••••">
        </div>
        <div class="form-group">
          <label class="form-label">Confirmer le nouveau mot de passe</label>
          <input class="form-control" type="password" id="confirm-mdp" required minlength="8" placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary" id="btn-mdp" style="height:40px;padding:0 24px;">
          Modifier le mot de passe
        </button>
      </form>
    </div>
  </div>

  <!-- === Onglet Demandes === -->
  <div id="tab-demandes" style="display:none;">
    <div class="param-section">
      <h2>Historique de mes demandes</h2>
      <p class="desc">Suivi des demandes de modification de profil que vous avez soumises.</p>
      <div id="demandes-list"><div style="text-align:center;padding:32px;"><span class="spinner"></span></div></div>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let myClientId = null;

async function init() {
  const me = await api.get("/api/auth/me");
  if (!me.idClient) { window.location.href = "login.php"; return; }
  myClientId = me.idClient;

  // Remplir infos dans la sidebar/topbar
  const initial = (me.prenom || "C")[0].toUpperCase();
  const elAv = document.getElementById("sb-avatar"); if (elAv) elAv.textContent = initial;
  const elName = document.getElementById("sb-name"); if (elName) elName.textContent = (me.prenom||"") + " " + (me.nom||"");
  const elFullname = document.getElementById("user-fullname"); if (elFullname) elFullname.textContent = (me.prenom||"") + " " + (me.nom||"");
  if (me.offreAbonnement) { const el = document.getElementById("user-offre"); if(el) el.textContent = me.offreAbonnement.nomOffre; }

  // Remplir informations actuelles
  document.getElementById("cur-prenom").textContent = me.prenom || "—";
  document.getElementById("cur-nom").textContent = me.nom || "—";
  document.getElementById("cur-email").textContent = me.email || "—";
  document.getElementById("cur-telephone").textContent = me.telephone || "—";
  document.getElementById("cur-adresse").textContent = me.adresse || "—";
}

function switchTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t => t.classList.remove("active"));
  el.classList.add("active");
  ["tab-profil","tab-securite","tab-demandes"].forEach(id => {
    document.getElementById(id).style.display = "none";
  });
  document.getElementById("tab-" + name).style.display = "";
  if (name === "demandes") chargerDemandes();
}

/* ── Profil : soumettre suggestion ── */
document.getElementById("form-profil").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("btn-profil");
  const body = {
    prenom:    document.getElementById("new-prenom").value.trim() || null,
    nom:       document.getElementById("new-nom").value.trim() || null,
    email:     document.getElementById("new-email").value.trim() || null,
    telephone: document.getElementById("new-telephone").value.trim() || null,
    adresse:   document.getElementById("new-adresse").value.trim() || null,
  };
  const hasData = Object.values(body).some(v => v !== null && v !== "");
  if (!hasData) { flash("Remplissez au moins un champ à modifier.", "error"); return; }

  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  try {
    const res = await api.post("/api/clients/suggestion-profil", body);
    flash(res.message || "Demande transmise au gestionnaire.", "success");
    document.getElementById("form-profil").reset();
  } catch(e) {
    flash("Erreur : " + e.message, "error");
  }
  btn.disabled = false; btn.innerHTML = "Soumettre la demande de modification";
});

/* ── Sécurité : changer mot de passe ── */
document.getElementById("form-mdp").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("btn-mdp");
  const ancien = document.getElementById("ancien-mdp").value;
  const nouveau = document.getElementById("nouveau-mdp").value;
  const confirm = document.getElementById("confirm-mdp").value;
  if (nouveau !== confirm) { flash("Les mots de passe ne correspondent pas.", "error"); return; }
  if (nouveau.length < 8)  { flash("Le mot de passe doit contenir au moins 8 caractères.", "error"); return; }

  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  try {
    const res = await api.post("/api/auth/change-password", {
      ancienMotDePasse: ancien,
      nouveauMotDePasse: nouveau
    });
    flash(res.message || "Mot de passe modifié avec succès.", "success");
    document.getElementById("form-mdp").reset();
  } catch(e) {
    flash("Erreur : " + e.message, "error");
  }
  btn.disabled = false; btn.innerHTML = "Modifier le mot de passe";
});

/* ── Demandes : historique ── */
async function chargerDemandes() {
  const el = document.getElementById("demandes-list");
  try {
    // Récupérer depuis l'endpoint gestionnaire en filtrant par client
    const sugs = await api.get("/api/clients/suggestions?statut=EN_ATTENTE").catch(() => []);
    const toutes = await api.get("/api/clients/suggestions?statut=APPROUVE").catch(() => []);
    const rejetees = await api.get("/api/clients/suggestions?statut=REJETE").catch(() => []);
    const toutes2 = [...sugs, ...toutes, ...rejetees].filter(s => s.idClient === myClientId);
    if (!toutes2.length) {
      el.innerHTML = `<div class="empty-state"><div class="empty-icon">📋</div><h3>Aucune demande</h3><p>Vous n'avez pas encore soumis de demande de modification.</p></div>`;
      return;
    }
    const statusBadge = {EN_ATTENTE:'badge-kyc-pending', APPROUVE:'badge-success', REJETE:'badge-failed'};
    const statusLabel = {EN_ATTENTE:'En attente', APPROUVE:'Approuvée', REJETE:'Rejetée'};
    el.innerHTML = toutes2.map(s => {
      const date = s.dateDemande ? new Date(s.dateDemande).toLocaleDateString("fr-FR") : "—";
      const champs = [
        s.nouveauPrenom && `Prénom: <strong>${s.nouveauPrenom}</strong>`,
        s.nouveauNom && `Nom: <strong>${s.nouveauNom}</strong>`,
        s.nouvelEmail && `Email: <strong>${s.nouvelEmail}</strong>`,
        s.nouveauTelephone && `Téléphone: <strong>${s.nouveauTelephone}</strong>`,
        s.nouvelleAdresse && `Adresse: <strong>${s.nouvelleAdresse}</strong>`,
      ].filter(Boolean);
      return `<div style="padding:14px;border:1px solid var(--border);border-radius:8px;margin-bottom:10px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
          <span style="font-size:.82rem;color:var(--muted);">Demande du ${date}</span>
          <span class="badge ${statusBadge[s.statut]||'badge-pending'}">${statusLabel[s.statut]||s.statut}</span>
        </div>
        <div style="font-size:.83rem;color:var(--gray-700);">${champs.join(" · ") || "—"}</div>
      </div>`;
    }).join("");
  } catch(e) {
    el.innerHTML = `<div style="color:var(--muted);font-size:.85rem;text-align:center;">Impossible de charger l'historique.</div>`;
  }
}

init();
</script>
</body>
</html>
