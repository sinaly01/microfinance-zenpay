<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Abonnements — ' . APP_NAME;
$sidebar_active = 'abonnements';
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
<?php $topbar_title = 'Gestion des Abonnements'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Gestion des <span>Abonnements</span></h1><p style="color:var(--muted);font-size:.85rem;">Offres disponibles et demandes clients</p></div>
    <div class="dash-header-right">
      <span class="badge badge-kyc-pending" id="pending-badge">— demandes</span>
    </div>
  </div>

  <div class="tab-group">
    <div class="tab-item active" onclick="showTab('demandes',this)">Demandes en attente</div>
    <div class="tab-item" onclick="showTab('offres',this)">Offres</div>
    <div class="tab-item" onclick="showTab('assigner',this)">Assigner manuellement</div>
    <div class="tab-item" onclick="showTab('historique',this)">Historique</div>
  </div>

  <!-- Tab demandes -->
  <div id="tab-demandes">
    <div id="demandes-list"><div style="text-align:center;padding:40px;"><span class="spinner"></span></div></div>
  </div>

  <!-- Tab offres -->
  <div id="tab-offres" style="display:none;">
    <div id="offres-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:8px;"><div style="text-align:center;padding:40px;"><span class="spinner"></span></div></div>
  </div>

  <!-- Tab assigner -->
  <div id="tab-assigner" style="display:none;" class="card card-pad">
    <h3 style="font-weight:700;margin-bottom:16px;">Assigner une offre à un client</h3>
    <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
      <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
        <label class="form-label">Client</label>
        <select class="form-control" id="sel-client"></select>
      </div>
      <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
        <label class="form-label">Offre</label>
        <select class="form-control" id="sel-offre"></select>
      </div>
      <button class="btn btn-primary" onclick="assignerOffre()">Assigner</button>
    </div>
  </div>

  <!-- Tab historique -->
  <div id="tab-historique" style="display:none;" class="card">
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Client</th><th>Offre demandée</th><th>Statut</th><th>Date</th><th>Traité par</th></tr></thead>
        <tbody id="histo-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let offresMap = {};
let allOffres = [];

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t=>t.classList.remove("active"));
  el.classList.add("active");
  ["demandes","offres","assigner","historique"].forEach(n =>
    document.getElementById("tab-"+n).style.display = n===name ? "" : "none");
  if (name==="offres" && !allOffres.length) loadOffres();
  if (name==="assigner") loadAssigner();
  if (name==="historique") loadHistorique();
}

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  await loadDemandes();
}

async function loadDemandes() {
  try {
    const list = await api.get("/api/demandes-abonnement/en-attente");
    document.getElementById("pending-badge").textContent = list.length + " demande(s)";
    const el = document.getElementById("demandes-list");
    if (!list.length) {
      el.innerHTML = `<div class="card"><div class="empty-state"><div class="empty-icon">✅</div><h3>Aucune demande en attente</h3></div></div>`;
      return;
    }
    el.innerHTML = list.map(d => {
      const client = d.client ? (d.client.prenom+" "+d.client.nom) : "—";
      const offre = d.offreDemandee ? d.offreDemandee.nomOffre : "—";
      const dt = d.dateCreation ? new Date(d.dateCreation).toLocaleDateString("fr-FR") : "—";
      return `<div class="card card-pad" style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div>
            <div style="font-weight:700;">${client}</div>
            <div style="font-size:.83rem;color:var(--muted);">Demande vers : <strong>${offre}</strong></div>
            ${d.messageClient?`<div style="font-size:.8rem;margin-top:4px;font-style:italic;">"${d.messageClient}"</div>`:""}
            <div style="font-size:.76rem;color:var(--muted);margin-top:4px;">${dt}</div>
          </div>
          <div style="display:flex;gap:8px;flex-shrink:0;">
            <button class="btn btn-success btn-sm" onclick="approuver(${d.idDemande})">✓ Approuver</button>
            <button class="btn btn-danger btn-sm" onclick="rejeter(${d.idDemande})">✕ Rejeter</button>
          </div>
        </div>
      </div>`;
    }).join("");
  } catch(e) { document.getElementById("demandes-list").innerHTML=`<div class="card card-pad" style="color:var(--danger);">${e.message}</div>`; }
}

