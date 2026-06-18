<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Gestion Comptes — ' . APP_NAME;
$sidebar_active = 'comptes';
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
<?php $topbar_title = 'Gestion des Comptes'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Gestion des <span>Comptes</span></h1><p style="color:var(--muted);font-size:.85rem;">Visualisation et administration des comptes bancaires</p></div>
  </div>

  <!-- Recherche -->
  <div class="card card-pad" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;align-items:center;">
      <input class="form-control" type="text" id="search-input" placeholder="Rechercher par numéro, client..."
             oninput="filtrer(this.value)" style="max-width:380px;">
      <select class="form-control" id="filter-statut" onchange="filtrer(document.getElementById('search-input').value)" style="max-width:160px;">
        <option value="">Tous les statuts</option>
        <option value="ACTIF">Actifs</option>
        <option value="BLOQUE">Bloqués</option>
        <option value="SUSPENDU">Suspendus</option>
        <option value="EN_ATTENTE">En attente</option>
        <option value="FERME">Fermés</option>
      </select>
    </div>
  </div>

  <div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Comptes (<span id="count">0</span>)</div></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>N° Compte</th><th>Titulaire</th><th>Solde</th><th>Statut</th><th>Ouverture</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="comptes-tbody"><tr><td colspan="6" style="text-align:center;padding:40px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Panneau détail -->
  <div id="detail-panel" style="display:none;" class="card card-pad" style="margin-top:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
      <h3 id="detail-title" style="font-size:1rem;font-weight:700;"></h3>
      <button onclick="fermerDetail()" class="btn btn-ghost btn-sm">✕ Fermer</button>
    </div>
    <div id="detail-content"></div>
    <div id="detail-tx" style="margin-top:16px;"></div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let allComptes = [];
const statutBadge = {ACTIF:'badge-success',BLOQUE:'badge-failed',SUSPENDU:'badge-warning',EN_ATTENTE:'badge-kyc-pending',FERME:'badge-failed'};

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  try {
    allComptes = await api.get("/api/comptes");
    afficher(allComptes);
  } catch(e) {
    document.getElementById("comptes-tbody").innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--danger);">${e.message}</td></tr>`;
  }
}

function filtrer(q) {
  const statut = document.getElementById("filter-statut").value;
  const lq = (q||"").toLowerCase();
  const filtered = allComptes.filter(c => {
    const match = !lq || (c.numeroCompte||"").toLowerCase().includes(lq) ||
                  (c.nomClient||"").toLowerCase().includes(lq);
    const sMatch = !statut || c.statut === statut;
    return match && sMatch;
  });
  afficher(filtered);
}

function afficher(list) {
  document.getElementById("count").textContent = list.length;
  const tbody = document.getElementById("comptes-tbody");
  if (!list.length) { tbody.innerHTML=`<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">Aucun compte trouvé</td></tr>`; return; }
  tbody.innerHTML = list.map(c => {
    const solde = Number(c.solde||0).toLocaleString("fr-FR");
    const dt = c.dateOuverture ? new Date(c.dateOuverture).toLocaleDateString("fr-FR") : "—";
    const s = c.statut || "EN_ATTENTE";
    return `<tr style="cursor:pointer;" onclick="voirCompte(${c.idCompte})">
      <td style="font-family:monospace;font-size:.83rem;">${c.numeroCompte||"—"}</td>
      <td><div class="tx-name">${c.nomClient||"—"}</div></td>
      <td style="font-weight:700;">${solde} FCFA</td>
      <td><span class="badge ${statutBadge[s]||"badge-pending"}">${s}</span></td>
      <td style="font-size:.82rem;">${dt}</td>
      <td style="text-align:right;">
        ${s==="ACTIF"?`<button class="btn btn-warning btn-sm" onclick="event.stopPropagation();bloquer(${c.idCompte})">Bloquer</button>`:""}
        ${s==="BLOQUE"?`<button class="btn btn-success btn-sm" onclick="event.stopPropagation();debloquer(${c.idCompte})">Débloquer</button>`:""}
        ${s==="EN_ATTENTE"?`<button class="btn btn-primary btn-sm" onclick="event.stopPropagation();valider(${c.idCompte})">Valider</button>`:""}
        ${s!=="FERME"&&s!=="BLOQUE"?`<button class="btn btn-ghost btn-sm" onclick="event.stopPropagation();suspendre(${c.idCompte})" style="margin-left:4px;">Suspendre</button>`:""}
      </td>
    </tr>`;
  }).join("");
}

