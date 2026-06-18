<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Simulateur de frais — ZEN-PAY';
$sidebar_active = 'simulateur';
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
<?php $topbar_title = 'Simulateur de frais'; include __DIR__ . '/../includes/topbar-client.php'; ?>

    <main class="page-content">
      <div class="feature-layout">

        <div class="feature-header">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <div style="width:44px;height:44px;background:var(--g100);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">🧮</div>
            <div>
              <h2 style="margin:0;">Simulateur de frais</h2>
              <p style="margin:0;">Estimez les frais avant de faire votre transaction</p>
            </div>
          </div>
        </div>

        <!-- Carte abonnement actuel -->
        <div class="card card-pad mb-4" id="offre-card" style="background:var(--g50);border-color:var(--g200);">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
              <div style="font-size:.8rem;color:var(--muted);">Votre offre actuelle</div>
              <div style="font-weight:800;font-size:1.2rem;color:var(--g700);" id="offre-nom">STANDARD</div>
            </div>
            <div style="text-align:right;">
              <div style="font-size:.8rem;color:var(--muted);">Frais MoMo</div>
              <div style="font-weight:700;color:var(--g700);" id="offre-frais-momo">1,5 %</div>
            </div>
            <div style="text-align:right;">
              <div style="font-size:.8rem;color:var(--muted);">Frais virement</div>
              <div style="font-weight:700;color:var(--g700);" id="offre-frais-vir">1,0 %</div>
            </div>
          </div>
        </div>

        <div class="card card-pad">
          <h3 style="font-size:1rem;font-weight:700;margin-bottom:20px;">Calculer mes frais</h3>

          <div class="form-group">
            <label class="form-label">Type d'opération</label>
            <select class="form-control form-select" id="type-op">
              <option value="MOMO">Versement / Retrait Mobile Money</option>
              <option value="VIREMENT">Virement interne (entre clients ZEN-PAY)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Opérateur Mobile Money</label>
            <select class="form-control form-select" id="operateur" style="display:block;">
              <option>Wave</option>
              <option>Orange Money</option>
              <option>MTN MoMo</option>
              <option>Moov Money</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Montant à envoyer</label>
            <div class="input-group">
              <input class="form-control" type="number" id="montant" placeholder="ex: 50000" min="500" step="500">
              <span class="input-addon">FCFA</span>
            </div>
          </div>

          <button class="btn btn-primary w-full" onclick="simuler()">Calculer les frais</button>
        </div>

        <!-- Résultat -->
        <div class="result-card" id="result-card" style="display:none;">
          <h3 style="font-size:1rem;font-weight:700;margin-bottom:16px;color:var(--g800);">Détail des frais</h3>
          <div class="result-row">
            <span class="result-label">Montant envoyé</span>
            <span class="result-value" id="res-montant">—</span>
          </div>
          <div class="result-row">
            <span class="result-label">Taux de commission</span>
            <span class="result-value" id="res-taux">—</span>
          </div>
          <div class="result-row">
            <span class="result-label">Frais prélevés</span>
            <span class="result-value" style="color:var(--danger);" id="res-frais">—</span>
          </div>
          <div class="result-row" style="padding-top:12px;margin-top:4px;">
            <span class="result-label" style="font-weight:700;font-size:1rem;">Total débité</span>
            <span class="result-value result-total" id="res-total">—</span>
          </div>
          <div style="margin-top:16px;padding:12px;background:white;border-radius:var(--r);font-size:.82rem;color:var(--gray-600);line-height:1.6;" id="res-conseil">
          </div>
        </div>

        <!-- Tableau comparatif offres -->
        <div class="card mt-6">
          <div class="tx-table-header">
            <div>
              <div class="tx-table-title">Comparatif des offres</div>
              <div class="tx-table-sub">Économisez en choisissant la bonne offre</div>
            </div>
          </div>
          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th>Offre</th><th>Prix mensuel</th><th>Frais MoMo</th><th>Virement interne</th><th>RIB PDF</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Standard</strong></td>
                  <td>Gratuit</td>
                  <td>1,5 %</td>
                  <td>1,0 %</td>
                  <td><span style="color:var(--danger);">✗</span></td>
                </tr>
                <tr style="background:var(--g50);">
                  <td><strong style="color:var(--primary);">Offre 1</strong></td>
                  <td>1 000 FCFA</td>
                  <td>1,0 %</td>
                  <td>1,0 %</td>
                  <td><span style="color:var(--danger);">✗</span></td>
                </tr>
                <tr>
                  <td><strong>Offre 2</strong></td>
                  <td>5 000 FCFA</td>
                  <td>1,0 %</td>
                  <td><span style="color:var(--g600);font-weight:700;">Gratuit</span></td>
                  <td><span style="color:var(--g600);">✓</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<script src="js/api.js"></script>
