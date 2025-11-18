<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}

$db = getDB();
$page_title = 'Demandes de devis';

// Filtres
$status = get('status', 'all');
$search = get('search', '');

// Construction de la requête
$sql = "SELECT * FROM demandes_devis WHERE 1=1";
$params = [];

if ($status !== 'all') {
    $sql .= " AND statut = :status";
    $params['status'] = $status;
}

if ($search) {
    $sql .= " AND (nom LIKE :search OR prenom LIKE :search OR email LIKE :search OR telephone LIKE :search)";
    $params['search'] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$quotes = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="quotes-page">
    <h1>📝 Demandes de devis</h1>

    <!-- Filtres -->
    <div class="section-card">
        <form method="GET" class="form-grid">
            <div class="form-group">
                <label>Statut</label>
                <select name="status">
                    <option value="all">Tous</option>
                    <option value="nouveau" <?= $status === 'nouveau' ? 'selected' : '' ?>>Nouveau</option>
                    <option value="en_cours" <?= $status === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="devis_envoyes" <?= $status === 'devis_envoyes' ? 'selected' : '' ?>>Devis envoyés</option>
                    <option value="converti" <?= $status === 'converti' ? 'selected' : '' ?>>Converti</option>
                    <option value="annule" <?= $status === 'annule' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
            <div class="form-group">
                <label>Recherche</label>
                <input type="text" name="search" placeholder="Nom, email, téléphone..." value="<?= e($search) ?>">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </form>
    </div>

    <!-- Liste des demandes -->
    <div class="section-card">
        <p><strong><?= count($quotes) ?></strong> demande(s) trouvée(s)</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Trajet</th>
                    <th>Détails</th>
                    <th>Estimation</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $quote): ?>
                <tr>
                    <td>#<?= $quote['id'] ?></td>
                    <td>
                        <strong><?= e($quote['prenom'] . ' ' . $quote['nom']) ?></strong><br>
                        <small><?= e($quote['email']) ?></small><br>
                        <small><?= formatPhone($quote['telephone']) ?></small>
                    </td>
                    <td>
                        <strong><?= e($quote['depart_postal']) ?></strong> → <strong><?= e($quote['arrivee_postal']) ?></strong>
                    </td>
                    <td>
                        <?= e(ucfirst($quote['type_depart'])) ?> - <?= $quote['superficie'] ?> m²<br>
                        <?= $quote['pieces'] ?> pièce(s)
                    </td>
                    <td>
                        <?= formatPrice($quote['estimation_min']) ?> - <?= formatPrice($quote['estimation_max']) ?><br>
                        <small><?= $quote['volume_estime'] ?> m³</small>
                    </td>
                    <td>
                        <small><?= formatDateTime($quote['created_at']) ?></small>
                    </td>
                    <td>
                        <span class="badge badge-<?= $quote['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $quote['statut'])) ?>
                        </span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-secondary">Voir détail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
