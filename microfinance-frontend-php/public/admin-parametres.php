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
    .info-field { display:flex; align-items:center; padding:10px 0; border-bottom:1px solid var(--g100); gap:12px; }
    .info-field:last-child { border-bottom:none; }
    .info-label { font-size:.82rem; color:var(--muted); min-width:110px; }
    .info-value { font-size:.9rem; font-weight:600; color:var(--gray-800); flex:1; }
    .locked-badge { font-size:.72rem; background:#f1f5f9; border:1px solid var(--border); color:var(--muted); padding:2px 8px; border-radius:100px; }
    .lock-icon { color:var(--g400); flex-shrink:0; }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Paramètres'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Mes <span>paramètres</span></h1><p style="color:var(--muted);font-size:.85rem;">Paramètres de votre compte professionnel</p></div>
  </div>

  <!-- Alerte sécurité -->
  <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;display:flex;gap:12px;align-items:flex-start;margin-bottom:20px;">
    <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <div>
      <div style="font-weight:700;font-size:.88rem;color:#92400e;">Sécurité interne — Informations professionnelles verrouillées</div>
      <div style="font-size:.8rem;color:#a16207;margin-top:2px;">Vos informations d'identité professionnelle (nom, email, rôle) sont gérées par l'administration. Pour toute modification, adressez une demande formelle signée à votre Admin Système ou Super Admin.</div>
    </div>
  </div>

  <!-- Informations du compte -->
  <div class="param-section">
    <h2>Informations du compte</h2>
    <p class="desc">Ces informations sont verrouillées et ne peuvent être modifiées que par un administrateur.</p>

    <div class="info-field">
      <span class="info-label">Prénom</span>
      <span class="info-value" id="info-prenom">—</span>
      <svg class="lock-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
    <div class="info-field">
      <span class="info-label">Nom</span>
      <span class="info-value" id="info-nom">—</span>
      <svg class="lock-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
    <div class="info-field">
      <span class="info-label">Email</span>
      <span class="info-value" id="info-email">—</span>
      <svg class="lock-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
    <div class="info-field">
      <span class="info-label">Rôle</span>
      <span class="info-value" id="info-role">—</span>
      <svg class="lock-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
  </div>

  <!-- Mode maintenance (Super Admin uniquement) -->
  <div class="param-section" id="section-maintenance" style="display:none;border-left:4px solid #dc2626;">
    <h2 style="color:#dc2626;">Mode maintenance d'urgence</h2>
    <p class="desc">Gèle ou rétablit le service. En mode maintenance, toutes les transactions (versement, retrait, virement) sont bloquées pour tous les utilisateurs.</p>
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;padding:14px 16px;background:#fafafa;border:1px solid var(--border);border-radius:8px;">
      <span style="font-size:.83rem;color:var(--muted);font-weight:600;">Statut actuel :</span>
      <span id="maintenance-status-badge">—</span>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <button class="btn" id="btn-activer-maint" onclick="toggleMaintenance(true)"
        style="background:#dc2626;color:#fff;height:40px;padding:0 20px;font-weight:700;">
        Activer la maintenance
      </button>
      <button class="btn" id="btn-desactiver-maint" onclick="toggleMaintenance(false)"
        style="background:#16a34a;color:#fff;height:40px;padding:0 20px;font-weight:700;">
        Désactiver la maintenance
      </button>
    </div>
    <p style="font-size:.76rem;color:var(--muted);margin-top:10px;">⚠️ Les administrateurs restent connectés et peuvent naviguer. Seules les opérations financières sont gelées.</p>
  </div>

  <!-- Clé secrète portail (Super Admin uniquement) -->
  <div class="param-section" id="section-cle-secrete" style="display:none;border-left:4px solid #7c3aed;">
    <h2 style="color:#7c3aed;">Clé secrète du portail administrateur</h2>
    <p class="desc">Cette clé est demandée à l'étape 1 du portail Super Admin. Ne la partagez jamais.</p>
    <form id="form-cle" style="max-width:400px;">
      <div class="form-group">
        <label class="form-label">Clé secrète actuelle</label>
        <input class="form-control" type="password" id="ancienne-cle" required placeholder="••••••••">
      </div>
      <div class="form-group">
        <label class="form-label">Nouvelle clé secrète</label>
        <input class="form-control" type="password" id="nouvelle-cle" required minlength="6" placeholder="Minimum 6 caractères">
      </div>
      <div class="form-group">
        <label class="form-label">Confirmer la nouvelle clé</label>
        <input class="form-control" type="password" id="confirm-cle" required minlength="6" placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary" id="btn-cle" style="height:40px;padding:0 24px;background:#7c3aed;">
        Modifier la clé secrète
      </button>
    </form>
  </div>

  <!-- Changer mot de passe -->
  <div class="param-section">
    <h2>Changer le mot de passe</h2>
    <p class="desc">La modification du mot de passe est immédiate et ne nécessite pas de validation externe.</p>
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

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();

document.addEventListener("sidebarReady", ({ detail: { me, role } }) => {
  document.getElementById("info-prenom").textContent = me.prenom || "—";
  document.getElementById("info-nom").textContent = me.nom || "—";
  document.getElementById("info-email").textContent = me.email || "—";
  const roleLabels = {
    SUPER_ADMIN:"Super Admin", GESTIONNAIRE:"Gestionnaire",
    SUPERVISOR:"Superviseur", ADMIN_SYSTEME:"Admin Système", ADMIN_BD:"Admin BD"
  };
  document.getElementById("info-role").textContent = roleLabels[role] || role;
  // Afficher la section clé secrète uniquement pour le Super Admin
  if (role === "SUPER_ADMIN") {
    document.getElementById("section-cle-secrete").style.display = "block";
    document.getElementById("section-maintenance").style.display = "block";
    chargerStatutMaintenance();
  }
});

/* ── Maintenance (Super Admin) ── */
async function chargerStatutMaintenance() {
  try {
    const data = await api.get("/api/system/status");
    const enMaint = data.status === "MAINTENANCE_CRITIQUE";
    const badge = document.getElementById("maintenance-status-badge");
    if (enMaint) {
      badge.innerHTML = '<span style="color:#dc2626;background:#fef2f2;padding:3px 12px;border-radius:20px;font-weight:700;font-size:.82rem;">🔴 EN MAINTENANCE</span>';
    } else {
      badge.innerHTML = '<span style="color:#16a34a;background:#f0fdf4;padding:3px 12px;border-radius:20px;font-weight:700;font-size:.82rem;">🟢 OPÉRATIONNEL</span>';
    }
    document.getElementById("btn-activer-maint").disabled = enMaint;
    document.getElementById("btn-desactiver-maint").disabled = !enMaint;
  } catch(e) {}
}

async function toggleMaintenance(activer) {
  const msg = activer
    ? "⚠️ Activer la maintenance bloquera toutes les transactions. Confirmer ?"
    : "Désactiver la maintenance et reprendre le service normal ?";
  if (!confirm(msg)) return;
  const btn = document.getElementById(activer ? "btn-activer-maint" : "btn-desactiver-maint");
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  try {
    const res = await api.postQuery("/api/system/kill-switch", { activer });
    flash(res.message || "Statut mis à jour.", activer ? "error" : "success");
    await chargerStatutMaintenance();
  } catch(e) {
    flash("Erreur : " + e.message, "error");
    btn.disabled = false;
    btn.innerHTML = activer ? "Activer la maintenance" : "Désactiver la maintenance";
  }
}

/* ── Modifier clé secrète (Super Admin) ── */
document.getElementById("form-cle").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("btn-cle");
  const ancienne = document.getElementById("ancienne-cle").value;
  const nouvelle = document.getElementById("nouvelle-cle").value;
  const confirm  = document.getElementById("confirm-cle").value;
  if (nouvelle !== confirm) { flash("Les clés ne correspondent pas.", "error"); return; }
  if (nouvelle.length < 6)  { flash("La clé doit contenir au moins 6 caractères.", "error"); return; }
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  try {
    const res = await api.put("/api/gestionnaires/me/cle-secrete", {
      ancienneCle: ancienne,
      nouvelleCle: nouvelle
    });
    flash(res.message || "Clé secrète modifiée.", "success");
    document.getElementById("form-cle").reset();
  } catch(e) {
    flash("Erreur : " + e.message, "error");
  }
  btn.disabled = false; btn.innerHTML = "Modifier la clé secrète";
});

/* ── Changer mot de passe ── */
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
</script>
</body>
</html>
