<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Validation KYC — ' . APP_NAME;
$sidebar_active = 'kyc';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .doc-thumb { width:80px; height:60px; object-fit:cover; border-radius:6px; border:1px solid var(--border); cursor:pointer; transition:.15s; }
    .doc-thumb:hover { transform:scale(1.05); }
    .doc-pdf { width:80px; height:60px; background:var(--g100); border-radius:6px; border:1px solid var(--border); display:flex; flex-direction:column; align-items:center; justify-content:center; font-size:.65rem; color:var(--muted); cursor:pointer; gap:2px; }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Validation KYC'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Validation <span>KYC</span></h1><p style="color:var(--muted);font-size:.85rem;">Vérification d'identité et demandes de modification de profil</p></div>
    <div class="dash-header-right">
      <span class="badge badge-kyc-pending" id="pending-count">— en attente</span>
    </div>
  </div>

  <div class="tab-group">
    <div class="tab-item active" onclick="showTab('pending',this)">Documents à valider</div>
    <div class="tab-item" onclick="showTab('all',this)">Tous les clients</div>
    <div class="tab-item" onclick="showTab('suggestions',this)">
      Demandes de profil
      <span id="badge-sugg" style="display:none;" class="badge badge-kyc-pending" style="margin-left:4px;font-size:.7rem;padding:1px 6px;"></span>
    </div>
  </div>

  <!-- Tab en attente -->
  <div id="tab-pending">
    <div id="kyc-list"><div style="text-align:center;padding:40px;"><span class="spinner"></span></div></div>
  </div>

  <!-- Tab tous -->
  <div id="tab-all" style="display:none;" class="card">
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Client</th><th>Email</th><th>KYC</th><th>Inscrit le</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="all-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Tab suggestions de modification de profil -->
  <div id="tab-suggestions" style="display:none;">
    <div id="sugg-list"><div style="text-align:center;padding:40px;"><span class="spinner"></span></div></div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
const kycBadge = {PENDING:'badge-kyc-pending', DOCUMENTS_SOUMIS:'badge-pending', VALIDE:'badge-success', REJETE:'badge-failed'};
const kycLabel = {PENDING:'En attente', DOCUMENTS_SOUMIS:'Docs soumis', VALIDE:'Validé', REJETE:'Rejeté'};

const API_URL = localStorage.getItem("mf_api_base") || (
  (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
    ? "http://localhost:8080" : ""
);

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t => t.classList.remove("active"));
  el.classList.add("active");
  ["tab-pending","tab-all","tab-suggestions"].forEach(id => {
    document.getElementById(id).style.display = "none";
  });
  document.getElementById("tab-" + name).style.display = "";
  if (name === "suggestions") loadSuggestions();
}

async function init() {
  await Promise.all([loadPending(), loadAll(), checkSuggestions()]);
}

async function checkSuggestions() {
  try {
    const sugs = await api.get("/api/clients/suggestions?statut=EN_ATTENTE");
    if (sugs.length > 0) {
      const badge = document.getElementById("badge-sugg");
      badge.textContent = sugs.length;
      badge.style.display = "inline";
    }
  } catch(e) {}
}

async function loadPending() {
  try {
    const list = await api.get("/api/kyc/en-attente");
    document.getElementById("pending-count").textContent = list.length + " en attente";
    const el = document.getElementById("kyc-list");
    if (!list.length) {
      el.innerHTML = `<div class="card"><div class="empty-state"><div class="empty-icon">✅</div><h3>Aucune vérification en attente</h3><p>Tous les clients ont été traités.</p></div></div>`;
      return;
    }
    const cardsHtml = await Promise.all(list.map(async c => {
      const dt = c.dateInscription ? new Date(c.dateInscription).toLocaleDateString("fr-FR") : "—";
      let docsHtml = "";
      try {
        const docs = await api.get("/api/kyc/documents/" + c.idClient);
        if (docs.length) {
          docsHtml = `<div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">` +
            docs.map(d => {
              const url = API_URL + d.urlDocument;
              const isPdf = d.urlDocument.toLowerCase().endsWith(".pdf");
              const label = d.typeDocument === "CNI_RECTO" ? "Recto" : d.typeDocument === "CNI_VERSO" ? "Verso" : d.typeDocument;
              if (isPdf) {
                return `<div><div class="doc-pdf" onclick="window.open('${url}','_blank')">📄<span>${label}</span><span style="font-size:.6rem;">PDF</span></div><div style="font-size:.65rem;text-align:center;color:var(--muted);margin-top:2px;">${label}</div></div>`;
              }
              return `<div><img src="${url}" class="doc-thumb" onclick="window.open('${url}','_blank')" title="${label}" onerror="this.src='img/logo.svg'"><div style="font-size:.65rem;text-align:center;color:var(--muted);margin-top:2px;">${label}</div></div>`;
            }).join("") + `</div>`;
        } else {
          docsHtml = `<div style="font-size:.78rem;color:var(--muted);margin-top:8px;font-style:italic;">Aucun document téléversé</div>`;
        }
      } catch(e) {}
      return `<div class="card card-pad" style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div style="flex:1;">
            <div style="font-weight:700;font-size:.95rem;">${c.prenom||""} ${c.nom||""}</div>
            <div style="font-size:.82rem;color:var(--muted);">${c.email||"—"} · ${c.telephone||""}</div>
            <div style="font-size:.8rem;margin-top:4px;">Inscrit le ${dt} · Opérateur: ${c.operateurMomo||"—"}
              <span class="badge ${kycBadge[c.statutKyc]||'badge-pending'}" style="margin-left:6px;">${kycLabel[c.statutKyc]||c.statutKyc}</span>
            </div>
            ${docsHtml}
          </div>
          <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
            <button class="btn btn-success btn-sm" onclick="validerKyc(${c.idClient})">✓ Valider</button>
            <button class="btn btn-danger btn-sm" onclick="rejeterKyc(${c.idClient})">✕ Rejeter</button>
          </div>
        </div>
      </div>`;
    }));
    el.innerHTML = cardsHtml.join("");
  } catch(e) {
    document.getElementById("kyc-list").innerHTML = `<div class="card card-pad" style="color:var(--danger);">${e.message}</div>`;
  }
}

