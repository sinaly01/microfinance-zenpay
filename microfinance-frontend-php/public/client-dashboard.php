<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mon Tableau de Bord — ' . APP_NAME;
$sidebar_active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    /* ── Balance masquée ───────────────────────────────────── */
    .balance-hidden-text { letter-spacing: 2px; }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Tableau de bord'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div>
      <h1>Bienvenue, <span id="user-name">...</span></h1>
      <p style="color:var(--muted);font-size:.85rem;" id="kyc-status-info"></p>
    </div>
    <div class="dash-header-right">
      <div class="dash-date-btn">📅 <span id="current-date"></span></div>
      <a href="client-virement.php" class="btn btn-primary btn-sm">+ Nouveau virement</a>
    </div>
  </div>

  <div class="dash-grid">

    <!-- Colonne 1 : Carte + Actions rapides -->
    <div style="display:flex;flex-direction:column;gap:20px;">
      <div class="card card-pad">
        <div class="card-header">
          <span class="card-title">MA CARTE</span>
          <svg class="card-expand" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>
        </div>
        <div class="payment-card-visual" id="main-card">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div id="card-brand-zone">
              <div class="card-logo" id="card-operateur">ZEN-PAY</div>
            </div>
            <button class="balance-toggle-btn" id="toggle-card" onclick="toggleBalance()" title="Afficher / masquer le solde">
              <svg id="eye-card" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <span id="toggle-label">Masquer</span>
            </button>
          </div>
          <div id="chip-zone"><div class="card-momo-chip"></div></div>
          <div class="card-amount" id="card-solde">— FCFA</div>
          <div style="font-size:.72rem;opacity:.7;letter-spacing:.08em;margin-bottom:6px;font-family:monospace;" id="card-numero">•••• •••• ••••</div>
          <div class="card-bottom">
            <div class="card-tel" id="card-tel">•••• ••••</div>
            <div class="card-op" id="card-type">COURANT</div>
          </div>
        </div>
        <div class="weekly-stat" style="margin-top:12px;">
          <div>
            <div class="weekly-label">Dépôts ce mois</div>
            <div class="weekly-value" id="weekly-in">+0 FCFA</div>
          </div>
          <span class="badge-up" id="badge-in">↑</span>
        </div>
      </div>

      <div class="card card-pad">
        <div class="card-header"><span class="card-title">ACTIONS RAPIDES</span></div>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <a href="client-simulateur.php" class="btn btn-outline w-full" style="justify-content:flex-start;gap:10px;">🧮 Simulateur de frais</a>
          <a href="client-virement.php"   class="btn btn-outline w-full" style="justify-content:flex-start;gap:10px;">⚡ Virement rapide</a>
          <a href="client-releve.php"     class="btn btn-outline w-full" style="justify-content:flex-start;gap:10px;">📥 Télécharger mon relevé</a>
          <a href="client-rib.php"        class="btn btn-outline w-full" style="justify-content:flex-start;gap:10px;">🏦 Mon RIB</a>
          <a href="client-abonnement.php" class="btn btn-outline w-full" style="justify-content:flex-start;gap:10px;">📦 Mon abonnement</a>
          <a href="client-support.php"    class="btn btn-outline w-full" style="justify-content:flex-start;gap:10px;">💬 Contacter le support</a>
        </div>
      </div>
    </div>

    <!-- Colonne 2 : Graphique activité réel -->
    <div class="card card-pad">
      <div class="card-header">
        <div>
          <span class="card-title">ACTIVITÉ DU COMPTE</span>
          <div style="font-size:.9rem;font-weight:600;color:var(--gray-700);margin-top:4px;">Transactions par mois</div>
        </div>
        <div class="chart-toggles">
          <button class="chart-toggle active" onclick="switchChart('jour-montant',this)">Montants/j</button>
          <button class="chart-toggle" onclick="switchChart('jour-nombre',this)">Tx/j</button>
          <button class="chart-toggle" onclick="switchChart('mois-montant',this)">Mois</button>
        </div>
      </div>
      <div class="chart-container"><canvas id="activityChart"></canvas></div>
      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:24px;flex-wrap:wrap;">
        <div>
          <div style="font-size:.75rem;color:var(--muted);">Nb transactions</div>
          <div style="font-weight:700;font-size:1.1rem;" id="total-tx-count">—</div>
        </div>
        <div>
          <div style="font-size:.75rem;color:var(--muted);">Dernier mouvement</div>
          <div style="font-weight:700;font-size:1.1rem;" id="last-tx-date">—</div>
        </div>
        <div>
          <div style="font-size:.75rem;color:var(--muted);">Compte n°</div>
          <div style="font-weight:700;font-size:.9rem;font-family:monospace;" id="numero-compte">—</div>
        </div>
      </div>
    </div>

    <!-- Colonne 3 : Solde + KYC -->
    <div style="display:flex;flex-direction:column;gap:20px;">
      <div class="card">
        <div class="balance-card-top">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <div class="balance-label">SOLDE TOTAL</div>
            <button class="amount-toggle-btn" onclick="toggleBalance()" title="Afficher / masquer">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="balance-amount" id="balance-total">— FCFA</div>
          <div class="balance-chart"><canvas id="balanceMiniChart"></canvas></div>
        </div>
        <div class="balance-actions">
          <a href="client-transactions.php?mode=versement" class="action-btn action-btn-send">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Déposer
          </a>
          <a href="client-transactions.php?mode=retrait" class="action-btn action-btn-receive">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Retirer
          </a>
        </div>
      </div>

      <div class="card">
        <div class="info-metric">
          <div class="info-metric-icon">📦</div>
          <div class="info-metric-label">MON ABONNEMENT</div>
          <div class="info-metric-amount" id="abonnement-info">STANDARD</div>
          <div style="font-size:.75rem;color:var(--muted);margin-top:4px;">
            <a href="client-abonnement.php" style="color:var(--primary);">Changer d'offre →</a>
          </div>
        </div>
        <div class="info-metric" style="border-top:1px solid var(--border);">
          <div class="info-metric-icon">📋</div>
          <div class="info-metric-label">STATUT KYC</div>
          <div id="kyc-badge" style="margin-top:6px;"><span class="badge badge-pending">En attente</span></div>
        </div>
      </div>
    </div>

  </div>

  <!-- Transactions récentes -->
  <div class="card tx-table-card">
    <div class="tx-table-header">
      <div>
        <div class="tx-table-title">Historique récent</div>
        <div class="tx-table-sub">Derniers mouvements sur votre compte</div>
      </div>
      <a href="client-transactions.php" class="btn btn-ghost btn-sm">Voir tout →</a>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Opération</th><th>Date</th><th>Heure</th><th>Statut</th><th style="text-align:right;">Montant</th></tr></thead>
        <tbody id="tx-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</main>
