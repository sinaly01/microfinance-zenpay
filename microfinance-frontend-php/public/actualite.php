<?php
require_once __DIR__ . '/../config.php';
$page_title = 'Actualités — ZEN-PAY';
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
    <li><a href="actualite.php" class="active">Actualités</a></li>
    <li><a href="apropos.php">À Propos</a></li>
  </ul>
  <div class="nav-cta">
    <a href="login.php" class="btn btn-ghost btn-sm">Se connecter</a>
    <a href="register.php" class="btn btn-primary btn-sm">S'inscrire</a>
  </div>
</nav>

<!-- Header actualités -->
<div style="background:linear-gradient(135deg,var(--g800),var(--g600));padding:56px 24px;text-align:center;color:white;">
  <div class="section-eyebrow" style="background:rgba(255,255,255,.15);color:white;">Actualités</div>
  <h1 style="font-size:2.2rem;font-weight:800;margin:12px 0;">Les dernières nouvelles de ZEN-PAY</h1>
  <p style="opacity:.8;max-width:480px;margin:0 auto;">Restez informé des évolutions de notre plateforme, de la réglementation BCEAO et des innovations Mobile Money.</p>
</div>

<!-- Filtres -->
<section style="background:white;padding:24px;border-bottom:1px solid var(--border);">
  <div style="max-width:1100px;margin:0 auto;display:flex;gap:12px;flex-wrap:wrap;">
    <button class="btn btn-primary btn-sm">Tout</button>
    <button class="btn btn-ghost btn-sm">Produits</button>
    <button class="btn btn-ghost btn-sm">Réglementation</button>
    <button class="btn btn-ghost btn-sm">Mobile Money</button>
    <button class="btn btn-ghost btn-sm">Sécurité</button>
  </div>
</section>

<!-- Articles -->
<section style="background:var(--gray-50);">
  <div class="news-grid">

    <div class="news-card">
      <div class="news-img" style="background:linear-gradient(135deg,var(--g700),var(--g500));">💳</div>
      <div class="news-body">
        <span class="news-tag">Produits</span>
        <h3>ZEN-PAY lance ses offres d'abonnement avec des frais réduits</h3>
        <p>Découvrez nos nouvelles offres mensuelle à 1 000 FCFA et professionnelle à 5 000 FCFA pour des transactions moins chères.</p>
        <div class="news-meta">
          <span class="news-date">3 juin 2026</span>
          <a href="#" class="news-link">Lire →</a>
        </div>
      </div>
    </div>

    <div class="news-card">
      <div class="news-img" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);">🏦</div>
      <div class="news-body">
        <span class="news-tag">Réglementation</span>
        <h3>Conformité BCEAO : ZEN-PAY renforce son dispositif KYC</h3>
        <p>Pour respecter les nouvelles directives de la Banque Centrale, ZEN-PAY a mis en place un processus de vérification d'identité numérique.</p>
        <div class="news-meta">
          <span class="news-date">28 mai 2026</span>
          <a href="#" class="news-link">Lire →</a>
        </div>
      </div>
    </div>

    <div class="news-card">
      <div class="news-img" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">📲</div>
      <div class="news-body">
        <span class="news-tag">Mobile Money</span>
        <h3>Intégration Moov Money : 4 opérateurs disponibles sur ZEN-PAY</h3>
        <p>ZEN-PAY s'intègre désormais avec Moov Money en plus de Wave, Orange Money et MTN MoMo pour une couverture maximale.</p>
        <div class="news-meta">
          <span class="news-date">15 mai 2026</span>
          <a href="#" class="news-link">Lire →</a>
        </div>
      </div>
    </div>

    <div class="news-card">
      <div class="news-img" style="background:linear-gradient(135deg,#b45309,#f59e0b);">🔑</div>
      <div class="news-body">
        <span class="news-tag">Sécurité</span>
        <h3>Kill Switch et authentification MFA : la nouvelle couche de sécurité</h3>
        <p>Pour protéger les fonds de nos clients, ZEN-PAY a implémenté un système de gel d'urgence et une authentification multi-facteurs.</p>
        <div class="news-meta">
          <span class="news-date">10 mai 2026</span>
          <a href="#" class="news-link">Lire →</a>
        </div>
      </div>
    </div>

    <div class="news-card">
      <div class="news-img" style="background:linear-gradient(135deg,#064e3b,#10b981);">📈</div>
      <div class="news-body">
        <span class="news-tag">Produits</span>
        <h3>RIB numérique : téléchargez votre relevé d'identité bancaire</h3>
        <p>Les clients Offre 2 peuvent désormais générer et télécharger leur RIB au format PDF depuis leur tableau de bord.</p>
        <div class="news-meta">
          <span class="news-date">5 mai 2026</span>
          <a href="#" class="news-link">Lire →</a>
        </div>
      </div>
    </div>

    <div class="news-card">
      <div class="news-img" style="background:linear-gradient(135deg,#831843,#ec4899);">🌍</div>
      <div class="news-body">
        <span class="news-tag">Mobile Money</span>
        <h3>ZEN-PAY disponible dans 12 villes de l'Afrique de l'Ouest</h3>
        <p>Notre service est désormais accessible depuis Abidjan, Dakar, Bamako, Ouagadougou, Conakry et 7 autres villes de la zone UMOA.</p>
        <div class="news-meta">
          <span class="news-date">1 mai 2026</span>
          <a href="#" class="news-link">Lire →</a>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Footer -->
<footer class="pub-footer">
  <div class="footer-bottom">&copy; <?= APP_YEAR ?> ZEN-PAY · Projet académique · Promotion 2026</div>
</footer>

</body>
</html>
