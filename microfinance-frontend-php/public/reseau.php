<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Gestion Réseau — ' . APP_NAME;
$sidebar_active = 'reseau';
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
<?php $topbar_title = 'Réseau & Accès IP'; include __DIR__ . '/../includes/topbar-admin.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div><h1>Gestion <span>Réseau</span></h1><p style="color:var(--muted);font-size:.85rem;">Whitelist IP et demandes d'accès extérieur des gestionnaires</p></div>
    <div class="dash-header-right">
      <span class="badge badge-kyc-pending" id="demandes-badge">— demandes</span>
    </div>
  </div>

  <div class="tab-group">
    <div class="tab-item active" onclick="showTab('demandes',this)">Demandes d'accès</div>
    <div class="tab-item" onclick="showTab('whitelist',this)">Whitelist IP</div>
    <div class="tab-item" onclick="showTab('ajout',this)">Ajouter IP</div>
  </div>

  <!-- Demandes en attente -->
  <div id="tab-demandes">
    <div id="demandes-list"><div style="text-align:center;padding:40px;"><span class="spinner"></span></div></div>
  </div>

  <!-- Whitelist -->
  <div id="tab-whitelist" style="display:none;" class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">IPs autorisées (<span id="ip-count">0</span>)</div></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Adresse IP</th><th>Machine</th><th>Statut</th><th>Ajoutée le</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody id="ip-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Ajout IP -->
  <div id="tab-ajout" style="display:none;" class="card card-pad">
    <h3 style="font-weight:700;margin-bottom:16px;">Ajouter une adresse IP à la whitelist</h3>

    <!-- IP détectée du navigateur actuel -->
    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--g50);border:1.5px solid var(--g200);border-radius:var(--r);margin-bottom:16px;">
      <svg width="16" height="16" fill="none" stroke="var(--g600)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span style="font-size:.83rem;color:var(--gray-700);">Votre IP actuelle détectée par le serveur :</span>
      <code id="my-ip-display" style="font-size:.88rem;font-weight:700;color:var(--g700);background:var(--g100);padding:2px 8px;border-radius:6px;">Détection...</code>
      <button onclick="copierMonIp()" style="background:var(--g600);color:white;border:none;border-radius:6px;padding:4px 10px;font-size:.75rem;font-weight:600;cursor:pointer;">Copier</button>
      <span id="my-ip-status" style="font-size:.75rem;font-weight:700;"></span>
    </div>

    <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
      <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
        <label class="form-label">Adresse IP</label>
        <input class="form-control" type="text" id="new-ip" placeholder="192.168.1.100">
      </div>
      <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
        <label class="form-label">Nom de la machine / réseau</label>
        <input class="form-control" type="text" id="new-machine" placeholder="PC-BUREAU-01">
      </div>
      <button class="btn btn-primary" onclick="ajouterIp()">Ajouter</button>
    </div>
    <div class="result-card" style="margin-top:20px;">
      <div style="font-size:.82rem;font-weight:700;color:var(--g700);margin-bottom:8px;">ℹ️ Comment ça fonctionne ?</div>
      <p style="font-size:.82rem;color:var(--muted);margin:0;line-height:1.6;">
        Les gestionnaires, superviseurs et admins doivent se connecter depuis un réseau autorisé.
        Ajoutez l'IP ci-dessus (votre réseau actuel) pour permettre la connexion depuis ce réseau.
        Le Super Admin n'est soumis à aucune restriction IP.
      </p>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();

