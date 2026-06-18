<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Virement rapide — ' . APP_NAME;
$sidebar_active = 'virement';
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
<?php $topbar_title = 'Virement rapide'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content">
<div class="feature-layout" style="max-width:560px;">

  <div class="feature-header">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
      <div style="width:44px;height:44px;background:var(--g100);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">⚡</div>
      <div>
        <h2 style="margin:0;">Virement rapide</h2>
        <p style="margin:0;color:var(--muted);font-size:.85rem;">Envoyez de l'argent à un autre client ZEN-PAY</p>
      </div>
    </div>
  </div>

  <!-- Compte source -->
  <div class="card card-pad" style="margin-bottom:16px;border-left:4px solid var(--primary);">
    <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Mon compte</div>
    <div style="font-weight:700;font-size:.95rem;" id="source-display">Chargement…</div>
  </div>

  <!-- Formulaire unique -->
  <div class="card card-pad" id="form-card">

    <!-- Destinataire -->
    <div class="form-group">
      <label class="form-label">Numéro de téléphone du destinataire</label>
      <div style="display:flex;gap:10px;">
        <input class="form-control" type="tel" id="tel-dest" placeholder="+225 07 00 00 00 00"
               style="flex:1;" onkeydown="if(event.key==='Enter') rechercherBeneficiaire()">
        <button class="btn btn-outline" onclick="rechercherBeneficiaire()" id="btn-check" style="white-space:nowrap;">
          Vérifier
        </button>
      </div>
    </div>

    <!-- Résultat recherche -->
    <div id="ben-found" style="display:none;padding:12px 16px;background:var(--g50);border:1.5px solid var(--g200);border-radius:var(--r);margin-bottom:16px;display:none;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:50%;background:var(--g600);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;" id="ben-avatar">?</div>
      <div style="flex:1;">
        <div style="font-weight:700;" id="ben-nom">—</div>
        <div style="font-size:.82rem;color:var(--muted);" id="ben-tel">—</div>
      </div>
      <span class="badge badge-success">✓ Trouvé</span>
    </div>

    <div id="ben-not-found" style="display:none;padding:10px 14px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:var(--r);color:var(--danger);font-size:.85rem;margin-bottom:16px;">
      Aucun client ZEN-PAY avec ce numéro.
    </div>

    <!-- Section montant (visible après validation du destinataire) -->
    <div id="form-montant" style="display:none;">
      <hr style="border:none;border-top:1px solid var(--border);margin:4px 0 16px;">

      <div class="form-group">
        <label class="form-label">Montant à envoyer</label>
        <div class="input-group">
          <input class="form-control" type="number" id="montant-vir" placeholder="ex: 25000" min="500" step="500" oninput="calculerFrais()">
          <span class="input-addon">FCFA</span>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Motif (optionnel)</label>
        <input class="form-control" type="text" id="motif-vir" placeholder="ex: Remboursement, Cadeau…">
      </div>

      <!-- Aperçu frais -->
      <div id="apercu-frais" style="display:none;padding:14px;background:var(--g50);border-radius:var(--r);margin-bottom:18px;font-size:.88rem;">
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span style="color:var(--muted);">Montant envoyé</span><strong id="ap-montant">—</strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span style="color:var(--muted);">Frais (<span id="ap-taux">—</span>%)</span>
          <strong style="color:var(--danger);" id="ap-frais">—</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding-top:8px;border-top:1px solid var(--g200);">
          <span style="font-weight:700;">Total débité</span>
          <strong style="color:var(--primary);" id="ap-total">—</strong>
        </div>
      </div>

      <button class="btn btn-primary w-full" onclick="confirmerVirement()" id="btn-virer" style="height:46px;font-size:.95rem;">
        ⚡ Envoyer le virement
      </button>
    </div>
  </div>

  <!-- Succès -->
  <div id="succes-card" style="display:none;" class="card card-pad text-center">
    <div style="font-size:3rem;margin-bottom:12px;">✅</div>
    <h3 style="font-size:1.2rem;font-weight:800;color:var(--g700);margin-bottom:8px;">Virement effectué !</h3>
    <p style="color:var(--muted);margin-bottom:20px;" id="succes-detail">—</p>
    <div style="display:flex;gap:12px;justify-content:center;">
      <a href="client-transactions.php" class="btn btn-primary btn-sm">Voir mes transactions</a>
      <button class="btn btn-outline btn-sm" onclick="reinitialiser()">Nouveau virement</button>
    </div>
  </div>

</div>
</main>
</div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

let destCompteId  = null;
let sourceCompteId = null;
let myClientId    = null;
let tauxVirement  = 1.0;
let searchTimeout = null;

async function init() {
  try {
    const me = await requireKycValide();
    if (!me) return;
    document.getElementById("user-avatar").textContent = (me.prenom || "?")[0].toUpperCase();
    myClientId   = me.idClient;
    tauxVirement = me.offreAbonnement ? (Number(me.offreAbonnement.fraisVirementInterne) || 1.0) : 1.0;

    if (me.idClient) {
      await api.post("/api/comptes/auto-init", {}).catch(() => {});
      const comptes = await api.get("/api/comptes/client/" + me.idClient).catch(() => []);
      if (comptes && comptes.length) {
        const c = comptes.find(x => x.statut === "ACTIF") || comptes[0];
        sourceCompteId = c.idCompte;
        document.getElementById("source-display").textContent =
          c.numeroCompte + "  ·  Solde : " + parseFloat(c.solde || 0).toLocaleString("fr-FR") + " FCFA";
      } else {
        document.getElementById("source-display").textContent = "Aucun compte actif";
      }
    }
  } catch(e) { flash("Erreur chargement : " + e.message, "error"); }
}

