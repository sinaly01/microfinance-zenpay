<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mes Tickets — ZEN-PAY';
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

  <!-- Sidebar client -->
  <aside class="sidebar">
    <a href="client-dashboard.php" class="sidebar-logo">Z</a>
    <a href="client-dashboard.php" class="sidebar-item" data-tip="Tableau de bord">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    </a>
    <a href="client-transactions.php" class="sidebar-item" data-tip="Transactions">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    </a>
    <a href="client-virement.php" class="sidebar-item" data-tip="Virement rapide">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
    </a>
    <a href="client-simulateur.php" class="sidebar-item" data-tip="Simulateur de frais">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M13 3h8m0 0v8m0-8l-8 8"/></svg>
    </a>
    <a href="client-releve.php" class="sidebar-item" data-tip="Télécharger relevé">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </a>
    <a href="client-rib.php" class="sidebar-item" data-tip="Mon RIB">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10z"/></svg>
    </a>
    <a href="client-tickets.php" class="sidebar-item active" data-tip="Support">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    </a>
    <div class="sidebar-spacer"></div>
    <button onclick="logout()" class="sidebar-item" data-tip="Déconnexion">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
    </button>
  </aside>

  <div class="main-area">
<?php $topbar_title = 'Mes Tickets Support'; include __DIR__ . '/../includes/topbar-client.php'; ?>

    <main class="page-content">

      <div style="max-width:860px;margin:0 auto;">

        <div class="dash-header">
          <div>
            <h1>Mes <span>Tickets</span></h1>
            <p style="color:var(--muted);font-size:.85rem;">Suivi de vos demandes d'assistance</p>
          </div>
          <div class="dash-header-right">
            <span id="open-badge" class="badge badge-pending" style="font-size:.8rem;"></span>
          </div>
        </div>

        <!-- Formulaire nouveau ticket -->
        <div class="card card-pad mb-4" id="form-section">
          <div class="card-header">
            <span class="card-title">NOUVEAU TICKET</span>
            <button onclick="toggleForm()" class="btn btn-ghost btn-sm" id="toggle-btn">− Réduire</button>
          </div>
          <div id="form-body">
            <form id="form-ticket">
              <div class="form-group">
                <label class="form-label">Sujet</label>
                <input class="form-control" type="text" id="titreObjet" placeholder="Ex: Problème de virement, Compte bloqué..." required maxlength="120">
              </div>
              <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="4"
                          placeholder="Décrivez votre problème en détail..." required
                          style="resize:vertical;"></textarea>
              </div>
              <div style="display:flex;gap:10px;align-items:center;">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                  Envoyer le ticket
                </button>
                <span style="font-size:.78rem;color:var(--muted);">Notre équipe répond sous 24h</span>
              </div>
            </form>
          </div>
        </div>

        <!-- Onglets -->
        <div class="tab-group">
          <div class="tab-item active" onclick="showTab('ouverts', this)">Ouverts / En cours</div>
          <div class="tab-item" onclick="showTab('tous', this)">Tous mes tickets</div>
        </div>

        <!-- Tab tickets ouverts -->
        <div id="tab-ouverts" class="card">
          <div class="tx-table-header">
            <div>
              <div class="tx-table-title">Tickets actifs</div>
              <div class="tx-table-sub">Tickets en attente de réponse ou en cours de traitement</div>
            </div>
          </div>
          <div id="ouverts-list">
            <div style="text-align:center;padding:40px;"><span class="spinner"></span></div>
          </div>
        </div>

        <!-- Tab tous les tickets -->
        <div id="tab-tous" style="display:none;" class="card">
          <div class="tx-table-header">
            <div>
              <div class="tx-table-title">Historique complet (<span id="all-count">0</span>)</div>
              <div class="tx-table-sub">Toutes vos demandes d'assistance</div>
            </div>
          </div>
          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th>Sujet</th>
                  <th>Statut</th>
                  <th>Date</th>
                  <th style="text-align:right;">Mise à jour</th>
                </tr>
              </thead>
              <tbody id="all-tbody">
                <tr><td colspan="4" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();
let myIdClient = null;
let formVisible = true;

function toggleForm() {
  formVisible = !formVisible;
  document.getElementById("form-body").style.display = formVisible ? "" : "none";
  document.getElementById("toggle-btn").textContent = formVisible ? "− Réduire" : "+ Créer un ticket";
}

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t => t.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("tab-ouverts").style.display = name === "ouverts" ? "" : "none";
  document.getElementById("tab-tous").style.display    = name === "tous" ? "" : "none";
}