</div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

let chartInstance = null;
let miniChartInstance = null;
let balanceVisible = localStorage.getItem("zp_balance_visible") !== "false";
let rawSolde = 0;
let txData = {
  jourLabels:[], parJour:[], montantsParJour:[],
  moisLabels:[], parMois:[], montantsParMois:[]
};

/* ── Balance toggle ─────────────────────────────────────── */
function applyBalanceVisibility() {
  const masque = "•••• ••";
  ["card-solde", "balance-total"].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    if (balanceVisible) {
      el.textContent = el.dataset.real || el.textContent;
      el.classList.remove("balance-blurred");
    } else {
      if (el.dataset.real === undefined) el.dataset.real = el.textContent;
      el.textContent = masque;
      el.classList.add("balance-blurred");
    }
  });
  const label = document.getElementById("toggle-label");
  if (label) label.textContent = balanceVisible ? "Masquer" : "Afficher";
  const eye = document.getElementById("eye-card");
  if (eye) eye.innerHTML = balanceVisible
    ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
    : '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/>';
}

function setSoldeText(el, valeur) {
  const texte = valeur.toLocaleString("fr-FR") + " FCFA";
  el.dataset.real = texte;
  el.textContent = balanceVisible ? texte : "•••• ••";
  el.classList.toggle("balance-blurred", !balanceVisible);
}

