<?php
session_start();
require_once('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // 1. Chercher l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Vérifier si le compte est activé
        // On vérifie si la colonne est_verifie vaut 1
        if ($user['est_verifie'] == 0) {
            die("Erreur : Votre compte n'est pas encore activé. Modifiez le 0 en 1 dans PHPMyAdmin.");
        }

        // 3. Vérifier le mot de passe
        // CHANGEMENT ICI : On compare directement les textes pour ton test
        if ($password === $user['mot_de_passe']) { 
            
            // SUCCESS : On remplit la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role']; 

            // REDIRECTION MAGIQUE
            header("Location: ../public/dashboard.php");
            exit();
        } else {
            header("Location: ../public/login.php?error=mdp_incorrect");
            exit();
        }
    } else {
        header("Location: ../public/login.php?error=utilisateur_inconnu");
        exit();
    }
}