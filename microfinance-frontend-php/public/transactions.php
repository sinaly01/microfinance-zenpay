<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Transactions — ' . APP_NAME;
$sidebar_active = 'transactions';
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
<?php $topbar_title = 'Gestion des Transactions'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Transactions <span>ZEN-PAY</span></h1><p style="color:var(--muted);font-size:.85rem;">Consultation uniquement — le Super Admin ne peut pas initier de transactions</p></div>
    <div class="dash-header-right">
      <span class="badge badge-pending" id="tx-count">— transactions</span>
    </div>
  </div>

  <div class="card card-pad" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <input class="form-control" type="text" id="search-input" placeholder="Référence, compte..." oninput="filtrer()" style="max-width:300px;">
      <select class="form-control" id="filter-type" onchange="filtrer()" style="max-width:160px;">
        <option value="">Tous types</option>
        <option value="VERSEMENT">Versement</option>
        <option value="RETRAIT">Retrait</option>
        <option value="VIREMENT">Virement</option>
      </select>
      <select class="form-control" id="filter-statut" onchange="filtrer()" style="max-width:160px;">
        <option value="">Tous statuts</option>
        <option value="VALIDEE">Validée</option>
        <option value="REJETEE">Rejetée</option>
        <option value="EN_COURS">En cours</option>
        <option value="ANNULEE">Annulée</option>
      </select>
    </div>
  </div>

  <div class="card">
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr><th>Référence</th><th>Type</th><th>Montant</th><th>Compte source</th><th>Statut</th><th>Date</th></tr>
        </thead>
        <tbody id="tx-tbody"><tr><td colspan="6" style="text-align:center;padding:40px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Opérations suspectes -->
  <div class="card" style="margin-top:24px;">
    <div class="tx-table-header">
      <div><div class="tx-table-title" style="color:var(--danger);">⚠️ Opérations suspectes / Rejetées</div></div>
    </div>
    <div id="surveillance-list"><div style="text-align:center;padding:32px;"><span class="spinner"></span></div></div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let allTx = [];
const txBadge = {VALIDEE:'badge-success',EN_COURS:'badge-kyc-pending',REJETEE:'badge-failed',ANNULEE:'badge-failed'};
const txLabel = {VERSEMENT:'Versement',RETRAIT:'Retrait',VIREMENT:'Virement'};
const txColor = {VERSEMENT:'#16a34a',RETRAIT:'#dc2626',VIREMENT:'#2563eb'};

// L'endpoint GET /api/transactions retourne les entités brutes sans typeTransaction ni numeroCompteSource.
// On les déduit : présence de "source" → VERSEMENT, "canal" → RETRAIT, sinon VIREMENT.
function normaliserTx(t) {
  const type = t.typeTransaction
    || ("source" in t ? "VERSEMENT" : "canal" in t ? "RETRAIT" : "VIREMENT");
  const compteSource = t.numeroCompteSource
    || t.compte?.numeroCompte
    || null;
  return {...t, typeTransaction: type, numeroCompteSource: compteSource};
}

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  try {
    const raw = await api.get("/api/transactions");
    allTx = raw.map(normaliserTx);
    afficher(allTx);
    document.getElementById("tx-count").textContent = allTx.length + " transactions";
  } catch(e) {
    document.getElementById("tx-tbody").innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--danger);">${e.message}</td></tr>`;
  }
  try {
    const suspects = await api.get("/api/transactions/surveillance");
    const el = document.getElementById("surveillance-list");
    if (!suspects.length) { el.innerHTML=`<div style="padding:24px;text-align:center;color:var(--muted);">Aucune opération suspecte</div>`; return; }
    el.innerHTML = suspects.map(t => `
      <div style="display:flex;justify-content:space-between;padding:10px 20px;border-bottom:1px solid var(--border);font-size:.83rem;">
        <span style="font-family:monospace;">${t.reference||"—"}</span>
        <span>${t.typeTransaction||"—"}</span>
        <span style="font-weight:700;color:var(--danger);">${Number(t.montant||0).toLocaleString("fr-FR")} FCFA</span>
        <span>${t.numeroCompteSource||"—"}</span>
        <span style="font-size:.76rem;color:var(--muted);">${t.dateHeure?new Date(t.dateHeure).toLocaleString("fr-FR"):"—"}</span>
      </div>`).join("");
  } catch(e) {}
}

function filtrer() {
  const q = document.getElementById("search-input").value.toLowerCase();
  const type = document.getElementById("filter-type").value;
  const statut = document.getElementById("filter-statut").value;
  const filtered = allTx.filter(t => {
    const matchQ = !q || (t.reference||"").toLowerCase().includes(q) || (t.numeroCompteSource||"").toLowerCase().includes(q);
    const matchType = !type || (t.typeTransaction||"") === type;
    const matchSt = !statut || t.statut === statut;
    return matchQ && matchType && matchSt;
  });
  afficher(filtered);
}

function afficher(list) {
  const tbody = document.getElementById("tx-tbody");
  if (!list.length) { tbody.innerHTML=`<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">Aucune transaction trouvée</td></tr>`; return; }
  tbody.innerHTML = list.map(t => {
    const dt = t.dateHeure ? new Date(t.dateHeure).toLocaleString("fr-FR") : "—";
    const s = t.statut || "—";
    const type = t.typeTransaction || "—";
    const montant = Number(t.montant||0).toLocaleString("fr-FR");
    const color = txColor[type] || "#888";
    const label = txLabel[type] || type;
    const compteSource = t.numeroCompteSource || "—";
    return `<tr>
      <td style="font-family:monospace;font-size:.8rem;">${t.reference||"—"}</td>
      <td><span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:.72rem;font-weight:700;color:${color};background:${color}18;">${label}</span></td>
      <td style="font-weight:700;">${montant} FCFA</td>
      <td style="font-size:.8rem;font-family:monospace;">${compteSource}</td>
      <td><span class="badge ${txBadge[s]||"badge-pending"}">${s}</span></td>
      <td style="font-size:.78rem;">${dt}</td>
    </tr>`;
  }).join("");
}

init();
</script>
</body>
</html>