function toggleBalance() {
  balanceVisible = !balanceVisible;
  localStorage.setItem("zp_balance_visible", balanceVisible);
  applyBalanceVisibility();
}


/* ── Graphique activité réel ────────────────────────────── */
let currentChartMode = "jour-montant";

function construireDataJournaliere(txs) {
  const now = new Date();
  const labels = [], jours = [], parJour = [], montantsParJour = [];
  for (let i = 29; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth(), now.getDate() - i);
    labels.push(d.toLocaleDateString("fr-FR", {day:"2-digit", month:"short"}));
    jours.push({ y: d.getFullYear(), m: d.getMonth(), d: d.getDate() });
    parJour.push(0);
    montantsParJour.push(0);
  }
  txs.forEach(t => {
    if (!t.dateHeure) return;
    const d = new Date(t.dateHeure);
    const idx = jours.findIndex(j => j.y===d.getFullYear() && j.m===d.getMonth() && j.d===d.getDate());
    if (idx >= 0) {
      parJour[idx]++;
      montantsParJour[idx] += t.montant || 0;
    }
  });
  return { labels, parJour, montantsParJour };
}

function construireDataMensuelle(txs) {
  const now = new Date();
  const mois = [], labels = [];
  for (let i = 11; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
    labels.push(d.toLocaleDateString("fr-FR", {month:"short", year:"2-digit"}));
    mois.push({ year: d.getFullYear(), month: d.getMonth() });
  }
  const parMois = new Array(12).fill(0);
  const montantsParMois = new Array(12).fill(0);
  txs.forEach(t => {
    if (!t.dateHeure) return;
    const d = new Date(t.dateHeure);
    const idx = mois.findIndex(m => m.year===d.getFullYear() && m.month===d.getMonth());
    if (idx >= 0) { parMois[idx]++; montantsParMois[idx] += t.montant || 0; }
  });
  return { labels, parMois, montantsParMois };
}

function dessinerGraphique(mode) {
  currentChartMode = mode;
  const ctx = document.getElementById("activityChart").getContext("2d");

  let labels, data, isMontant;
  if (mode === "jour-montant") {
    labels = txData.jourLabels; data = txData.montantsParJour; isMontant = true;
  } else if (mode === "jour-nombre") {
    labels = txData.jourLabels; data = txData.parJour; isMontant = false;
  } else {
    labels = txData.moisLabels; data = txData.montantsParMois; isMontant = true;
  }

  const maxVal = Math.max(...(data || []), 1);
  if (chartInstance) chartInstance.destroy();
  chartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels || [],
      datasets: [{
        data: data || [],
        backgroundColor: (data || []).map(v => v > 0 && v === maxVal ? "#16a34a" : (v > 0 ? "#bbf7d0" : "#f3f4f6")),
        borderRadius: 5, borderSkipped: false
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: {display:false},
        tooltip: { callbacks: {
          label: c => isMontant ? c.raw.toLocaleString("fr-FR")+" FCFA" : c.raw+" tx"
        }}
      },
      scales: {
        x: { grid:{display:false}, ticks:{font:{size:9}, maxTicksLimit: mode.startsWith("jour") ? 10 : 12} },
        y: { grid:{color:"#f3f4f6"}, ticks:{
          callback: v => isMontant ? (v >= 1000 ? Math.round(v/1000)+"k" : v) : v,
          font:{size:9}
        }}
      }
    }
  });
}

