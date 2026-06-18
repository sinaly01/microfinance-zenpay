    <header class="topbar">
      <div style="display:flex;align-items:center;gap:10px;">
        <button class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Menu">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="topbar-page-title">
          <div class="tpt-marker"></div>
          <span><?= h($topbar_title ?? 'Mon Espace') ?></span>
        </div>
      </div>
      <div class="topbar-right" style="gap:12px;">
        <!-- Infos utilisateur connecté -->
        <div id="user-info-bar" style="display:flex;align-items:center;gap:10px;padding:6px 14px;background:var(--g50);border:1px solid var(--border);border-radius:20px;font-size:.82rem;">
          <div id="user-avatar" style="width:28px;height:28px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0;">?</div>
          <div style="line-height:1.2;">
            <div id="user-fullname" style="font-weight:700;color:var(--gray-900);white-space:nowrap;">—</div>
            <div id="user-offre" style="font-size:.72rem;color:var(--muted);">—</div>
          </div>
          <span id="user-kyc-badge" style="display:none;"></span>
        </div>
        <button onclick="logout()" class="topbar-logout">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Déconnexion
        </button>
      </div>
    </header>