const statusBadge = {OUVERT:"badge-pending",EN_COURS:"badge-kyc-pending",RESOLU:"badge-success",FERME:"badge-failed"};
const statusLabel = {OUVERT:"Ouvert",EN_COURS:"En cours",RESOLU:"Résolu",FERME:"Fermé"};
const statusIcon  = {OUVERT:"⏳",EN_COURS:"🔧",RESOLU:"✅",FERME:"🔒"};

async function init() {
  try {
    const me = await api.get("/api/auth/me");
    const prenom = me.prenom || me.email.split("@")[0];
    document.getElementById("user-avatar").textContent = prenom[0].toUpperCase();
    myIdClient = me.idClient;
    await Promise.all([loadOuverts(), loadAll()]);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function loadOuverts() {
  if (!myIdClient) return;
  try {
    const mine = await api.get("/api/tickets/mes-tickets/" + myIdClient);
    const ouverts = mine.filter(t => t.statut === "OUVERT" || t.statut === "EN_COURS");
    document.getElementById("open-badge").textContent = ouverts.length ? ouverts.length + " actif(s)" : "Aucun actif";

    const el = document.getElementById("ouverts-list");
    if (!ouverts.length) {
      el.innerHTML = `<div class="empty-state">
        <div class="empty-icon">🎉</div>
        <h3>Aucun ticket en cours</h3>
        <p>Créez un ticket ci-dessus si vous avez besoin d'aide.</p>
      </div>`;
      return;
    }
    el.innerHTML = ouverts.map(t => renderTicketCard(t)).join("");
  } catch(e) {
    document.getElementById("ouverts-list").innerHTML =
      `<div style="text-align:center;padding:32px;color:var(--muted);">Erreur chargement</div>`;
  }
}

async function loadAll() {
  if (!myIdClient) return;
  try {
    const mine = await api.get("/api/tickets/mes-tickets/" + myIdClient);
    document.getElementById("all-count").textContent = mine.length;
    const tbody = document.getElementById("all-tbody");
    if (!mine.length) {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:32px;color:var(--muted);">Aucun ticket trouvé</td></tr>`;
      return;
    }
    tbody.innerHTML = mine.map(t => {
      const statut = t.statut || "OUVERT";
      const date = t.dateCreation ? new Date(t.dateCreation).toLocaleDateString("fr-FR") : "—";
      const maj = t.dateResolution ? new Date(t.dateResolution).toLocaleDateString("fr-FR") : date;
      return `<tr>
        <td>
          <div class="tx-name">${statusIcon[statut] || "💬"} ${t.titreObjet ?? "Sans titre"}</div>
          <div class="tx-sub">${t.descriptionProbleme ? t.descriptionProbleme.substring(0,60) + "…" : ""}</div>
        </td>
        <td><span class="badge ${statusBadge[statut]||"badge-pending"}">${statusLabel[statut]||statut}</span></td>
        <td style="font-size:.82rem;">${date}</td>
        <td style="text-align:right;font-size:.82rem;color:var(--muted);">${maj}</td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

function renderTicketCard(t) {
  const statut = t.statut || "OUVERT";
  const date = t.dateCreation ? new Date(t.dateCreation).toLocaleDateString("fr-FR") : "—";
  const id = t.idTicket;
  const statutColors = {
    OUVERT: "border-left:4px solid var(--warning);",
    EN_COURS: "border-left:4px solid var(--info);"
  };
  return `<div class="ticket-item" style="${statutColors[statut]||""}padding-left:18px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
      <div style="flex:1;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
          <span class="badge ${statusBadge[statut]||"badge-pending"}">${statusIcon[statut]||""} ${statusLabel[statut]||statut}</span>
          <span style="font-size:.76rem;color:var(--muted);">#${id}</span>
        </div>
        <div class="ticket-title">${t.titreObjet ?? "Sans titre"}</div>
        <div class="ticket-meta">Envoyé le ${date}</div>
        ${t.descriptionProbleme ? `<div class="ticket-body">${t.descriptionProbleme}</div>` : ""}
      </div>
    </div>
  </div>`;
}

document.getElementById("form-ticket").addEventListener("submit", async (e) => {
  e.preventDefault();
  const btn = document.getElementById("submit-btn");
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  const titreObjet = document.getElementById("titreObjet").value.trim();
  const description = document.getElementById("description").value.trim();
  try {
    await api.postQuery("/api/tickets", { idClient: myIdClient, titreObjet, description });
    flash("Ticket envoyé avec succès ! Notre équipe vous répond bientôt.", "success");
    e.target.reset();
    await Promise.all([loadOuverts(), loadAll()]);
    showTab("ouverts", document.querySelector(".tab-item"));
  } catch(err) { flash("Erreur : " + err.message, "error"); }
  finally { btn.disabled = false; btn.innerHTML = "Envoyer le ticket"; }
});

init();
</script>
</body>
</html>
