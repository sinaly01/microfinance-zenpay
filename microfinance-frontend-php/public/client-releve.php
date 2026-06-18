<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mon Relevé — ZEN-PAY';
$sidebar_active = 'releve';
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

<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>

  <div class="main-area">
<?php $topbar_title = 'Mon Relevé'; include __DIR__ . '/../includes/topbar-client.php'; ?>

    <main class="page-content">
      <div class="feature-layout">

        <div class="feature-header">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <div style="width:44px;height:44px;background:var(--g100);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">📥</div>
            <div>
              <h2 style="margin:0;">Mon relevé de compte</h2>
              <p style="margin:0;">Consultez et imprimez vos transactions par période</p>
            </div>
          </div>
        </div>

        <!-- Filtre période -->
        <div class="card card-pad mb-4">
          <h3 style="font-size:1rem;font-weight:700;margin-bottom:20px;">Sélectionner la période</h3>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div class="form-group" style="margin:0;">
              <label class="form-label">Date de début</label>
              <input class="form-control" type="date" id="date-debut">
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label">Date de fin</label>
              <input class="form-control" type="date" id="date-fin">
            </div>
          </div>

          <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
            <button class="btn btn-ghost btn-sm" onclick="setPeriode(7)">7 derniers jours</button>
            <button class="btn btn-ghost btn-sm" onclick="setPeriode(30)">30 derniers jours</button>
            <button class="btn btn-ghost btn-sm" onclick="setPeriode(90)">3 derniers mois</button>
            <button class="btn btn-ghost btn-sm" onclick="setPeriodeMois()">Ce mois</button>
          </div>

          <div style="display:flex;gap:12px;">
            <button class="btn btn-primary" onclick="chargerReleve()" style="flex:1;">
              📊 Afficher le relevé
            </button>
            <button class="btn btn-outline" onclick="imprimerReleve()">
              🖨️ Imprimer / PDF
            </button>
          </div>
        </div>

        <!-- Résumé période -->
        <div id="resume-periode" style="display:none;" class="mb-4">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
            <div class="stat-card">
              <div class="stat-card-icon" style="background:var(--g100);font-size:1.1rem;">📥</div>
              <h3 id="sum-entrees">—</h3>
              <p>Total entrées</p>
            </div>
            <div class="stat-card">
              <div class="stat-card-icon" style="background:#fee2e2;font-size:1.1rem;">📤</div>
              <h3 id="sum-sorties">—</h3>
              <p>Total sorties</p>
            </div>
            <div class="stat-card">
              <div class="stat-card-icon" style="background:#fef3c7;font-size:1.1rem;">📋</div>
              <h3 id="sum-count">—</h3>
              <p>Nb transactions</p>
            </div>
          </div>
        </div>

        <!-- Tableau relevé -->
        <div class="card" id="releve-table-card" style="display:none;">
          <div class="tx-table-header">
            <div>
              <div class="tx-table-title">Relevé de compte</div>
              <div class="tx-table-sub" id="releve-periode-label">—</div>
            </div>
          </div>
          <div style="overflow-x:auto;" id="releve-printable">
            <!-- En-tête imprimable -->
            <div class="print-header" style="display:none;padding:24px;border-bottom:2px solid var(--g600);">
              <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                  <div style="font-weight:800;font-size:1.4rem;color:var(--g700);">ZEN-PAY</div>
                  <div style="font-size:.85rem;color:#666;">Microfinance Digitale · Zone BCEAO</div>
                </div>
                <div style="text-align:right;font-size:.85rem;color:#666;">
                  <div id="print-client-info">—</div>
                  <div id="print-compte-info">—</div>
                  <div>Édité le <?= date('d/m/Y') ?></div>
                </div>
              </div>
              <div style="margin-top:16px;font-size:1rem;font-weight:700;">
                RELEVÉ DE COMPTE — <span id="print-periode">—</span>
              </div>
            </div>
            <table id="releve-table">
              <thead>
                <tr>
                  <th>Date</th><th>Heure</th><th>Type</th><th>Référence</th>
                  <th>Description</th><th style="text-align:right;">Débit</th>
                  <th style="text-align:right;">Crédit</th><th>Statut</th>
                </tr>
              </thead>
              <tbody id="releve-tbody">
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--muted);">
                  Sélectionnez une période et cliquez sur "Afficher"
                </td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<style>
