<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}

$db = getDB();
$page_title = 'Gestion des déménageurs';

// Liste des déménageurs
$stmt = $db->query("SELECT * FROM demenageurs ORDER BY created_at DESC");
$movers = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="movers-page">
    <div class="page-header">
        <h1>🚚 Gestion des déménageurs</h1>
        <a href="/admin/movers-add.php" class="btn btn-primary">+ Ajouter un déménageur</a>
    </div>

    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>

    <div class="section-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Entreprise</th>
                    <th>Contact</th>
                    <th>Localisation</th>
                    <th>Note</th>
                    <th>Services</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movers as $mover): ?>
                <tr>
                    <td>#<?= $mover['id'] ?></td>
                    <td>
                        <strong><?= e($mover['nom_entreprise']) ?></strong><br>
                        <small>SIRET: <?= e($mover['siret']) ?></small>
                    </td>
                    <td>
                        <?= e($mover['email']) ?><br>
                        <?= formatPhone($mover['telephone']) ?>
                    </td>
                    <td>
                        <?= e($mover['ville']) ?> (<?= e($mover['departement']) ?>)
                    </td>
                    <td>
                        ⭐ <?= number_format($mover['note_moyenne'], 1) ?>/5
                        <br><small>(<?= $mover['nombre_avis'] ?> avis)</small>
                    </td>
                    <td>
                        <?php if ($mover['service_eco']): ?><span class="badge badge-nouveau">Éco</span> <?php endif; ?>
                        <?php if ($mover['service_standard']): ?><span class="badge badge-en_cours">Standard</span> <?php endif; ?>
                        <?php if ($mover['service_premium']): ?><span class="badge badge-converti">Premium</span> <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($mover['actif']): ?>
                            <span class="badge badge-converti">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-annule">Inactif</span>
                        <?php endif; ?>
                        <?php if ($mover['verifie']): ?>
                            <span class="badge badge-devis_envoyes">✓ Vérifié</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/admin/movers-edit.php?id=<?= $mover['id'] ?>" class="btn btn-sm btn-secondary">Modifier</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
