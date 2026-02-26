<?php
session_start();
require_once('../config/db.php');

// 1. Vérification de la session (l'utilisateur doit être connecté)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// 2. Vérification que l'ID du menu a bien été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_menu']) && isset($_POST['choix'])) {
    
    $id_utilisateur = $_SESSION['user_id'];
    $id_menu = intval($_POST['id_menu']); // On sécurise en forçant un nombre entier
    $choix = $_POST['choix']; // 'traditionnel', 'pizza', ou 'vegetarien'

    try {
        // 3. Insertion dans la table reservations avec le choix de plat
        $stmt = $pdo->prepare("INSERT INTO reservations (id_utilisateur, id_menu, choix_plat) VALUES (?, ?, ?)");
        $stmt->execute([$id_utilisateur, $id_menu, $choix]);

        // Succès : retour au dashboard avec un message positif
        header("Location: ../public/dashboard.php?res=success");
        exit();

    } catch (PDOException $e) {
        // 4. Gestion de l'erreur "Déjà réservé"
        if ($e->getCode() == 23000) {
            header("Location: ../public/dashboard.php?res=already");
        } else {
            header("Location: ../public/dashboard.php?res=error");
        }
        exit();
    }
} else {
    // Si on arrive ici sans POST complet, on redirige simplement
    header("Location: ../public/dashboard.php");
    exit();
}