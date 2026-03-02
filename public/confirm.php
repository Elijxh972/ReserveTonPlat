<?php
require '../config/db.php';

$token = $_GET['token'] ?? '';

if ($token) {
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE token_confirmation = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // On active le compte et on vide le token
        $update = $pdo->prepare("UPDATE utilisateurs SET est_verifie = 1, token_confirmation = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        
        header("Location: login.php?res=verified");
    } else {
        echo "Lien invalide ou compte déjà validé.";
    }
}