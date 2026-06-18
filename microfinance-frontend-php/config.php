<?php
/**
 * Configuration globale ZEN-PAY
 */

// URL du backend Spring Boot (à adapter pour le déploiement réseau)
define('API_BASE_URL', 'http://localhost:8080');

// Nom de l'application
define('APP_NAME', 'ZEN-PAY');
define('APP_SLOGAN', 'Votre argent, partout, simplement.');

// Année courante (utilisée dans le footer)
define('APP_YEAR', date('Y'));

// Démarrage de session PHP (utile si on veut stocker le JWT côté serveur)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Détecte la page courante depuis le nom du fichier appelant.
 * Utilisé pour mettre la classe "active" sur le bon lien de la navbar.
 */
function current_page(): string {
    return basename($_SERVER['PHP_SELF'], '.php');
}

/**
 * Renvoie 'active' si la page courante correspond, sinon vide.
 */
function active_if(string $page): string {
    return current_page() === $page ? 'active' : '';
}

/**
 * Échappement HTML rapide.
 */
function h(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
