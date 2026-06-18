<?php
/**
 * Sidebar admin — inclure avec $sidebar_active = 'dashboard' | 'clients' | ...
 */
$sa = $sidebar_active ?? '';
?>
<aside class="sidebar sidebar-dark">

  <!-- Marque -->
  <div class="sidebar-brand">
    <img src="img/logo.svg" alt="ZEN-PAY" class="sidebar-brand-logo" style="filter:brightness(0) invert(1);">
    <div>
      <div class="sidebar-brand-name">ZEN-PAY</div>
      <div class="sidebar-brand-tagline">Back-Office</div>
    </div>
  </div>

  <nav class="sidebar-nav">

    <!-- MENU -->
    <div class="sidebar-section-label">Menu</div>

    <a href="dashboard.php" class="sidebar-item <?= $sa==='dashboard'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span class="sidebar-item-label">Tableau de bord</span>
    </a>

    <a href="clients.php" class="sidebar-item <?= $sa==='clients'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE,SUPERVISOR,ADMIN_BD">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm14 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      <span class="sidebar-item-label">Clients</span>
    </a>

    <a href="comptes.php" class="sidebar-item <?= $sa==='comptes'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE,SUPERVISOR,ADMIN_SYSTEME,ADMIN_BD">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      <span class="sidebar-item-label">Comptes</span>
    </a>

    <a href="transactions.php" class="sidebar-item <?= $sa==='transactions'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE,SUPERVISOR,ADMIN_SYSTEME,ADMIN_BD">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      <span class="sidebar-item-label">Transactions</span>
    </a>

    <!-- OPÉRATIONS -->
    <div class="sidebar-section-label">Opérations</div>

    <a href="kyc.php" class="sidebar-item <?= $sa==='kyc'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE,SUPERVISOR">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      <span class="sidebar-item-label">Validation KYC</span>
    </a>

    <a href="tickets.php" class="sidebar-item <?= $sa==='tickets'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE,SUPERVISOR">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
      <span class="sidebar-item-label">Tickets support</span>
    </a>

    <a href="abonnements.php" class="sidebar-item <?= $sa==='abonnements'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
      <span class="sidebar-item-label">Abonnements</span>
    </a>

    <a href="admin-rib.php" class="sidebar-item <?= $sa==='admin-rib'?'active':'' ?>" data-roles="SUPER_ADMIN,GESTIONNAIRE">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <span class="sidebar-item-label">RIB Clients</span>
    </a>

    <!-- SYSTÈME -->
    <div class="sidebar-section-label">Système</div>

    <a href="personnel.php" class="sidebar-item <?= $sa==='personnel'?'active':'' ?>" data-roles="SUPER_ADMIN,ADMIN_SYSTEME">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
      <span class="sidebar-item-label">Personnel</span>
    </a>

    <a href="reseau.php" class="sidebar-item <?= $sa==='reseau'?'active':'' ?>" data-roles="SUPER_ADMIN,ADMIN_SYSTEME">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/></svg>
      <span class="sidebar-item-label">Réseau / IP</span>
    </a>

    <a href="audit.php" class="sidebar-item <?= $sa==='audit'?'active':'' ?>" data-roles="SUPER_ADMIN,ADMIN_SYSTEME,SUPERVISOR">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span class="sidebar-item-label">Piste d'audit</span>
    </a>

    <!-- MON COMPTE -->
    <div class="sidebar-section-label">Mon compte</div>

    <a href="admin-parametres.php" class="sidebar-item <?= $sa==='parametres'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
      <span class="sidebar-item-label">Paramètres</span>
    </a>

  </nav>

  <!-- Utilisateur connecté -->
  <div class="sidebar-user">
    <div class="sidebar-user-avatar" id="sb-avatar">G</div>
    <div style="flex:1;min-width:0;">
      <div class="sidebar-user-name" id="sb-name">Chargement…</div>
      <div class="sidebar-user-role" id="sb-role">—</div>
    </div>
    <button onclick="logout()" title="Déconnexion" style="background:none;border:none;cursor:pointer;color:#f87171;padding:4px;border-radius:6px;flex-shrink:0;" onmouseover="this.style.background='rgba(239,68,68,.15)'" onmouseout="this.style.background='none'">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
    </button>
  </div>

</aside>

<script>
(async () => {
  try {
    const token = localStorage.getItem("mf_jwt_token");
    if (!token) return;
    const res = await fetch((localStorage.getItem("mf_api_base") || "http://localhost:8080") + "/api/auth/me", {
      headers: { "Authorization": "Bearer " + token }
    });
    if (!res.ok) return;
    const me = await res.json();
    const role = (me.role || "").replace("ROLE_", "");

    // Remplir les infos utilisateur
    const initial = (me.prenom || me.email || "?")[0].toUpperCase();
    const elAv = document.getElementById("sb-avatar");
    if (elAv) elAv.textContent = initial;
    const elName = document.getElementById("sb-name");
    if (elName) elName.textContent = (me.prenom || "") + " " + (me.nom || "");
    const elRole = document.getElementById("sb-role");
    const roleLabels = {
      SUPER_ADMIN: "Super Admin", GESTIONNAIRE: "Gestionnaire",
      SUPERVISOR: "Superviseur", ADMIN_SYSTEME: "Admin Système",
      ADMIN_BD: "Admin BD", CLIENT: "Client"
    };
    if (elRole) elRole.textContent = roleLabels[role] || role;

    // Afficher seulement les items autorisés (ils sont cachés par défaut via CSS)
    document.querySelectorAll(".sidebar-item[data-roles]").forEach(el => {
      if (el.dataset.roles.split(",").includes(role)) el.style.display = "flex";
    });

    // Propager le nom au topbar si présent
    const topbarName = document.getElementById("topbar-user-name");
    if (topbarName) topbarName.textContent = (me.prenom || "") + " " + (me.nom || "");
    const topbarRole = document.getElementById("topbar-user-role");
    if (topbarRole) {
      topbarRole.textContent = roleLabels[role] || role;
      const roleColors = {
        SUPER_ADMIN: "#7c3aed", GESTIONNAIRE: "#16a34a",
        SUPERVISOR: "#d97706", ADMIN_SYSTEME: "#2563eb", ADMIN_BD: "#0891b2"
      };
      topbarRole.style.color = roleColors[role] || "#6b7280";
    }
    const topbarAv = document.getElementById("user-avatar");
    if (topbarAv) topbarAv.textContent = initial;

    // Exposer le rôle globalement pour le dashboard
    window.__userRole = role;
    window.__userMe   = me;
    document.dispatchEvent(new CustomEvent("sidebarReady", { detail: { me, role } }));
  } catch(e) {}
})();
</script>
<div class="sidebar-overlay" onclick="closeSidebar()"></div>
<script>
function toggleSidebar() { document.body.classList.toggle("sidebar-open"); }
function closeSidebar()  { document.body.classList.remove("sidebar-open"); }
document.querySelectorAll(".sidebar-item").forEach(el => {
  el.addEventListener("click", () => { if (window.innerWidth <= 768) closeSidebar(); });
});
</script>
