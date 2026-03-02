<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>RéserveTonPlat</title>
    <link rel="stylesheet" href="<?= isset($basePath) ? $basePath : '' ?>assets/css/style.css">
</head>
<body class="has-layout">
    <header class="site-header">
        <div class="header-inner">
            <a href="<?= isset($basePath) ? $basePath : '' ?>index.php" class="logo">
                <span class="logo-icon">🍴</span>
                <span class="logo-text">RéserveTonPlat</span>
            </a>
            <nav class="main-nav">
                <a href="<?= isset($basePath) ? $basePath : '' ?>index.php">Accueil</a>
                <a href="<?= isset($basePath) ? $basePath : '' ?>login.php">Connexion</a>
                <a href="<?= isset($basePath) ? $basePath : '' ?>register.html">Inscription</a>
            </nav>
        </div>
    </header>
    <main class="main-content">