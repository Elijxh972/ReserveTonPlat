<?php
session_start();
require_once('../config/db.php');

// helper : génère un code unique pour une réservation (hexadécimal 16 caractères)
function generateReservationCode(PDO $pdo)
{
    do {
        $code = bin2hex(random_bytes(8));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_code = ?");
        $stmt->execute([$code]);
    } while ($stmt->fetchColumn() > 0);

    return $code;
}

// 1. Vérification de la session (l'utilisateur doit être connecté)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// 2. Bloquer les réservations après une certaine heure (heure de Martinique)
$heureLimite = 11; // 11h00
$now = new DateTime('now', new DateTimeZone('America/Martinique'));
if ((int)$now->format('G') >= $heureLimite) {
    header("Location: ../public/dashboard.php?res=late");
    exit();
}

// 3. Vérification que l'ID du menu a bien été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_menu']) && isset($_POST['choix'])) {
    
    $id_utilisateur = $_SESSION['user_id'];
    $id_menu = intval($_POST['id_menu']); // On sécurise en forçant un nombre entier
    $choix = $_POST['choix']; // 'traditionnel', 'pizza', ou 'vegetarien'

    // 2bis. Vérifier la capacité restante pour ce choix si un maximum est défini
    $stmtMenu = $pdo->prepare("SELECT max_traditionnel, max_pizza, max_vegetarien FROM menus WHERE id = ?");
    $stmtMenu->execute([$id_menu]);
    $menu = $stmtMenu->fetch();

    if ($menu) {
        $max = null;
        if ($choix === 'traditionnel') {
            $max = $menu['max_traditionnel'];
        } elseif ($choix === 'pizza') {
            $max = $menu['max_pizza'];
        } elseif ($choix === 'vegetarien') {
            $max = $menu['max_vegetarien'];
        }

        if ($max !== null && (int)$max > 0) {
            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE id_menu = ? AND choix_plat = ?");
            $stmtCount->execute([$id_menu, $choix]);
            $nbDeja = (int)$stmtCount->fetchColumn();

            if ($nbDeja >= (int)$max) {
                header("Location: ../public/dashboard.php?res=full");
                exit();
            }
        }
    }

    try {
        // 3. Insertion dans la table reservations avec le choix de plat
        // génération du code et insertion
        $reservationCode = generateReservationCode($pdo);
        $stmt = $pdo->prepare("INSERT INTO reservations (id_utilisateur, id_menu, choix_plat, reservation_code) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_utilisateur, $id_menu, $choix, $reservationCode]);

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