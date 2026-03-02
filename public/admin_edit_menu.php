<?php
session_start();

// Sécurité : seul l'admin peut modifier les menus
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

require_once('../config/db.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: dashboard.php#admin-menus');
    exit();
}

// Récupération du menu à éditer (avant traitement POST pour pouvoir réutiliser date existante)
$stmt = $pdo->prepare('SELECT * FROM menus WHERE id = ?');
$stmt->execute([$id]);
$menu = $stmt->fetch();

if (!$menu) {
    header('Location: dashboard.php#admin-menus');
    exit();
}

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_menu = trim($_POST['date_menu'] ?? '');
    $plat = trim($_POST['plat_du_jour'] ?? '');
    $pizza = trim($_POST['pizza_grillade'] ?? '');
    $vege = trim($_POST['vegetarien'] ?? '');

    $maxTrad = $_POST['max_traditionnel'] !== '' ? (int) $_POST['max_traditionnel'] : null;
    $maxPizza = $_POST['max_pizza'] !== '' ? (int) $_POST['max_pizza'] : null;
    $maxVege = $_POST['max_vegetarien'] !== '' ? (int) $_POST['max_vegetarien'] : null;

    // Normalisation simple de la date (si vide, on garde l'ancienne valeur)
    if ($date_menu === '') {
        $date_menu = $menu['date_menu'];
    }

    $stmt = $pdo->prepare('UPDATE menus SET date_menu = ?, plat_du_jour = ?, pizza_grillade = ?, vegetarien = ?, max_traditionnel = ?, max_pizza = ?, max_vegetarien = ? WHERE id = ?');
    $stmt->execute([$date_menu, $plat, $pizza, $vege, $maxTrad, $maxPizza, $maxVege, $id]);

include 'includes/header.php';
?>

<div class="content-card">
    <h1>Modifier le menu</h1>

    <form method="POST" style="text-align:left; max-width: 700px; margin: 0 auto;">
        <div style="margin-bottom: 15px;">
            <label for="date_menu" style="font-weight:bold; display:block; margin-bottom:5px;">Date du menu</label>
            <input type="date" id="date_menu" name="date_menu" value="<?= htmlspecialchars($menu['date_menu']) ?>" style="width:250px; padding:8px; border-radius:8px; border:1px solid #ccc;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="plat_du_jour" style="font-weight:bold; display:block; margin-bottom:5px;">Plat du jour</label>
            <textarea id="plat_du_jour" name="plat_du_jour" rows="3" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;"><?= htmlspecialchars($menu['plat_du_jour']) ?></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="pizza_grillade" style="font-weight:bold; display:block; margin-bottom:5px;">Pizza / Grillade</label>
            <textarea id="pizza_grillade" name="pizza_grillade" rows="3" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;"><?= htmlspecialchars($menu['pizza_grillade']) ?></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="vegetarien" style="font-weight:bold; display:block; margin-bottom:5px;">Végétarien</label>
            <textarea id="vegetarien" name="vegetarien" rows="3" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;"><?= htmlspecialchars($menu['vegetarien']) ?></textarea>
        </div>

        <hr style="margin:20px 0;">
        <h3>Capacité de réservations</h3>
        <p style="font-size:0.9rem; color:#555;">Laisse vide ou mets 0 pour illimité.</p>

        <div style="display:flex; gap:20px; margin-bottom:20px; flex-wrap:wrap;">
            <div>
                <label for="max_traditionnel" style="font-weight:bold; display:block; margin-bottom:5px;">Nombre max Traditionnel</label>
                <input type="number" min="0" id="max_traditionnel" name="max_traditionnel" value="<?= htmlspecialchars($menu['max_traditionnel'] ?? '') ?>" style="width:140px; padding:8px; border-radius:8px; border:1px solid #ccc;">
            </div>
            <div>
                <label for="max_pizza" style="font-weight:bold; display:block; margin-bottom:5px;">Nombre max Pizza / Grillade</label>
                <input type="number" min="0" id="max_pizza" name="max_pizza" value="<?= htmlspecialchars($menu['max_pizza'] ?? '') ?>" style="width:140px; padding:8px; border-radius:8px; border:1px solid #ccc;">
            </div>
            <div>
                <label for="max_vegetarien" style="font-weight:bold; display:block; margin-bottom:5px;">Nombre max Végétarien</label>
                <input type="number" min="0" id="max_vegetarien" name="max_vegetarien" value="<?= htmlspecialchars($menu['max_vegetarien'] ?? '') ?>" style="width:140px; padding:8px; border-radius:8px; border:1px solid #ccc;">
            </div>
        </div>

        <button type="submit" class="btn-reserve">💾 Enregistrer les modifications</button>
        <a href="dashboard.php#admin-menus" class="btn-edit-cancel" style="margin-left:10px;">Annuler</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

