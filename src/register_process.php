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

    // --- TES VÉRIFICATIONS (Email UA + 8 caractères) ---
    // (Garde le code qu'on a fait précédemment ici)

    // 1. Génération du Token de simulation
    $token = bin2hex(random_bytes(16)); // Crée une clé de 32 caractères
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        // 2. Insertion avec le token
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, token_confirmation) 
                VALUES (:nom, :email, :password, :token)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':password' => $password_hashed,
            ':token' => $token
        ]);

        // 3. LA SIMULATION : On affiche le lien au lieu de l'envoyer
        echo "<h2>Inscription enregistrée !</h2>";
        echo "<p>Ceci est une simulation de l'email que l'étudiant recevrait sur <b>$email</b> :</p>";
        echo "<div style='border: 2px dashed #d32f2f; padding: 20px; background: #fff;'>";
        echo "   <h3>RéserveTonPlat - Confirmation</h3>";
        echo "   <p>Bonjour $nom, merci de valider ton compte en cliquant ici :</p>";
        echo \"   <a href='../public/confirm.php?token=$token' style='background: #2e7d32; color: white; padding: 10px; text-decoration: none;'>ACTIVER MON COMPTE</a>\";
        echo "</div>";

    } catch (Exception $e) {
        header("Location: ../public/register.html?error=already_exists");
        exit();
    }
}