<script>
requireAuth();

let clientId = null;
let tauxMomo = 1.5, tauxVir = 1.0;

// Grille de frais locale (fallback si API non disponible)
const fraisLocaux = {
  STANDARD: { momo: 1.5, vir: 1.0 },
  OFFRE_1:  { momo: 1.0, vir: 1.0 },
  OFFRE_2:  { momo: 1.0, vir: 0.0 },
};

async function init() {
  try {
    const me = await api.get("/api/auth/me");
    document.getElementById("user-avatar").textContent = (me.prenom||"?")[0].toUpperCase();
    clientId = me.idClient;

    if (me.offreAbonnement) {
      const offre = me.offreAbonnement;
      document.getElementById("offre-nom").textContent = offre.nomOffre || "STANDARD";
      tauxMomo = offre.pourcentageFraisMomo || 1.5;
      tauxVir  = offre.fraisVirementInterne || 1.0;
      document.getElementById("offre-frais-momo").textContent = tauxMomo.toFixed(1) + " %";
      document.getElementById("offre-frais-vir").textContent  = tauxVir.toFixed(1) + " %";
    }
  } catch(e) { /* silencieux */ }
}

function simuler() {
  const montant = parseFloat(document.getElementById("montant").value);
  const typeOp  = document.getElementById("type-op").value;

  if (!montant || montant < 500) { flash("Montant minimum : 500 FCFA", "error"); return; }

  const taux  = typeOp === "VIREMENT" ? tauxVir : tauxMomo;
  const frais = montant * taux / 100;
  const total = montant + frais;

  document.getElementById("res-montant").textContent = montant.toLocaleString("fr-FR") + " FCFA";
  document.getElementById("res-taux").textContent    = taux.toFixed(2) + " %";
  document.getElementById("res-frais").textContent   = frais.toLocaleString("fr-FR", {minimumFractionDigits:0, maximumFractionDigits:0}) + " FCFA";
  document.getElementById("res-total").textContent   = total.toLocaleString("fr-FR", {minimumFractionDigits:0, maximumFractionDigits:0}) + " FCFA";

  let conseil = "💡 ";
  if (typeOp === "VIREMENT" && tauxVir === 0) {
    conseil += "Bonne nouvelle ! Avec votre Offre 2, ce virement est entièrement gratuit.";
  } else if (taux > 1.0) {
    conseil += "Passez à l'Offre 1 (1 000 FCFA/mois) pour réduire vos frais à 1,0 %.";
  } else {
    conseil += "Vous bénéficiez du meilleur taux disponible sur ZEN-PAY.";
  }
  document.getElementById("res-conseil").textContent = conseil;

  document.getElementById("result-card").style.display = "block";
  document.getElementById("result-card").scrollIntoView({ behavior: "smooth", block: "nearest" });
}

// Cacher l'opérateur si virement
document.getElementById("type-op").addEventListener("change", function() {
  document.getElementById("operateur").parentElement.style.display =
    this.value === "VIREMENT" ? "none" : "block";
});

init();
</script>
</body>
</html>
