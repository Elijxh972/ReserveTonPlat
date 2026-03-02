<?php
session_start();
require_once('../config/db.php');

// Seuls les administrateurs (ou le personnel autorisé) peuvent vérifier une réservation
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo "<p>Accès refusé : cette page est réservée aux administrateurs.</p>";
    exit();
}

if (!isset($_GET['code'])) {
    echo "<p>Code manquant.</p>";
    exit();
}

$code = $_GET['code'];

$stmt = $pdo->prepare("SELECT r.*, u.nom AS utilisateur, u.email, m.date_menu
                     FROM reservations r
                     JOIN utilisateurs u ON r.id_utilisateur = u.id
                     JOIN menus m ON r.id_menu = m.id
                     WHERE r.reservation_code = ?");
$stmt->execute([$code]);
$res = $stmt->fetch();

if (!$res) {
    echo "<p>Code invalide.</p>";
    exit();
}

// marquer comme utilisé si ce n'est pas déjà le cas
if ($res['status'] !== 'used') {
    $upd = $pdo->prepare("UPDATE reservations SET status = 'used' WHERE id = ?");
    $upd->execute([$res['id']]);
    $res['status'] = 'used';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Vérification de réservation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .info { margin-bottom: 1rem; }
    </style>
</head>
<body>
    <h1>Vérification de réservation</h1>
    <div class="info"><strong>Utilisateur :</strong> <?= htmlspecialchars($res['utilisateur']) ?> (<?= htmlspecialchars($res['email']) ?>)</div>
    <div class="info"><strong>Date du menu :</strong> <?= htmlspecialchars($res['date_menu']) ?></div>
    <div class="info"><strong>Choix :</strong> <?= htmlspecialchars($res['choix_plat']) ?></div>
    <div class="info"><strong>Statut :</strong> <?= htmlspecialchars($res['status']) ?></div>
    <div class="info"><strong>Code :</strong> <?= htmlspecialchars($res['reservation_code']) ?></div>
</body>
</html>