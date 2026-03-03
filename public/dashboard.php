<?php
// 1. LOGIQUE : contrôleur du dashboard
session_start();
require_once('../config/db.php'); 

// --- VERIFICATION DE SECURITÉ ---
// Si l'ID n'est pas en session, on dégage vers le login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fuseau horaire Martinique
date_default_timezone_set('America/Martinique');

// Variable pour que le header.php sache qu'on est connecté
$isLoggedIn = true; 
$basePath = ""; // Utile pour les liens dans le header

// Vérifier l'état des réservations
$currentHour = intval(date('H'));
$reservations_not_started = $currentHour < 9;  // Avant 9h
$reservations_closed = $currentHour >= 11;     // Après 11h
$reservations_open = $currentHour >= 9 && $currentHour < 11;  // Entre 9h et 11h

// 2. Récupération du menu du jour (marqué comme visible)
$aujourdhui = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM menus WHERE date_menu = ? AND est_visible = 1");
$stmt->execute([$aujourdhui]);
$menu = $stmt->fetch();

// 3. Si admin : récupérer tous les menus pour la table de gestion
$menusAdmin = [];
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $stmtAll = $pdo->query("SELECT * FROM menus ORDER BY date_menu DESC LIMIT 50");
    $menusAdmin = $stmtAll->fetchAll();
}

// 4. Vérification si l'utilisateur a déjà réservé
$deja_reserve = false;
if ($menu) {
    // On récupère la réservation ET le token QR code associé
    $stmt_res = $pdo->prepare("SELECT * FROM reservations WHERE id_utilisateur = ? AND id_menu = ?");
    $stmt_res->execute([$_SESSION['user_id'], $menu['id']]);
    $deja_reserve = $stmt_res->fetch();
}

// 5. Inclure la vue HTML
// On s'assure que le fichier existe avant de l'appeler pour éviter une page blanche
if (file_exists(__DIR__ . '/dashboard_view.php')) {
    require __DIR__ . '/dashboard_view.php';
} else {
    die("Erreur : Le fichier dashboard_view.php est introuvable dans le dossier public.");
}