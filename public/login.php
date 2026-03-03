<?php
$pageTitle = "Connexion";
$basePath = ""; 
include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Connexion</h2>

        <?php if (isset($_GET['res']) && $_GET['res'] == 'verified'): ?>
            <div class="btn-success" style="padding: 10px; border-radius: 10px; margin-bottom: 20px;">
                ✅ Compte validé ! Connectez-vous.
            </div>
        <?php endif; ?>

        <form action="../src/login_process.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-reserve" style="width: 100%;">
                Se connecter
            </button>
        </form>

        <p style="margin-top: 25px; font-size: 0.9rem; color: var(--text-muted);">
            Nouveau ici ? <a href="register.php" style="font-weight: 800;">Créer un compte</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>