function switchChart(mode, btn) {
  document.querySelectorAll(".chart-toggle").forEach(b => b.classList.remove("active"));
  btn.classList.add("active");
  dessinerGraphique(mode);
}

function dessinerMiniChart(balances) {
  const ctx = document.getElementById("balanceMiniChart").getContext("2d");
  const pts = balances && balances.length ? balances : Array.from({length:12},(_,i)=>i+1);
  if (miniChartInstance) miniChartInstance.destroy();
  miniChartInstance = new Chart(ctx, {
    type: "line",
    data: { labels: pts.map((_,i)=>i), datasets:[{
      data: pts, fill:true,
      borderColor:"#16a34a", backgroundColor:"rgba(22,163,74,.1)",
      borderWidth:2, pointRadius:0, tension:.4
    }]},
    options: { responsive:true, maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{enabled:false}},
      scales:{x:{display:false},y:{display:false}} }
  });
}

/* ── Chargement principal ───────────────────────────────── */
async function init() {
  applyBalanceVisibility();
  document.getElementById("current-date").textContent =
    new Date().toLocaleDateString("fr-FR", {day:"numeric", month:"long", year:"numeric"});

  try {
    const me = await api.get("/api/auth/me");
    const prenom = me.prenom || me.email.split("@")[0];
    document.getElementById("user-name").textContent = prenom;
    document.getElementById("user-avatar").textContent = prenom[0].toUpperCase();

    const idClient = me.idClient;
    if (!idClient) { dessinerGraphique(currentChartMode); dessinerMiniChart(); return; }

    await api.post("/api/comptes/auto-init", {}).catch(() => {});
    const comptes = await api.get("/api/comptes/client/" + idClient).catch(() => []);
    if (comptes && comptes.length > 0) {
      const c = comptes[0];
      rawSolde = parseFloat(c.solde || 0);
      document.getElementById("card-tel").textContent    = (me.telephone || "—").substring(0,6) + "••••";
      document.getElementById("card-type").textContent   = c.typeCompte || "COURANT";
      document.getElementById("card-numero").textContent = c.numeroCompte || "—";
      document.getElementById("numero-compte").textContent = c.numeroCompte || "—";

      // Définir les valeurs masquables
      setSoldeText(document.getElementById("card-solde"), rawSolde);
      setSoldeText(document.getElementById("balance-total"), rawSolde);

      await chargerTransactions(c.idCompte);
    } else {
      dessinerGraphique(currentChartMode);
      dessinerMiniChart();
    }

    const kycMap = {
      PENDING:           ["En attente", "badge-kyc-pending"],
      DOCUMENTS_SOUMIS:  ["Docs soumis", "badge-pending"],
      VALIDE:            ["Validé ✓",   "badge-success"],
      REJETE:            ["Rejeté",     "badge-failed"]
    };
    const statut = me.statutKyc || "PENDING";
    const [kycLabel, kycClass] = kycMap[statut] || ["Inconnu", "badge-pending"];
    document.getElementById("kyc-badge").innerHTML = `<span class="badge ${kycClass}">${kycLabel}</span>`;
    if (statut !== "VALIDE") {
      const kycMessages = {
        PENDING: "⚠️ Votre compte n'est pas encore activé. <a href='client-validation.php' style='color:var(--primary);font-weight:700;'>→ Télécharger votre pièce d'identité</a>",
        DOCUMENTS_SOUMIS: "⏳ Documents reçus — validation en cours par notre équipe (24-48h).",
        REJETE: "❌ Votre dossier a été rejeté. <a href='client-validation.php' style='color:var(--danger);font-weight:700;'>→ Soumettre à nouveau</a>"
      };
      document.getElementById("kyc-status-info").innerHTML = kycMessages[statut] || "⚠️ Compte en attente de validation.";
    }

    if (me.offreAbonnement && me.offreAbonnement.nomOffre)
      document.getElementById("abonnement-info").textContent = me.offreAbonnement.nomOffre;

  } catch(e) { flash("Erreur chargement : " + e.message, "error"); }

  applyBalanceVisibility();
}

