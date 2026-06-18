<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mes Transactions — ' . APP_NAME;
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
<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Mes Transactions'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div>
      <h1>Mes <span>Transactions</span></h1>
      <p style="color:var(--muted);font-size:.85rem;">Dépôts, retraits et historique complet</p>
    </div>
    <div class="dash-header-right">
      <a href="client-releve.php" class="btn btn-outline btn-sm">📥 Télécharger relevé</a>
      <a href="client-virement.php" class="btn btn-primary btn-sm">+ Virement</a>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- Formulaire opération -->
    <div class="card card-pad">
      <h3 style="font-size:.95rem;font-weight:700;margin-bottom:16px;">Nouvelle opération</h3>

      <div class="form-group">
        <label class="form-label">Type d'opération</label>
        <select class="form-control" id="type-op" onchange="onTypeChange()">
          <option value="versement">📥 Dépôt (versement)</option>
          <option value="retrait">📤 Retrait Wave</option>
        </select>
      </div>

      <!-- Compte (toujours compte principal) -->
      <div class="form-group">
        <label class="form-label">Compte</label>
        <div id="compte-display" style="padding:10px 12px;background:var(--g50);border:1.5px solid var(--border);border-radius:var(--r);font-size:.88rem;font-weight:600;">—</div>
        <input type="hidden" id="compte-id">
      </div>

      <!-- Opérateur MoMo (versement seulement) -->
      <div class="form-group" id="zone-operateur">
        <label class="form-label">Opérateur Mobile Money</label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
          <label class="momo-option" onclick="selectOp(this,'ORANGE_MONEY')" style="background:#ff6600;">
            <input type="radio" name="operateur" value="ORANGE_MONEY" style="display:none;">
            🟠 Orange Money
          </label>
          <label class="momo-option" onclick="selectOp(this,'WAVE')" style="background:#0066cc;">
            <input type="radio" name="operateur" value="WAVE" style="display:none;">
            🌊 Wave
          </label>
          <label class="momo-option" onclick="selectOp(this,'MTN_MONEY')" style="background:#f59e0b;">
            <input type="radio" name="operateur" value="MTN_MONEY" style="display:none;">
            💛 MTN Money
          </label>
          <label class="momo-option" onclick="selectOp(this,'MOOV_MONEY')" style="background:#16a34a;">
            <input type="radio" name="operateur" value="MOOV_MONEY" style="display:none;">
            💚 Moov Money
          </label>
        </div>
        <div id="op-error" style="display:none;color:var(--danger);font-size:.8rem;margin-top:4px;">Sélectionnez un opérateur</div>
      </div>

      <!-- Numéro MoMo expéditeur (versement seulement) -->
      <div class="form-group" id="zone-momo-number" style="display:none;">
        <label class="form-label">Votre numéro Mobile Money</label>
        <input class="form-control" type="tel" id="momo-number" placeholder="+225 07 00 00 00 00">
        <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Numéro depuis lequel vous envoyez le dépôt.</div>
      </div>

      <!-- Numéro destination (retrait seulement) -->
      <div class="form-group" id="zone-wave" style="display:none;">
        <label class="form-label">Numéro Mobile Money de retrait</label>
        <input class="form-control" type="tel" id="wave-number" placeholder="+225 07 00 00 00 00">
        <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Le montant sera envoyé sur ce numéro.</div>
      </div>

      <div class="form-group">
        <label class="form-label">Montant (FCFA)</label>
        <div class="input-group">
          <input class="form-control" type="number" id="montant-op" placeholder="ex: 25000" min="500" step="500">
          <span class="input-addon">FCFA</span>
        </div>
      </div>

      <button class="btn btn-primary w-full" onclick="effectuerOperation()">Valider</button>
    </div>

    <!-- Stats rapides -->
    <div style="display:flex;flex-direction:column;gap:12px;">
      <div class="stat-card">
        <div class="stat-card-icon" style="background:var(--g100);">📊</div>
        <h3 id="stat-total">—</h3>
        <p>Total transactions</p>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon" style="background:var(--g100);">📈</div>
        <h3 id="stat-entrees">—</h3>
        <p>Total entrées</p>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon" style="background:#fee2e2;">📉</div>
        <h3 id="stat-sorties">—</h3>
        <p>Total sorties</p>
      </div>
    </div>
  </div>

  <!-- Tableau historique -->
  <div class="card">
    <div class="tx-table-header">
      <div>
        <div class="tx-table-title">Historique complet</div>
        <div class="tx-table-sub" id="tx-compte-label">Chargement...</div>
      </div>
      <select class="form-control" id="filtre-type" style="width:auto;padding:6px 30px 6px 12px;font-size:.82rem;" onchange="filtrerTransactions()">
        <option value="">Tous les types</option>
        <option value="VERSEMENT">Versements</option>
        <option value="RETRAIT">Retraits</option>
        <option value="VIREMENT">Virements</option>
      </select>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Opération</th><th>Date</th><th>Heure</th><th>Référence</th><th>Statut</th><th style="text-align:right;">Montant</th></tr></thead>
        <tbody id="tx-tbody"><tr><td colspan="6" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</main>
