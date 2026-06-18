<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Gestion Clients — ' . APP_NAME;
$sidebar_active = 'clients';
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
<?php $topbar_title = 'Gestion des Clients'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Clients <span>ZEN-PAY</span></h1><p style="color:var(--muted);font-size:.85rem;">Gestion complète des comptes clients</p></div>
    <div class="dash-header-right">
      <button class="btn btn-primary btn-sm" onclick="showModalAjout()">+ Ajouter client</button>
    </div>
  </div>

  <!-- Barre de recherche -->
  <div class="card card-pad" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;align-items:center;">
      <input class="form-control" type="text" id="search-input" placeholder="Rechercher par nom, email, téléphone..."
             oninput="filtrerClients(this.value)" style="max-width:420px;">
      <select class="form-control" id="filter-statut" onchange="filtrerClients(document.getElementById('search-input').value)" style="max-width:160px;">
        <option value="">Tous les statuts</option>
        <option value="actif">Actifs</option>
        <option value="inactif">Suspendus</option>
      </select>
      <select class="form-control" id="filter-kyc" onchange="filtrerClients(document.getElementById('search-input').value)" style="max-width:160px;">
        <option value="">Tous KYC</option>
        <option value="PENDING">En attente</option>
        <option value="VALIDE">Validé</option>
        <option value="REJETE">Rejeté</option>
      </select>
    </div>
  </div>

  <!-- Tableau clients -->
  <div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Liste des clients (<span id="count">0</span>)</div></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Client</th><th>Email / Tél</th><th>KYC</th><th>Offre</th><th>Statut</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="clients-tbody"><tr><td colspan="6" style="text-align:center;padding:40px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Panneau détail client -->
  <div id="detail-panel" style="display:none;" class="card card-pad" style="margin-top:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
      <h3 id="detail-nom" style="font-size:1.05rem;font-weight:700;"></h3>
      <button onclick="fermerDetail()" class="btn btn-ghost btn-sm">✕ Fermer</button>
    </div>
    <div id="detail-content"></div>
    <div id="detail-comptes" style="margin-top:16px;"></div>
  </div>

</main>
</div>
</div>

<!-- Modal Ajout Client -->
<div id="modal-ajout" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;display:none;align-items:center;justify-content:center;">
  <div class="card card-pad" style="width:500px;max-width:95vw;max-height:90vh;overflow-y:auto;position:relative;">
    <h3 style="margin-bottom:16px;font-weight:700;">Ajouter un client</h3>
    <form id="form-ajout">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group"><label class="form-label">Prénom</label><input class="form-control" id="a-prenom" required></div>
        <div class="form-group"><label class="form-label">Nom</label><input class="form-control" id="a-nom" required></div>
        <div class="form-group"><label class="form-label">Email</label><input class="form-control" type="email" id="a-email" required></div>
        <div class="form-group"><label class="form-label">Téléphone</label><input class="form-control" id="a-tel" placeholder="+225XXXXXXXX" required></div>
        <div class="form-group"><label class="form-label">Adresse</label><input class="form-control" id="a-adresse" required></div>
        <div class="form-group"><label class="form-label">N° CNI</label><input class="form-control" id="a-cni" required></div>
        <div class="form-group"><label class="form-label">Opérateur MoMo</label>
          <select class="form-control" id="a-operateur">
            <option value="WAVE">WAVE</option><option value="ORANGE">ORANGE</option>
            <option value="MTN">MTN</option><option value="MOOV">MOOV</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Mot de passe</label><input class="form-control" type="password" id="a-mdp" value="Client@2024" required></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:16px;">
        <button type="submit" class="btn btn-primary" id="btn-ajout">Créer le compte</button>
        <button type="button" onclick="closeModal()" class="btn btn-ghost">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();
let allClients = [];
const kycBadge = {PENDING:'badge-kyc-pending',VALIDE:'badge-success',REJETE:'badge-failed'};
const kycLabel = {PENDING:'En attente',VALIDE:'Validé',REJETE:'Rejeté'};

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  await chargerClients();
}

async function chargerClients() {
  try {
    allClients = await api.get("/api/clients");
    afficherClients(allClients);
  } catch(e) {
    document.getElementById("clients-tbody").innerHTML =
      `<tr><td colspan="6" style="text-align:center;color:var(--danger);">${e.message}</td></tr>`;
  }
}

function filtrerClients(q) {
  const statut = document.getElementById("filter-statut").value;
  const kyc    = document.getElementById("filter-kyc").value;
  const lq = (q||"").toLowerCase();
  let filtered = allClients.filter(c => {
    const match = !lq || (c.nom||"").toLowerCase().includes(lq) ||
                  (c.prenom||"").toLowerCase().includes(lq) ||
                  (c.email||"").toLowerCase().includes(lq) ||
                  (c.telephone||"").includes(lq);
    const sMatch = !statut || (statut==="actif" ? c.actif : !c.actif);
    const kMatch = !kyc || c.statutKyc === kyc;
    return match && sMatch && kMatch;
  });
  afficherClients(filtered);
}