async function chargerTransactions(idCompte) {
  try {
    const txs = await api.get("/api/transactions/releve/" + idCompte);
    document.getElementById("total-tx-count").textContent = txs.length;
    if (txs.length > 0)
      document.getElementById("last-tx-date").textContent = new Date(txs[0].dateHeure).toLocaleDateString("fr-FR");

    // Dépôts ce mois
    const now = new Date();
    const totalDepots = txs
      .filter(t => t.typeTransaction === "VERSEMENT" &&
                   new Date(t.dateHeure).getMonth()    === now.getMonth() &&
                   new Date(t.dateHeure).getFullYear() === now.getFullYear())
      .reduce((s, t) => s + (t.montant || 0), 0);
    document.getElementById("weekly-in").textContent = "+" + totalDepots.toLocaleString("fr-FR") + " FCFA";

    // Données graphique — journalier + mensuel
    const jourData = construireDataJournaliere(txs);
    const moisData = construireDataMensuelle(txs);
    txData = {
      jourLabels: jourData.labels, parJour: jourData.parJour, montantsParJour: jourData.montantsParJour,
      moisLabels: moisData.labels, parMois: moisData.parMois, montantsParMois: moisData.montantsParMois
    };
    dessinerGraphique(currentChartMode);

    // Mini graphique (évolution solde approximatif)
    const runningBalance = [];
    let bal = rawSolde;
    const sorted = [...txs].reverse();
    sorted.forEach(t => {
      if (t.typeTransaction === "VERSEMENT") bal -= (t.montant || 0);
      else bal += (t.montant || 0);
      runningBalance.push(Math.max(bal, 0));
    });
    dessinerMiniChart(runningBalance.reverse().slice(-12));

    // Tableau historique
    const tbody = document.getElementById("tx-tbody");
    if (!txs.length) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucune transaction</td></tr>`;
      return;
    }
    const icons  = {VERSEMENT:"📥", RETRAIT:"📤", VIREMENT:"💸"};
    const colors = {VERSEMENT:"#dcfce7", RETRAIT:"#fee2e2", VIREMENT:"#fef3c7"};
    tbody.innerHTML = txs.slice(0, 8).map(t => {
      const type = t.typeTransaction || "—";
      const dt = t.dateHeure ? new Date(t.dateHeure) : null;
      const sc = t.statut === "VALIDEE" ? "badge-success" : t.statut === "REJETEE" ? "badge-failed" : "badge-pending";
      const sl = t.statut === "VALIDEE" ? "Réussi" : t.statut === "REJETEE" ? "Rejeté" : "En cours";
      const isPlus = type === "VERSEMENT";
      return `<tr>
        <td><div style="display:flex;align-items:center;gap:12px;">
          <div class="tx-icon" style="background:${colors[type]||"#f3f4f6"}">${icons[type]||"🔄"}</div>
          <div><div class="tx-name">${type}</div><div class="tx-sub">${t.reference||"—"}</div></div>
        </div></td>
        <td>${dt ? dt.toLocaleDateString("fr-FR") : "—"}</td>
        <td>${dt ? dt.toLocaleTimeString("fr-FR",{hour:"2-digit",minute:"2-digit"}) : "—"}</td>
        <td><span class="badge ${sc}">${sl}</span></td>
        <td style="text-align:right;" class="${isPlus?"tx-amount-plus":"tx-amount-minus"}">${isPlus?"+":"-"}${(t.montant||0).toLocaleString("fr-FR")} FCFA</td>
      </tr>`;
    }).join("");

  } catch(e) {
    document.getElementById("tx-tbody").innerHTML =
      `<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--muted);">Erreur chargement</td></tr>`;
    dessinerGraphique(currentChartMode);
    dessinerMiniChart();
  }
}

init();
</script>
</body>
</html>
