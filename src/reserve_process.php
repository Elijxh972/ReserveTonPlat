<?php
session_start();
require_once('../config/db.php');

// On définit l'heure de la Martinique pour la limite de 9h à 11h
date_default_timezone_set('America/Martinique');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    
    $id_utilisateur = $_SESSION['user_id'];
    $id_menu = intval($_POST['id_menu']);
    $choix = $_POST['choix']; 
    
    // 1. Vérification de l'heure (Créneau 9h00 - 11h00)
    $currentHour = intval(date('H'));
    if ($currentHour < 9 || $currentHour >= 11) {
        header("Location: ../public/dashboard.php?res=late");
        exit();
    }

    // 2. Génération du token unique
    $token = bin2hex(random_bytes(8)); // Ex: 4fbc23...

    try {
        // 3. Insertion SQL (On remplit qr_token ET reservation_code pour être sûr)
        // Note: id_res est en auto-increment donc on ne l'inclut pas.
        $sql = "INSERT INTO reservations (
                    id_utilisateur, 
                    id_menu, 
                    date_reservation, 
                    choix_plat, 
                    qr_token, 
                    est_scanne, 
                    reservation_code, 
                    status
                ) VALUES (?, ?, NOW(), ?, ?, 0, ?, 'pending')";
        
        $stmt = $pdo->prepare($sql);
        // On envoie le même code dans qr_token et reservation_code
        $stmt->execute([$id_utilisateur, $id_menu, $choix, $token, $token]);

        header("Location: ../public/dashboard.php?res=success");
        exit();

    } catch (PDOException $e) {
        // Si l'erreur est "Duplicate entry", c'est que l'utilisateur a déjà réservé
        if ($e->getCode() == 23000) {
            header("Location: ../public/dashboard.php?res=already");
        } else {
            // Affiche l'erreur réelle si ça ne marche toujours pas
            die("Erreur SQL : " . $e->getMessage());
        }
        exit();
    }
} else {
    header("Location: ../public/dashboard.php");
    exit();
}