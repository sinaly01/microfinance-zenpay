<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Support — ' . APP_NAME;
$sidebar_active = 'support';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .chat-wrapper { display:flex;flex-direction:column;height:calc(100vh - 200px);min-height:400px; }
    .chat-header { display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid var(--border); }
    .chat-messages { flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:12px; }
    .msg { max-width:70%;display:flex;flex-direction:column;gap:4px; }
    .msg.client { align-self:flex-end;align-items:flex-end; }
    .msg.gestionnaire { align-self:flex-start;align-items:flex-start; }
    .msg-bubble { padding:10px 14px;border-radius:16px;font-size:.88rem;line-height:1.5;word-break:break-word; }
    .msg.client .msg-bubble { background:var(--primary);color:#fff;border-bottom-right-radius:4px; }
    .msg.gestionnaire .msg-bubble { background:var(--g50);border:1px solid var(--border);color:var(--gray-800);border-bottom-left-radius:4px; }
    .msg-time { font-size:.72rem;color:var(--muted); }
    .chat-input-bar { padding:14px 16px;border-top:1px solid var(--border);display:flex;gap:10px;align-items:flex-end; }
    .chat-textarea { flex:1;resize:none;min-height:40px;max-height:120px;border:1.5px solid var(--border);border-radius:20px;padding:10px 16px;font-size:.88rem;font-family:inherit;outline:none;line-height:1.4; }
    .chat-textarea:focus { border-color:var(--primary); }
    .chat-send-btn { width:40px;height:40px;border-radius:50%;background:var(--primary);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;transition:background .15s; }
    .chat-send-btn:hover { background:var(--g700); }
    .typing-indicator { display:flex;gap:4px;align-items:center;padding:6px 10px; }
    .typing-dot { width:7px;height:7px;border-radius:50%;background:var(--muted);animation:bounce 1.2s infinite; }
    .typing-dot:nth-child(2){animation-delay:.2s}
    .typing-dot:nth-child(3){animation-delay:.4s}
    @keyframes bounce{0%,80%,100%{transform:scale(0.8)}40%{transform:scale(1.2)}}
    .online-dot { width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block;margin-right:4px; }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Support client'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content" style="padding-bottom:0;display:flex;flex-direction:column;height:calc(100vh - 60px);">

  <div class="dash-header" style="padding-bottom:12px;flex-shrink:0;">
    <div><h1>Support <span>ZEN-PAY</span></h1><p style="color:var(--muted);font-size:.85rem;">Discutez avec un gestionnaire disponible</p></div>
  </div>

  <div class="card chat-wrapper" style="flex:1;margin-bottom:0;border-radius:var(--r) var(--r) 0 0;overflow:hidden;">

    <!-- En-tête chat -->
    <div class="chat-header">
      <div style="width:38px;height:38px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;" id="agent-avatar">?</div>
      <div style="flex:1;">
        <div style="font-weight:700;font-size:.95rem;" id="agent-nom">Support ZEN-PAY</div>
        <div style="font-size:.76rem;color:var(--muted);"><span class="online-dot"></span><span id="agent-statut">En attente d'un gestionnaire...</span></div>
      </div>
      <span class="badge badge-success" style="font-size:.72rem;">Support actif</span>
    </div>

    <!-- Messages -->
    <div class="chat-messages" id="chat-messages">
      <div style="text-align:center;padding:40px;"><span class="spinner"></span></div>
    </div>

    <!-- Zone de saisie -->
    <div class="chat-input-bar">
      <textarea class="chat-textarea" id="msg-input" placeholder="Écrivez votre message..." rows="1"
        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();envoyerMessage();}"></textarea>
      <button class="chat-send-btn" onclick="envoyerMessage()" title="Envoyer">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
      </button>
    </div>

  </div>

</main>
</div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

let myClientId = null;
let lastMsgCount = 0;
let refreshTimer = null;

async function init() {
  try {
    const me = await api.get("/api/auth/me");
    document.getElementById("user-avatar").textContent = (me.prenom||"C")[0].toUpperCase();
    myClientId = me.idClient;
    if (!myClientId) { flash("Identifiant client introuvable.", "error"); return; }
    await chargerMessages();
    refreshTimer = setInterval(chargerMessages, 8000);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
}

async function chargerMessages() {
  if (!myClientId) return;
  try {
    const msgs = await api.get("/api/support/" + myClientId);
    if (msgs.length === lastMsgCount) return;
    lastMsgCount = msgs.length;

    // Identifier le gestionnaire assigné
    const gMsg = msgs.find(m => m.expediteur === "GESTIONNAIRE" && m.gestionnaire);
    if (gMsg && gMsg.gestionnaire) {
      const g = gMsg.gestionnaire;
      const initiales = ((g.prenom||"G")[0] + (g.nom||"S")[0]).toUpperCase();
      document.getElementById("agent-avatar").textContent = initiales;
      document.getElementById("agent-nom").textContent = (g.prenom||"") + " " + (g.nom||"");
      document.getElementById("agent-statut").textContent = "Gestionnaire ZEN-PAY assigné";
    }

    const container = document.getElementById("chat-messages");
    if (!msgs.length) {
      container.innerHTML = `<div style="text-align:center;padding:40px;color:var(--muted);">
        <div style="font-size:2.5rem;margin-bottom:12px;">💬</div>
        <div style="font-weight:700;margin-bottom:6px;">Commencez la conversation</div>
        <div style="font-size:.85rem;">Un gestionnaire vous répondra dès que possible.</div>
      </div>`;
      return;
    }

    const scrolledToBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 40;

    container.innerHTML = msgs.map(m => {
      const isClient = m.expediteur === "CLIENT";
      const dt = m.dateEnvoi ? new Date(m.dateEnvoi).toLocaleTimeString("fr-FR",{hour:"2-digit",minute:"2-digit"}) : "";
      const dateStr = m.dateEnvoi ? new Date(m.dateEnvoi).toLocaleDateString("fr-FR") : "";
      return `<div class="msg ${isClient?"client":"gestionnaire"}">
        ${!isClient?`<div style="font-size:.75rem;font-weight:700;color:var(--muted);margin-bottom:2px;">
          ${m.gestionnaire ? (m.gestionnaire.prenom||"") + " " + (m.gestionnaire.nom||"") : "Support ZEN-PAY"}
        </div>`:""}
        <div class="msg-bubble">${escHtml(m.contenu)}</div>
        <div class="msg-time">${dateStr} ${dt}</div>
      </div>`;
    }).join("");

    if (scrolledToBottom) container.scrollTop = container.scrollHeight;
  } catch(e) {}
}

function escHtml(str) {
  return (str||"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br>");
}

async function envoyerMessage() {
  const input = document.getElementById("msg-input");
  const contenu = input.value.trim();
  if (!contenu || !myClientId) return;

  input.value = "";
  input.style.height = "auto";

  try {
    await api.postQuery("/api/support/message", { idClient: myClientId, contenu });
    await chargerMessages();
    const container = document.getElementById("chat-messages");
    container.scrollTop = container.scrollHeight;
  } catch(e) {
    flash("Erreur envoi : " + e.message, "error");
    input.value = contenu;
  }
}

// Auto-resize textarea
document.addEventListener("DOMContentLoaded", () => {
  const ta = document.getElementById("msg-input");
  if (ta) ta.addEventListener("input", function() {
    this.style.height = "auto";
    this.style.height = Math.min(this.scrollHeight, 120) + "px";
  });
});

init();
</script>
</body>
</html>
