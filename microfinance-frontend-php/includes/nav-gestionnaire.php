<?php
/**
 * Navbar de l'espace gestionnaire (bleu Zen).
 * Utilise active_if() pour surligner le lien actif selon la page courante.
 */
?>
<header class="navbar">
    <a class="brand" href="dashboard.php">
        <img src="assets/logo.svg" alt="<?= h(APP_NAME) ?>">
    </a>
    <nav>
        <a href="dashboard.php"    class="<?= active_if('dashboard') ?>">Tableau de bord</a>
        <a href="clients.php"      class="<?= active_if('clients') ?>">Clients</a>
        <a href="comptes.php"      class="<?= active_if('comptes') ?>">Comptes</a>
        <a href="transactions.php" class="<?= active_if('transactions') ?>">Transactions</a>
    </nav>
    <div class="user-info">
        <span id="user-email" class="muted" style="color: rgba(255,255,255,0.85);"></span>
        <button onclick="logout()">Déconnexion</button>
    </div>
</header>
