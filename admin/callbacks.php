<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}

$db = getDB();
$page_title = 'Demandes de rappel';

// Liste des rappels
$stmt = $db->query("SELECT * FROM demandes_rappel ORDER BY created_at DESC");
$callbacks = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="callbacks-page">
    <h1>📞 Demandes de rappel</h1>

    <div class="section-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Créneau</th>
                    <th>Date demande</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($callbacks as $callback): ?>
                <tr>
                    <td>#<?= $callback['id'] ?></td>
                    <td><?= e($callback['nom']) ?></td>
                    <td><a href="tel:<?= $callback['telephone'] ?>"><?= formatPhone($callback['telephone']) ?></a></td>
                    <td><?= e($callback['creneau']) ?></td>
                    <td><?= formatDateTime($callback['created_at']) ?></td>
                    <td>
                        <span class="badge badge-<?= $callback['statut'] ?>">
                            <?= ucfirst($callback['statut']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($callback['statut'] === 'nouveau'): ?>
                        <a href="tel:<?= $callback['telephone'] ?>" class="btn btn-sm btn-success">Appeler</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
