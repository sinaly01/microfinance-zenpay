<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Validation de compte — ' . APP_NAME;
$sidebar_active = 'validation';

function buildUploadZone(string $side, string $type): string {
  return <<<HTML
    <!-- Zone d'upload : {$side} -->
    <div class="upload-zone" id="zone-{$side}">
      <!-- Icône centrale -->
      <div id="zone-icon-{$side}">
        <svg width="36" height="36" fill="none" stroke="var(--g400)" stroke-width="1.3" viewBox="0 0 24 24" style="display:block;margin:0 auto 8px;">
          <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <div style="font-size:.83rem;color:var(--muted);margin-bottom:14px;">Choisissez une option :</div>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
          <button type="button" onclick="event.stopPropagation();document.getElementById('file-camera-{$side}').click()"
                  style="display:flex;align-items:center;gap:6px;background:var(--primary);color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:.83rem;font-weight:700;cursor:pointer;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
              <circle cx="12" cy="13" r="4"/>
            </svg>
            Prendre en photo
          </button>
          <button type="button" onclick="event.stopPropagation();document.getElementById('file-doc-{$side}').click()"
                  style="display:flex;align-items:center;gap:6px;background:#fff;color:var(--primary);border:2px solid var(--primary);border-radius:8px;padding:9px 16px;font-size:.83rem;font-weight:700;cursor:pointer;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
            Charger PDF / image
          </button>
        </div>
        <div style="font-size:.72rem;color:var(--g400);margin-top:10px;">JPG · PNG · PDF · max 10 Mo — ou glisser-déposer</div>
      </div>
      <!-- Aperçu image -->
      <div id="preview-img-{$side}" style="display:none;flex-direction:column;align-items:center;gap:8px;">
        <img id="thumb-{$side}" src="" alt="aperçu" style="max-height:160px;max-width:100%;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.12);">
        <div style="display:flex;align-items:center;gap:8px;">
          <span id="name-{$side}" style="font-size:.8rem;color:var(--gray-700);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
          <button type="button" onclick="supprimerFichier('{$side}')" style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:.8rem;font-weight:700;">✕ Changer</button>
        </div>
      </div>
      <!-- Aperçu PDF -->
      <div id="preview-pdf-{$side}" style="display:none;flex-direction:column;align-items:center;gap:8px;">
        <div style="width:72px;height:72px;background:#fee2e2;border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;">
          <svg width="28" height="28" fill="none" stroke="#dc2626" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          <span style="font-size:.6rem;font-weight:800;color:#dc2626;letter-spacing:.05em;">PDF</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <span id="name-pdf-{$side}" style="font-size:.8rem;color:var(--gray-700);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
          <button type="button" onclick="supprimerFichier('{$side}')" style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:.8rem;font-weight:700;">✕ Changer</button>
        </div>
      </div>
    </div>
    <!-- Input caméra (mobile uniquement) -->
    <input type="file" id="file-camera-{$side}" accept="image/*" capture="environment" style="display:none;" onchange="previewFichier('{$side}', this)">
    <!-- Input document (PDF + image, tous appareils) -->
    <input type="file" id="file-doc-{$side}" accept="image/*,.pdf,application/pdf" style="display:none;" onchange="previewFichier('{$side}', this)">
    <!-- Input actif (référence unifiée pour l'upload) -->
    <input type="file" id="file-{$side}" style="display:none;">
    <button class="btn btn-primary" id="btn-{$side}" onclick="uploadFichier('{$side}')"
            style="margin-top:14px;display:none;height:40px;padding:0 24px;width:100%;">
      ✓ Envoyer ce document
    </button>
HTML;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .val-step { display:flex; align-items:flex-start; gap:16px; padding:20px; background:#fff; border:1px solid var(--border); border-radius:12px; margin-bottom:12px; transition:.2s; }
    .val-step.done  { border-color:#86efac; background:#f0fdf4; }
    .val-step.active{ border-color:var(--primary); background:#eff6ff; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    .val-step.waiting{ border-color:#fde047; background:#fefce8; }
    .step-num { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.9rem; flex-shrink:0; }
    .step-num.done    { background:#22c55e; color:#fff; }
    .step-num.active  { background:var(--primary); color:#fff; }
    .step-num.waiting { background:#eab308; color:#fff; }
    .step-num.locked  { background:var(--g200); color:var(--muted); }
    .upload-zone { border:2px dashed var(--g300); border-radius:10px; padding:20px 16px; text-align:center; cursor:default; transition:.2s; background:var(--g50); }
    .upload-zone:not(.uploaded):hover { border-color:var(--primary); background:#eff6ff; }
    .upload-zone.dragover { border-color:var(--primary); background:#dbeafe; border-style:solid; }
    .upload-zone.uploaded { border-color:#22c55e; background:#f0fdf4; }
    .progress-bar { height:8px; background:var(--g100); border-radius:100px; overflow:hidden; margin-top:16px; }
    .progress-fill { height:100%; border-radius:100px; transition:width .5s; background:linear-gradient(90deg,var(--primary),#06b6d4); }
  </style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar-client.php'; ?>
<div class="main-area">
<?php $topbar_title = 'Validation de compte'; include __DIR__ . '/../includes/topbar-client.php'; ?>
<main class="page-content">

  <div class="dash-header">
    <div>
      <h1>Activation de <span>votre compte</span></h1>
      <p style="color:var(--muted);font-size:.85rem;">Complétez ces étapes pour accéder à tous les services ZEN-PAY</p>
    </div>
    <div id="statut-badge"></div>
  </div>

  <!-- Barre de progression -->
  <div class="card card-pad" style="margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
      <span style="font-size:.85rem;font-weight:600;color:var(--gray-700);">Progression du dossier</span>
      <span id="progress-pct" style="font-size:.85rem;font-weight:700;color:var(--primary);">0%</span>
    </div>
    <div class="progress-bar"><div class="progress-fill" id="progress-fill" style="width:0%"></div></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;" id="step-pills"></div>
  </div>

  <!-- Étape 1 : Recto CNI -->
  <div class="val-step active" id="block-recto">
    <div class="step-num active" id="num-recto">1</div>
    <div style="flex:1;">
      <div style="font-weight:700;font-size:.95rem;margin-bottom:4px;">Recto de votre pièce d'identité</div>
      <div style="font-size:.82rem;color:var(--muted);margin-bottom:14px;">CNI, passeport ou permis de conduire — face avant</div>
      <?php echo buildUploadZone('recto', 'CNI_RECTO'); ?>
    </div>
  </div>

  <!-- Étape 2 : Verso CNI -->
  <div class="val-step" id="block-verso">
    <div class="step-num locked" id="num-verso">2</div>
    <div style="flex:1;">
      <div style="font-weight:700;font-size:.95rem;margin-bottom:4px;">Verso de votre pièce d'identité</div>
      <div style="font-size:.82rem;color:var(--muted);margin-bottom:14px;">Face arrière de la même pièce d'identité</div>
      <?php echo buildUploadZone('verso', 'CNI_VERSO'); ?>
    </div>
  </div>

  <!-- Étape 3 : En attente -->
  <div class="val-step" id="block-attente">
    <div class="step-num locked" id="num-attente">3</div>
    <div style="flex:1;">
      <div style="font-weight:700;font-size:.95rem;margin-bottom:4px;">Validation par un gestionnaire</div>
      <div style="font-size:.82rem;color:var(--muted);">Une fois vos documents envoyés, un gestionnaire ZEN-PAY vérifiera votre dossier sous 24-48h ouvrées.</div>
    </div>
  </div>

  <!-- Message compte validé (si statutKyc = VALIDE) -->
  <div id="block-valide" style="display:none;" class="card card-pad" style="border-color:#86efac;background:#f0fdf4;">
    <div style="text-align:center;padding:24px 0;">
      <div style="font-size:3rem;margin-bottom:12px;">✅</div>
      <h2 style="font-size:1.3rem;font-weight:800;color:#166534;">Compte activé !</h2>
      <p style="color:#15803d;margin-bottom:20px;">Votre identité a été vérifiée avec succès. Vous pouvez accéder à tous les services ZEN-PAY.</p>
      <a href="client-dashboard.php" class="btn btn-primary">Accéder à mon tableau de bord</a>
    </div>
  </div>

</main>
</div>
</div>
<script src="js/api.js"></script>
<script>
requireAuth();
const API_URL = localStorage.getItem("mf_api_base") || (
  (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")
    ? "http://localhost:8080" : ""
);
let myClientId = null;
let rectoOk = false;
let versoOk = false;

async function init() {
  const me = await api.get("/api/auth/me");
  if (!me.idClient) { window.location.href = "login.php"; return; }
  myClientId = me.idClient;

  const initial = (me.prenom || "C")[0].toUpperCase();
  const elAv = document.getElementById("sb-avatar"); if (elAv) elAv.textContent = initial;
  const elName = document.getElementById("sb-name"); if (elName) elName.textContent = (me.prenom||"") + " " + (me.nom||"");
  const elFullname = document.getElementById("user-fullname"); if (elFullname) elFullname.textContent = (me.prenom||"") + " " + (me.nom||"");

  const statut = me.statutKyc || "PENDING";

  // Si déjà validé → afficher succès
  if (statut === "VALIDE") {
    ["block-recto","block-verso","block-attente"].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = "none";
    });
    document.getElementById("block-valide").style.display = "";
    setProgress(100);
    return;
  }

  // Charger les documents déjà uploadés
  try {
    const docs = await api.get("/api/kyc/documents/" + myClientId);
    rectoOk = docs.some(d => d.typeDocument === "CNI_RECTO");
    versoOk = docs.some(d => d.typeDocument === "CNI_VERSO");
  } catch(e) {}

  // Statut badge
  const badges = {
    PENDING: {label:"En attente de documents", cls:"badge-kyc-pending"},
    DOCUMENTS_SOUMIS: {label:"Documents envoyés — en révision", cls:"badge-pending"},
    REJETE: {label:"Dossier rejeté", cls:"badge-failed"},
  };
  const b = badges[statut] || {label:statut, cls:"badge-pending"};
  document.getElementById("statut-badge").innerHTML = `<span class="badge ${b.cls}" style="font-size:.85rem;padding:6px 14px;">${b.label}</span>`;

  if (statut === "DOCUMENTS_SOUMIS") {
    afficherEtatAttenteValidation();
  } else if (statut === "REJETE") {
    afficherEtatRejete();
  } else {
    refreshUI();
  }
}

function refreshUI() {
  // Recto
  if (rectoOk) {
    marquerFait("block-recto","num-recto");
    setProgress(versoOk ? 66 : 33);
  }
  // Verso
  if (versoOk) {
    marquerFait("block-verso","num-verso");
    setProgress(rectoOk ? 66 : 33);
  }
  if (rectoOk && versoOk) {
    setProgress(66);
    marquerActif("block-attente","num-attente","waiting");
  }
  if (!rectoOk) marquerActif("block-recto","num-recto","active");
  else if (!versoOk) marquerActif("block-verso","num-verso","active");

  updatePills();
}

function afficherEtatAttenteValidation() {
  marquerFait("block-recto","num-recto");
  marquerFait("block-verso","num-verso");
  marquerActif("block-attente","num-attente","waiting");
  setProgress(80);
  updatePills();
}

function afficherEtatRejete() {
  document.getElementById("block-recto").classList.remove("done","active");
  document.getElementById("block-recto").style.borderColor = "var(--danger)";
  document.getElementById("block-recto").style.background = "#fff1f2";
  setProgress(0);
  const msg = document.createElement("div");
  msg.style.cssText = "background:#fff1f2;border:1px solid #fca5a5;border-radius:8px;padding:12px;font-size:.84rem;color:#dc2626;margin-top:12px;";
  msg.innerHTML = "⚠️ Votre dossier a été rejeté. Veuillez soumettre à nouveau vos documents en vous assurant qu'ils sont lisibles et complets.";
  document.querySelector(".page-content").insertBefore(msg, document.getElementById("block-recto"));
}

function marquerFait(blockId, numId) {
  const block = document.getElementById(blockId);
  const num = document.getElementById(numId);
  block.classList.remove("active","waiting"); block.classList.add("done");
  num.className = "step-num done"; num.textContent = "✓";
  // Remplacer le contenu de la zone par un message de succès
  const uploadZone = block.querySelector(".upload-zone");
  if (uploadZone) {
    uploadZone.innerHTML = `<div style="color:#166534;font-weight:700;font-size:.9rem;padding:12px 0;">✅ Document reçu avec succès</div>`;
    uploadZone.classList.add("uploaded");
    uploadZone.style.cursor = "default";
  }
  const btn = block.querySelector("button[onclick*='uploadFichier']");
  if (btn) btn.style.display = "none";
}

function marquerActif(blockId, numId, type) {
  const block = document.getElementById(blockId);
  const num = document.getElementById(numId);
  block.classList.add(type);
  num.className = "step-num " + type;
  if (type === "waiting") num.textContent = "⏳";
}

function setProgress(pct) {
  document.getElementById("progress-fill").style.width = pct + "%";
  document.getElementById("progress-pct").textContent = pct + "%";
}

function updatePills() {
  const steps = [
    {label:"Recto CNI", done: rectoOk},
    {label:"Verso CNI", done: versoOk},
    {label:"Validation gestionnaire", done: false},
  ];
  document.getElementById("step-pills").innerHTML = steps.map(s =>
    `<span style="font-size:.75rem;padding:3px 10px;border-radius:100px;background:${s.done?"#dcfce7":"var(--g100)"};color:${s.done?"#166534":"var(--muted)"};font-weight:600;">${s.done?"✓ ":""}${s.label}</span>`
  ).join("");
}

/* ── Upload ── */
function previewFichier(side, input) {
  if (!input.files.length) return;
  const file = input.files[0];

  // Vérification taille (10 Mo max)
  if (file.size > 10 * 1024 * 1024) {
    flash("Fichier trop volumineux (max 10 Mo).", "error");
    input.value = "";
    return;
  }

  // Copier le fichier dans l'input unifié
  const unified = document.getElementById("file-" + side);
  try {
    const dt = new DataTransfer();
    dt.items.add(file);
    unified.files = dt.files;
  } catch(e) { /* fallback : garder référence dans input original */ }

  // Masquer l'icône centrale
  document.getElementById("zone-icon-" + side).style.display = "none";
  document.getElementById("zone-" + side).classList.add("uploaded");
  document.getElementById("btn-" + side).style.display = "block";

  const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");

  if (isPdf) {
    // Aperçu PDF : icône + nom fichier
    document.getElementById("preview-img-" + side).style.display = "none";
    document.getElementById("preview-pdf-" + side).style.display = "flex";
    document.getElementById("name-pdf-" + side).textContent = file.name + " · " + (file.size / 1024).toFixed(0) + " Ko";
  } else {
    // Aperçu image : miniature
    document.getElementById("preview-pdf-" + side).style.display = "none";
    document.getElementById("preview-img-" + side).style.display = "flex";
    document.getElementById("name-" + side).textContent = file.name + " · " + (file.size / 1024).toFixed(0) + " Ko";
    const reader = new FileReader();
    reader.onload = e => { document.getElementById("thumb-" + side).src = e.target.result; };
    reader.readAsDataURL(file);
  }
}

function supprimerFichier(side) {
  ["file-" + side, "file-camera-" + side, "file-doc-" + side].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  document.getElementById("preview-img-" + side).style.display = "none";
  document.getElementById("preview-pdf-" + side).style.display = "none";
  document.getElementById("zone-icon-" + side).style.display = "";
  document.getElementById("btn-" + side).style.display = "none";
  document.getElementById("zone-" + side).classList.remove("uploaded");
}

async function uploadFichier(side) {
  // Chercher le fichier dans l'input unifié ou les deux sources
  let inputRef = document.getElementById("file-" + side);
  if (!inputRef.files || !inputRef.files.length) {
    inputRef = document.getElementById("file-camera-" + side);
  }
  if (!inputRef.files || !inputRef.files.length) {
    inputRef = document.getElementById("file-doc-" + side);
  }
  if (!inputRef || !inputRef.files || !inputRef.files.length) {
    flash("Sélectionnez un fichier.", "error"); return;
  }
  const btn = document.getElementById("btn-" + side);
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Envoi en cours…';

  const formData = new FormData();
  formData.append("file", inputRef.files[0]);
  formData.append("typeDocument", side === "recto" ? "CNI_RECTO" : "CNI_VERSO");

  try {
    const token = localStorage.getItem("mf_jwt_token");
    const res = await fetch(API_URL + "/api/kyc/upload/" + myClientId, {
      method: "POST",
      headers: { "Authorization": "Bearer " + token },
      body: formData
    });
    if (!res.ok) {
      const data = await res.json().catch(() => ({}));
      throw new Error(data.error || "Erreur serveur");
    }
    flash("Document envoyé avec succès !", "success");
    if (side === "recto") { rectoOk = true; marquerFait("block-recto","num-recto"); }
    else                  { versoOk = true; marquerFait("block-verso","num-verso"); }
    if (rectoOk && versoOk) {
      setProgress(66);
      marquerActif("block-attente","num-attente","waiting");
      flash("Les deux documents ont été envoyés. Un gestionnaire va vérifier votre dossier.", "success");
    } else {
      refreshUI();
    }
    updatePills();
  } catch(e) {
    flash("Erreur : " + e.message, "error");
    btn.disabled = false;
    btn.innerHTML = "✓ Envoyer ce document";
  }
}

// Drag & drop sur la zone
["recto","verso"].forEach(side => {
  const zone = document.getElementById("zone-" + side);
  zone.addEventListener("dragover", e => { e.preventDefault(); zone.classList.add("dragover"); });
  zone.addEventListener("dragleave", () => zone.classList.remove("dragover"));
  zone.addEventListener("drop", e => {
    e.preventDefault();
    zone.classList.remove("dragover");
    const files = e.dataTransfer.files;
    if (!files.length) return;
    // Créer un input factice pour déclencher previewFichier
    const fakeInput = { files: files };
    previewFichier(side, fakeInput);
    // Stocker le fichier dans file-doc-{side}
    try {
      const realInput = document.getElementById("file-doc-" + side);
      const dt = new DataTransfer();
      dt.items.add(files[0]);
      realInput.files = dt.files;
    } catch(e) {}
  });
});

init();
</script>
</body>
</html>