async function rechercherBeneficiaire() {
  const tel = document.getElementById("tel-dest").value.trim().replace(/\s/g, "");
  if (!tel) { flash("Entrez un numéro de téléphone", "error"); return; }

  document.getElementById("ben-found").style.display     = "none";
  document.getElementById("ben-not-found").style.display = "none";
  document.getElementById("form-montant").style.display  = "none";
  destCompteId = null;

  const btn = document.getElementById("btn-check");
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner" style="width:14px;height:14px;border-width:2px;"></span>';

  try {
    const dest = await api.get("/api/clients/by-telephone?telephone=" + encodeURIComponent(tel));

    if (!dest || !dest.idClient) {
      document.getElementById("ben-not-found").style.display = "";
      return;
    }
    if (dest.idClient === myClientId) {
      flash("Vous ne pouvez pas vous virer à vous-même.", "error");
      return;
    }

    const comptes = await api.get("/api/comptes/client/" + dest.idClient).catch(() => []);
    if (comptes && comptes.length) {
      const actif = comptes.find(c => c.statut === "ACTIF") || comptes[0];
      destCompteId = actif.idCompte;
    }

    const initiales = ((dest.prenom || "?")[0] + (dest.nom || "?")[0]).toUpperCase();
    document.getElementById("ben-avatar").textContent = initiales;
    document.getElementById("ben-nom").textContent    = (dest.prenom || "") + " " + (dest.nom || "");
    document.getElementById("ben-tel").textContent    = dest.telephone;
    document.getElementById("ben-found").style.display = "flex";
    document.getElementById("form-montant").style.display = "";
    document.getElementById("montant-vir").focus();

  } catch(e) {
    const msg = e.message || "";
    if (msg.includes("404") || msg.includes("Not Found")) {
      document.getElementById("ben-not-found").style.display = "";
    } else {
      flash("Erreur : " + msg, "error");
    }
  } finally {
    btn.disabled = false;
    btn.innerHTML = "Vérifier";
  }
}

function calculerFrais() {
  const montant = parseFloat(document.getElementById("montant-vir").value);
  if (!montant || montant < 500) { document.getElementById("apercu-frais").style.display = "none"; return; }
  const frais = montant * tauxVirement / 100;
  const total = montant + frais;
  document.getElementById("ap-montant").textContent = montant.toLocaleString("fr-FR") + " FCFA";
  document.getElementById("ap-taux").textContent    = tauxVirement.toFixed(2);
  document.getElementById("ap-frais").textContent   = frais.toLocaleString("fr-FR", {maximumFractionDigits:0}) + " FCFA";
  document.getElementById("ap-total").textContent   = total.toLocaleString("fr-FR", {maximumFractionDigits:0}) + " FCFA";
  document.getElementById("apercu-frais").style.display = "block";
}

async function confirmerVirement() {
  const montant = parseFloat(document.getElementById("montant-vir").value);
  const motif   = document.getElementById("motif-vir").value.trim() || "Virement ZEN-PAY";

  if (!montant || montant < 500)  { flash("Montant minimum : 500 FCFA", "error"); return; }
  if (!sourceCompteId)            { flash("Compte source introuvable.", "error"); return; }
  if (!destCompteId)              { flash("Vérifiez d'abord le destinataire.", "error"); return; }

  const btn = document.getElementById("btn-virer");
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Envoi en cours…';

  try {
    const tx = await api.post("/api/transactions/virement", {
      idCompte: sourceCompteId,
      idCompteDestination: destCompteId,
      montant,
      description: motif
    });

    document.getElementById("form-card").style.display   = "none";
    const destNom = document.getElementById("ben-nom").textContent;
    document.getElementById("succes-detail").textContent =
      montant.toLocaleString("fr-FR") + " FCFA envoyés à " + destNom + " (Réf. " + (tx.reference || "—") + ")";
    document.getElementById("succes-card").style.display = "block";
  } catch(e) {
    flash("Erreur : " + e.message, "error");
    btn.disabled = false; btn.innerHTML = "⚡ Envoyer le virement";
  }
}

function reinitialiser() {
  document.getElementById("form-card").style.display        = "";
  document.getElementById("succes-card").style.display      = "none";
  document.getElementById("tel-dest").value                 = "";
  document.getElementById("montant-vir").value              = "";
  document.getElementById("motif-vir").value                = "";
  document.getElementById("ben-found").style.display        = "none";
  document.getElementById("ben-not-found").style.display    = "none";
  document.getElementById("form-montant").style.display     = "none";
  document.getElementById("apercu-frais").style.display     = "none";
  document.getElementById("btn-virer").innerHTML            = "⚡ Envoyer le virement";
  document.getElementById("btn-virer").disabled             = false;
  destCompteId = null;
}

init();
</script>
</body>
</html>
