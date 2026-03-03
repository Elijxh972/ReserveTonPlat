<?php
// On définit le titre de la page et le chemin de base avant d'inclure le header
$pageTitle = "Accueil";
$basePath = ""; // Nous sommes à la racine du dossier /public

include 'includes/header.php'; 
?>

<div class="page-container">
    <section class="content-card" style="margin-top: 20px;">
        <div class="hero-section">
            <h1 class="plat-principal">Simplifiez votre pause déjeuner</h1>
            <p style="font-size: 1.2rem; color: #555; margin-bottom: 30px;">
                Réservez votre plateau au CROUS de Schœlcher en quelques clics et évitez les files d'attente.
            </p>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="welcome-back">
                    <p>Ravi de vous revoir, <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong> !</p>
                    <a href="dashboard.php" class="btn-reserve">Accéder à mon Dashboard</a>
                </div>
            <?php else: ?>
                <div class="auth-cta">
                    <a href="login.php" class="btn-res btn-blue" style="text-decoration: none; padding: 15px 30px; font-size: 1.1rem;">Se connecter</a>
                    <p style="margin-top: 15px; font-size: 0.9rem;">
                        Pas encore de compte ? <a href="register.php" style="color: var(--primary-blue); font-weight: bold;">Inscrivez-vous ici</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="menu-grid" style="margin-top: 40px;">
        <div class="card card-traditionnel">
            <h3 class="title-traditionnel">Gain de temps</h3>
            <p>Plus besoin d'arriver à 11h30 pour être sûr d'avoir votre plat préféré.</p>
        </div>
        <div class="card card-pizza">
            <h3 class="title-pizza">Stocks en temps réel</h3>
            <p>Consultez la disponibilité des menus (Pizza, Traditionnel, Veggie) avant de vous déplacer.</p>
        </div>
        <div class="card card-vegetarien">
            <h3 class="title-vegetarien">QR Code Unique</h3>
            <p>Présentez votre QR Code à la borne de validation et récupérez votre plateau instantanément.</p>
        </div>
    </section>

    <div style="text-align: center; margin-top: 30px; color: #777; font-style: italic;">
        <p>⚠️ Les réservations sont ouvertes tous les jours jusqu'à 11h00.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>