<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mon Abonnement — ' . APP_NAME;
$sidebar_active = 'abonnement';
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
<?php $topbar_title = 'Mon Abonnement'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Mon <span>Abonnement</span></h1><p style="color:var(--muted);font-size:.85rem;">Gérez votre offre et demandez un changement de plan</p></div>
  </div>

  <!-- Plan actuel -->
  <div class="card card-pad" id="plan-actuel" style="margin-bottom:20px;border-left:4px solid var(--primary);">
    <div style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">Votre plan actuel</div>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
      <div>
        <div style="font-size:1.3rem;font-weight:800;" id="plan-nom">—</div>
        <div style="font-size:.85rem;color:var(--muted);margin-top:4px;" id="plan-details">Chargement...</div>
      </div>
      <span class="badge badge-success" id="plan-badge">Actif</span>
    </div>
  </div>

  <div class="tab-group">
    <div class="tab-item active" onclick="showTab('offres',this)">Offres disponibles</div>
    <div class="tab-item" onclick="showTab('demande',this)">Demander un changement</div>
    <div class="tab-item" onclick="showTab('historique',this)">Mes demandes</div>
  </div>

  <!-- Offres disponibles -->
  <div id="tab-offres">
    <div id="offres-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-top:8px;">
      <div style="text-align:center;padding:40px;"><span class="spinner"></span></div>
    </div>
  </div>

  <!-- Demander un changement -->
  <div id="tab-demande" style="display:none;" class="card card-pad">
    <h3 style="font-weight:700;margin-bottom:16px;">Demander un changement d'offre</h3>
    <div class="form-group">
      <label class="form-label">Offre souhaitée</label>
      <select class="form-control" id="sel-offre">
        <option value="">— Sélectionnez une offre —</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Message (optionnel)</label>
      <textarea class="form-control" id="msg-demande" rows="3" placeholder="Expliquez pourquoi vous souhaitez changer d'offre..."></textarea>
    </div>
    <button class="btn btn-primary" onclick="soumettreDemande()">Envoyer la demande</button>
    <div style="font-size:.8rem;color:var(--muted);margin-top:10px;">Un gestionnaire examinera votre demande et vous notifiera de sa décision.</div>
  </div>

  <!-- Historique demandes -->
  <div id="tab-historique" style="display:none;" class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Mes demandes de changement</div></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Offre demandée</th><th>Message</th><th>Statut</th><th>Date demande</th><th>Traité le</th></tr></thead>
        <tbody id="histo-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
let myClientId = null;
let offreCourante = null;
let allOffres = [];

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t=>t.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("tab-offres").style.display     = name==="offres"     ? "" : "none";
  document.getElementById("tab-demande").style.display    = name==="demande"    ? "" : "none";
  document.getElementById("tab-historique").style.display = name==="historique" ? "" : "none";
  if (name==="historique") chargerHistorique();
}