async function voirCompte(id) {
  const panel = document.getElementById("detail-panel");
  panel.style.display = "";
  document.getElementById("detail-content").innerHTML = '<span class="spinner"></span>';
  document.getElementById("detail-tx").innerHTML = '';
  try {
    const [c, txs] = await Promise.all([
      api.get("/api/comptes/" + id),
      api.get("/api/transactions/releve/" + id)
    ]);
    document.getElementById("detail-title").textContent = "Compte " + c.numeroCompte;
    const s = c.statut || "EN_ATTENTE";
    document.getElementById("detail-content").innerHTML = `
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;font-size:.85rem;">
        <div><div style="color:var(--muted);font-size:.75rem;">Numéro</div><strong style="font-family:monospace;">${c.numeroCompte}</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;">Solde</div><strong>${Number(c.solde||0).toLocaleString("fr-FR")} FCFA</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;">Statut</div><span class="badge ${statutBadge[s]||"badge-pending"}">${s}</span></div>
        <div><div style="color:var(--muted);font-size:.75rem;">Plafond retrait</div><strong>${Number(c.plafondRetrait||0).toLocaleString("fr-FR")} FCFA</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;">Solde minimum</div><strong>${Number(c.montantMinSolde||0).toLocaleString("fr-FR")} FCFA</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;">Titulaire</div><strong>${c.nomClient||"—"}</strong></div>
      </div>
      <div style="display:flex;gap:8px;margin-top:16px;flex-wrap:wrap;">
        ${s==="ACTIF"?`<button class="btn btn-warning btn-sm" onclick="bloquer(${c.idCompte})">Bloquer</button>`:""}
        ${s==="BLOQUE"?`<button class="btn btn-success btn-sm" onclick="debloquer(${c.idCompte})">Débloquer</button>`:""}
        ${s==="EN_ATTENTE"?`<button class="btn btn-primary btn-sm" onclick="valider(${c.idCompte})">Valider ouverture</button>`:""}
        ${s!=="FERME"&&s!=="BLOQUE"?`<button class="btn btn-ghost btn-sm" onclick="suspendre(${c.idCompte})">Suspendre</button>`:""}
        ${s==="SUSPENDU"?`<button class="btn btn-success btn-sm" onclick="debloquer(${c.idCompte})">Réactiver</button>`:""}
      </div>`;
    if (txs.length) {
      document.getElementById("detail-tx").innerHTML = `
        <div style="font-weight:700;font-size:.85rem;margin-bottom:8px;color:var(--gray-700);">Dernières transactions</div>
        ${txs.slice(0,5).map(t => `
          <div style="display:flex;justify-content:space-between;padding:8px 12px;background:var(--gray-50);border-radius:8px;margin-bottom:6px;font-size:.82rem;">
            <span style="font-family:monospace;">${t.reference||"—"}</span>
            <span>${t.typeTransaction||"—"}</span>
            <span style="font-weight:700;">${Number(t.montant||0).toLocaleString("fr-FR")} FCFA</span>
            <span class="badge ${t.statut==="VALIDEE"?"badge-success":t.statut==="REJETEE"?"badge-failed":"badge-pending"}">${t.statut}</span>
          </div>`).join("")}`;
    }
    panel.scrollIntoView({behavior:"smooth"});
  } catch(e) { document.getElementById("detail-content").innerHTML=`<div style="color:var(--danger);">${e.message}</div>`; }
}

function fermerDetail() { document.getElementById("detail-panel").style.display = "none"; }

async function bloquer(id) {
  if (!confirm("Bloquer ce compte ?")) return;
  try { await api.put("/api/admin/comptes/"+id+"/bloquer"); flash("Compte bloqué.", "info"); init(); fermerDetail(); }
  catch(e) { flash("Erreur : " + e.message, "error"); }
}
async function debloquer(id) {
  try { await api.put("/api/admin/comptes/"+id+"/debloquer"); flash("Compte débloqué.", "success"); init(); fermerDetail(); }
  catch(e) { flash("Erreur : " + e.message, "error"); }
}
async function valider(id) {
  try { await api.put("/api/comptes/"+id+"/valider"); flash("Compte validé.", "success"); init(); fermerDetail(); }
  catch(e) { flash("Erreur : " + e.message, "error"); }
}
async function suspendre(id) {
  if (!confirm("Suspendre ce compte ?")) return;
  try { await api.put("/api/comptes/"+id+"/suspendre"); flash("Compte suspendu.", "info"); init(); fermerDetail(); }
  catch(e) { flash("Erreur : " + e.message, "error"); }
}

init();
</script>
</body>
</html>
