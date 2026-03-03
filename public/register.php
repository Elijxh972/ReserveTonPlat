<?php
$pageTitle = "Inscription";
$basePath = ""; 
include 'includes/header.php';

if ($isLoggedIn) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Créer un compte</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="btn-danger" style="padding: 10px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem;">
                <?php 
                    if($_GET['error'] == 'invalid_email') echo "❌ Email @etu.univ-antilles.fr requis.";
                    elseif($_GET['error'] == 'already_exists') echo "❌ Cet email est déjà utilisé.";
                ?>
            </div>
        <?php endif; ?>

        <form action="../src/register_process.php" method="POST">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" name="nom" id="nom" class="form-control" required placeholder="Ex: Jean Dupont">
            </div>

            <div class="form-group">
                <label for="email">Email Universitaire</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="nom.prenom@etu.univ-antilles.fr">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-reserve" style="width: 100%; margin-top: 10px;">
                S'inscrire
            </button>
        </form>

        <p style="margin-top: 25px; font-size: 0.9rem; color: var(--text-muted);">
            Déjà inscrit ? <a href="login.php" style="font-weight: 800;">Se connecter</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>