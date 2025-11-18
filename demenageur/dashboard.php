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

// Récupérer infos déménageur + abonnement
$stmt = $db->prepare("
    SELECT d.*, ds.id as subscription_id, ds.status as subscription_status, ds.leads_used_this_month,
           ds.end_date as subscription_end_date, sp.name as plan_name, sp.leads_per_month, sp.price_per_extra_lead
    FROM demenageurs d
    LEFT JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id AND ds.status = 'active'
    LEFT JOIN subscription_plans sp ON ds.plan_id = sp.id
    WHERE d.id = ?
    LIMIT 1
");
$stmt->execute([$demenageur_id]);
$demenageur = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les leads récents (10 derniers)
$stmt = $db->prepare("
    SELECT dl.*, qr.name as client_name, qr.postal_code_from, qr.postal_code_to,
           qr.total_volume, qr.moving_date, qr.created_at as request_date
    FROM demenageur_leads dl
    INNER JOIN quote_requests qr ON dl.quote_request_id = qr.id
    WHERE dl.demenageur_id = ?
    ORDER BY dl.sent_at DESC
    LIMIT 10
");
$stmt->execute([$demenageur_id]);
$recent_leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques du mois
$stmt = $db->prepare("
    SELECT
        COUNT(*) as total_leads,
        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as pending_leads,
        SUM(CASE WHEN status = 'quoted' THEN 1 ELSE 0 END) as quoted_leads,
        SUM(CASE WHEN status = 'won' THEN 1 ELSE 0 END) as won_leads,
        SUM(CASE WHEN status = 'viewed' THEN 1 ELSE 0 END) as viewed_leads
    FROM demenageur_leads
    WHERE demenageur_id = ?
        AND MONTH(sent_at) = MONTH(CURRENT_DATE())
        AND YEAR(sent_at) = YEAR(CURRENT_DATE())
");
$stmt->execute([$demenageur_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculer taux de conversion
$conversion_rate = $stats['quoted_leads'] > 0
    ? round(($stats['won_leads'] / $stats['quoted_leads']) * 100, 1)
    : 0;

// Leads restants ce mois
$leads_per_month = intval($demenageur['leads_per_month']);
$leads_used = intval($demenageur['leads_used_this_month']);
$leads_remaining = $leads_per_month > 0 ? max(0, $leads_per_month - $leads_used) : '∞';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Déménageur - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .dashboard {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 32px;
        }

        .plan-badge {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-card .icon {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 42px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #718096;
            font-size: 14px;
        }

        .stat-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card.primary .value,
        .stat-card.primary .label {
            color: white;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }

        .action-btn {
            padding: 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #2d3748;
            font-weight: 600;
            transition: 0.3s;
        }

        .action-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .action-btn .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .leads-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .leads-section h2 {
            margin: 0 0 25px 0;
            font-size: 24px;
        }

        .leads-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leads-table thead {
            background: #f7fafc;
        }

        .leads-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
        }

        .leads-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .leads-table tr:hover {
            background: #f7fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-sent {
            background: #fef3c7;
            color: #92400e;
        }

        .status-viewed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-quoted {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-won {
            background: #d1fae5;
            color: #065f46;
        }

        .status-lost {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-view {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-view:hover {
            background: #5568d3;
        }

        .alert {
            padding: 18px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #fde68a;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #bfdbfe;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .leads-table {
                font-size: 14px;
            }

            .leads-table th,
            .leads-table td {
                padding: 10px;
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
                <a href="leads.php" style="color: #2d3748; text-decoration: none;">Mes Leads</a>
                <a href="profil.php" style="color: #2d3748; text-decoration: none;">Mon Profil</a>
                <a href="abonnement.php" style="color: #2d3748; text-decoration: none;">Abonnement</a>
                <a href="logout.php" style="color: #f56565; text-decoration: none;">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="dashboard">
        <div class="dashboard-header">
            <div>
                <h1>👋 Bonjour, <?= htmlspecialchars($demenageur['company_name']) ?></h1>
                <p style="color: #718096; margin: 5px 0 0 0;">Voici votre activité en un coup d'œil</p>
            </div>
            <div class="plan-badge">
                Plan <?= htmlspecialchars($demenageur['plan_name']) ?>
            </div>
        </div>

        <?php if ($demenageur['status'] === 'pending'): ?>
            <div class="alert alert-warning">
                ⏳ <strong>Compte en attente de validation</strong> - Notre équipe vérifie vos informations. Vous serez notifié sous 48h.
            </div>
        <?php endif; ?>

        <?php if ($demenageur['verified'] && $leads_remaining !== '∞' && $leads_remaining <= 5): ?>
            <div class="alert alert-info">
                ℹ️ <strong>Attention:</strong> Il vous reste seulement <?= $leads_remaining ?> leads ce mois-ci.
                <a href="abonnement.php" style="color: #1e40af; font-weight: 600;">Upgrader mon plan</a>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="icon">📊</div>
                <div class="value"><?= $stats['total_leads'] ?></div>
                <div class="label">Leads ce mois</div>
            </div>

            <div class="stat-card">
                <div class="icon">📬</div>
                <div class="value"><?= $stats['pending_leads'] ?></div>
                <div class="label">En attente de réponse</div>
            </div>

            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="value"><?= $stats['won_leads'] ?></div>
                <div class="label">Devis acceptés</div>
            </div>

            <div class="stat-card">
                <div class="icon">📈</div>
                <div class="value"><?= $conversion_rate ?>%</div>
                <div class="label">Taux de conversion</div>
            </div>

            <div class="stat-card">
                <div class="icon">🎯</div>
                <div class="value"><?= is_numeric($leads_remaining) ? $leads_remaining : $leads_remaining ?></div>
                <div class="label">Leads restants</div>
                <?php if (is_numeric($leads_remaining) && $leads_per_month > 0): ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= ($leads_remaining / $leads_per_month) * 100 ?>%"></div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="stat-card">
                <div class="icon">⭐</div>
                <div class="value"><?= number_format($demenageur['average_rating'], 1) ?>/5</div>
                <div class="label">Note moyenne (<?= $demenageur['total_reviews'] ?> avis)</div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <a href="leads.php" class="action-btn">
                <div class="icon">📋</div>
                <div>Tous mes leads</div>
            </a>
            <a href="profil.php" class="action-btn">
                <div class="icon">⚙️</div>
                <div>Modifier mon profil</div>
            </a>
            <a href="abonnement.php" class="action-btn">
                <div class="icon">💳</div>
                <div>Mon abonnement</div>
            </a>
            <a href="statistiques.php" class="action-btn">
                <div class="icon">📊</div>
                <div>Statistiques</div>
            </a>
        </div>

        <!-- Leads récents -->
        <div class="leads-section">
            <h2>🎯 Leads Récents</h2>

            <?php if (empty($recent_leads)): ?>
                <div style="text-align: center; padding: 60px 20px; color: #718096;">
                    <div style="font-size: 64px; margin-bottom: 20px;">📭</div>
                    <p style="font-size: 18px; margin-bottom: 10px;">Aucun lead pour le moment</p>
                    <p>Les leads correspondant à votre zone apparaîtront ici automatiquement</p>
                </div>
            <?php else: ?>
                <table class="leads-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Trajet</th>
                            <th>Volume</th>
                            <th>Date déménagement</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_leads as $lead): ?>
                            <?php
                            $lead_data = json_decode($lead['lead_details'], true);
                            $quote_data = $lead_data['quote_data'] ?? [];

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
                            <tr>
                                <td><?= date('d/m/Y', strtotime($lead['sent_at'])) ?></td>
                                <td><strong><?= htmlspecialchars($lead['client_name']) ?></strong></td>
                                <td><?= htmlspecialchars($lead['postal_code_from']) ?> → <?= htmlspecialchars($lead['postal_code_to']) ?></td>
                                <td><?= round($lead['total_volume'], 1) ?> m³</td>
                                <td><?= $lead['moving_date'] ? date('d/m/Y', strtotime($lead['moving_date'])) : '-' ?></td>
                                <td>
                                    <span class="status-badge status-<?= $lead['status'] ?>">
                                        <?= $status_labels[$lead['status']] ?? $lead['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="lead-details.php?id=<?= $lead['id'] ?>" class="btn-view">Voir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: center; margin-top: 25px;">
                    <a href="leads.php" style="color: #667eea; font-weight: 600; text-decoration: none;">
                        Voir tous mes leads →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
