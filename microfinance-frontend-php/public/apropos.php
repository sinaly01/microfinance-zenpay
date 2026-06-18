<?php
require_once __DIR__ . '/../config.php';
$page_title = 'À Propos — ZEN-PAY';
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

<nav class="pub-nav">
  <a href="index.php" class="logo"><div class="logo-icon">Z</div>ZEN-PAY</a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="actualite.php">Actualités</a></li>
    <li><a href="apropos.php" class="active">À Propos</a></li>
  </ul>
  <div class="nav-cta">
    <a href="login.php" class="btn btn-ghost btn-sm">Se connecter</a>
    <a href="register.php" class="btn btn-primary btn-sm">S'inscrire</a>
  </div>
</nav>

<!-- Hero -->
<div class="about-hero">
  <div class="section-eyebrow" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);">À Propos</div>
  <h1 style="margin-top:12px;">Qui sommes-nous ?</h1>
  <p>ZEN-PAY est une plateforme de microfinance 100% digitale conçue pour la zone BCEAO. Nous croyons que les services financiers doivent être accessibles à tous, partout, depuis un simple smartphone.</p>
</div>

<!-- Mission -->
<section style="background:white;">
  <div style="max-width:800px;margin:0 auto;text-align:center;">
    <div class="section-eyebrow">Notre Mission</div>
    <h2 style="font-size:2rem;font-weight:800;margin:12px 0 16px;">Démocratiser la finance en Afrique de l'Ouest</h2>
    <p style="color:var(--gray-600);line-height:1.8;font-size:1.02rem;">
      En Afrique de l'Ouest, des millions de personnes n'ont pas accès aux services bancaires traditionnels. ZEN-PAY change cela en proposant une microfinance entièrement numérique, régulée par la BCEAO, intégrée aux opérateurs Mobile Money les plus populaires de la région.
    </p>
    <p style="color:var(--gray-600);line-height:1.8;font-size:1.02rem;margin-top:16px;">
      Notre plateforme permet à chaque client de créer son compte en ligne, de vérifier son identité depuis chez lui (KYC numérique), et d'effectuer toutes ses opérations — versements, retraits, virements — depuis son téléphone.
    </p>
  </div>
</section>

<!-- Valeurs -->
<section style="background:var(--gray-50);">
  <div class="section-title">
    <div class="section-eyebrow">Nos Valeurs</div>
    <h2>Ce qui nous guide</h2>
  </div>
  <div class="values-grid">
    <div class="value-card">
      <div style="font-size:1.8rem;">🔒</div>
      <h3>Sécurité</h3>
      <p>JWT, OTP, KYC, audit logs immuables, Kill Switch. La protection des fonds est notre priorité absolue.</p>
    </div>
    <div class="value-card">
      <div style="font-size:1.8rem;">⚖️</div>
      <h3>Conformité</h3>
      <p>Plafonds BCEAO, reportings réglementaires, lutte anti-blanchiment. Nous respectons chaque règle de la zone UMOA.</p>
    </div>
    <div class="value-card">
      <div style="font-size:1.8rem;">🌍</div>
      <h3>Inclusion</h3>
      <p>Des offres accessibles à partir de 0 FCFA. Chaque habitant de la zone doit pouvoir accéder à des services financiers.</p>
    </div>
    <div class="value-card">
      <div style="font-size:1.8rem;">⚡</div>
      <h3>Simplicité</h3>
      <p>Une interface claire, des transactions en quelques secondes, un support en ligne réactif. La finance doit être simple.</p>
    </div>
  </div>
</section>

<!-- Équipe -->
<section style="background:white;">
  <div class="section-title">
    <div class="section-eyebrow">L'équipe</div>
    <h2>Qui a construit ZEN-PAY ?</h2>
    <p>Un groupe de 5 étudiants passionnés — Promotion 2026</p>
  </div>
  <div class="team-grid">
    <div class="team-card">
      <div class="team-avatar" style="background:var(--g600);">AD</div>
      <h3>Amadou Diallo</h3>
      <p>Lead Backend · Spring Boot</p>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:#2563eb;">FK</div>
      <h3>Fatou Koné</h3>
      <p>Frontend · PHP / CSS</p>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:#7c3aed;">MT</div>
      <h3>Moussa Traoré</h3>
      <p>Base de données · Oracle</p>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:#b45309;">AC</div>
      <h3>Aissatou Camara</h3>
      <p>Sécurité · Spring Security</p>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:#be185d;">IB</div>
      <h3>Ibrahim Barry</h3>
      <p>DevOps · Docker</p>
    </div>
  </div>
</section>

<!-- Stack technique -->
<section style="background:var(--g50);">
  <div class="section-title">
    <div class="section-eyebrow">Technologies</div>
    <h2>Notre stack technique</h2>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;max-width:900px;margin:0 auto;">
    <?php
    $techs = [
      ['☕', 'Java 21', 'Langage backend'],
      ['🍃', 'Spring Boot 3.4.5', 'Framework API REST'],
      ['🗄️', 'Oracle XE 21c', 'Base de données'],
      ['🐘', 'PHP 8.2', 'Frontend templating'],
      ['🐳', 'Docker Compose', 'Orchestration'],
      ['🔑', 'JWT + Spring Security', 'Authentification'],
    ];
    foreach ($techs as $t): ?>
      <div style="background:white;border-radius:var(--r-lg);padding:20px;border:1px solid var(--g200);text-align:center;">
        <div style="font-size:2rem;margin-bottom:8px;"><?= $t[0] ?></div>
        <div style="font-weight:700;font-size:.9rem;"><?= $t[1] ?></div>
        <div style="font-size:.8rem;color:var(--muted);margin-top:4px;"><?= $t[2] ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Contact CTA -->
<section style="background:linear-gradient(135deg,var(--g800),var(--g600));text-align:center;color:white;">
  <h2 style="font-size:1.8rem;font-weight:800;margin-bottom:12px;">Rejoignez ZEN-PAY</h2>
  <p style="opacity:.8;margin-bottom:28px;">Ouvrez votre compte en moins de 2 minutes et commencez à gérer votre argent de manière moderne.</p>
  <a href="login.php" class="btn btn-lg" style="background:white;color:var(--g700);font-weight:700;">Créer mon compte</a>
</section>

<footer class="pub-footer">
  <div class="footer-bottom">&copy; <?= APP_YEAR ?> ZEN-PAY · Projet académique · Promotion 2026</div>
</footer>

</body>
</html>
