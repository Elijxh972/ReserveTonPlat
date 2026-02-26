<?php
require_once('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $password_raw = $_POST['password'];

    // 1. Vérification de la longueur du mot de passe
    if (strlen($password_raw) < 8) {
        header("Location: ../public/register.html?error=password_too_short");
        exit();
    }

    // 2. Vérification du domaine de l'email
    if (!str_ends_with($email, '@etu.univ-antilles.fr')) {
        header("Location: ../public/register.html?error=invalid_email");
        exit();
    }

    // 3. Hachage et Insertion
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $password_hashed]);
        
        // Redirection vers une page de succès
        header("Location: ../public/index.php?success=registered");
        exit();
    } catch (Exception $e) {
        header("Location: ../public/register.html?error=already_exists");
        exit();
    }
}