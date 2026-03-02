<?php
session_start();
require_once('../config/db.php');

// Seul un étudiant connecté peut décommander (pas l'admin)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_menu'])) {
    $id_utilisateur = $_SESSION['user_id'];
    $id_menu = (int) $_POST['id_menu'];

    if ($id_menu > 0) {
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id_utilisateur = ? AND id_menu = ?");
        $stmt->execute([$id_utilisateur, $id_menu]);
    }
}

header("Location: ../public/dashboard.php?res=cancelled");
exit();
