<?php
/**
 * Header HTML commun à toutes les pages.
 * Variables attendues (à définir avant l'include) :
 *   $page_title  (string)  — titre de la page
 *   $page_class  (string)  — classe CSS du <body> (optionnel)
 *   $extra_head  (string)  — HTML additionnel dans le <head> (optionnel)
 */
$title = $page_title ?? APP_NAME;
$cls   = $page_class ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title) ?></title>
    <meta name="description" content="<?= h(APP_NAME . ' — ' . APP_SLOGAN) ?>">
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">
    <link rel="stylesheet" href="css/style.css">
    <?= $extra_head ?? '' ?>
</head>
<body class="<?= h($cls) ?>">
<div class="bg-mesh"></div>
