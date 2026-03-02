<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gestion dynamique du chemin pour le CSS et les liens
// Déterminer le base path en fonction de la structure des répertoires
if (!isset($basePath)) {
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    // Si le script est dans /public/ ou /public/includes/, le base path est vide
    // Depuis ces emplacements, 'assets/css/style.css' fonctionne
    $basePath = '';
}
$base = $basePath;
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? 'etudiant';

// Lien vers l'accueil ou le dashboard selon la connexion
$homeHref = $base . ($isLoggedIn ? 'dashboard.php' : 'index.html');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>RéserveTonPlat</title>
    
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body class="has-layout">

    <header class="site-header">
        <div class="header-inner">
            <a href="<?= $homeHref ?>" class="logo">
                <span class="logo-icon">🍴</span>
                <span class="logo-text">RéserveTonPlat</span>
            </a>

            <nav class="main-nav">
                <?php if ($isLoggedIn): ?>
                    <div class="user-info">
                        <strong>Bonjour, <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Étudiant') ?></strong>

                        <?php if ($userRole === 'admin'): ?>
                            <a href="<?= $base ?>admin_scan.php" class="btn-scan-header">📷 Scanner une réservation</a>
                        <?php else: ?>
                            <a href="<?= $base ?>dashboard.php">Mon menu</a>
                        <?php endif; ?>

                        <a href="<?= $base ?>logout.php" class="btn-logout">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="<?= $base ?>login.php">Connexion</a>
                    <a href="<?= $base ?>register.html">Inscription</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="page-container">