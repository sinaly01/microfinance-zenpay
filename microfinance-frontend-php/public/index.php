<?php
require_once __DIR__ . '/../config.php';
$page_title = 'ZEN-PAY — Votre argent, partout, simplement';
$current = 'index';
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

<!-- Navbar publique -->
<nav class="pub-nav">
  <a href="index.php" class="logo" style="padding:0;">
    <img src="img/logo.svg" alt="ZEN-PAY" style="height:34px;width:auto;">
  </a>
  <ul class="nav-links">
    <li><a href="index.php" class="active">Accueil</a></li>
    <li><a href="actualite.php">Actualités</a></li>
    <li><a href="apropos.php">À Propos</a></li>
  </ul>
  <div class="nav-cta">
    <a href="login.php" class="btn btn-ghost btn-sm">Se connecter</a>
    <a href="register.php" class="btn btn-primary btn-sm">S'inscrire</a>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div>
    <div class="hero-badge">🌍 Zone BCEAO · Franc CFA · 100% Mobile Money</div>
    <h1>Votre argent,<br><span>partout, simplement.</span></h1>
    <p>La microfinance digitale de la zone UMOA. Rechargez, retirez et transférez de l'argent depuis votre smartphone — Wave, Orange Money, MTN, Moov.</p>
    <div class="hero-actions">
      <a href="register.php" class="btn btn-primary btn-lg">Créer mon compte gratuitement</a>
      <a href="apropos.php" class="btn btn-lg" style="color:white;border:1.5px solid rgba(255,255,255,.35);background:rgba(255,255,255,.08)">En savoir plus</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><div class="val">150+</div><div class="lbl">Clients actifs</div></div>
      <div class="hero-stat"><div class="val">600+</div><div class="lbl">Transactions / mois</div></div>
      <div class="hero-stat"><div class="val">4</div><div class="lbl">Opérateurs MoMo</div></div>
      <div class="hero-stat"><div class="val">0 FCFA</div><div class="lbl">Pour démarrer</div></div>
    </div>
  </div>
</section>

<!-- Fonctionnalités -->
<section style="background:white;">
  <div class="section-title">
    <div class="section-eyebrow">Fonctionnalités</div>
    <h2>Tout ce dont vous avez besoin</h2>
    <p>Une plateforme complète pour gérer votre argent au quotidien</p>
  </div>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">📱</div>
      <h3>Mobile Money intégré</h3>
      <p>Rechargez votre compte et retirez vos fonds via Wave, Orange Money, MTN ou Moov. Push payment en temps réel.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔒</div>
      <h3>Sécurité renforcée</h3>
      <p>Authentification JWT, OTP sur chaque transaction, conformité KYC / BCEAO. Votre argent est protégé 24h/24.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">⚡</div>
      <h3>Virements instantanés</h3>
      <p>Transférez de l'argent entre clients ZEN-PAY en quelques secondes. Gratuit avec l'Offre 2.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📊</div>
      <h3>Tableau de bord complet</h3>
      <p>Visualisez votre solde, historique et statistiques mensuelles depuis n'importe quel appareil.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🧾</div>
      <h3>Relevé & RIB PDF</h3>
      <p>Téléchargez vos relevés sur la période de votre choix. RIB officiel disponible avec l'Offre 2.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🎧</div>
      <h3>Support en ligne</h3>
      <p>Soumettez une réclamation en un clic. Notre équipe répond à distance, sans file d'attente.</p>
    </div>
  </div>
</section>

<!-- Offres -->
<section style="background:var(--gray-50);">
  <div class="section-title">
    <div class="section-eyebrow">Tarification</div>
    <h2>Des offres pour tous les besoins</h2>
    <p>Commencez gratuitement, évoluez selon vos usages</p>
  </div>
  <div class="plans-grid">
    <div class="plan-card">
      <h3 style="font-weight:700;font-size:1.1rem;">Standard</h3>
      <p style="color:var(--muted);font-size:.85rem;margin-top:6px;">Pour démarrer</p>
      <div class="plan-price"><span class="amount">0 FCFA</span><span class="period"> / mois</span></div>
      <ul class="plan-features">
        <li>Versements & retraits MoMo (1,5 %)</li>
        <li>Virements internes (1 %)</li>
        <li>Tableau de bord complet</li>
        <li>Support par ticket</li>
      </ul>
      <a href="login.php" class="btn btn-outline w-full">Commencer</a>
    </div>
    <div class="plan-card featured">
      <div class="plan-badge">Populaire</div>
      <h3 style="font-weight:700;font-size:1.1rem;">Offre 1</h3>
      <p style="color:var(--muted);font-size:.85rem;margin-top:6px;">Pour les actifs</p>
      <div class="plan-price"><span class="amount">1 000 FCFA</span><span class="period"> / mois</span></div>
      <ul class="plan-features">
        <li>Frais MoMo réduits (1,0 %)</li>
        <li>Virements internes (1 %)</li>
        <li>Tableau de bord complet</li>
        <li>Support prioritaire</li>
      </ul>
      <a href="login.php" class="btn btn-primary w-full">Souscrire</a>
    </div>
    <div class="plan-card">
      <h3 style="font-weight:700;font-size:1.1rem;">Offre 2</h3>
      <p style="color:var(--muted);font-size:.85rem;margin-top:6px;">Pro / Entreprise</p>
      <div class="plan-price"><span class="amount">5 000 FCFA</span><span class="period"> / mois</span></div>
      <ul class="plan-features">
        <li>Frais MoMo réduits (1,0 %)</li>
        <li>Virements internes <strong>gratuits</strong></li>
        <li>Génération de RIB PDF</li>
        <li>Support VIP dédié</li>
      </ul>
      <a href="login.php" class="btn btn-outline w-full">Souscrire</a>
    </div>
  </div>
</section>

<!-- CTA -->
<section style="background:linear-gradient(135deg,var(--g800),var(--g600));text-align:center;color:white;">
  <div style="max-width:560px;margin:0 auto;">
    <h2 style="font-size:2rem;font-weight:800;margin-bottom:16px;">Prêt à commencer ?</h2>
    <p style="opacity:.8;margin-bottom:32px;">Rejoignez des centaines de clients qui font confiance à ZEN-PAY pour gérer leur argent.</p>
    <a href="register.php" class="btn btn-lg" style="background:white;color:var(--g700);font-weight:700;">Ouvrir mon compte maintenant</a>
  </div>
</section>

<!-- Footer -->
<footer class="pub-footer">
  <div class="footer-grid">
    <div class="footer-col">
      <div style="margin-bottom:12px;">
        <img src="img/logo.svg" alt="ZEN-PAY" style="height:30px;width:auto;filter:brightness(0) invert(1);">
      </div>
      <p style="font-size:.85rem;line-height:1.6;">Microfinance digitale de la zone BCEAO.</p>
    </div>
    <div class="footer-col">
      <h4>Produits</h4>
      <ul>
        <li><a href="#">Versement MoMo</a></li>
        <li><a href="#">Retrait MoMo</a></li>
        <li><a href="#">Virement interne</a></li>
        <li><a href="#">Abonnements</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Entreprise</h4>
      <ul>
        <li><a href="apropos.php">À Propos</a></li>
        <li><a href="actualite.php">Actualités</a></li>
        <li><a href="#">Carrières</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Conformité</h4>
      <ul>
        <li><a href="#">Réglementation BCEAO</a></li>
        <li><a href="#">Politique KYC</a></li>
        <li><a href="#">Conditions d'utilisation</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">&copy; <?= APP_YEAR ?> ZEN-PAY · Projet académique · Promotion 2026</div>
</footer>

</body>
</html>
