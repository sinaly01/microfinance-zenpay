<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Gestion Personnel — ' . APP_NAME;
$sidebar_active = 'personnel';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Gestion du Personnel'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Gestion du <span>Personnel</span></h1><p style="color:var(--muted);font-size:.85rem;">Gestionnaires, administrateurs, superviseurs</p></div>
    <div class="dash-header-right" id="btn-ajouter-wrap" style="display:none;">
      <button class="btn btn-primary btn-sm" onclick="showModal()">+ Ajouter membre</button>
    </div>
  </div>

  <!-- Filtres -->
  <div class="card card-pad" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;align-items:center;">
      <input class="form-control" type="text" id="search" placeholder="Nom, email..." oninput="filtrer()" style="max-width:320px;">
      <select class="form-control" id="filter-role" onchange="filtrer()" style="max-width:220px;">
        <option value="">Tous les rôles</option>
        <option value="ROLE_SUPER_ADMIN">Super Admin</option>
        <option value="ROLE_ADMIN_SYSTEME">Admin Système</option>
        <option value="ROLE_SUPERVISOR">Superviseur</option>
        <option value="ROLE_GESTIONNAIRE">Gestionnaire</option>
        <option value="ROLE_ADMIN_BD">Admin BD</option>
      </select>
    </div>
  </div>

  <div class="card">
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Membre</th><th>Email</th><th>Rôle</th><th>Embauche</th><th>Statut</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="personnel-tbody"><tr><td colspan="6" style="text-align:center;padding:40px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</main>
</div>
</div>

<!-- Modal -->
<div id="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;">
  <div class="card card-pad" style="width:480px;max-width:95vw;">
    <h3 style="margin-bottom:16px;font-weight:700;">Ajouter un membre du personnel</h3>
    <form id="form-membre">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group"><label class="form-label">Prénom</label><input class="form-control" id="m-prenom" required></div>
        <div class="form-group"><label class="form-label">Nom</label><input class="form-control" id="m-nom" required></div>
        <div class="form-group" style="grid-column:span 2"><label class="form-label">Email</label><input class="form-control" type="email" id="m-email" required></div>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label">Rôle</label>
          <select class="form-control" id="m-role">
            <option value="ROLE_GESTIONNAIRE">Gestionnaire</option>
            <option value="ROLE_SUPERVISOR">Superviseur</option>
            <option value="ROLE_ADMIN_SYSTEME">Admin Système</option>
            <option value="ROLE_ADMIN_BD">Admin BD</option>
            <option value="ROLE_SUPER_ADMIN" id="opt-super-admin" style="display:none;">Super Admin</option>
          </select>
          <div id="super-admin-note" style="display:none;font-size:.76rem;color:#d97706;margin-top:6px;padding:6px 10px;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;">
            ⚠️ Ce nouveau Super Admin recevra la clé secrète par défaut <strong>SuperKey@2024</strong>. Il devra la changer dans ses Paramètres dès sa première connexion.
          </div>
        </div>
        <div class="form-group" style="grid-column:span 2"><label class="form-label">Mot de passe temporaire</label><input class="form-control" type="password" id="m-mdp" value="Admin@2024" required></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:16px;">
        <button type="submit" class="btn btn-primary" id="btn-ajout">Créer</button>
        <button type="button" onclick="closeModal()" class="btn btn-ghost">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();
let allPersonnel = [];
let isSuperAdmin = false;
const roleLabel = {
  ROLE_SUPER_ADMIN:'Super Admin', ROLE_ADMIN_SYSTEME:'Admin Système',
  ROLE_SUPERVISOR:'Superviseur', ROLE_GESTIONNAIRE:'Gestionnaire', ROLE_ADMIN_BD:'Admin BD'
};
const roleBadge = {
  ROLE_SUPER_ADMIN:'badge-failed', ROLE_ADMIN_SYSTEME:'badge-kyc-pending',
  ROLE_SUPERVISOR:'badge-warning', ROLE_GESTIONNAIRE:'badge-success', ROLE_ADMIN_BD:'badge-pending'
};

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  isSuperAdmin = me.role === "ROLE_SUPER_ADMIN";
  if (isSuperAdmin) {
    document.getElementById("btn-ajouter-wrap").style.display = "";
    // Afficher l'option Super Admin dans le select de création
    const optSA = document.getElementById("opt-super-admin");
    if (optSA) optSA.style.display = "";
  }
  try {
    allPersonnel = await api.get("/api/gestionnaires");
    afficher(allPersonnel);
  } catch(e) {
    document.getElementById("personnel-tbody").innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--danger);">${e.message}</td></tr>`;
  }
}

