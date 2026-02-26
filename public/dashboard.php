<?php
// 1. LOGIQUE : On vérifie la session
session_start();
require_once('../config/db.php'); // AJOUT : Connexion à la BDD

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. RÉCUPÉRATION DU MENU DU JOUR
$aujourdhui = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM menus WHERE date_menu = ?");
$stmt->execute([$aujourdhui]);
$menu = $stmt->fetch();

// 3. VÉRIFICATION SI L'UTILISATEUR A DÉJÀ RÉSERVÉ
$stmt_res = $pdo->prepare("SELECT * FROM reservations WHERE id_utilisateur = ? AND id_menu = ?");
// On ne tente la requête que si un menu existe pour éviter les erreurs
$deja_reserve = false;
if ($menu) {
    $stmt_res->execute([$_SESSION['user_id'], $menu['id']]);
    $deja_reserve = $stmt_res->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - RéserveTonPlat</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <span>RéserveTonPlat - UA</span>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['user_nom']); ?></strong>
            <a href="../src/logout.php" class="btn-logout">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <h1>Bienvenue !</h1>
        
<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="admin-section" style="border: 2px dashed var(--primary-blue); padding: 20px; border-radius: 15px; margin-bottom: 30px; background-color: #f0f7ff;">
        <h2 style="color: var(--primary-blue); margin-top: 0;">🛠️ Administration</h2>
        <p>Bienvenue, Admin. Utilisez le bouton ci-dessous pour mettre à jour le menu via l'IA.</p>
        <a href="admin_upload.php" class="btn-reserve" style="text-decoration: none; display: inline-block;">
            📷 SCANNER LE MENU DU JOUR
        </a>
    </div>
<?php endif; ?>
        <?php if (isset($_GET['res'])): ?>
    <div style="padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        <?php if ($_GET['res'] == 'success'): ?>
            <p style="color: green; font-weight: bold;">✅ C'est noté ! Ta portion est réservée.</p>
        <?php elseif ($_GET['res'] == 'already'): ?>
            <p style="color: orange; font-weight: bold;">⚠️ Tu as déjà réservé pour ce menu.</p>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">❌ Erreur lors de la réservation. Réessaie.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

        <?php if ($menu): ?>
<div class="menu-container">
    <h2>🍴 Menu du Jour</h2>

    <div class="menu-grid">
        <div class="card card-traditionnel">
            <h3 class="title-traditionnel">Traditionnel</h3>
            <p><?= htmlspecialchars($menu['plat_du_jour']) ?></p>
            <form action="../src/reserve_process.php" method="POST">
                <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                <input type="hidden" name="choix" value="traditionnel">
                <button type="submit" class="btn-res btn-blue">Choisir</button>
            </form>
        </div>

        <div class="card card-pizza">
            <h3 class="title-pizza">Pizza / Grillade</h3>
            <p><?= htmlspecialchars($menu['pizza_grillade']) ?></p>
            <form action="../src/reserve_process.php" method="POST">
                <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                <input type="hidden" name="choix" value="pizza">
                <button type="submit" class="btn-res btn-orange">Choisir</button>
            </form>
        </div>

        <div class="card card-vegetarien">
            <h3 class="title-vegetarien">Végétarien</h3>
            <p><?= htmlspecialchars($menu['vegetarien']) ?></p>
            <form action="../src/reserve_process.php" method="POST">
                <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                <input type="hidden" name="choix" value="vegetarien">
                <button type="submit" class="btn-res btn-green">Choisir</button>
            </form>
        </div>
    </div>
</div>
        <?php else: ?>
            <p class="no-menu">Le menu pour aujourd'hui n'est pas encore disponible.</p>
            <p><small>L'IA est en train de scanner le menu papier du CROUS...</small></p>
        <?php endif; ?>

        <div class="mes-reservations">
            <h3>Mes dernières activités</h3>
            <p>Historique de vos réservations bientôt disponible.</p>
        </div>
    </div>
</body>
</html>