async function init() {
  try {
    const me = await api.get("/api/auth/me");
    document.getElementById("user-avatar").textContent = (me.prenom||"C")[0].toUpperCase();
    myClientId = me.idClient;
    offreCourante = me.offreAbonnement;

    if (offreCourante) {
      document.getElementById("plan-nom").textContent = offreCourante.nomOffre || "—";
      const details = [];
      if (offreCourante.prixMensuel !== undefined) details.push(Number(offreCourante.prixMensuel).toLocaleString("fr-FR") + " FCFA/mois");
      if (offreCourante.pourcentageFraisMomo !== undefined) details.push("MoMo: " + offreCourante.pourcentageFraisMomo + "%");
      if (offreCourante.optionRibDispo) details.push("RIB inclus ✓");
      document.getElementById("plan-details").textContent = details.join("  ·  ") || "Plan standard";
    } else {
      document.getElementById("plan-nom").textContent = "STANDARD";
      document.getElementById("plan-details").textContent = "Aucune offre spéciale";
    }

    await chargerOffres();
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function chargerOffres() {
  try {
    allOffres = await api.get("/api/abonnements");
    const selO = document.getElementById("sel-offre");
    selO.innerHTML = '<option value="">— Sélectionnez une offre —</option>' +
      allOffres.map(o => `<option value="${o.idOffre}">${o.nomOffre} — ${Number(o.prixMensuel||0).toLocaleString("fr-FR")} FCFA/mois</option>`).join("");

    document.getElementById("offres-grid").innerHTML = allOffres.map(o => {
      const isCurrent = offreCourante && offreCourante.idOffre === o.idOffre;
      return `<div class="card card-pad" style="border:2px solid ${isCurrent?"var(--primary)":"var(--border)"};position:relative;">
        ${isCurrent?`<span class="badge badge-success" style="position:absolute;top:12px;right:12px;font-size:.72rem;">Plan actuel</span>`:""}
        <div style="font-weight:800;font-size:1.05rem;margin-bottom:6px;">${o.nomOffre}</div>
        <div style="font-size:1.4rem;font-weight:700;color:var(--primary);margin:10px 0;">${Number(o.prixMensuel||0).toLocaleString("fr-FR")} FCFA<span style="font-size:.75rem;font-weight:400;color:var(--muted);">/mois</span></div>
        <div style="display:flex;flex-direction:column;gap:4px;font-size:.82rem;color:var(--muted);">
          <div>💸 Frais MoMo : ${o.pourcentageFraisMomo}%</div>
          <div>🔄 Frais virement : ${o.fraisVirementInterne}%</div>
          <div>${o.optionRibDispo?"✅ RIB inclus":"❌ RIB non inclus"}</div>
        </div>
        ${!isCurrent?`<button class="btn btn-outline btn-sm w-full" style="margin-top:14px;" onclick="demanderOffre(${o.idOffre})">Demander cette offre</button>`:""}
      </div>`;
    }).join("");
  } catch(e) {
    document.getElementById("offres-grid").innerHTML = `<div class="card card-pad" style="color:var(--danger);">${e.message}</div>`;
  }
}

function demanderOffre(idOffre) {
  document.getElementById("sel-offre").value = idOffre;
  showTab("demande", document.querySelectorAll(".tab-item")[1]);
}

async function soumettreDemande() {
  const idOffre = document.getElementById("sel-offre").value;
  const msg = document.getElementById("msg-demande").value;
  if (!idOffre) { flash("Sélectionnez une offre.", "error"); return; }
  if (!myClientId) { flash("Identifiant client manquant.", "error"); return; }

  try {
    await api.postQuery("/api/demandes-abonnement", { idClient: myClientId, idOffre, messageClient: msg });
    flash("Demande envoyée ! Un gestionnaire la traitera prochainement.", "success");
    document.getElementById("sel-offre").value = "";
    document.getElementById("msg-demande").value = "";
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function chargerHistorique() {
  const tbody = document.getElementById("histo-tbody");
  if (!myClientId) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Identifiant client indisponible.</td></tr>`;
    return;
  }
  try {
    const list = await api.get("/api/demandes-abonnement/client/" + myClientId);
    const badge = {EN_ATTENTE:"badge-kyc-pending",APPROUVE:"badge-success",REJETE:"badge-failed"};
    if (!list.length) { tbody.innerHTML=`<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucune demande</td></tr>`; return; }
    tbody.innerHTML = list.map(d => {
      const offre = d.offreDemandee ? d.offreDemandee.nomOffre : "—";
      const dtC = d.dateCreation ? new Date(d.dateCreation).toLocaleDateString("fr-FR") : "—";
      const dtT = d.dateTraitement ? new Date(d.dateTraitement).toLocaleDateString("fr-FR") : "—";
      return `<tr>
        <td style="font-weight:700;">${offre}</td>
        <td style="font-size:.82rem;font-style:italic;color:var(--muted);">${d.messageClient||"—"}</td>
        <td><span class="badge ${badge[d.statut]||"badge-pending"}">${d.statut||"—"}</span></td>
        <td style="font-size:.82rem;">${dtC}</td>
        <td style="font-size:.82rem;">${dtT}</td>
      </tr>`;
    }).join("");
  } catch(e) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucune demande</td></tr>`;
  }
}

init();
</script>
</body>
</html>
