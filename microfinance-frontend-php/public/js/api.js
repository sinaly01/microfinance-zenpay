// =========================================================
// api.js — wrapper unique pour parler à l'API Spring Boot
// Toutes les pages utilisent ce fichier.
// =========================================================

// L'URL du backend est définie selon l'environnement :
// - localhost direct (dev)        → http://localhost:8080
// - Docker (depuis le navigateur) → http://localhost:8080 (port mappé)
// - Réseau LAN groupe             → http://192.168.1.20:8080
//
// Le navigateur appelle TOUJOURS le port hôte exposé, jamais le DNS interne Docker.
// Pour overrider sans toucher au code, ouvre la console du navigateur :
//   localStorage.setItem("mf_api_base", "http://192.168.1.20:8080");
// En local → localhost:8080. Sur un serveur distant → même IP/domaine que le frontend, port 8080.
const API_BASE = localStorage.getItem("mf_api_base") || (
  (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
    ? "http://localhost:8080"
    : window.location.protocol + "//" + window.location.hostname + ":8080"
);
const TOKEN_KEY = "mf_jwt_token";
const USER_KEY = "mf_user";

// ── Stockage du token JWT dans le navigateur ──────────────
function saveSession(authResponse) {
    localStorage.setItem(TOKEN_KEY, authResponse.token);
    localStorage.setItem(USER_KEY, JSON.stringify({
        email: authResponse.email,
        role: authResponse.role
    }));
}

function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

function getUser() {
    const raw = localStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
}

function logout() {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    window.location.href = "index.php";
}

// ── Garde de page : redirige vers login si pas de token ──
function requireAuth() {
    if (!getToken()) {
        window.location.href = "login.php";
    }
}

// ── Appel HTTP générique avec ajout automatique du JWT ───
async function apiCall(path, options = {}) {
    const headers = {
        "Content-Type": "application/json",
        ...(options.headers || {})
    };
    const token = getToken();
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }

    const response = await fetch(API_BASE + path, {
        ...options,
        headers
    });

    // Token expiré ou invalide → on force la reconnexion
    if (response.status === 401) {
        logout();
        throw new Error("Session expirée — veuillez vous reconnecter");
    }

    if (response.status === 204) return null; // No Content

    const text = await response.text();
    const data = text ? JSON.parse(text) : null;

    if (!response.ok) {
        const msg = data?.message || data?.error || `Erreur HTTP ${response.status}`;
        throw new Error(msg);
    }

    return data;
}

// ── Helpers raccourcis pour chaque verbe HTTP ────────────
const api = {
    get:  (path)        => apiCall(path),
    post: (path, body)  => apiCall(path, { method: "POST", body: JSON.stringify(body) }),
    put:  (path, body)  => apiCall(path, { method: "PUT",  body: body ? JSON.stringify(body) : undefined }),
    del:  (path)        => apiCall(path, { method: "DELETE" }),

    // POST sans body, mais avec query params (pour /api/comptes/ouvrir)
    postQuery: (path, params) => {
        const qs = new URLSearchParams(params).toString();
        return apiCall(path + "?" + qs, { method: "POST" });
    }
};

// ── Affichage d'un message flash (succès/erreur) ─────────
function flash(message, type = "info") {
    const div = document.createElement("div");
    div.className = "flash flash-" + type;
    div.textContent = message;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 4000);
}

// ── Format d'un montant en FCFA ──────────────────────────
function formatFCFA(n) {
    if (n === null || n === undefined) return "-";
    return new Intl.NumberFormat("fr-FR").format(n) + " FCFA";
}

// ── Format d'une date ISO ────────────────────────────────
function formatDate(iso) {
    if (!iso) return "-";
    return new Date(iso).toLocaleString("fr-FR");
}