async function loadAll() {
  try {
    const clients = await api.get("/api/clients");
    const tbody = document.getElementById("all-tbody");
    if (!clients.length) { tbody.innerHTML=`<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucun client</td></tr>`; return; }
    tbody.innerHTML = clients.map(c => {
      const kyc = c.statutKyc || "PENDING";
      const dt = c.dateInscription ? new Date(c.dateInscription).toLocaleDateString("fr-FR") : "—";
      return `<tr>
        <td><div class="tx-name">${c.prenom||""} ${c.nom||""}</div></td>
        <td style="font-size:.83rem;">${c.email||"—"}</td>
        <td><span class="badge ${kycBadge[kyc]||"badge-pending"}">${kycLabel[kyc]||kyc}</span></td>
        <td style="font-size:.82rem;">${dt}</td>
        <td style="text-align:right;">
          ${kyc!=="VALIDE"?`<button class="btn btn-success btn-sm" onclick="validerKyc(${c.idClient})">Valider</button>`:``}
          ${kyc!=="REJETE"?`<button class="btn btn-danger btn-sm" onclick="rejeterKyc(${c.idClient})" style="margin-left:4px;">Rejeter</button>`:``}
        </td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

async function loadSuggestions() {
  const el = document.getElementById("sugg-list");
  try {
    const sugs = await api.get("/api/clients/suggestions?statut=EN_ATTENTE");
    if (!sugs.length) {
      el.innerHTML = `<div class="card"><div class="empty-state"><div class="empty-icon">✅</div><h3>Aucune demande en attente</h3><p>Toutes les demandes de modification de profil ont été traitées.</p></div></div>`;
      return;
    }
    el.innerHTML = sugs.map(s => {
      const date = s.dateDemande ? new Date(s.dateDemande).toLocaleDateString("fr-FR") : "—";
      const champs = [
        s.nouveauPrenom && `<div><span class="info-label">Prénom :</span> <strong>${s.nouveauPrenom}</strong></div>`,
        s.nouveauNom && `<div><span class="info-label">Nom :</span> <strong>${s.nouveauNom}</strong></div>`,
        s.nouvelEmail && `<div><span class="info-label">Email :</span> <strong>${s.nouvelEmail}</strong></div>`,
        s.nouveauTelephone && `<div><span class="info-label">Téléphone :</span> <strong>${s.nouveauTelephone}</strong></div>`,
        s.nouvelleAdresse && `<div><span class="info-label">Adresse :</span> <strong>${s.nouvelleAdresse}</strong></div>`,
      ].filter(Boolean);
      return `<div class="card card-pad" style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div style="flex:1;">
            <div style="font-weight:700;font-size:.95rem;margin-bottom:2px;">${s.clientNom}</div>
            <div style="font-size:.82rem;color:var(--muted);margin-bottom:10px;">${s.clientEmail} · Demande du ${date}</div>
            <div style="font-size:.85rem;color:var(--gray-700);display:flex;flex-wrap:wrap;gap:8px;">
              ${champs.join("") || "<em style='color:var(--muted);'>Aucune modification précisée</em>"}
            </div>
          </div>
          <div style="display:flex;gap:8px;flex-shrink:0;">
            <button class="btn btn-success btn-sm" onclick="traiterSuggestion(${s.idSuggestion},'APPROUVE')">✓ Approuver</button>
            <button class="btn btn-danger btn-sm" onclick="traiterSuggestion(${s.idSuggestion},'REJETE')">✕ Rejeter</button>
          </div>
        </div>
      </div>`;
    }).join("");
  } catch(e) {
    el.innerHTML = `<div class="card card-pad" style="color:var(--danger);">Erreur : ${e.message}</div>`;
  }
}

async function validerKyc(id) {
  if (!confirm("Valider le KYC de ce client et activer son compte ?")) return;
  try {
    await api.put("/api/kyc/valider/" + id);
    flash("KYC validé — compte client activé.", "success");
    await Promise.all([loadPending(), loadAll()]);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function rejeterKyc(id) {
  if (!confirm("Rejeter le KYC de ce client ?")) return;
  try {
    await api.put("/api/kyc/rejeter/" + id);
    flash("KYC rejeté.", "info");
    await Promise.all([loadPending(), loadAll()]);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function traiterSuggestion(id, decision) {
  const label = decision === "APPROUVE" ? "approuver" : "rejeter";
  if (!confirm("Voulez-vous " + label + " cette demande de modification ?")) return;
  try {
    await api.put("/api/clients/suggestions/" + id, { decision });
    flash("Demande " + (decision === "APPROUVE" ? "approuvée et appliquée" : "rejetée") + ".", decision === "APPROUVE" ? "success" : "info");
    await loadSuggestions();
    await checkSuggestions();
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

init();
</script>
</body>
</html>