@media print {
  .sidebar, .main-area > header, .topbar, .feature-header,
  .card:not(#releve-table-card), #resume-periode .stat-card > *:not(h3):not(p),
  button, a.btn { display: none !important; }
  .main-area { margin-left: 0 !important; }
  .page-content { padding: 0 !important; }
  #releve-table-card { box-shadow: none !important; border: none !important; }
  .print-header { display: flex !important; }
}
</style>

<script src="js/api.js"></script>
<script>
requireAuth();

let compteId = null;
let clientInfo = {};

function setPeriode(jours) {
  const fin = new Date();
  const debut = new Date();
  debut.setDate(debut.getDate() - jours);
  document.getElementById("date-debut").value = debut.toISOString().split("T")[0];
  document.getElementById("date-fin").value   = fin.toISOString().split("T")[0];
}

function setPeriodeMois() {
  const now = new Date();
  const debut = new Date(now.getFullYear(), now.getMonth(), 1);
  document.getElementById("date-debut").value = debut.toISOString().split("T")[0];
  document.getElementById("date-fin").value   = now.toISOString().split("T")[0];
}

async function init() {
  // Initialiser à ce mois par défaut
  setPeriodeMois();

  try {
    const me = await requireKycValide();
    if (!me) return;
    document.getElementById("user-avatar").textContent = (me.prenom||"?")[0].toUpperCase();
    clientInfo = me;

    document.getElementById("print-client-info").textContent =
      (me.prenom||"") + " " + (me.nom||"") + " · " + (me.telephone||"");

    if (me.idClient) {
      const comptes = await api.get("/api/comptes/client/" + me.idClient).catch(()=>[]);
      if (comptes && comptes.length) {
        compteId = comptes[0].idCompte;
        document.getElementById("print-compte-info").textContent = "Compte : " + comptes[0].numeroCompte;
      }
    }
  } catch(e) {}
}

async function chargerReleve() {
  if (!compteId) { flash("Compte non trouvé", "error"); return; }

  const debut = document.getElementById("date-debut").value;
  const fin   = document.getElementById("date-fin").value;

  if (!debut || !fin) { flash("Sélectionnez une période", "error"); return; }

  const debutISO = debut + "T00:00:00";
  const finISO   = fin   + "T23:59:59";

  try {
    const txs = await api.get(`/api/transactions/releve/${compteId}/periode?debut=${debutISO}&fin=${finISO}`);

    // Résumé
    let entrees = 0, sorties = 0;
    txs.forEach(t => {
      if (t.typeTransaction === "VERSEMENT") entrees += t.montant||0;
      else sorties += t.montant||0;
    });

    document.getElementById("sum-entrees").textContent = entrees.toLocaleString("fr-FR") + " FCFA";
    document.getElementById("sum-sorties").textContent = sorties.toLocaleString("fr-FR") + " FCFA";
    document.getElementById("sum-count").textContent   = txs.length + " opérations";
    document.getElementById("resume-periode").style.display = "block";

    const periodeLabel = `Du ${new Date(debut).toLocaleDateString("fr-FR")} au ${new Date(fin).toLocaleDateString("fr-FR")}`;
    document.getElementById("releve-periode-label").textContent = periodeLabel;
    document.getElementById("print-periode").textContent = periodeLabel;

    const tbody = document.getElementById("releve-tbody");
    if (!txs.length) {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--muted);">
        Aucune transaction sur cette période</td></tr>`;
      document.getElementById("releve-table-card").style.display = "block";
      return;
    }

    tbody.innerHTML = txs.map(t => {
      const dt = t.dateHeure ? new Date(t.dateHeure) : null;
      const isEntree = t.typeTransaction === "VERSEMENT";
      const montant  = (t.montant||0).toLocaleString("fr-FR") + " FCFA";
      const statusClass = t.statut==="VALIDEE"?"badge-success":t.statut==="REJETEE"?"badge-failed":"badge-pending";
      return `<tr>
        <td>${dt ? dt.toLocaleDateString("fr-FR") : "—"}</td>
        <td>${dt ? dt.toLocaleTimeString("fr-FR",{hour:"2-digit",minute:"2-digit"}) : "—"}</td>
        <td>${t.typeTransaction||"—"}</td>
        <td style="font-family:monospace;font-size:.8rem;">${t.reference||"—"}</td>
        <td style="color:var(--muted);">${t.description||"—"}</td>
        <td style="text-align:right;" class="${isEntree?"":"tx-amount-minus"}">${isEntree ? "—" : montant}</td>
        <td style="text-align:right;" class="${isEntree?"tx-amount-plus":""}">${isEntree ? montant : "—"}</td>
        <td><span class="badge ${statusClass}">${t.statut==="VALIDEE"?"✓":"✗"}</span></td>
      </tr>`;
    }).join("");

    document.getElementById("releve-table-card").style.display = "block";
  } catch(e) {
    flash("Erreur chargement : " + e.message, "error");
  }
}

function imprimerReleve() {
  document.querySelectorAll(".print-header").forEach(el => el.style.display = "flex");
  window.print();
  document.querySelectorAll(".print-header").forEach(el => el.style.display = "none");
}

init();
</script>
</body>
</html>
