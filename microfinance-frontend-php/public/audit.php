<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Piste d\'audit — ' . APP_NAME;
$sidebar_active = 'audit';
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
<?php $topbar_title = "Piste d'audit"; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Piste <span>d'Audit</span></h1><p style="color:var(--muted);font-size:.85rem;">Journal horodaté de toutes les actions — mis à jour automatiquement</p></div>
    <div class="dash-header-right">
      <span class="badge badge-success" id="log-count">0 entrées</span>
      <button class="btn btn-sm btn-outline" onclick="charger()" style="margin-left:8px;">↻ Actualiser</button>
    </div>
  </div>

  <!-- Filtres -->
  <div class="card card-pad" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <input class="form-control" type="text" id="search" placeholder="Rechercher une action, un email..." oninput="filtrer()" style="max-width:400px;">
      <select class="form-control" id="filter-acteur" onchange="filtrer()" style="max-width:200px;">
        <option value="">Tous les acteurs</option>
      </select>
    </div>
  </div>

  <!-- Sessions actives -->
  <div class="card" style="margin-bottom:20px;">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Qui est connecté maintenant</div><div class="tx-table-sub" id="sessions-sub">—</div></div>
    </div>
    <div id="sessions-list" style="padding:0 0 8px 0;"></div>
  </div>

  <!-- Journal principal -->
  <div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Journal complet (<span id="total">0</span> entrées)</div></div>
    </div>
    <div id="audit-container" style="max-height:600px;overflow-y:auto;">
      <div style="text-align:center;padding:40px;"><span class="spinner"></span></div>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let allLogs = [];
let acteurs = new Set();

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  await charger();
  setInterval(charger, 15000); // refresh auto
}

async function charger() {
  await Promise.all([chargerSessions(), chargerLogs()]);
}

async function chargerSessions() {
  try {
    const sessions = await api.get("/api/admin/sessions");
    document.getElementById("sessions-sub").textContent = sessions.length + " connecté(s) en ce moment";
    const el = document.getElementById("sessions-list");
    if (!sessions.length) {
      el.innerHTML = `<div style="padding:12px 20px;color:var(--muted);font-size:.85rem;">Aucune session active</div>`;
      return;
    }
    el.innerHTML = sessions.map(s => {
      const dt = s.dateConnexion ? new Date(s.dateConnexion).toLocaleString("fr-FR") : "—";
      const roleLabel = (s.role||"").replace("ROLE_","");
      const typeBadge = s.type==="CLIENT" ? "badge-success" : "badge-kyc-pending";
      return `<div style="display:flex;align-items:center;gap:12px;padding:8px 20px;border-bottom:1px solid var(--border);">
        <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0;"></div>
        <div style="flex:1;">
          <span style="font-weight:600;font-size:.85rem;">${s.nom||"—"}</span>
          <span style="font-size:.76rem;color:var(--muted);margin-left:8px;">${s.email||""}</span>
        </div>
        <span class="badge ${typeBadge}" style="font-size:.72rem;">${roleLabel}</span>
        <span style="font-size:.76rem;color:var(--muted);">IP: ${s.adresseIp||"—"}</span>
        <span style="font-size:.76rem;color:var(--muted);">depuis ${dt}</span>
      </div>`;
    }).join("");
  } catch(e) {}
}

async function chargerLogs() {
  try {
    allLogs = await api.get("/api/admin/audit-logs");
    document.getElementById("log-count").textContent = allLogs.length + " entrées";
    document.getElementById("total").textContent = allLogs.length;
    // Populer filtre acteurs
    acteurs.clear();
    allLogs.forEach(l => {
      if (l.utilisateur) acteurs.add(l.utilisateur.email || "Système");
      else acteurs.add("Système");
    });
    const sel = document.getElementById("filter-acteur");
    const curVal = sel.value;
    sel.innerHTML = `<option value="">Tous les acteurs</option>` +
      [...acteurs].sort().map(a=>`<option value="${a}" ${a===curVal?"selected":""}>${a}</option>`).join("");
    filtrer();
  } catch(e) {
    document.getElementById("audit-container").innerHTML = `<div style="padding:24px;color:var(--danger);">${e.message}</div>`;
  }
}

function filtrer() {
  const q = document.getElementById("search").value.toLowerCase();
  const acteur = document.getElementById("filter-acteur").value;
  const filtered = allLogs.filter(l => {
    const action = (l.actionEffectuee||"").toLowerCase();
    const who = l.utilisateur ? (l.utilisateur.email||"Système") : "Système";
    const matchQ = !q || action.includes(q) || who.toLowerCase().includes(q);
    const matchA = !acteur || who === acteur;
    return matchQ && matchA;
  });
  afficher(filtered);
}

function afficher(list) {
  const el = document.getElementById("audit-container");
  if (!list.length) { el.innerHTML=`<div style="padding:32px;text-align:center;color:var(--muted);">Aucun log correspondant</div>`; return; }
  const html = list.map(l => {
    const dt = l.dateHeure ? new Date(l.dateHeure).toLocaleString("fr-FR") : "—";
    const who = l.utilisateur ? ((l.utilisateur.prenom||"")+" "+(l.utilisateur.nom||"")).trim()||l.utilisateur.email : "Système";
    const action = l.actionEffectuee || "—";
    const isAlert = action.includes("FRAUDE") || action.includes("BLOQUE") || action.includes("REJETE");
    const isSuccess = action.includes("CONNEXION") || action.includes("VALIDE") || action.includes("APPROUVE");
    const color = isAlert ? "var(--danger)" : isSuccess ? "var(--g700)" : "var(--gray-700)";
    return `<div style="display:flex;gap:12px;align-items:flex-start;padding:10px 20px;border-bottom:1px solid var(--border);">
      <div style="width:8px;height:8px;border-radius:50%;background:${color};flex-shrink:0;margin-top:5px;"></div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:.83rem;font-weight:600;color:${color};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${action}</div>
        <div style="font-size:.75rem;color:var(--muted);">${who} · ${dt} · IP: ${l.adresseIp||"N/A"}</div>
      </div>
    </div>`;
  }).join("");
  el.innerHTML = html;
}

init();
</script>
</body>
</html>
