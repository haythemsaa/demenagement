<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Vérifier l'authentification
if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}

$db = getDB();

// Statistiques
$stats = [];

// Nombre total de demandes de devis
$stmt = $db->query("SELECT COUNT(*) as total FROM demandes_devis");
$stats['total_devis'] = $stmt->fetch()['total'];

// Demandes du jour
$stmt = $db->query("SELECT COUNT(*) as total FROM demandes_devis WHERE DATE(created_at) = CURDATE()");
$stats['devis_today'] = $stmt->fetch()['total'];

// Demandes du mois
$stmt = $db->query("SELECT COUNT(*) as total FROM demandes_devis WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stats['devis_month'] = $stmt->fetch()['total'];

// Demandes de rappel
$stmt = $db->query("SELECT COUNT(*) as total FROM demandes_rappel WHERE statut = 'nouveau'");
$stats['pending_callbacks'] = $stmt->fetch()['total'];

// Déménageurs actifs
$stmt = $db->query("SELECT COUNT(*) as total FROM demenageurs WHERE actif = 1");
$stats['active_movers'] = $stmt->fetch()['total'];

// Dernières demandes de devis
$stmt = $db->query("SELECT * FROM demandes_devis ORDER BY created_at DESC LIMIT 10");
$recent_quotes = $stmt->fetchAll();

// Dernières demandes de rappel
$stmt = $db->query("SELECT * FROM demandes_rappel WHERE statut = 'nouveau' ORDER BY created_at DESC LIMIT 5");
$pending_callbacks = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <h1>📊 Tableau de bord</h1>
    <p class="subtitle">Bienvenue <?= e($_SESSION['admin_name']) ?></p>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <h3><?= $stats['total_devis'] ?></h3>
                <p>Demandes totales</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">📈</div>
            <div class="stat-content">
                <h3><?= $stats['devis_today'] ?></h3>
                <p>Aujourd'hui</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <h3><?= $stats['devis_month'] ?></h3>
                <p>Ce mois</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">📞</div>
            <div class="stat-content">
                <h3><?= $stats['pending_callbacks'] ?></h3>
                <p>Rappels en attente</p>
            </div>
        </div>

        <div class="stat-card stat-secondary">
            <div class="stat-icon">🚚</div>
            <div class="stat-content">
                <h3><?= $stats['active_movers'] ?></h3>
                <p>Déménageurs actifs</p>
            </div>
        </div>
    </div>

    <!-- Demandes de rappel en attente -->
    <?php if (!empty($pending_callbacks)): ?>
    <div class="section-card urgent">
        <h2>🔔 Rappels en attente</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Créneau</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_callbacks as $callback): ?>
                <tr>
                    <td>#<?= $callback['id'] ?></td>
                    <td><?= e($callback['nom']) ?></td>
                    <td><a href="tel:<?= $callback['telephone'] ?>"><?= formatPhone($callback['telephone']) ?></a></td>
                    <td><?= e($callback['creneau']) ?></td>
                    <td><?= formatDateTime($callback['created_at']) ?></td>
                    <td>
                        <a href="/admin/callbacks.php?id=<?= $callback['id'] ?>" class="btn btn-sm btn-primary">Traiter</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Dernières demandes de devis -->
    <div class="section-card">
        <h2>📋 Dernières demandes de devis</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>De → Vers</th>
                    <th>Date déménagement</th>
                    <th>Statut</th>
                    <th>Date demande</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_quotes as $quote): ?>
                <tr>
                    <td>#<?= $quote['id'] ?></td>
                    <td>
                        <strong><?= e($quote['prenom'] . ' ' . $quote['nom']) ?></strong><br>
                        <small><?= e($quote['email']) ?></small><br>
                        <small><?= formatPhone($quote['telephone']) ?></small>
                    </td>
                    <td>
                        <?= e($quote['depart_postal']) ?> → <?= e($quote['arrivee_postal']) ?>
                    </td>
                    <td><?= $quote['date_demenagement'] ? formatDate($quote['date_demenagement']) : '-' ?></td>
                    <td>
                        <span class="badge badge-<?= $quote['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $quote['statut'])) ?>
                        </span>
                    </td>
                    <td><?= formatDateTime($quote['created_at']) ?></td>
                    <td>
                        <a href="/admin/quotes.php?id=<?= $quote['id'] ?>" class="btn btn-sm btn-secondary">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-center" style="margin-top: 1rem;">
            <a href="/admin/quotes.php" class="btn btn-primary">Voir toutes les demandes</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
