<?php
session_start();
// Si déjà connecté, redirige vers dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - RéserveTonPlat</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <p>Accédez à votre espace étudiant</p>

        <form action="../src/login_process.php" method="POST">
            <input type="email" name="email" placeholder="votre.nom@etu.univ-antilles.fr" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <br>
        <p>Pas encore inscrit ? <a href="register.html">Créer un compte</a></p>
        <p><a href="index.html">Retour à l'accueil</a></p>
    </div>
</body>
</html>