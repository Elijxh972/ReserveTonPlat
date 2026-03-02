<?php include 'includes/header.php'; ?>

<div class="content-card">
    <h1>Bienvenue !</h1>
    
    <?php if (isset($_GET['res'])): ?>
        <div class="alert">
            <?php if ($_GET['res'] == 'success'): ?>
                <p style="color: green;">✅ Portion réservée !</p>
            <?php elseif ($_GET['res'] == 'already'): ?>
                <p style="color: orange;">⚠️ Tu as déjà réservé pour ce menu.</p>
            <?php elseif ($_GET['res'] == 'full'): ?>
                <p style="color: red;">❌ Il n'y a plus de portions disponibles pour ce choix.</p>
            <?php elseif ($_GET['res'] == 'cancelled'): ?>
                <p style="color: green;">✅ Réservation annulée.</p>
            <?php elseif ($_GET['res'] == 'late'): ?>
                <p style="color: red;">⏰ Les réservations sont fermées pour aujourd'hui.</p>
            <?php else: ?>
                <p style="color: red;">❌ Une erreur est survenue. Réessaie.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($menu) && $menu && (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')): ?>
        <div class="menu-section">
            <h2>🍴 Menu du Jour</h2>
            <?php if (!empty($deja_reserve)): ?>
                <?php
                $choixLabel = [
                    'traditionnel' => 'Traditionnel',
                    'pizza' => 'Pizza / Grillade',
                    'vegetarien' => 'Végétarien'
                ];
                $choix = $deja_reserve['choix_plat'] ?? 'traditionnel';
                ?>
                <p style="margin: 20px 0;">Tu as réservé <strong><?= htmlspecialchars($choixLabel[$choix] ?? $choix) ?></strong> pour ce menu.</p>
                <form action="../src/decommander_process.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                    <button type="submit" class="btn-decommander">Annuler ma réservation</button>
                </form>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Le menu n'est pas encore disponible.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="admin-section" style="border: 2px dashed var(--primary-blue); padding: 20px; border-radius: 15px; margin-top: 30px; background-color: #f0f7ff; text-align:left;">
            <h2>🛠️ Administration</h2>
            <div class="table-responsive">
                <?php if (!empty($menusAdmin)): ?>
                    <h3 id="admin-menus" style="margin-bottom: 10px;">Menus enregistrés</h3>
                    <table style="width:100%; border-collapse: collapse; font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Date</th>
                                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Plat du jour</th>
                                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Pizza / Grillade</th>
                                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Végétarien</th>
                                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Affichage</th>
                                <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menusAdmin as $m): ?>
                                <tr>
                                    <td style="padding:8px; border-bottom:1px solid #f0f0f0;"><?= htmlspecialchars($m['date_menu']) ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f0f0f0;"><?= htmlspecialchars($m['plat_du_jour']) ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f0f0f0;"><?= htmlspecialchars($m['pizza_grillade']) ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f0f0f0;"><?= htmlspecialchars($m['vegetarien']) ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f0f0f0;">
                                        <?php if (!empty($m['est_visible'])): ?>
                                            <span style="color:green; font-weight:bold;">Affiché</span>
                                        <?php else: ?>
                                            <span style="color:#999;">Caché</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:8px; border-bottom:1px solid #f0f0f0;">
                                        <form action="../src/admin_toggle_menu.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_menu" value="<?= (int)$m['id'] ?>">
                                            <input type="hidden" name="est_visible" value="<?= !empty($m['est_visible']) ? 0 : 1 ?>">
                                            <button type="submit" class="btn-reserve" style="padding:6px 12px; font-size:0.85rem;">
                                                <?= !empty($m['est_visible']) ? 'Cacher' : 'Afficher' ?>
                                            </button>
                                        </form>
                                        <a href="admin_edit_menu.php?id=<?= (int)$m['id'] ?>" class="btn-edit-menu">Modifier</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="margin-top:15px; font-size:0.9rem; color:#555;">Aucun menu enregistré pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>