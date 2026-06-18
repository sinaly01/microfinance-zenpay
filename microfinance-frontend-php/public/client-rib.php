<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Mon RIB — ZEN-PAY';
$sidebar_active = 'rib';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-layout">

<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>

  <div class="main-area">
<?php $topbar_title = 'Mon RIB'; include __DIR__ . '/../includes/topbar-client.php'; ?>

    <main class="page-content">

      <div class="feature-layout">

        <div class="feature-header">
          <h2>Mon RIB</h2>
          <p>Relevé d'Identité Bancaire — partagez ces informations pour recevoir des virements</p>
        </div>

        <!-- RIB Card -->
        <div id="rib-loading" style="text-align:center;padding:60px;"><span class="spinner"></span></div>

        <div id="rib-zone" style="display:none;">
          <div class="rib-card" id="rib-card">
            <div class="rib-header">
              <div>
                <div class="rib-bank">ZEN-PAY</div>
                <div style="font-size:.72rem;color:rgba(255,255,255,.5);margin-top:2px;letter-spacing:.04em;">MICROFINANCE DIGITALE</div>
              </div>
              <div class="rib-logo">Z</div>
            </div>

            <div class="rib-field">
              <div class="rib-label">Titulaire du compte</div>
              <div class="rib-value" id="rib-titulaire">—</div>
            </div>

            <div class="rib-field">
              <div class="rib-label">Numéro de compte</div>
              <div class="rib-value" id="rib-numero">—</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
              <div class="rib-field" style="margin-bottom:0;">
                <div class="rib-label">Code banque</div>
                <div class="rib-value">CI005</div>
              </div>
              <div class="rib-field" style="margin-bottom:0;">
                <div class="rib-label">Code guichet</div>
                <div class="rib-value">00001</div>
              </div>
              <div class="rib-field" style="margin-bottom:0;">
                <div class="rib-label">Clé RIB</div>
                <div class="rib-value" id="rib-cle">—</div>
              </div>
            </div>
          </div>

          <!-- BIC / IBAN -->
          <div class="card card-pad" style="margin-top:20px;">
            <div class="card-header"><span class="card-title">COORDONNÉES INTERNATIONALES</span></div>
            <div style="display:flex;flex-direction:column;gap:14px;">
              <div>
                <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">BIC / SWIFT</div>
                <div style="font-family:monospace;font-size:1.1rem;font-weight:700;letter-spacing:.1em;color:var(--gray-800);">ZENPCIAB</div>
              </div>
              <hr>
              <div>
                <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">IBAN</div>
                <div style="font-family:monospace;font-size:1rem;font-weight:700;letter-spacing:.08em;color:var(--gray-800);" id="rib-iban">—</div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;">
            <button class="btn btn-primary" onclick="copierRib()">
              📋 Copier le RIB
            </button>
            <button class="btn btn-outline" id="pdf-btn" onclick="telechargerPDF()">
              📄 Télécharger PDF
            </button>
          </div>
          <div style="margin-top:8px;">
            <button class="btn btn-ghost w-full btn-sm" onclick="imprimerRib()" style="font-size:.78rem;">
              🖨️ Imprimer (aperçu navigateur)
            </button>
          </div>

          <!-- Partage info -->
          <div class="result-card" style="margin-top:20px;">
            <div style="font-size:.85rem;font-weight:700;color:var(--g700);margin-bottom:12px;">ℹ️ Comment utiliser mon RIB ?</div>
            <div class="result-row">
              <span class="result-label">Recevoir un virement</span>
              <span class="result-value" style="font-size:.82rem;">Communiquez votre numéro de compte</span>
            </div>
            <div class="result-row">
              <span class="result-label">Virement international</span>
              <span class="result-value" style="font-size:.82rem;">Utilisez IBAN + BIC</span>
            </div>
            <div class="result-row" style="border-bottom:none;">
              <span class="result-label">Domiciliation</span>
              <span class="result-value" style="font-size:.82rem;">ZEN-PAY — Abidjan, CI</span>
            </div>
          </div>
        </div>

        <!-- Offre sans RIB -->
        <div id="rib-locked" style="display:none;" class="card card-pad">
          <div class="empty-state">
            <div class="empty-icon">🔒</div>
            <h3 style="margin-bottom:8px;">RIB non inclus dans votre offre</h3>
            <p style="color:var(--muted);margin-bottom:20px;">Votre offre actuelle ne comprend pas l'option RIB.<br>Passez à l'offre <strong>PREMIUM</strong> pour y accéder.</p>
            <a href="client-abonnement.php" class="btn btn-primary">Changer d'offre →</a>
          </div>
        </div>

        <!-- État vide si pas de compte -->
        <div id="rib-empty" style="display:none;" class="card">
          <div class="empty-state">
            <div class="empty-icon">🏦</div>
            <h3>Aucun compte actif</h3>
            <p>Vous n'avez pas encore de compte ouvert. Contactez votre gestionnaire.</p>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

let ribData = {};
let myIdClient = null;

