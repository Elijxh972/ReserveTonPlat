<?php
session_start();

// Sécurité : seulement l'admin peut modifier les menus
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_menu'], $_POST['est_visible'])) {
    $idMenu = (int) $_POST['id_menu'];
    $estVisible = (int) $_POST['est_visible'] === 1 ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE menus SET est_visible = ? WHERE id = ?");
    $stmt->execute([$estVisible, $idMenu]);
}

// Retour au dashboard admin, sur la section des menus
header("Location: ../public/dashboard.php#admin-menus");
exit();