function showTab(name, el) {
  document.querySelectorAll(".tab-item").forEach(t=>t.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("tab-demandes").style.display  = name==="demandes" ? "" : "none";
  document.getElementById("tab-whitelist").style.display = name==="whitelist" ? "" : "none";
  document.getElementById("tab-ajout").style.display     = name==="ajout" ? "" : "none";
  if (name==="whitelist") chargerIps();
}

let monIpValue = "";

async function init() {
  const me = await api.get("/api/auth/me");
  document.getElementById("user-avatar").textContent = (me.prenom||"A")[0].toUpperCase();
  await Promise.all([chargerDemandes(), chargerMonIp()]);
}

async function chargerMonIp() {
  try {
    const res = await fetch((localStorage.getItem("mf_api_base") || "http://localhost:8080") + "/api/auth/my-ip");
    const data = await res.json();
    monIpValue = data.ip || data.ipRaw || "";
    const el = document.getElementById("my-ip-display");
    if (el) el.textContent = monIpValue || "—";
    const st = document.getElementById("my-ip-status");
    if (st) {
      if (data.whitelisted === "true") {
        st.textContent = "✅ Déjà autorisée";
        st.style.color = "var(--g600)";
      } else {
        st.textContent = "⚠ Non autorisée";
        st.style.color = "var(--warning)";
      }
    }
  } catch(e) {}
}

function copierMonIp() {
  if (!monIpValue) return;
  navigator.clipboard.writeText(monIpValue).then(() => flash("IP copiée !", "success")).catch(() => {
    document.getElementById("new-ip").value = monIpValue;
    flash("IP copiée dans le champ.", "success");
  });
  document.getElementById("new-ip").value = monIpValue;
}

async function chargerDemandes() {
  try {
    const list = await api.get("/api/admin/demandes-acces");
    document.getElementById("demandes-badge").textContent = list.length + " demande(s)";
    const el = document.getElementById("demandes-list");
    if (!list.length) {
      el.innerHTML = `<div class="card"><div class="empty-state"><div class="empty-icon">✅</div><h3>Aucune demande en attente</h3><p>Tous les accès extérieurs ont été traités.</p></div></div>`;
      return;
    }
    el.innerHTML = list.map(d => {
      const g = d.gestionnaire ? (d.gestionnaire.prenom+" "+d.gestionnaire.nom) : "Inconnu";
      const email = d.gestionnaire ? d.gestionnaire.email : "—";
      const role = d.gestionnaire ? (d.gestionnaire.role||"—").replace("ROLE_","") : "—";
      const dt = d.dateCreation ? new Date(d.dateCreation).toLocaleString("fr-FR") : "—";
      return `<div class="card card-pad" style="margin-bottom:12px;border-left:4px solid var(--warning);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div>
            <div style="font-weight:700;font-size:.95rem;">${g}</div>
            <div style="font-size:.82rem;color:var(--muted);">${email} · <span class="badge badge-pending" style="font-size:.72rem;">${role}</span></div>
            <div style="font-size:.8rem;margin-top:6px;">IP demandée : <strong style="font-family:monospace;">${d.adresseIp||"—"}</strong></div>
            <div style="font-size:.76rem;color:var(--muted);margin-top:2px;">${dt}</div>
          </div>
          <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
            <div style="display:flex;gap:6px;align-items:center;">
              <input type="number" id="h-${d.idDemande}" value="4" min="1" max="72" style="width:60px;" class="form-control">
              <span style="font-size:.8rem;color:var(--muted);">h de validité</span>
            </div>
            <button class="btn btn-success btn-sm" onclick="approuver(${d.idDemande})">✓ Autoriser</button>
            <button class="btn btn-danger btn-sm" onclick="rejeter(${d.idDemande})">✕ Refuser</button>
          </div>
        </div>
      </div>`;
    }).join("");
  } catch(e) {
    document.getElementById("demandes-list").innerHTML = `<div class="card card-pad" style="color:var(--danger);">${e.message}</div>`;
  }
}

async function chargerIps() {
  try {
    const list = await api.get("/api/admin/ips");
    document.getElementById("ip-count").textContent = list.length;
    const tbody = document.getElementById("ip-tbody");
    if (!list.length) { tbody.innerHTML=`<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">Aucune IP configurée</td></tr>`; return; }
    tbody.innerHTML = list.map(ip => {
      const dt = ip.dateAjout ? new Date(ip.dateAjout).toLocaleDateString("fr-FR") : "—";
      return `<tr>
        <td style="font-family:monospace;font-weight:700;">${ip.adresseIp||"—"}</td>
        <td>${ip.nomMachine||"—"}</td>
        <td><span class="badge ${ip.estActive?"badge-success":"badge-failed"}">${ip.estActive?"Active":"Désactivée"}</span></td>
        <td style="font-size:.82rem;">${dt}</td>
        <td style="text-align:right;">
          ${ip.estActive
            ? `<button class="btn btn-danger btn-sm" onclick="desactiverIp(${ip.idIp})">Désactiver</button>`
            : `<button class="btn btn-success btn-sm" onclick="activerIp(${ip.idIp})">Activer</button>`}
        </td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

async function approuver(id) {
  const h = document.getElementById("h-"+id)?.value || 4;
  if (!confirm(`Autoriser cet accès pour ${h}h ?`)) return;
  try {
    await api.put(`/api/admin/demandes-acces/${id}/approuver?heuresValidite=${h}&emailApprovateur=superadmin@microfinance.local`);
    flash("Accès autorisé pour " + h + "h.", "success");
    await chargerDemandes();
  } catch(e) { flash("Erreur : "+e.message, "error"); }
}

async function rejeter(id) {
  if (!confirm("Refuser cette demande ?")) return;
  try {
    await api.put("/api/admin/demandes-acces/"+id+"/rejeter");
    flash("Demande refusée.", "info");
    await chargerDemandes();
  } catch(e) { flash("Erreur : "+e.message, "error"); }
}

async function ajouterIp() {
  const ip = document.getElementById("new-ip").value.trim();
  const machine = document.getElementById("new-machine").value.trim();
  if (!ip || !machine) { flash("Remplissez l'IP et le nom de machine.", "error"); return; }
  try {
    await api.postQuery("/api/admin/ips", { adresseIp: ip, nomMachine: machine });
    flash("IP ajoutée à la whitelist.", "success");
    document.getElementById("new-ip").value = "";
    document.getElementById("new-machine").value = "";
  } catch(e) { flash("Erreur : "+e.message, "error"); }
}

async function desactiverIp(id) {
  if (!confirm("Désactiver cette IP ?")) return;
  try {
    await api.del("/api/admin/ips/"+id);
    flash("IP désactivée.", "info");
    await chargerIps();
  } catch(e) { flash("Erreur : "+e.message, "error"); }
}

async function activerIp(id) {
  if (!confirm("Réactiver cette IP ?")) return;
  try {
    await api.put("/api/admin/ips/"+id+"/activer");
    flash("IP réactivée.", "success");
    await chargerIps();
  } catch(e) { flash("Erreur : "+e.message, "error"); }
}

init();
</script>
</body>
</html>
