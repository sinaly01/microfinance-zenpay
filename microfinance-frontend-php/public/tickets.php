<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Tickets Support — ' . APP_NAME;
$sidebar_active = 'tickets';
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
<div class="app-layout">

<?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>

  <div class="main-area">
<?php $topbar_title = 'Tickets Support'; include __DIR__ . '/../includes/topbar-admin.php'; ?>

    <main class="page-content">

      <div class="dash-header">
        <div>
          <h1>Tickets <span>Support</span></h1>
          <p style="color:var(--muted);font-size:.85rem;">Gérer les demandes d'assistance des clients</p>
        </div>
        <div class="dash-header-right">
          <span class="badge badge-pending" id="open-count">0 ouverts</span>
        </div>
      </div>

      <div class="tab-group">
        <div class="tab-item active" onclick="showTab('open',this)">Ouverts</div>
        <div class="tab-item" onclick="showTab('all',this)">Tous</div>
      </div>

      <div id="tab-open" class="card">
        <div class="tx-table-header">
          <div>
            <div class="tx-table-title">Tickets ouverts</div>
            <div class="tx-table-sub">Prendre en charge ou résoudre</div>
          </div>
        </div>
        <div id="tickets-open-list">
          <div style="text-align:center;padding:40px;"><span class="spinner"></span></div>
        </div>
      </div>

      <div id="tab-all" style="display:none;" class="card">
        <div class="tx-table-header">
          <div>
            <div class="tx-table-title">Tous les tickets (<span id="all-count">0</span>)</div>
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table>
            <thead>
              <tr><th>Sujet</th><th>Client</th><th>Statut</th><th>Date</th><th style="text-align:right;">Actions</th></tr>
            </thead>
            <tbody id="all-tbody">
              <tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t => t.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("tab-open").style.display = name === "open" ? "" : "none";
  document.getElementById("tab-all").style.display  = name === "all"  ? "" : "none";
}

async function init() {
  try {
    const me = await api.get("/api/auth/me");
    document.getElementById("user-avatar").textContent = (me.prenom || "G")[0].toUpperCase();
    window._idGestionnaire = me.idGestionnaire || null;
    await Promise.all([loadOpen(), loadAll()]);
  } catch(e) {}
}

const statusBadge = {OUVERT:"badge-pending",EN_COURS:"badge-kyc-pending",RESOLU:"badge-success",FERME:"badge-failed"};
const statusLabel = {OUVERT:"Ouvert",EN_COURS:"En cours",RESOLU:"Résolu",FERME:"Fermé"};

async function loadOpen() {
  try {
    const list = await api.get("/api/tickets/ouverts");
    document.getElementById("open-count").textContent = list.length + " ouvert(s)";
    const el = document.getElementById("tickets-open-list");
    if (!list.length) {
      el.innerHTML = `<div class="empty-state"><div class="empty-icon">🎉</div>
        <h3>Aucun ticket ouvert</h3><p>Tous les tickets ont été traités.</p></div>`;
      return;
    }
    el.innerHTML = list.map(t => {
      const id = t.idTicket;
      const dt = t.dateCreation ? new Date(t.dateCreation).toLocaleDateString("fr-FR") : "—";
      const client = t.client ? (t.client.prenom + " " + t.client.nom) : "—";
      return `<div class="ticket-item">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div style="flex:1;">
            <div class="ticket-title">${t.titreObjet ?? "Sans titre"}</div>
            <div class="ticket-meta">${client} · ${dt}</div>
            ${t.descriptionProbleme ? `<div class="ticket-body">${t.descriptionProbleme}</div>` : ""}
          </div>
          <div style="display:flex;gap:8px;flex-shrink:0;">
            <button class="btn btn-warning btn-sm" onclick="prendreEnCharge(${id})">En charge</button>
            <button class="btn btn-success btn-sm" onclick="resoudre(${id})">Résoudre</button>
          </div>
        </div>
      </div>`;
    }).join("");
  } catch(e) {
    document.getElementById("tickets-open-list").innerHTML =
      `<div style="text-align:center;padding:32px;color:var(--muted);">${e.message}</div>`;
  }
}

async function loadAll() {
  try {
    const list = await api.get("/api/tickets");
    document.getElementById("all-count").textContent = list.length;
    const tbody = document.getElementById("all-tbody");
    if (!list.length) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucun ticket</td></tr>`;
      return;
    }
    tbody.innerHTML = list.map(t => {
      const id = t.idTicket;
      const dt = t.dateCreation ? new Date(t.dateCreation).toLocaleDateString("fr-FR") : "—";
      const client = t.client ? (t.client.prenom + " " + t.client.nom) : "—";
      const statut = t.statut || "OUVERT";
      return `<tr>
        <td><div class="tx-name">${t.titreObjet ?? "Sans titre"}</div>
            <div class="tx-sub">${t.descriptionProbleme ? t.descriptionProbleme.substring(0,60)+"…" : ""}</div></td>
        <td>${client}</td>
        <td><span class="badge ${statusBadge[statut]||"badge-pending"}">${statusLabel[statut]||statut}</span></td>
        <td>${dt}</td>
        <td style="text-align:right;">
          ${statut === "OUVERT" ? `<button class="btn btn-warning btn-sm" onclick="prendreEnCharge(${id})">En charge</button>` : ""}
          ${statut !== "RESOLU" && statut !== "FERME" ? `<button class="btn btn-success btn-sm" onclick="resoudre(${id})" style="margin-left:4px;">Résoudre</button>` : ""}
        </td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

async function prendreEnCharge(id) {
  const idG = window._idGestionnaire;
  if (!idG) { flash("Identifiant gestionnaire introuvable — rechargez la page", "error"); return; }
  try {
    await api.put(`/api/tickets/${id}/prendre-en-charge?idGestionnaire=${idG}`);
    flash("Ticket pris en charge", "info");
    await Promise.all([loadOpen(), loadAll()]);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function resoudre(id) {
  if (!confirm("Marquer ce ticket comme résolu ?")) return;
  try {
    await api.put(`/api/tickets/${id}/resoudre`);
    flash("Ticket résolu !", "success");
    await Promise.all([loadOpen(), loadAll()]);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

init();
</script>
</body>
</html>
