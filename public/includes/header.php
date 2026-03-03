<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chemin CSS robuste (marche si /public est docroot, ou si le projet est servi via /ReserveTonPlat/public)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$publicBase = '';
if ($scriptName && preg_match('#^(.*?/public)(?:/|$)#', $scriptName, $m)) {
    $publicBase = $m[1];
} else {
    // Si le docroot est déjà /public (ex: /login.php), on reste à la racine web
    $publicBase = rtrim(dirname($scriptName), '/\\');
    if ($publicBase === '') {
        $publicBase = '/';
    }
}
$cssPath = rtrim($publicBase, '/\\') . '/assets/css/style.css';
if (!isset($basePath)) {
    $basePath = '';
}
$base = $basePath;
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? 'etudiant';

// Lien vers l'accueil ou le dashboard selon la connexion
$homeHref = $base . ($isLoggedIn ? 'dashboard.php' : 'index.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>RéserveTonPlat</title>
    
    <link rel="stylesheet" href="<?= $cssPath ?>">
</head>
<body class="has-layout">

    <header class="site-header">
        <div class="header-inner">
            <a href="<?= $homeHref ?>" class="logo">
                <img src="assets/img/logo_clear.png" alt="Logo" class="logo-img">
                <span class="logo-text">RéserveTonPlat</span>
            </a>

            <nav class="main-nav">
                <?php if ($isLoggedIn): ?>
                    <div class="user-info">

                        <?php if ($userRole === 'admin'): ?>
                            <a href="<?= $base ?>admin_scan.php" class="btn-scan-header">Scanner une réservation</a>
                        <?php else: ?>
                            <a href="<?= $base ?>dashboard.php">Mon menu</a>
                            <a href="<?= $base ?>logout.php">CampusEat</a>
                        <?php endif; ?>

                        <a href="<?= $base ?>logout.php" class="btn-logout">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="<?= $base ?>index.php">Accueil</a>    
                    <a href="<?= $base ?>login.php">Connexion</a>
                    <a href="<?= $base ?>register.php">Inscription</a>
                    <a href="<?= $base ?>campuseat.php">CampusEat</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>   

    <main class="page-container">