async function loadOffres() {
  try {
    allOffres = await api.get("/api/abonnements");
    allOffres.forEach(o => offresMap[o.idOffre] = o);
    document.getElementById("offres-grid").innerHTML = allOffres.map(o => `
      <div class="card card-pad" style="border:2px solid var(--border);">
        <div style="font-weight:800;font-size:1rem;margin-bottom:4px;">${o.nomOffre}</div>
        <div style="font-size:1.3rem;font-weight:700;color:var(--primary);margin:12px 0;">${Number(o.prixMensuel||0).toLocaleString("fr-FR")} FCFA<span style="font-size:.75rem;font-weight:400;color:var(--muted);">/mois</span></div>
        <div style="font-size:.82rem;color:var(--muted);">Frais MoMo: ${o.pourcentageFraisMomo}%</div>
        <div style="font-size:.82rem;color:var(--muted);">Frais virement: ${o.fraisVirementInterne}%</div>
        <div style="font-size:.82rem;color:var(--muted);">RIB: ${o.optionRibDispo?"✅ Inclus":"❌ Non inclus"}</div>
      </div>`).join("");
  } catch(e) {}
}

async function loadAssigner() {
  try {
    const [clients, offres] = await Promise.all([api.get("/api/clients"), api.get("/api/abonnements")]);
    const selC = document.getElementById("sel-client");
    const selO = document.getElementById("sel-offre");
    selC.innerHTML = clients.map(c=>`<option value="${c.idClient}">${c.prenom} ${c.nom}</option>`).join("");
    selO.innerHTML = offres.map(o=>`<option value="${o.idOffre}">${o.nomOffre} (${Number(o.prixMensuel).toLocaleString("fr-FR")} FCFA)</option>`).join("");
    allOffres = offres;
    offres.forEach(o=>offresMap[o.idOffre]=o);
  } catch(e) {}
}

async function loadHistorique() {
  try {
    const list = await api.get("/api/demandes-abonnement");
    const tbody = document.getElementById("histo-tbody");
    if (!list.length) { tbody.innerHTML=`<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucune demande</td></tr>`; return; }
    const badge = {EN_ATTENTE:'badge-kyc-pending',APPROUVE:'badge-success',REJETE:'badge-failed'};
    tbody.innerHTML = list.map(d => {
      const client = d.client ? (d.client.prenom+" "+d.client.nom) : "—";
      const offre = d.offreDemandee ? d.offreDemandee.nomOffre : "—";
      const dt = d.dateCreation ? new Date(d.dateCreation).toLocaleDateString("fr-FR") : "—";
      const traite = d.traitePar ? (d.traitePar.prenom+" "+d.traitePar.nom) : "—";
      return `<tr>
        <td>${client}</td><td>${offre}</td>
        <td><span class="badge ${badge[d.statut]||"badge-pending"}">${d.statut||"—"}</span></td>
        <td style="font-size:.82rem;">${dt}</td>
        <td style="font-size:.82rem;">${traite}</td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

async function approuver(id) {
  if (!confirm("Approuver cette demande ?")) return;
  try { await api.put("/api/demandes-abonnement/"+id+"/approuver"); flash("Demande approuvée.", "success"); await loadDemandes(); }
  catch(e) { flash("Erreur : "+e.message, "error"); }
}
async function rejeter(id) {
  if (!confirm("Rejeter cette demande ?")) return;
  try { await api.put("/api/demandes-abonnement/"+id+"/rejeter"); flash("Demande rejetée.", "info"); await loadDemandes(); }
  catch(e) { flash("Erreur : "+e.message, "error"); }
}
async function assignerOffre() {
  const idClient = document.getElementById("sel-client").value;
  const idOffre  = document.getElementById("sel-offre").value;
  if (!idClient || !idOffre) return;
  try {
    await api.put(`/api/abonnements/changer/${idClient}?idOffre=${idOffre}`);
    flash("Offre assignée avec succès.", "success");
  } catch(e) { flash("Erreur : "+e.message, "error"); }
}

init();
</script>
</body>
</html>