function filtrer() {
  const q = document.getElementById("search").value.toLowerCase();
  const role = document.getElementById("filter-role").value;
  const filtered = allPersonnel.filter(g => {
    const match = !q || (g.nom||"").toLowerCase().includes(q) ||
                  (g.prenom||"").toLowerCase().includes(q) || (g.email||"").toLowerCase().includes(q);
    const rMatch = !role || g.role === role;
    return match && rMatch;
  });
  afficher(filtered);
}

function afficher(list) {
  const tbody = document.getElementById("personnel-tbody");
  if (!list.length) { tbody.innerHTML=`<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">Aucun membre trouvé</td></tr>`; return; }
  tbody.innerHTML = list.map(g => {
    const role = g.role || "—";
    const dt = g.dateEmbauche ? new Date(g.dateEmbauche).toLocaleDateString("fr-FR") : "—";
    const isSuperAdmin = role === "ROLE_SUPER_ADMIN";
    return `<tr>
      <td>
        <div class="tx-name">${g.prenom||""} ${g.nom||""}</div>
        <div class="tx-sub">#${g.idGestionnaire}</div>
      </td>
      <td style="font-size:.83rem;">${g.email||"—"}</td>
      <td><span class="badge ${roleBadge[role]||"badge-pending"}">${roleLabel[role]||role}</span></td>
      <td style="font-size:.82rem;">${dt}</td>
      <td><span class="badge ${g.actif?'badge-success':'badge-failed'}">${g.actif?'Actif':'Suspendu'}</span></td>
      <td style="text-align:right;">
        ${isSuperAdmin && role !== "ROLE_SUPER_ADMIN" && g.actif
          ? `<button class="btn btn-warning btn-sm" onclick="suspendre(${g.idGestionnaire})">Suspendre</button>`
          : ``}
        ${isSuperAdmin && role !== "ROLE_SUPER_ADMIN" && !g.actif
          ? `<button class="btn btn-success btn-sm" onclick="activer(${g.idGestionnaire})">Activer</button>`
          : ``}
        ${isSuperAdmin && role !== "ROLE_SUPER_ADMIN"
          ? `<button class="btn btn-ghost btn-sm" onclick="resetMdp(${g.idGestionnaire})" style="margin-left:4px;">Reset mdp</button>`
          : ``}
        ${!isSuperAdmin ? `<span style="font-size:.78rem;color:var(--muted);">Lecture seule</span>` : ``}
      </td>
    </tr>`;
  }).join("");
}

async function suspendre(id) {
  if (!confirm("Suspendre ce membre ?")) return;
  try { await api.put("/api/gestionnaires/"+id+"/suspendre"); flash("Membre suspendu.", "info"); await init(); }
  catch(e) { flash("Erreur : "+e.message, "error"); }
}
async function activer(id) {
  try { await api.put("/api/gestionnaires/"+id+"/activer"); flash("Membre réactivé.", "success"); await init(); }
  catch(e) { flash("Erreur : "+e.message, "error"); }
}
async function resetMdp(id) {
  if (!confirm("Réinitialiser le mot de passe à Admin@2024 ?")) return;
  try { await api.put("/api/gestionnaires/"+id+"/reset-password"); flash("Mot de passe réinitialisé.", "success"); }
  catch(e) { flash("Erreur : "+e.message, "error"); }
}

function showModal() { document.getElementById("modal").style.display="flex"; }
function closeModal() { document.getElementById("modal").style.display="none"; }

document.getElementById("m-role").addEventListener("change", function() {
  const note = document.getElementById("super-admin-note");
  note.style.display = this.value === "ROLE_SUPER_ADMIN" ? "block" : "none";
});

document.getElementById("form-membre").addEventListener("submit", async e => {
  e.preventDefault();
  const btn = document.getElementById("btn-ajout");
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span>';
  try {
    await api.post("/api/gestionnaires", {
      prenom: document.getElementById("m-prenom").value,
      nom: document.getElementById("m-nom").value,
      email: document.getElementById("m-email").value,
      role: document.getElementById("m-role").value,
      motDePasse: document.getElementById("m-mdp").value
    });
    flash("Membre créé.", "success");
    closeModal(); e.target.reset();
    await init();
  } catch(err) { flash("Erreur : "+err.message, "error"); }
  finally { btn.disabled=false; btn.innerHTML="Créer"; }
});

init();
</script>
</body>
</html>
