    <header class="topbar">
      <div style="display:flex;align-items:center;gap:10px;">
        <button class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Menu">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="topbar-page-title">
          <div class="tpt-marker"></div>
          <span><?= h($topbar_title ?? 'Back-Office') ?></span>
        </div>
      </div>
      <div class="topbar-right">
        <div style="display:flex;flex-direction:column;align-items:flex-end;line-height:1.2;">
          <span id="topbar-user-name" style="font-size:.85rem;font-weight:600;color:var(--gray-800);">Chargement…</span>
          <span id="topbar-user-role" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">—</span>
        </div>
        <div class="avatar-btn" id="user-avatar">G</div>
      </div>
    </header>