// ── Charge et affiche les infos utilisateur dans le topbar + sidebar client ──
async function loadClientTopbar() {
    try {
        const me = await api.get("/api/auth/me");
        const initial = (me.prenom || me.email || "?")[0].toUpperCase();

        // Topbar avatar
        const avatar = document.getElementById("user-avatar");
        if (avatar) avatar.textContent = initial;

        // Sidebar client : avatar + nom
        const sbAv = document.getElementById("sb-avatar");
        if (sbAv) sbAv.textContent = initial;
        const sbName = document.getElementById("sb-name");
        if (sbName) sbName.textContent = (me.prenom || "") + " " + (me.nom || "");
        const sbRole = document.getElementById("sb-role");
        if (sbRole) sbRole.textContent = "Client";

        const fullname = document.getElementById("user-fullname");
        if (fullname) fullname.textContent = (me.prenom || "") + " " + (me.nom || "");

        const offre = document.getElementById("user-offre");
        if (offre) offre.textContent = me.offreAbonnement?.nomOffre || "Standard";

        const kycBadge = document.getElementById("user-kyc-badge");
        if (kycBadge && me.statutKyc) {
            const kyc = me.statutKyc;
            const cfg = {
                VALIDE:           { label: "KYC validé",     color: "#16a34a", bg: "#dcfce7" },
                PENDING:          { label: "KYC en attente",  color: "#d97706", bg: "#fef3c7" },
                DOCUMENTS_SOUMIS: { label: "KYC en révision", color: "#2563eb", bg: "#dbeafe" },
                REJETE:           { label: "KYC rejeté",      color: "#dc2626", bg: "#fee2e2" }
            }[kyc] || {};
            if (cfg.label) {
                kycBadge.style.cssText = `display:inline-block;padding:2px 8px;border-radius:10px;font-size:.7rem;font-weight:700;color:${cfg.color};background:${cfg.bg};`;
                kycBadge.textContent = cfg.label;
            }
        }
        return me;
    } catch(e) {
        return null;
    }
}

// ── Vérifie que le KYC est validé, sinon affiche une alerte et bloque la page ──
// Appeler dans init() des pages transactionnelles (transactions, virement, releve…)
async function requireKycValide() {
    try {
        const me = await api.get("/api/auth/me");
        if (!me.idClient) return me; // pas un client, pas de check
        const statut = me.statutKyc || "PENDING";
        if (statut !== "VALIDE") {
            const msgs = {
                PENDING:          "⚠️ Votre compte n'est pas encore activé. Veuillez télécharger votre pièce d'identité.",
                DOCUMENTS_SOUMIS: "⏳ Vos documents sont en cours de vérification (24-48h). Cette section sera disponible après validation.",
                REJETE:           "❌ Votre dossier KYC a été rejeté. Veuillez soumettre à nouveau vos documents."
            };
            const msg = msgs[statut] || "⚠️ Compte non activé.";
            // Masquer le contenu principal et afficher une bannière
            const main = document.querySelector("main.page-content") || document.querySelector("main");
            if (main) {
                main.innerHTML = `
                  <div style="max-width:540px;margin:48px auto;text-align:center;padding:32px;">
                    <div style="font-size:3rem;margin-bottom:16px;">${statut==="REJETE"?"❌":"🔒"}</div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:var(--gray-800);margin-bottom:8px;">Accès restreint</h2>
                    <p style="color:var(--gray-600);margin-bottom:24px;font-size:.9rem;">${msg}</p>
                    <a href="client-validation.php" class="btn btn-primary" style="display:inline-flex;gap:8px;align-items:center;">
                      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                      ${statut==="PENDING"||statut==="REJETE" ? "Valider mon identité" : "Voir l'état de mon dossier"}
                    </a>
                    <div style="margin-top:16px;"><a href="client-dashboard.php" style="color:var(--muted);font-size:.83rem;">← Retour au tableau de bord</a></div>
                  </div>`;
            }
            return null; // signale que la page est bloquée
        }
        return me;
    } catch(e) {
        return null;
    }
}
