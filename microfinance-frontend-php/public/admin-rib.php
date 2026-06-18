<?php
require_once __DIR__ . '/../config.php';
$page_title = 'RIB Clients — ' . APP_NAME;
$sidebar_active = 'admin-rib';
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
<?php $topbar_title = 'RIB Clients'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>RIB <span>Clients</span></h1><p style="color:var(--muted);font-size:.85rem;">Clients éligibles à la génération de RIB (Offre 2)</p></div>
  </div>

  <div class="tab-group">
    <div class="tab-item active" onclick="showTab('eligible',this)">Éligibles RIB</div>
    <div class="tab-item" onclick="showTab('tous',this)">Tous les clients</div>
  </div>

  <!-- Clients éligibles -->
  <div id="tab-eligible">
    <div id="eligible-list"><div style="text-align:center;padding:40px;"><span class="spinner"></span></div></div>
  </div>

  <!-- Tous clients -->
  <div id="tab-tous" style="display:none;" class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Tous les clients (<span id="all-count">0</span>)</div></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Client</th><th>Offre actuelle</th><th>RIB dispo</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="all-tbody"><tr><td colspan="4" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let allClients = [];
let allOffres  = [];

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t=>t.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("tab-eligible").style.display = name==="eligible" ? "" : "none";
  document.getElementById("tab-tous").style.display     = name==="tous"     ? "" : "none";
  if (name==="tous" && !allClients.length) loadTous();
}

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  try {
    allOffres = await api.get("/api/abonnements");
    allClients = await api.get("/api/clients");
    const eligibles = allClients.filter(c => {
      if (!c.offreAbonnement) return false;
      const offre = allOffres.find(o => o.idOffre === c.offreAbonnement.idOffre);
      return offre && offre.optionRibDispo;
    });
    afficherEligibles(eligibles);
  } catch(e) {
    document.getElementById("eligible-list").innerHTML = `<div class="card card-pad" style="color:var(--danger);">${e.message}</div>`;
  }
}

function afficherEligibles(list) {
  const el = document.getElementById("eligible-list");
  if (!list.length) {
    el.innerHTML = `<div class="card"><div class="empty-state"><div class="empty-icon">📄</div><h3>Aucun client éligible au RIB</h3><p>L'option RIB nécessite l'Offre 2.</p></div></div>`;
    return;
  }
  el.innerHTML = list.map(c => {
    const offre = c.offreAbonnement ? c.offreAbonnement.nomOffre : "—";
    return `<div class="card card-pad" style="margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
        <div>
          <div style="font-weight:700;">${c.prenom||""} ${c.nom||""}</div>
          <div style="font-size:.82rem;color:var(--muted);">${c.email||"—"} · ${c.telephone||""}</div>
          <div style="font-size:.8rem;margin-top:4px;"><span class="badge badge-success">${offre}</span> · RIB disponible</div>
        </div>
        <div style="display:flex;gap:8px;">
          <button class="btn btn-primary btn-sm" onclick="telechargerRib(${c.idClient})">📄 Télécharger RIB</button>
        </div>
      </div>
    </div>`;
  }).join("");
}

function loadTous() {
  document.getElementById("all-count").textContent = allClients.length;
  const tbody = document.getElementById("all-tbody");
  tbody.innerHTML = allClients.map(c => {
    const offre = c.offreAbonnement ? c.offreAbonnement.nomOffre : "STANDARD";
    const offreObj = allOffres.find(o => c.offreAbonnement && o.idOffre === c.offreAbonnement.idOffre);
    const ribDispo = offreObj && offreObj.optionRibDispo;
    return `<tr>
      <td><div class="tx-name">${c.prenom||""} ${c.nom||""}</div><div class="tx-sub">${c.email||""}</div></td>
      <td><span class="badge badge-pending" style="font-size:.72rem;">${offre}</span></td>
      <td>${ribDispo ? '<span class="badge badge-success">✓ Disponible</span>' : '<span class="badge badge-failed">✗ Non disponible</span>'}</td>
      <td style="text-align:right;">
        ${ribDispo?`<button class="btn btn-primary btn-sm" onclick="telechargerRib(${c.idClient})">📄 RIB PDF</button>`:""}
      </td>
    </tr>`;
  }).join("");
}

async function telechargerRib(idClient) {
  try {
    const token = localStorage.getItem("mf_jwt_token");
    const apiBase = localStorage.getItem("mf_api_base") || (
      (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
        ? "http://localhost:8080" : ""
    );
    const res = await fetch(`${apiBase}/api/rib/${idClient}`, {
      headers: { "Authorization": "Bearer " + token }
    });
    if (!res.ok) { flash("Erreur téléchargement", "error"); return; }
    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url; a.download = `RIB_ZenPay_${idClient}.pdf`; a.click();
    URL.revokeObjectURL(url);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

init();
</script>
</body>
</html>
