<?php
// 1. LOGIQUE : contrôleur du dashboard (PHP uniquement)
session_start();
require_once('../config/db.php'); // Connexion à la BDD

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Récupération du menu du jour (uniquement s'il est marqué comme visible)
$aujourdhui = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM menus WHERE date_menu = ? AND est_visible = 1");
$stmt->execute([$aujourdhui]);
$menu = $stmt->fetch();

// 3. Si admin : récupérer tous les menus pour gestion
$menusAdmin = [];
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $stmtAll = $pdo->query("SELECT * FROM menus ORDER BY date_menu DESC LIMIT 50");
    $menusAdmin = $stmtAll->fetchAll();
}

// 4. Vérification si l'utilisateur a déjà réservé
$stmt_res = $pdo->prepare("SELECT * FROM reservations WHERE id_utilisateur = ? AND id_menu = ?");
$deja_reserve = false;
if ($menu) {
    $stmt_res->execute([$_SESSION['user_id'], $menu['id']]);
    $deja_reserve = $stmt_res->fetch();
}

// 5. Inclure la vue HTML (séparée du contrôleur)
require __DIR__ . '/dashboard_view.php';