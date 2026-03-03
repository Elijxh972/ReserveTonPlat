<?php 
// Sécurité : on empêche l'accès direct au fichier sans passer par le contrôleur
if (!isset($isLoggedIn)) { 
    header("Location: login.php"); 
    exit(); 
}
include 'includes/header.php'; 
?>

<div class="page-container">
    <div class="content-card">
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Étudiant') ?> !</h1>
        
        <?php if (isset($_GET['res'])): ?>
            <div style="margin: 20px 0;">
                <?php if ($_GET['res'] == 'success'): ?>
                    <p class="btn-success" style="padding: 10px; border-radius: 10px;">✅ Portion réservée avec succès !</p>
                <?php elseif ($_GET['res'] == 'already'): ?>
                    <p class="btn-warning" style="padding: 10px; border-radius: 10px;">⚠️ Tu as déjà réservé pour ce menu.</p>
                <?php elseif ($_GET['res'] == 'full'): ?>
                    <p class="btn-danger" style="padding: 10px; border-radius: 10px;">❌ Plus de portions disponibles.</p>
                <?php elseif ($_GET['res'] == 'cancelled'): ?>
                    <p class="btn-success" style="padding: 10px; border-radius: 10px;">✅ Réservation annulée.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($deja_reserve)): ?>
            <div class="auth-card" style="margin: 30px auto; max-width: 500px; border-top: 8px solid var(--vibrant-green); text-align: center;">
                <h2 style="color: var(--vibrant-green);">Ma Réservation</h2>
                
                <?php
                $choixLabel = [
                    'traditionnel' => 'Traditionnel',
                    'pizza' => 'Pizza / Grillade',
                    'vegetarien' => 'Végétarien'
                ];
                $choix = $deja_reserve['choix_plat'] ?? 'traditionnel';
                $token = $deja_reserve['qr_token'] ?? 'ERREUR';
                
                // Nouvelle API QR Code plus fiable
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($token);
                ?>

                <p style="margin: 15px 0;">Menu choisi : <strong><?= htmlspecialchars($choixLabel[$choix] ?? $choix) ?></strong></p>

                <?php if ($deja_reserve['est_scanne'] == 0): ?>
                    <div style="background: #fff; padding: 20px; display: inline-block; border: 2px solid #eee; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <div id="qrcode" style="display: flex; justify-content: center; align-items: center; width: 250px; height: 250px; margin: 0 auto; background: #f9f9f9;"></div>
                    </div>
                    
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                    <script>
                        new QRCode(document.getElementById("qrcode"), {
                            text: <?= json_encode($token) ?>,
                            width: 250,
                            height: 250,
                            colorDark: "#000000",
                            colorLight: "#ffffff"
                        });
                    </script>
                    <p style="font-size: 0.95rem; color: var(--text-muted); margin-top: 15px;">Présente ce code au comptoir du CROUS.</p>

                    <form action="../src/decommander_process.php" method="POST" style="margin-top: 25px;">
                        <input type="hidden" name="id_menu" value="<?= $menu['id'] ?? '' ?>">
                        <button type="button" class="btn-logout" style="width: 100%;" onclick="openCancelModal()">
                            Annuler ma portion
                        </button>
                    </form>
                <?php else: ?>
                    <div class="btn-success" style="padding: 15px; border-radius: 10px; margin-top: 10px;">
                        🍽️ Plat déjà récupéré. Bon appétit !
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif (isset($menu) && $menu && $reservations_open): ?>
            <div class="menu-section">
                <h2 style="margin-bottom: 25px;">Menu du Jour (<?= date('d/m/Y') ?>)</h2>
                <div class="menu-grid">
                    <div class="card card-traditionnel">
                        <h3>Traditionnel</h3>
                        <p class="plat-principal"><?= htmlspecialchars($menu['plat_du_jour']) ?></p>
                        <form action="../src/reserve_process.php" method="POST">
                            <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                            <input type="hidden" name="choix" value="traditionnel">
                            <button type="submit" class="btn-res btn-blue">Réserver ce plat</button>
                        </form>
                    </div>

                    <div class="card card-pizza">
                        <h3>Pizza / Grillade</h3>
                        <p class="plat-principal"><?= htmlspecialchars($menu['pizza_grillade']) ?></p>
                        <form action="../src/reserve_process.php" method="POST">
                            <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                            <input type="hidden" name="choix" value="pizza">
                            <button type="submit" class="btn-res btn-orange">Réserver ce plat</button>
                        </form>
                    </div>

                    <div class="card card-vegetarien">
                        <h3>Végétarien</h3>
                        <p class="plat-principal"><?= htmlspecialchars($menu['vegetarien']) ?></p>
                        <form action="../src/reserve_process.php" method="POST">
                            <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                            <input type="hidden" name="choix" value="vegetarien">
                            <button type="submit" class="btn-res btn-green">Réserver ce plat</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php elseif ($reservations_not_started): ?>
            <div style="padding: 40px; text-align: center;">
                <div style="background: linear-gradient(135deg, #4a90e2, #357abd); padding: 40px; border-radius: 15px; color: white;">
                    <h2 style="margin: 0 0 15px 0; font-size: 1.8rem;">⏰ À bientôt !</h2>
                    <p style="margin: 0; font-size: 1.1rem;">Les réservations commencent à <strong>9h00</strong></p>
                    <p style="margin: 10px 0 0 0; font-size: 0.95rem; opacity: 0.9;">Reviens à partir de 9h pour réserver ta portion.</p>
                </div>
            </div>
        <?php elseif ($reservations_closed): ?>
            <div style="padding: 40px; text-align: center;">
                <div style="background: linear-gradient(135deg, #ff6b6b, #ee5a6f); padding: 40px; border-radius: 15px; color: white;">
                    <h2 style="margin: 0 0 15px 0; font-size: 1.8rem;">🔒 Réservations Fermées</h2>
                    <p style="margin: 0; font-size: 1.1rem;">Les réservations pour aujourd'hui sont terminées.</p>
                    <p style="margin: 10px 0 0 0; font-size: 0.95rem; opacity: 0.9;">Le créneau de réservation était de 9h00 à 11h00.</p>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 40px; color: var(--text-muted);">
                <p>Le menu n'est pas encore publié pour aujourd'hui. Repasse d'ici quelques minutes !</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <div class="admin-section" style="margin-top: 50px; padding-top: 30px; border-top: 2px dashed var(--border-color);">
                <h2 style="text-align: left; color: var(--primary-blue);">Espace Gestionnaire</h2>
                <div style="display: flex; gap: 15px; margin: 20px 0;">
                    <a href="admin_scan.php" class="btn-scan-header">Valider un QR Code</a>
                    <a href="admin_upload.php" class="btn-outline btn">Ajouter un menu</a>
                </div>

                <div class="table-responsive" style="overflow-x: auto; background: #fff; padding: 15px; border-radius: 10px; box-shadow: var(--shadow-sm);">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color);">
                                <th style="padding: 10px;">Date</th>
                                <th style="padding: 10px;">Plats</th>
                                <th style="padding: 10px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($menusAdmin)): ?>
                                <?php foreach ($menusAdmin as $m): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px;"><?= htmlspecialchars($m['date_menu']) ?></td>
                                    <td style="padding: 10px; font-size: 0.85rem; color: #666;">
                                        T: <?= htmlspecialchars(substr($m['plat_du_jour'], 0, 20)) ?>...
                                    </td>
                                    <td style="padding: 10px;">
                                        <a href="admin_edit_menu.php?id=<?= $m['id'] ?>" style="color: var(--primary-blue); font-weight: bold;">Modifier</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Modal Confirmation Annulation -->
        <div id="cancelModal" class="modal-overlay" style="display: none;">
            <div class="modal-box">
                <h3>Confirmer l'annulation</h3>
                <p>Es-tu sûr de vouloir annuler ta réservation ?</p>
                <div class="modal-actions">
                    <button class="btn-secondary" onclick="closeCancelModal()">Non, garder</button>
                    <button class="btn-danger" onclick="confirmCancel()">Oui, annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>