</div>
</div>

<style>
.momo-option {
  display:flex;align-items:center;justify-content:center;gap:6px;
  padding:10px 8px;border-radius:8px;color:#fff;font-weight:700;font-size:.82rem;
  cursor:pointer;opacity:.7;transition:opacity .15s,transform .1s;border:3px solid transparent;
}
.momo-option.selected { opacity:1;transform:scale(1.04);border-color:#fff; }
</style>

<script src="js/api.js"></script>
<script>
requireAuth();

let allTxs = [];
let compteId = null;
let selectedOp = null;

function onTypeChange() {
  const type = document.getElementById("type-op").value;
  document.getElementById("zone-operateur").style.display  = "";
  document.getElementById("zone-momo-number").style.display = type === "versement" ? "" : "none";
  document.getElementById("zone-wave").style.display        = type === "retrait"   ? "" : "none";
  selectedOp = null;
  document.querySelectorAll(".momo-option").forEach(o => o.classList.remove("selected"));
}

function selectOp(el, val) {
  document.querySelectorAll(".momo-option").forEach(o=>o.classList.remove("selected"));
  el.classList.add("selected");
  selectedOp = val;
  document.getElementById("op-error").style.display = "none";
}

// Initialise l'état des zones selon le mode par défaut ou le paramètre URL
const urlMode = new URLSearchParams(window.location.search).get("mode");
if (urlMode) document.getElementById("type-op").value = urlMode;
onTypeChange();

async function init() {
  try {
    const me = await requireKycValide();
    if (!me) return;
    await loadClientTopbar();
    if (me.idClient) {
      await api.post("/api/comptes/auto-init", {}).catch(() => {});
      const comptes = await api.get("/api/comptes/client/" + me.idClient).catch(()=>[]);
      const actifs = (comptes||[]).filter(c => c.statut === "ACTIF");
      if (actifs.length) {
        const c = actifs[0];
        compteId = c.idCompte;
        document.getElementById("compte-display").textContent =
          c.numeroCompte + "  ·  Solde : " + parseFloat(c.solde||0).toLocaleString("fr-FR") + " FCFA";
        document.getElementById("compte-id").value = c.idCompte;
        document.getElementById("tx-compte-label").textContent = "Compte : " + c.numeroCompte;
        await chargerTransactions(c.idCompte);
      } else {
        document.getElementById("compte-display").textContent = "Aucun compte actif";
        document.getElementById("tx-compte-label").textContent = "Aucun compte disponible";
        flash("Aucun compte actif — contactez votre gestionnaire.", "error");
      }
    }
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function chargerTransactions(idCompte) {
  try {
    allTxs = await api.get("/api/transactions/releve/" + idCompte);
    let entrees=0, sorties=0;
    allTxs.forEach(t => {
      if (t.typeTransaction==="VERSEMENT") entrees += t.montant||0;
      else sorties += t.montant||0;
    });
    document.getElementById("stat-total").textContent   = allTxs.length;
    document.getElementById("stat-entrees").textContent = entrees.toLocaleString("fr-FR") + " FCFA";
    document.getElementById("stat-sorties").textContent = sorties.toLocaleString("fr-FR") + " FCFA";
    afficherTransactions(allTxs);
  } catch(e) {
    document.getElementById("tx-tbody").innerHTML =
      `<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--muted);">Impossible de charger</td></tr>`;
  }
}

function filtrerTransactions() {
  const f = document.getElementById("filtre-type").value;
  afficherTransactions(f ? allTxs.filter(t=>t.typeTransaction===f) : allTxs);
}

function afficherTransactions(txs) {
  const tbody = document.getElementById("tx-tbody");
  if (!txs.length) { tbody.innerHTML=`<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">Aucune transaction</td></tr>`; return; }
  const icons  = {VERSEMENT:"📥",RETRAIT:"📤",VIREMENT:"💸"};
  const colors = {VERSEMENT:"#dcfce7",RETRAIT:"#fee2e2",VIREMENT:"#fef3c7"};
  tbody.innerHTML = txs.map(t => {
    const type = t.typeTransaction||"—";
    const dt = t.dateHeure ? new Date(t.dateHeure) : null;
    const sc = t.statut==="VALIDEE"?"badge-success":t.statut==="REJETEE"?"badge-failed":"badge-pending";
    const isPlus = type==="VERSEMENT";
    return `<tr>
      <td><div style="display:flex;align-items:center;gap:10px;">
        <div class="tx-icon" style="background:${colors[type]||"#f3f4f6"}">${icons[type]||"🔄"}</div>
        <div class="tx-name">${type}</div>
      </div></td>
      <td>${dt?dt.toLocaleDateString("fr-FR"):"—"}</td>
      <td>${dt?dt.toLocaleTimeString("fr-FR",{hour:"2-digit",minute:"2-digit"}):"—"}</td>
      <td style="font-family:monospace;font-size:.8rem;">${t.reference||"—"}</td>
      <td><span class="badge ${sc}">${t.statut==="VALIDEE"?"Réussi":t.statut==="REJETEE"?"Rejeté":"En cours"}</span></td>
      <td style="text-align:right;" class="${isPlus?"tx-amount-plus":"tx-amount-minus"}">${isPlus?"+":"-"}${(t.montant||0).toLocaleString("fr-FR")} FCFA</td>
    </tr>`;
  }).join("");
}

async function effectuerOperation() {
  const type    = document.getElementById("type-op").value;
  const montant = parseFloat(document.getElementById("montant-op").value);
  const idCompte = parseInt(document.getElementById("compte-id").value);

  if (!montant || montant < 500) { flash("Montant minimum : 500 FCFA", "error"); return; }
  if (!idCompte) { flash("Compte non disponible.", "error"); return; }

  if (type === "versement") {
    if (!selectedOp) { document.getElementById("op-error").style.display=""; flash("Sélectionnez un opérateur MoMo", "error"); return; }
    const momoNum = document.getElementById("momo-number").value.trim();
    if (!momoNum) { flash("Entrez votre numéro Mobile Money.", "error"); return; }
    const opLabels = {ORANGE_MONEY:"Orange Money",WAVE:"Wave",MTN_MONEY:"MTN Money",MOOV_MONEY:"Moov Money"};
    try {
      await api.post("/api/transactions/versement", {
        idCompte, montant,
        description: "Dépôt via " + (opLabels[selectedOp]||selectedOp) + " depuis " + momoNum,
        canal: selectedOp
      });
      flash("Dépôt effectué via " + (opLabels[selectedOp]||selectedOp) + " !", "success");
      document.getElementById("montant-op").value = "";
      selectedOp = null;
      document.querySelectorAll(".momo-option").forEach(o=>o.classList.remove("selected"));
      await chargerTransactions(idCompte);
    } catch(e) { flash("Erreur : " + e.message, "error"); }

  } else {
    if (!selectedOp) { document.getElementById("op-error").style.display=""; flash("Sélectionnez un opérateur MoMo", "error"); return; }
    const destNum = document.getElementById("wave-number").value.trim();
    if (!destNum) { flash("Entrez le numéro Mobile Money de destination.", "error"); return; }
    const opLabels = {ORANGE_MONEY:"Orange Money",WAVE:"Wave",MTN_MONEY:"MTN Money",MOOV_MONEY:"Moov Money"};
    try {
      await api.post("/api/transactions/retrait", {
        idCompte, montant,
        description: "Retrait " + (opLabels[selectedOp]||selectedOp) + " vers " + destNum,
        canal: selectedOp,
        numeroDestination: destNum
      });
      flash("Retrait envoyé sur " + (opLabels[selectedOp]||selectedOp) + " " + destNum + " !", "success");
      document.getElementById("montant-op").value = "";
      document.getElementById("wave-number").value = "";
      selectedOp = null;
      document.querySelectorAll(".momo-option").forEach(o=>o.classList.remove("selected"));
      await chargerTransactions(idCompte);
    } catch(e) { flash("Erreur : " + e.message, "error"); }
  }
}

init();
</script>
</body>
</html>
