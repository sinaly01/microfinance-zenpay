<?php
/**
 * Navbar de l'espace client (vert).
 */
?>
<header class="navbar client">
    <a class="brand" href="client-dashboard.php">
        <img src="assets/logo.svg" alt="<?= h(APP_NAME) ?>">
    </a>
    <nav>
        <a href="client-dashboard.php"    class="<?= active_if('client-dashboard') ?>">Mes comptes</a>
        <a href="client-transactions.php" class="<?= active_if('client-transactions') ?>">Mes opérations</a>
    </nav>
    <div class="user-info">
        <span id="user-name" class="muted" style="color: rgba(255,255,255,0.9);"></span>
        <button onclick="logout()">Déconnexion</button>
    </div>
</header>
