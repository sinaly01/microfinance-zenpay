<?php
/**
 * Sidebar client — inclure avec $sidebar_active = 'dashboard' | 'transactions' | ...
 */
$sc = $sidebar_active ?? '';
?>
<aside class="sidebar">

  <!-- Marque -->
  <div class="sidebar-brand">
    <img src="img/logo.svg" alt="ZEN-PAY" class="sidebar-brand-logo">
    <div>
      <div class="sidebar-brand-name" style="color:#0f172a;">ZEN-PAY</div>
      <div class="sidebar-brand-tagline">Espace Client</div>
    </div>
  </div>

  <nav class="sidebar-nav">

    <!-- MON ESPACE -->
    <div class="sidebar-section-label">Mon espace</div>

    <a href="client-dashboard.php" class="sidebar-item <?= $sc==='dashboard'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span class="sidebar-item-label">Tableau de bord</span>
    </a>

    <a href="client-transactions.php" class="sidebar-item <?= $sc==='transactions'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      <span class="sidebar-item-label">Transactions</span>
    </a>

    <a href="client-virement.php" class="sidebar-item <?= $sc==='virement'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
      <span class="sidebar-item-label">Virement</span>
    </a>

    <!-- COMPTE -->
    <div class="sidebar-section-label">Compte</div>

    <a href="client-releve.php" class="sidebar-item <?= $sc==='releve'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <span class="sidebar-item-label">Relevé de compte</span>
    </a>

    <a href="client-rib.php" class="sidebar-item <?= $sc==='rib'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <span class="sidebar-item-label">Mon RIB</span>
    </a>

    <a href="client-abonnement.php" class="sidebar-item <?= $sc==='abonnement'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
      <span class="sidebar-item-label">Mon Abonnement</span>
    </a>

    <!-- OUTILS -->
    <div class="sidebar-section-label">Outils</div>

    <a href="client-simulateur.php" class="sidebar-item <?= $sc==='simulateur'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M13 3h8m0 0v8m0-8l-8 8"/></svg>
      <span class="sidebar-item-label">Simulateur de frais</span>
    </a>

    <a href="client-support.php" class="sidebar-item <?= $sc==='support'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
      <span class="sidebar-item-label">Support en ligne</span>
    </a>

    <!-- MON COMPTE -->
    <div class="sidebar-section-label">Mon compte</div>

    <a href="client-validation.php" class="sidebar-item <?= $sc==='validation'?'active':'' ?>" id="sb-validation-link" style="display:none;">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      <span class="sidebar-item-label">Validation ID <span id="sb-kyc-dot" style="display:inline-block;width:7px;height:7px;background:#ef4444;border-radius:50%;margin-left:4px;vertical-align:middle;"></span></span>
    </a>

    <a href="client-parametres.php" class="sidebar-item <?= $sc==='parametres'?'active':'' ?>">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
      <span class="sidebar-item-label">Paramètres</span>
    </a>

  </nav>

  <!-- Utilisateur connecté -->
  <div class="sidebar-user">
    <div class="sidebar-user-avatar" id="sb-avatar" style="background:var(--g100);color:var(--g700);">C</div>
    <div style="flex:1;min-width:0;">
      <div class="sidebar-user-name" id="sb-name">Chargement…</div>
      <div class="sidebar-user-role" id="sb-role">Client</div>
    </div>
    <button onclick="logout()" title="Déconnexion" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px;border-radius:6px;flex-shrink:0;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='none'">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
    </button>
  </div>

</aside>
<div class="sidebar-overlay" onclick="closeSidebar()"></div>
<script>
function toggleSidebar() { document.body.classList.toggle("sidebar-open"); }
function closeSidebar()  { document.body.classList.remove("sidebar-open"); }
document.querySelectorAll(".sidebar-item").forEach(el => {
  el.addEventListener("click", () => { if (window.innerWidth <= 768) closeSidebar(); });
});

// Charger infos utilisateur dans la sidebar client
(async () => {
  try {
    const token = localStorage.getItem("mf_jwt_token");
    if (!token) return;
    const API_SB = localStorage.getItem("mf_api_base") || (
      (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
        ? "http://localhost:8080"
        : window.location.protocol + "//" + window.location.hostname + ":8080"
    );
    const res = await fetch(API_SB + "/api/auth/me", {
      headers: { "Authorization": "Bearer " + token }
    });
    if (!res.ok) return;
    const me = await res.json();

    const initial = (me.prenom || me.email || "?")[0].toUpperCase();
    const elAv = document.getElementById("sb-avatar"); if (elAv) elAv.textContent = initial;
    const elName = document.getElementById("sb-name"); if (elName) elName.textContent = (me.prenom||"") + " " + (me.nom||"");

    // Topbar client
    const elFullname = document.getElementById("user-fullname"); if (elFullname) elFullname.textContent = (me.prenom||"") + " " + (me.nom||"");
    const elUserAv = document.getElementById("user-avatar"); if (elUserAv) elUserAv.textContent = initial;
    if (me.offreAbonnement) { const elOffre = document.getElementById("user-offre"); if (elOffre) elOffre.textContent = me.offreAbonnement.nomOffre; }
    const elKyc = document.getElementById("user-kyc-badge");
    if (elKyc) {
      const kycBadges = { PENDING:"badge-kyc-pending", DOCUMENTS_SOUMIS:"badge-pending", VALIDE:"badge-success", REJETE:"badge-failed" };
      const kycLabels = { PENDING:"KYC en attente", DOCUMENTS_SOUMIS:"KYC en révision", VALIDE:"KYC validé", REJETE:"KYC rejeté" };
      const statut = me.statutKyc || "PENDING";
      if (statut !== "VALIDE") {
        elKyc.style.display = "";
        elKyc.className = "badge " + (kycBadges[statut] || "badge-pending");
        elKyc.textContent = kycLabels[statut] || statut;
      }
    }

    // Afficher le lien "Validation" dans la sidebar si KYC non validé
    const statut = me.statutKyc || "PENDING";
    const validLink = document.getElementById("sb-validation-link");
    if (validLink && statut !== "VALIDE") {
      validLink.style.display = "flex";
      // Cacher le point rouge si docs déjà soumis (en attente gestionnaire)
      const dot = document.getElementById("sb-kyc-dot");
      if (dot && statut === "DOCUMENTS_SOUMIS") dot.style.background = "#eab308";
    }

    window.__userMe = me;
    window.__userKyc = statut;
    document.dispatchEvent(new CustomEvent("clientSidebarReady", { detail: { me, statut } }));
  } catch(e) {}
})();
</script>
