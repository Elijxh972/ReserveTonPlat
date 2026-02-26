<?php
require_once('../config/db.php');

// On récupère le token dans l'URL
$token_recu = $_GET['token'] ?? null;

if ($token_recu) {
    // On cherche l'utilisateur qui a ce token et on passe est_verifie à 1
    $stmt = $pdo->prepare("UPDATE utilisateurs SET est_verifie = 1, token_confirmation = NULL WHERE token_confirmation = ?");
    $stmt->execute([$token_recu]);

    if ($stmt->rowCount() > 0) {
        $titre = 'Succès !';
        $message = "Ton compte est maintenant activé. Tu peux aller manger !";
        $success = true;
    } else {
        $titre = 'Erreur';
        $message = 'Ce lien est invalide ou le compte est déjà activé.';
        $success = false;
    }
} else {
    $titre = 'Erreur';
    $message = 'Aucun jeton de sécurité trouvé.';
    $success = false;
}

// on redirige vers la page statique avec paramètres
$qs = http_build_query([
    'status' => $success ? 'success' : 'error',
    'message' => $message
]);
header('Location: ../public/confirm.html?' . $qs);
exit;