<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

// Vérifier si connecté
if (!isset($_SESSION['demenageur_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$demenageur_id = $_SESSION['demenageur_id'];

// Filtres
$status_filter = $_GET['status'] ?? 'all';
$date_filter = $_GET['date'] ?? 'all';
$sort = $_GET['sort'] ?? 'recent';

// Construction de la requête
$where_clauses = ["dl.demenageur_id = ?"];
$params = [$demenageur_id];

if ($status_filter !== 'all') {
    $where_clauses[] = "dl.status = ?";
    $params[] = $status_filter;
}

if ($date_filter === 'today') {
    $where_clauses[] = "DATE(dl.sent_at) = CURDATE()";
} elseif ($date_filter === 'week') {
    $where_clauses[] = "dl.sent_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($date_filter === 'month') {
    $where_clauses[] = "MONTH(dl.sent_at) = MONTH(NOW()) AND YEAR(dl.sent_at) = YEAR(NOW())";
}

$order_by = "dl.sent_at DESC"; // Par défaut
if ($sort === 'oldest') {
    $order_by = "dl.sent_at ASC";
} elseif ($sort === 'price_high') {
    $order_by = "qr.estimated_price DESC";
} elseif ($sort === 'price_low') {
    $order_by = "qr.estimated_price ASC";
} elseif ($sort === 'volume_high') {
    $order_by = "qr.total_volume DESC";
}

$sql = "
    SELECT dl.*, qr.name as client_name, qr.email as client_email, qr.phone as client_phone,
           qr.postal_code_from, qr.city_from, qr.postal_code_to, qr.city_to,
           qr.total_volume, qr.estimated_price, qr.moving_date, qr.created_at as request_date,
           qr.services, qr.comments, qr.urgency
    FROM demenageur_leads dl
    INNER JOIN quote_requests qr ON dl.quote_request_id = qr.id
    WHERE " . implode(' AND ', $where_clauses) . "
    ORDER BY {$order_by}
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques rapides
$stmt = $db->prepare("
    SELECT status, COUNT(*) as count
    FROM demenageur_leads
    WHERE demenageur_id = ?
    GROUP BY status
");
$stmt->execute([$demenageur_id]);
$stats_by_status = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stats_by_status[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Leads - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .leads-page {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 32px;
        }

        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
        }

        .filter-group select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .stat-pill {
            padding: 12px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            white-space: nowrap;
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid transparent;
        }

        .stat-pill:hover {
            border-color: #667eea;
        }

        .stat-pill.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-pill .count {
            font-size: 24px;
            font-weight: 700;
            margin-right: 8px;
        }

        .stat-pill .label {
            font-size: 14px;
            opacity: 0.9;
        }

        .leads-grid {
            display: grid;
            gap: 20px;
        }

        .lead-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
            border-left: 4px solid #e2e8f0;
        }

        .lead-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .lead-card.status-sent {
            border-left-color: #fbbf24;
        }

        .lead-card.status-viewed {
            border-left-color: #60a5fa;
        }

        .lead-card.status-quoted {
            border-left-color: #a78bfa;
        }

        .lead-card.status-won {
            border-left-color: #34d399;
        }

        .lead-card.status-lost {
            border-left-color: #f87171;
        }

        .lead-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }

        .lead-id {
            font-size: 12px;
            color: #718096;
            margin-bottom: 5px;
        }

        .client-name {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-sent { background: #fef3c7; color: #92400e; }
        .status-viewed { background: #dbeafe; color: #1e40af; }
        .status-quoted { background: #e0e7ff; color: #3730a3; }
        .status-won { background: #d1fae5; color: #065f46; }
        .status-lost { background: #fee2e2; color: #991b1b; }
        .status-expired { background: #f3f4f6; color: #4b5563; }

        .lead-route {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #f7fafc;
            border-radius: 10px;
        }

        .location {
            font-weight: 600;
            color: #2d3748;
        }

        .arrow {
            color: #667eea;
            font-size: 20px;
        }

        .lead-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .detail-item {
            text-align: center;
            padding: 12px;
            background: #f7fafc;
            border-radius: 8px;
        }

        .detail-value {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .detail-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
        }

        .lead-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .lead-date {
            font-size: 13px;
            color: #718096;
        }

        .btn-view {
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 15px;
        }

        .empty-state .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .urgency-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #fed7d7;
            color: #9b2c2c;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .lead-route {
                flex-direction: column;
                align-items: flex-start;
            }

            .arrow {
                transform: rotate(90deg);
            }
        }
    </style>
</head>
<body>
    <nav style="background: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: fixed; top: 0; left: 0; right: 0; z-index: 1000;">
        <div style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <a href="/index.php" style="font-size: 20px; font-weight: bold; color: #667eea; text-decoration: none;">
                🚚 <?= SITE_NAME ?>
            </a>
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="dashboard.php" style="color: #2d3748; text-decoration: none;">Tableau de bord</a>
                <a href="leads.php" style="color: #667eea; font-weight: 600; text-decoration: none;">Mes Leads</a>
                <a href="profil.php" style="color: #2d3748; text-decoration: none;">Mon Profil</a>
                <a href="abonnement.php" style="color: #2d3748; text-decoration: none;">Abonnement</a>
                <a href="logout.php" style="color: #f56565; text-decoration: none;">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="leads-page">
        <div class="page-header">
            <h1>📋 Mes Leads</h1>
            <div style="color: #718096;">
                Total : <?= count($leads) ?> lead<?= count($leads) > 1 ? 's' : '' ?>
            </div>
        </div>

        <!-- Stats rapides -->
        <div class="stats-row">
            <a href="?status=all" class="stat-pill <?= $status_filter === 'all' ? 'active' : '' ?>">
                <span class="count"><?= array_sum($stats_by_status) ?></span>
                <span class="label">Tous</span>
            </a>
            <a href="?status=sent" class="stat-pill <?= $status_filter === 'sent' ? 'active' : '' ?>">
                <span class="count"><?= $stats_by_status['sent'] ?? 0 ?></span>
                <span class="label">Nouveaux</span>
            </a>
            <a href="?status=viewed" class="stat-pill <?= $status_filter === 'viewed' ? 'active' : '' ?>">
                <span class="count"><?= $stats_by_status['viewed'] ?? 0 ?></span>
                <span class="label">Vus</span>
            </a>
            <a href="?status=quoted" class="stat-pill <?= $status_filter === 'quoted' ? 'active' : '' ?>">
                <span class="count"><?= $stats_by_status['quoted'] ?? 0 ?></span>
                <span class="label">Devis envoyés</span>
            </a>
            <a href="?status=won" class="stat-pill <?= $status_filter === 'won' ? 'active' : '' ?>">
                <span class="count"><?= $stats_by_status['won'] ?? 0 ?></span>
                <span class="label">Gagnés</span>
            </a>
            <a href="?status=lost" class="stat-pill <?= $status_filter === 'lost' ? 'active' : '' ?>">
                <span class="count"><?= $stats_by_status['lost'] ?? 0 ?></span>
                <span class="label">Perdus</span>
            </a>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <form method="GET">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Statut</label>
                        <select name="status" onchange="this.form.submit()">
                            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Tous les statuts</option>
                            <option value="sent" <?= $status_filter === 'sent' ? 'selected' : '' ?>>Nouveaux</option>
                            <option value="viewed" <?= $status_filter === 'viewed' ? 'selected' : '' ?>>Vus</option>
                            <option value="quoted" <?= $status_filter === 'quoted' ? 'selected' : '' ?>>Devis envoyés</option>
                            <option value="won" <?= $status_filter === 'won' ? 'selected' : '' ?>>Gagnés</option>
                            <option value="lost" <?= $status_filter === 'lost' ? 'selected' : '' ?>>Perdus</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Période</label>
                        <select name="date" onchange="this.form.submit()">
                            <option value="all" <?= $date_filter === 'all' ? 'selected' : '' ?>>Toutes les dates</option>
                            <option value="today" <?= $date_filter === 'today' ? 'selected' : '' ?>>Aujourd'hui</option>
                            <option value="week" <?= $date_filter === 'week' ? 'selected' : '' ?>>Cette semaine</option>
                            <option value="month" <?= $date_filter === 'month' ? 'selected' : '' ?>>Ce mois</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Tri</label>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Plus récents</option>
                            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Plus anciens</option>
                            <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Prix décroissant</option>
                            <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Prix croissant</option>
                            <option value="volume_high" <?= $sort === 'volume_high' ? 'selected' : '' ?>>Volume décroissant</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste des leads -->
        <?php if (empty($leads)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h2>Aucun lead trouvé</h2>
                <p style="color: #718096;">
                    <?php if ($status_filter !== 'all' || $date_filter !== 'all'): ?>
                        Essayez de modifier vos filtres pour voir plus de résultats.
                    <?php else: ?>
                        Les leads correspondant à votre zone apparaîtront ici automatiquement.
                    <?php endif; ?>
                </p>
                <?php if ($status_filter !== 'all' || $date_filter !== 'all'): ?>
                    <a href="leads.php" style="display: inline-block; margin-top: 20px; color: #667eea; font-weight: 600;">
                        Réinitialiser les filtres
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="leads-grid">
                <?php foreach ($leads as $lead):
                    $lead_data = json_decode($lead['lead_details'], true);
                    $match_score = $lead_data['match_score'] ?? 0;

                    $status_labels = [
                        'sent' => 'Nouveau',
                        'viewed' => 'Vu',
                        'quoted' => 'Devis envoyé',
                        'won' => 'Gagné',
                        'lost' => 'Perdu',
                        'declined' => 'Décliné',
                        'expired' => 'Expiré'
                    ];
                ?>
                    <div class="lead-card status-<?= $lead['status'] ?>">
                        <div class="lead-header">
                            <div>
                                <div class="lead-id">Lead #<?= $lead['id'] ?> • Reçu le <?= date('d/m/Y à H:i', strtotime($lead['sent_at'])) ?></div>
                                <h3 class="client-name">
                                    <?= htmlspecialchars($lead['client_name']) ?>
                                    <?php if ($lead['urgency'] === 'urgent'): ?>
                                        <span class="urgency-badge">⚠️ URGENT</span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            <span class="status-badge status-<?= $lead['status'] ?>">
                                <?= $status_labels[$lead['status']] ?? $lead['status'] ?>
                            </span>
                        </div>

                        <div class="lead-route">
                            <div class="location">
                                📍 <?= htmlspecialchars($lead['postal_code_from']) ?> - <?= htmlspecialchars($lead['city_from']) ?>
                            </div>
                            <div class="arrow">→</div>
                            <div class="location">
                                📍 <?= htmlspecialchars($lead['postal_code_to']) ?> - <?= htmlspecialchars($lead['city_to']) ?>
                            </div>
                        </div>

                        <div class="lead-details">
                            <div class="detail-item">
                                <div class="detail-value"><?= round($lead['total_volume'], 1) ?> m³</div>
                                <div class="detail-label">Volume</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value"><?= number_format($lead['estimated_price'], 0, ',', ' ') ?> €</div>
                                <div class="detail-label">Estimation</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value"><?= $lead['moving_date'] ? date('d/m/Y', strtotime($lead['moving_date'])) : 'À définir' ?></div>
                                <div class="detail-label">Date souhaité</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value"><?= round($match_score) ?>/100</div>
                                <div class="detail-label">Score match</div>
                            </div>
                        </div>

                        <?php if (!empty($lead['comments'])): ?>
                            <div style="background: #f7fafc; padding: 15px; border-radius: 8px; margin: 15px 0;">
                                <strong>💬 Commentaire client :</strong><br>
                                <em><?= htmlspecialchars(substr($lead['comments'], 0, 150)) ?><?= strlen($lead['comments']) > 150 ? '...' : '' ?></em>
                            </div>
                        <?php endif; ?>

                        <div class="lead-footer">
                            <div class="lead-date">
                                <?php if ($lead['status'] === 'sent'): ?>
                                    ⏰ Expire le <?= date('d/m/Y', strtotime($lead['expires_at'])) ?>
                                <?php elseif ($lead['status'] === 'won'): ?>
                                    🎉 Remporté le <?= date('d/m/Y', strtotime($lead['won_at'])) ?>
                                <?php elseif ($lead['status'] === 'quoted'): ?>
                                    📧 Devis envoyé le <?= date('d/m/Y', strtotime($lead['quoted_at'])) ?>
                                <?php endif; ?>
                            </div>
                            <a href="lead-details.php?id=<?= $lead['id'] ?>" class="btn-view">
                                Voir les détails →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