function afficherClients(list) {
  document.getElementById("count").textContent = list.length;
  const tbody = document.getElementById("clients-tbody");
  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">Aucun client trouvé</td></tr>`;
    return;
  }
  tbody.innerHTML = list.map(c => {
    const kyc = c.statutKyc || "PENDING";
    const offre = c.offreAbonnement ? c.offreAbonnement.nomOffre : "STANDARD";
    return `<tr style="cursor:pointer;" onclick="voirClient(${c.idClient})">
      <td>
        <div class="tx-name">${c.prenom||""} ${c.nom||""}</div>
        <div class="tx-sub">#${c.idClient}</div>
      </td>
      <td>
        <div style="font-size:.83rem;">${c.email||"—"}</div>
        <div style="font-size:.76rem;color:var(--muted);">${c.telephone||""}</div>
      </td>
      <td><span class="badge ${kycBadge[kyc]||"badge-pending"}">${kycLabel[kyc]||kyc}</span></td>
      <td style="font-size:.83rem;font-weight:600;">${offre}</td>
      <td><span class="badge ${c.actif?'badge-success':'badge-failed'}">${c.actif?'Actif':'Suspendu'}</span></td>
      <td style="text-align:right;">
        <button class="btn btn-ghost btn-sm" onclick="event.stopPropagation();voirClient(${c.idClient})">Détail</button>
        ${c.actif
          ? `<button class="btn btn-warning btn-sm" onclick="event.stopPropagation();suspendre(${c.idClient})" style="margin-left:4px;">Suspendre</button>`
          : `<button class="btn btn-success btn-sm" onclick="event.stopPropagation();activer(${c.idClient})" style="margin-left:4px;">Activer</button>`
        }
      </td>
    </tr>`;
  }).join("");
}

async function voirClient(id) {
  const panel = document.getElementById("detail-panel");
  panel.style.display = "";
  document.getElementById("detail-content").innerHTML = '<span class="spinner"></span>';
  document.getElementById("detail-comptes").innerHTML = '';
  try {
    const [client, comptes] = await Promise.all([
      api.get("/api/clients/" + id),
      api.get("/api/comptes/client/" + id)
    ]);
    document.getElementById("detail-nom").textContent = client.prenom + " " + client.nom;
    const kyc = client.statutKyc || "PENDING";
    const offre = client.offreAbonnement ? client.offreAbonnement.nomOffre : "STANDARD";
    document.getElementById("detail-content").innerHTML = `
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;font-size:.85rem;">
        <div><div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Email</div><strong>${client.email||"—"}</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Téléphone</div><strong>${client.telephone||"—"}</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Adresse</div><strong>${client.adresse||"—"}</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">KYC</div><span class="badge ${kycBadge[kyc]||"badge-pending"}">${kycLabel[kyc]||kyc}</span></div>
        <div><div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Offre</div><strong>${offre}</strong></div>
        <div><div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Statut</div><span class="badge ${client.actif?'badge-success':'badge-failed'}">${client.actif?'Actif':'Suspendu'}</span></div>
      </div>
      <div style="display:flex;gap:8px;margin-top:16px;">
        ${client.actif
          ? `<button class="btn btn-warning btn-sm" onclick="suspendre(${client.idClient})">Suspendre</button>`
          : `<button class="btn btn-success btn-sm" onclick="activer(${client.idClient})">Activer</button>`}
      </div>`;
    if (comptes.length) {
      document.getElementById("detail-comptes").innerHTML = `
        <div style="font-weight:700;font-size:.85rem;margin-bottom:8px;color:var(--gray-700);">Comptes (${comptes.length})</div>
        ${comptes.map(cp => `
          <div style="display:flex;justify-content:space-between;padding:8px 12px;background:var(--gray-50);border-radius:8px;margin-bottom:6px;font-size:.83rem;">
            <span style="font-family:monospace;">${cp.numeroCompte}</span>
            <span style="font-weight:700;">${Number(cp.solde||0).toLocaleString("fr-FR")} FCFA</span>
            <span class="badge ${cp.statut==="ACTIF"?"badge-success":cp.statut==="BLOQUE"?"badge-failed":"badge-pending"}">${cp.statut}</span>
          </div>`).join("")}`;
    }
    panel.scrollIntoView({behavior:"smooth"});
  } catch(e) { document.getElementById("detail-content").innerHTML = `<div style="color:var(--danger);">${e.message}</div>`; }
}

function fermerDetail() { document.getElementById("detail-panel").style.display = "none"; }

async function suspendre(id) {
  if (!confirm("Suspendre ce client ?")) return;
  try {
    await api.put("/api/clients/" + id + "/suspendre");
    flash("Client suspendu.", "info");
    await chargerClients();
    fermerDetail();
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function activer(id) {
  try {
    await api.put("/api/clients/" + id + "/activer");
    flash("Client réactivé.", "success");
    await chargerClients();
    fermerDetail();
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

function showModalAjout() {
  document.getElementById("modal-ajout").style.display = "flex";
}
function closeModal() { document.getElementById("modal-ajout").style.display = "none"; }

document.getElementById("form-ajout").addEventListener("submit", async e => {
  e.preventDefault();
  const btn = document.getElementById("btn-ajout");
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  try {
    await api.post("/api/clients", {
      prenom: document.getElementById("a-prenom").value,
      nom: document.getElementById("a-nom").value,
      email: document.getElementById("a-email").value,
      telephone: document.getElementById("a-tel").value,
      adresse: document.getElementById("a-adresse").value,
      numeroCni: document.getElementById("a-cni").value,
      operateurMomo: document.getElementById("a-operateur").value,
      motDePasse: document.getElementById("a-mdp").value
    });
    flash("Client créé avec succès.", "success");
    closeModal();
    e.target.reset();
    await chargerClients();
  } catch(err) { flash("Erreur : " + err.message, "error"); }
  finally { btn.disabled = false; btn.innerHTML = "Créer le compte"; }
});

init();
</script>
</body>
</html>