async function telechargerPDF() {
  const btn = document.getElementById("pdf-btn");
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
  try {
    const token = localStorage.getItem("mf_jwt_token");
    const apiBase = localStorage.getItem("mf_api_base") || (
      (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
        ? "http://localhost:8080" : ""
    );
    const res = await fetch(`${apiBase}/api/rib/${myIdClient}`, {
      headers: { "Authorization": "Bearer " + token }
    });
    if (!res.ok) {
      const d = await res.json().catch(()=>({}));
      flash(d.message || d.error || "Erreur téléchargement PDF", "error");
      return;
    }
    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url; a.download = `RIB_ZenPay_${myIdClient}.pdf`; a.click();
    URL.revokeObjectURL(url);
  } catch(e) { flash("Erreur : " + e.message, "error"); }
  finally { btn.disabled = false; btn.innerHTML = "📄 Télécharger PDF"; }
}

function computeKey(numeroCompte) {
  const digits = (numeroCompte || "").replace(/\D/g, "");
  if (!digits) return "00";
  let sum = 0;
  for (let i = 0; i < digits.length; i++) sum += parseInt(digits[i]) * (i + 1);
  return String(97 - (sum % 97)).padStart(2, "0");
}

function buildIBAN(numeroCompte) {
  const bban = "CI005" + "00001" + (numeroCompte || "").replace(/\D/g, "").substring(0, 11).padStart(11, "0");
  return "CI" + bban;
}

function formatIBAN(iban) {
  return iban.match(/.{1,4}/g)?.join(" ") || iban;
}

async function init() {
  try {
    const me = await api.get("/api/auth/me");
    const prenom = me.prenom || me.email.split("@")[0];
    document.getElementById("user-avatar").textContent = prenom[0].toUpperCase();

    myIdClient = me.idClient;
    const idClient = myIdClient;
    if (!idClient) throw new Error("idClient manquant");

    // Vérifier si l'offre inclut le RIB
    const ribDispo = me.offreAbonnement ? me.offreAbonnement.optionRibDispo : false;
    if (!ribDispo) {
      document.getElementById("rib-loading").style.display = "none";
      document.getElementById("rib-locked").style.display  = "";
      return;
    }

    await api.post("/api/comptes/auto-init", {}).catch(() => {});
    const comptes = await api.get("/api/comptes/client/" + idClient);
    const actif = comptes.find(c => c.statut === "ACTIF") || comptes[0];

    document.getElementById("rib-loading").style.display = "none";

    if (!actif) {
      document.getElementById("rib-empty").style.display = "";
      return;
    }

    const nom = `${me.prenom ?? ""} ${me.nom ?? ""}`.trim() || me.email;
    const numero = actif.numeroCompte || "";
    const cle = computeKey(numero);
    const iban = buildIBAN(numero);

    ribData = { nom, numero, cle, iban };

    document.getElementById("rib-titulaire").textContent = nom.toUpperCase();
    document.getElementById("rib-numero").textContent = numero;
    document.getElementById("rib-cle").textContent = cle;
    document.getElementById("rib-iban").textContent = formatIBAN(iban);
    document.getElementById("rib-zone").style.display = "";
  } catch(e) {
    document.getElementById("rib-loading").style.display = "none";
    document.getElementById("rib-empty").style.display = "";
    flash("Erreur : " + e.message, "error");
  }
}

function copierRib() {
  const texte = `ZEN-PAY — RIB\nTitulaire : ${ribData.nom}\nNuméro de compte : ${ribData.numero}\nCode banque : CI005 | Guichet : 00001 | Clé : ${ribData.cle}\nIBAN : ${formatIBAN(ribData.iban)}\nBIC : ZENPCIAB`;
  navigator.clipboard.writeText(texte).then(() => flash("RIB copié dans le presse-papier !", "success"))
    .catch(() => flash("Impossible de copier", "error"));
}

function imprimerRib() {
  const win = window.open("", "_blank");
  win.document.write(`
    <html><head><title>Mon RIB — ZEN-PAY</title>
    <style>
      body{font-family:'Segoe UI',sans-serif;padding:40px;background:#fff;color:#1f2937;}
      h2{color:#166534;margin-bottom:4px;} .sub{color:#9ca3af;font-size:14px;margin-bottom:24px;}
      table{width:100%;border-collapse:collapse;margin-top:16px;}
      td,th{padding:12px 16px;border:1px solid #e5e7eb;font-size:14px;}
      th{background:#f9fafb;text-align:left;color:#6b7280;font-weight:600;}
      .mono{font-family:monospace;font-size:15px;font-weight:700;letter-spacing:.04em;}
      .footer{margin-top:32px;color:#9ca3af;font-size:11px;}
    </style></head>
    <body>
    <h2>ZEN-PAY — Relevé d'Identité Bancaire</h2>
    <div class="sub">Microfinance Digitale — Côte d'Ivoire</div>
    <table>
      <tr><th>Titulaire</th><td class="mono">${ribData.nom}</td></tr>
      <tr><th>Numéro de compte</th><td class="mono">${ribData.numero}</td></tr>
      <tr><th>Code banque</th><td class="mono">CI005</td></tr>
      <tr><th>Code guichet</th><td class="mono">00001</td></tr>
      <tr><th>Clé RIB</th><td class="mono">${ribData.cle}</td></tr>
      <tr><th>IBAN</th><td class="mono">${formatIBAN(ribData.iban)}</td></tr>
      <tr><th>BIC / SWIFT</th><td class="mono">ZENPCIAB</td></tr>
      <tr><th>Domiciliation</th><td>ZEN-PAY — Abidjan, Côte d'Ivoire</td></tr>
    </table>
    <div class="footer">Généré le ${new Date().toLocaleDateString("fr-FR")} — ZEN-PAY © ${new Date().getFullYear()}</div>
    <script>window.print();<\/script>
    </body></html>
  `);
  win.document.close();
}

init();
</script>
</body>
</html>
