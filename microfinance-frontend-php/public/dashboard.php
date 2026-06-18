<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Tableau de bord — ' . APP_NAME;
$sidebar_active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    /* ─── Bannière de rôle ───────────────────────────────── */
    .role-banner {
      border-radius: var(--r-xl); padding: 22px 28px; margin-bottom: 24px;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
    }
    .role-banner-title { font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 2px; }
    .role-banner-sub   { font-size: .88rem; color: rgba(255,255,255,.75); }
    .role-badge-large  {
      padding: 6px 16px; border-radius: 30px; font-size: .82rem; font-weight: 800;
      letter-spacing: .04em; background: rgba(255,255,255,.18); color: white;
      border: 1px solid rgba(255,255,255,.3); text-transform: uppercase;
    }
    /* Couleurs par rôle */
    .banner-superadmin { background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%); }
    .banner-gestionnaire { background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); }
    .banner-supervisor { background: linear-gradient(135deg, #78350f 0%, #d97706 100%); }
    .banner-admin-systeme { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); }
    .banner-admin-bd { background: linear-gradient(135deg, #164e63 0%, #0891b2 100%); }

    /* ─── Grille KPI ─────────────────────────────────────── */
    .kpi-grid { display: grid; gap: 16px; margin-bottom: 24px; }
    .kpi-grid-4 { grid-template-columns: repeat(4, 1fr); }
    .kpi-grid-3 { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 1100px) { .kpi-grid-4 { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 720px)  { .kpi-grid-4, .kpi-grid-3 { grid-template-columns: 1fr; } }

    .kpi-card {
      background: white; border: 1px solid var(--border); border-radius: var(--r-xl);
      padding: 20px 24px; box-shadow: var(--s1);
      display: flex; align-items: flex-start; gap: 16px;
    }
    .kpi-icon {
      width: 44px; height: 44px; border-radius: var(--r); flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
    .kpi-value { font-size: 1.7rem; font-weight: 800; color: var(--gray-900); line-height: 1; }
    .kpi-label { font-size: .8rem; color: var(--muted); margin-top: 4px; font-weight: 500; }
    .kpi-delta { font-size: .75rem; font-weight: 600; margin-top: 6px; }
    .kpi-delta.up { color: #16a34a; }
    .kpi-delta.neutral { color: var(--muted); }

    /* ─── Section blocks ──────────────────────────────────── */
    .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    @media (max-width: 900px) { .section-grid { grid-template-columns: 1fr; } }
    .section-full { margin-bottom: 20px; }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Tableau de bord'; include __DIR__ . '/../includes/topbar-admin.php'; ?>

<main class="page-content">

  <!-- Bannière maintenance (visible si système en maintenance) -->
  <div id="banner-maintenance" style="display:none;background:#dc2626;color:#fff;border-radius:10px;padding:14px 20px;margin-bottom:14px;display:none;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:10px;">
      <span style="font-size:1.2rem;">🔴</span>
      <div>
        <div style="font-weight:800;font-size:.95rem;">Site en MODE MAINTENANCE</div>
        <div style="font-size:.78rem;opacity:.85;">Toutes les transactions sont bloquées. Les administrateurs restent connectés.</div>
      </div>
    </div>
    <button id="btn-desactiver-maint-dash" onclick="desactiverMaintenanceDash()"
      style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.5);color:#fff;padding:6px 16px;border-radius:6px;font-weight:700;cursor:pointer;font-size:.83rem;">
      Rétablir le service
    </button>
  </div>

  <!-- Bannière rôle (remplie en JS) -->
  <div class="role-banner banner-superadmin" id="role-banner">
    <div>
      <div class="role-banner-title" id="banner-title">Tableau de bord</div>
      <div class="role-banner-sub" id="banner-sub">Chargement de vos données…</div>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
      <span class="badge badge-success" id="sys-status">● Opérationnel</span>
      <span class="role-badge-large" id="banner-role-badge">—</span>
      <button class="btn btn-sm" onclick="refresh()" style="background:rgba(255,255,255,.18);color:white;border:1px solid rgba(255,255,255,.3);height:34px;">↻ Actualiser</button>
    </div>
  </div>

  <!-- KPI grid (injectée dynamiquement) -->
  <div id="kpi-section"></div>

  <!-- Graphes dynamiques -->
  <div id="chart-section" style="display:none;margin-bottom:24px;">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
      <div class="card card-pad">
        <div style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">Transactions par mois (12 derniers mois)</div>
        <div style="height:180px;"><canvas id="chartTxMois"></canvas></div>
      </div>
      <div class="card card-pad">
        <div style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">Répartition par type</div>
        <div style="height:180px;"><canvas id="chartTxType"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Sections de contenu (injectées dynamiquement) -->
  <div id="content-section"></div>

</main>
</div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

const txBadge = {VALIDEE:'badge-success',EN_COURS:'badge-kyc-pending',REJETEE:'badge-failed',ANNULEE:'badge-failed'};

const ROLE_CONFIG = {
  SUPER_ADMIN: {
    bannerClass: "banner-superadmin",
    title: (me) => `Bonjour, ${me.prenom || "Super Admin"} 👋`,
    sub: "Vue complète de la plateforme ZEN-PAY",
    badge: "Super Admin",
    kpis: ["clients","comptes","transactions","sessions","kyc","tickets"],
    sections: ["sessions","transactions","audit"]
  },
  GESTIONNAIRE: {
    bannerClass: "banner-gestionnaire",
    title: (me) => `Bonjour, ${me.prenom || "Gestionnaire"} 👋`,
    sub: "Gérez vos clients, comptes et opérations quotidiennes",
    badge: "Gestionnaire",
    kpis: ["clients","comptes","kyc","tickets"],
    sections: ["kyc","transactions","clients-recents"]
  },
  SUPERVISOR: {
    bannerClass: "banner-supervisor",
    title: (me) => `Bonjour, ${me.prenom || "Superviseur"} 👋`,
    sub: "Suivi des opérations et conformité en temps réel",
    badge: "Superviseur",
    kpis: ["transactions","comptes","kyc","tickets"],
    sections: ["transactions","kyc","audit"]
  },
  ADMIN_SYSTEME: {
    bannerClass: "banner-admin-systeme",
    title: (me) => `Bonjour, ${me.prenom || "Admin"} 👋`,
    sub: "Supervision technique et sécurité de l'infrastructure",
    badge: "Admin Système",
    kpis: ["sessions","comptes","transactions","audit-count"],
    sections: ["sessions","audit"]
  },
  ADMIN_BD: {
    bannerClass: "banner-admin-bd",
    title: (me) => `Bonjour, ${me.prenom || "Admin"} 👋`,
    sub: "Accès aux données et supervision des comptes",
    badge: "Admin BD",
    kpis: ["clients","comptes","transactions"],
    sections: ["transactions"]
  }
};

let cachedData = {};
let currentRole = null;

async function refresh() {
  const role = currentRole || window.__userRole;
  if (!role) return;
  await loadSections(role);
}

async function init() {
  // Appel direct — pas de dépendance à l'event sidebar pour éviter toute race condition
  let me, role;
  try {
    me = await api.get("/api/auth/me");
    role = (me.role || "").replace("ROLE_", "");
  } catch(e) {
    window.location.href = "login.php";
    return;
  }

  if (!role || role === "CLIENT") { window.location.href = "client-dashboard.php"; return; }

  currentRole = role;
  const cfg = ROLE_CONFIG[role] || ROLE_CONFIG.SUPER_ADMIN;

  // Bannière
  const banner = document.getElementById("role-banner");
  banner.className = "role-banner " + cfg.bannerClass;
  document.getElementById("banner-title").textContent = cfg.title(me);
  document.getElementById("banner-sub").textContent   = cfg.sub;
  document.getElementById("banner-role-badge").textContent = cfg.badge;

  // KPIs
  await buildKpis(cfg.kpis);

  // Sections
  await loadSections(role);

  // Refresh sessions auto (SUPER_ADMIN / ADMIN_SYSTEME seulement)
  if (role === "SUPER_ADMIN" || role === "ADMIN_SYSTEME") {
    setInterval(() => loadSessions(), 15000);
  }

  // Vérifier le statut du système
  try {
    const sys = await api.get("/api/system/status");
    const enMaint = sys.status === "MAINTENANCE_CRITIQUE";
    const sysEl = document.getElementById("sys-status");
    if (enMaint) {
      sysEl.textContent = "● Maintenance";
      sysEl.className = "badge badge-failed";
      const bm = document.getElementById("banner-maintenance");
      bm.style.display = "flex";
      // Le bouton "Rétablir" n'est visible que pour le Super Admin
      if (role !== "SUPER_ADMIN") {
        document.getElementById("btn-desactiver-maint-dash").style.display = "none";
      }
    }
  } catch(e) {}
}

async function desactiverMaintenanceDash() {
  if (!confirm("Désactiver la maintenance et reprendre le service normal ?")) return;
  const btn = document.getElementById("btn-desactiver-maint-dash");
  btn.disabled = true; btn.textContent = "…";
  try {
    await api.postQuery("/api/system/kill-switch", { activer: false });
    document.getElementById("banner-maintenance").style.display = "none";
    const sysEl = document.getElementById("sys-status");
    sysEl.textContent = "● Opérationnel";
    sysEl.className = "badge badge-success";
    flash("Service rétabli avec succès.", "success");
  } catch(e) {
    flash("Erreur : " + e.message, "error");
    btn.disabled = false; btn.textContent = "Rétablir le service";
  }
}

/* ─── KPI builder ───────────────────────────────────────── */
const kpiDefs = {
  clients:      { icon: "👥", bg: "#dcfce7", label: "Clients actifs",      id: "kpi-clients" },
  comptes:      { icon: "💳", bg: "#dbeafe", label: "Comptes ouverts",     id: "kpi-comptes" },
  transactions: { icon: "🔄", bg: "#fef3c7", label: "Transactions totales",id: "kpi-tx" },
  sessions:     { icon: "🟢", bg: "#f0fdf4", label: "Sessions actives",    id: "kpi-sessions" },
  kyc:          { icon: "✅", bg: "#fef9c3", label: "KYC en attente",      id: "kpi-kyc" },
  tickets:      { icon: "🎫", bg: "#fce7f3", label: "Tickets ouverts",     id: "kpi-tickets" },
  "audit-count":{ icon: "📋", bg: "#ede9fe", label: "Logs d'audit (24h)", id: "kpi-audit" }
};

async function buildKpis(keys) {
  const section = document.getElementById("kpi-section");
  const cols = keys.length >= 4 ? "kpi-grid-4" : "kpi-grid-3";
  section.innerHTML = `<div class="kpi-grid ${cols}" id="kpi-grid">${
    keys.map(k => {
      const d = kpiDefs[k] || { icon:"📊", bg:"#f3f4f6", label: k, id:"kpi-"+k };
      return `<div class="kpi-card">
        <div class="kpi-icon" style="background:${d.bg};">${d.icon}</div>
        <div>
          <div class="kpi-value" id="${d.id}"><span class="spinner" style="width:18px;height:18px;border-width:2px;"></span></div>
          <div class="kpi-label">${d.label}</div>
        </div>
      </div>`;
    }).join("")
  }</div>`;

  const safe = async (fn, fb) => { try { return await fn(); } catch { return fb; } };
  const detecterTypeTx = t => t.typeTransaction ||
    ("source" in t ? "VERSEMENT" : "canal" in t ? "RETRAIT" : "VIREMENT");

  const [clients, comptes, txs, sessions, kyc, tickets, auditLogs] = await Promise.all([
    keys.includes("clients")       ? safe(() => api.get("/api/clients"), []) : Promise.resolve([]),
    keys.includes("comptes")       ? safe(() => api.get("/api/comptes"), []) : Promise.resolve([]),
    keys.includes("transactions")  ? safe(() => api.get("/api/transactions"), []) : Promise.resolve([]),
    keys.includes("sessions")      ? safe(() => api.get("/api/admin/sessions"), []) : Promise.resolve([]),
    keys.includes("kyc")           ? safe(() => api.get("/api/kyc/en-attente"), []) : Promise.resolve([]),
    keys.includes("tickets")       ? safe(() => api.get("/api/tickets/ouverts"), []) : Promise.resolve([]),
    keys.includes("audit-count")   ? safe(() => api.get("/api/admin/audit-logs"), []) : Promise.resolve([])
  ]);

  cachedData = { clients, comptes, txs, sessions, kyc, tickets, auditLogs };

  const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  set("kpi-clients",  (clients||[]).filter(c=>c.actif).length);
  set("kpi-comptes",  (comptes||[]).filter(c=>c.statut==="ACTIF").length);
  set("kpi-tx",       (txs||[]).length);
  set("kpi-sessions", (sessions||[]).length);
  set("kpi-kyc",      (kyc||[]).length);
  set("kpi-tickets",  (tickets||[]).length);
  set("kpi-audit",    (auditLogs||[]).length);

  const txsNorm = (txs||[]).map(t => ({...t, typeTransaction: detecterTypeTx(t)}));
  if (txsNorm.length > 0) buildCharts(txsNorm);
}

/* ─── Graphes dynamiques ────────────────────────────────── */
let chartMois = null, chartType = null;

function buildCharts(txs) {
  document.getElementById("chart-section").style.display = "";

  // 12 derniers mois
  const now = new Date();
  const moisLabels = [];
  const moisMap    = [];
  for (let i = 11; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
    moisLabels.push(d.toLocaleDateString("fr-FR", {month:"short", year:"2-digit"}));
    moisMap.push({ y: d.getFullYear(), m: d.getMonth() });
  }

  const parMois = new Array(12).fill(0);
  const typeCounts = { VERSEMENT: 0, RETRAIT: 0, VIREMENT: 0 };

  (txs || []).forEach(t => {
    if (t.dateHeure) {
      const d = new Date(t.dateHeure);
      const idx = moisMap.findIndex(x => x.y === d.getFullYear() && x.m === d.getMonth());
      if (idx >= 0) parMois[idx]++;
    }
    const type = t.typeTransaction || "AUTRE";
    if (typeCounts[type] !== undefined) typeCounts[type]++;
  });

  // Bar chart transactions par mois
  const ctx1 = document.getElementById("chartTxMois").getContext("2d");
  if (chartMois) chartMois.destroy();
  const maxVal = Math.max(...parMois, 1);
  chartMois = new Chart(ctx1, {
    type: "bar",
    data: {
      labels: moisLabels,
      datasets: [{
        data: parMois,
        backgroundColor: parMois.map(v => v === maxVal && v > 0 ? "#7c3aed" : "#ddd6fe"),
        borderRadius: 6, borderSkipped: false
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: {display:false}, tooltip: { callbacks: { label: c => c.raw + " tx" } } },
      scales: {
        x: { grid:{display:false}, ticks:{font:{size:10}} },
        y: { grid:{color:"#f3f4f6"}, ticks:{font:{size:10}, stepSize:1} }
      }
    }
  });

  // Doughnut types
  const ctx2 = document.getElementById("chartTxType").getContext("2d");
  if (chartType) chartType.destroy();
  const typeData   = [typeCounts.VERSEMENT, typeCounts.RETRAIT, typeCounts.VIREMENT];
  const typeColors = ["#16a34a","#dc2626","#7c3aed"];
  chartType = new Chart(ctx2, {
    type: "doughnut",
    data: {
      labels: ["Versements","Retraits","Virements"],
      datasets: [{ data: typeData, backgroundColor: typeColors, borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position:"bottom", labels:{font:{size:10},padding:8,boxWidth:10} },
        tooltip: { callbacks: { label: c => c.label + " : " + c.raw } }
      }
    }
  });
}

/* ─── Sections de contenu par rôle ─────────────────────── */
async function loadSections(role) {
  const cfg = ROLE_CONFIG[role] || ROLE_CONFIG.SUPER_ADMIN;
  const container = document.getElementById("content-section");
  container.innerHTML = "";

  for (const s of cfg.sections) {
    const el = document.createElement("div");
    el.className = "section-full";
    container.appendChild(el);
    await renderSection(s, el);
  }
}

async function renderSection(name, container) {
  switch (name) {
    case "sessions":      await renderSessions(container); break;
    case "transactions":  await renderTransactions(container); break;
    case "audit":         await renderAudit(container); break;
    case "kyc":           await renderKyc(container); break;
    case "clients-recents": await renderClientsRecents(container); break;
  }
}

/* ─── Sessions actives ──────────────────────────────────── */
async function loadSessions() {
  const el = document.getElementById("sessions-tbody");
  const sub = document.getElementById("sessions-sub");
  if (!el) return;
  try {
    const list = await api.get("/api/admin/sessions");
    if (sub) sub.textContent = list.length + " connecté(s)";
    if (!list.length) { el.innerHTML=`<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--muted);">Aucune session active</td></tr>`; return; }
    el.innerHTML = list.map(s => {
      const dt = s.dateConnexion ? new Date(s.dateConnexion).toLocaleTimeString("fr-FR") : "—";
      const roleLabel = (s.role||"").replace("ROLE_","");
      const typeBadge = s.type==="CLIENT" ? "badge-success" : "badge-kyc-pending";
      return `<tr>
        <td><div class="tx-name">${s.nom||"—"}</div><div class="tx-sub">${s.email||""}</div></td>
        <td><span class="badge badge-pending" style="font-size:.72rem;">${roleLabel}</span></td>
        <td style="font-family:monospace;font-size:.82rem;">${s.adresseIp||"—"}</td>
        <td style="font-size:.82rem;">${dt}</td>
        <td><span class="badge ${typeBadge}">${s.type||"—"}</span></td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

async function renderSessions(container) {
  container.innerHTML = `<div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Sessions actives</div><div class="tx-table-sub" id="sessions-sub">Chargement...</div></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Utilisateur</th><th>Rôle</th><th>IP</th><th>Connecté à</th><th>Type</th></tr></thead>
        <tbody id="sessions-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>`;
  await loadSessions();
}

/* ─── Transactions récentes ─────────────────────────────── */
async function renderTransactions(container) {
  container.innerHTML = `<div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Transactions récentes</div></div>
      <a href="transactions.php" class="btn btn-ghost btn-sm">Voir tout →</a>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Référence</th><th>Type</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
        <tbody id="tx-tbody"><tr><td colspan="5" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>`;
  try {
    const rawList = cachedData.txs || await api.get("/api/transactions");
    const recent = (rawList||[]).slice(0,8).map(t => ({...t, typeTransaction: detecterTypeTx(t)}));
    const tbody = document.getElementById("tx-tbody");
    if (!recent.length) { tbody.innerHTML=`<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--muted);">Aucune transaction</td></tr>`; return; }
    const typeColor = {VERSEMENT:"#16a34a",RETRAIT:"#dc2626",VIREMENT:"#7c3aed"};
    const typeLabel = {VERSEMENT:"Versement",RETRAIT:"Retrait",VIREMENT:"Virement"};
    tbody.innerHTML = recent.map(t => {
      const dt = t.dateHeure ? new Date(t.dateHeure).toLocaleDateString("fr-FR") : "—";
      const type = t.typeTransaction;
      const c = typeColor[type]||"#888";
      const montant = t.montant ? Number(t.montant).toLocaleString("fr-FR")+" FCFA" : "—";
      const s = t.statut||"—";
      return `<tr>
        <td style="font-family:monospace;font-size:.8rem;">${t.reference||"—"}</td>
        <td><span style="display:inline-block;padding:2px 9px;border-radius:20px;font-size:.71rem;font-weight:700;color:${c};background:${c}18;">${typeLabel[type]||type}</span></td>
        <td style="font-weight:600;">${montant}</td>
        <td><span class="badge ${txBadge[s]||"badge-pending"}">${s}</span></td>
        <td style="font-size:.82rem;">${dt}</td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

/* ─── Audit récent ──────────────────────────────────────── */
async function renderAudit(container) {
  container.innerHTML = `<div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Journal d'audit récent</div></div>
      <a href="audit.php" class="btn btn-ghost btn-sm">Tout voir →</a>
    </div>
    <div id="audit-list" style="max-height:280px;overflow-y:auto;"></div>
  </div>`;
  try {
    const logs = cachedData.auditLogs || await api.get("/api/admin/audit-logs");
    const el = document.getElementById("audit-list");
    const recent = (logs||[]).slice(0,8);
    if (!recent.length) { el.innerHTML=`<div style="padding:24px;text-align:center;color:var(--muted);">Aucun log</div>`; return; }
    el.innerHTML = recent.map(l => {
      const dt = l.dateHeure ? new Date(l.dateHeure).toLocaleString("fr-FR") : "—";
      const who = l.utilisateur ? ((l.utilisateur.prenom||"")+" "+(l.utilisateur.nom||"")).trim() : "Système";
      return `<div style="display:flex;gap:12px;align-items:flex-start;padding:10px 20px;border-bottom:1px solid var(--border);">
        <div style="width:8px;height:8px;border-radius:50%;background:var(--g400);flex-shrink:0;margin-top:5px;"></div>
        <div style="flex:1;">
          <div style="font-size:.83rem;font-weight:600;color:var(--gray-800);">${l.actionEffectuee||"—"}</div>
          <div style="font-size:.76rem;color:var(--muted);">${who} · ${dt}</div>
        </div>
      </div>`;
    }).join("");
  } catch(e) {}
}

/* ─── KYC en attente ─────────────────────────────────────── */
async function renderKyc(container) {
  container.innerHTML = `<div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">KYC en attente de validation</div></div>
      <a href="kyc.php" class="btn btn-ghost btn-sm">Gérer →</a>
    </div>
    <div id="kyc-list" style="max-height:280px;overflow-y:auto;"><div style="text-align:center;padding:32px;"><span class="spinner"></span></div></div>
  </div>`;
  try {
    const list = cachedData.kyc || await api.get("/api/kyc/en-attente");
    const el = document.getElementById("kyc-list");
    if (!(list||[]).length) { el.innerHTML=`<div style="padding:24px;text-align:center;color:var(--muted);">Aucun KYC en attente</div>`; return; }
    el.innerHTML = list.slice(0,8).map(k => {
      const initials = ((k.prenom||"")[0]||(k.nom||"")[0]||"?").toUpperCase();
      return `<div class="kyc-row">
        <div class="kyc-avatar">${initials}</div>
        <div class="kyc-info">
          <div class="kyc-name">${k.prenom||""} ${k.nom||""}</div>
          <div class="kyc-detail">${k.email||"—"}</div>
        </div>
        <span class="badge badge-kyc-pending">En attente</span>
      </div>`;
    }).join("");
  } catch(e) {}
}

/* ─── Clients récents ────────────────────────────────────── */
async function renderClientsRecents(container) {
  container.innerHTML = `<div class="card">
    <div class="tx-table-header">
      <div><div class="tx-table-title">Clients récents</div></div>
      <a href="clients.php" class="btn btn-ghost btn-sm">Voir tout →</a>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>Client</th><th>Email</th><th>KYC</th><th>Offre</th></tr></thead>
        <tbody id="clients-tbody"><tr><td colspan="4" style="text-align:center;padding:32px;"><span class="spinner"></span></td></tr></tbody>
      </table>
    </div>
  </div>`;
  try {
    const list = cachedData.clients || await api.get("/api/clients");
    const tbody = document.getElementById("clients-tbody");
    const recent = (list||[]).slice(0,8);
    if (!recent.length) { tbody.innerHTML=`<tr><td colspan="4" style="text-align:center;padding:24px;color:var(--muted);">Aucun client</td></tr>`; return; }
    const kycBadge = { VALIDE:"badge-success", PENDING:"badge-kyc-pending", REJETE:"badge-failed" };
    tbody.innerHTML = recent.map(c => {
      const kyc = c.statutKyc || "PENDING";
      return `<tr>
        <td><div class="tx-name">${c.prenom||""} ${c.nom||""}</div></td>
        <td style="font-size:.82rem;">${c.email||"—"}</td>
        <td><span class="badge ${kycBadge[kyc]||"badge-pending"}" style="font-size:.72rem;">${kyc}</span></td>
        <td style="font-size:.82rem;">${c.offreAbonnement?.nomOffre||"Standard"}</td>
      </tr>`;
    }).join("");
  } catch(e) {}
}

init();
</script>
</body>
</html>
