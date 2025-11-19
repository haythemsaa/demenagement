<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

if (!isset($_SESSION['demenageur_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$demenageur_id = $_SESSION['demenageur_id'];

// Récupérer toutes les transactions
$stmt = $db->prepare("
    SELECT
        t.*,
        sp.name as plan_name,
        sp.slug as plan_slug
    FROM transactions t
    LEFT JOIN demenageur_subscriptions ds ON t.subscription_id = ds.id
    LEFT JOIN subscription_plans sp ON ds.plan_id = sp.id
    WHERE t.demenageur_id = ?
    ORDER BY t.created_at DESC
");
$stmt->execute([$demenageur_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer statistiques
$total_paye = 0;
$total_reussi = 0;
$total_echoue = 0;

foreach ($transactions as $t) {
    if ($t['status'] === 'succeeded') {
        $total_paye += $t['amount'];
        $total_reussi++;
    } elseif ($t['status'] === 'failed') {
        $total_echoue++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Factures - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .factures-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-card .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #718096;
            font-size: 14px;
        }

        .transactions-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
        }

        .table-header h2 {
            margin: 0;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f7fafc;
        }

        th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-succeeded {
            background: #d1fae5;
            color: #065f46;
        }

        .status-failed {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-refunded {
            background: #e0e7ff;
            color: #3730a3;
        }

        .amount {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
        }

        .download-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }

        .download-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state .icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .nav-bar {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #2d3748;
            text-decoration: none;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: #667eea;
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="/index.php" style="font-size: 20px; font-weight: bold; color: #667eea; text-decoration: none;">
                🚚 <?= SITE_NAME ?>
            </a>
            <div class="nav-links">
                <a href="dashboard.php">Tableau de bord</a>
                <a href="leads.php">Mes Leads</a>
                <a href="profil.php">Mon Profil</a>
                <a href="abonnement.php">Abonnement</a>
                <a href="factures.php" style="color: #667eea; font-weight: 600;">Factures</a>
                <a href="logout.php" style="color: #f56565;">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="factures-page">
        <h1 style="font-size: 36px; margin-bottom: 30px;">💳 Mes Factures</h1>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">💰</div>
                <div class="value"><?= number_format($total_paye, 2) ?> €</div>
                <div class="label">Total payé</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="value"><?= $total_reussi ?></div>
                <div class="label">Paiements réussis</div>
            </div>
            <div class="stat-card">
                <div class="icon">❌</div>
                <div class="value"><?= $total_echoue ?></div>
                <div class="label">Paiements échoués</div>
            </div>
            <div class="stat-card">
                <div class="icon">📄</div>
                <div class="value"><?= count($transactions) ?></div>
                <div class="label">Total transactions</div>
            </div>
        </div>

        <!-- Tableau des transactions -->
        <div class="transactions-table">
            <div class="table-header">
                <h2>Historique des transactions</h2>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="empty-state">
                    <div class="icon">📄</div>
                    <h3>Aucune facture pour le moment</h3>
                    <p>Vos factures apparaîtront ici après votre premier paiement.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Facture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($t['created_at'])) ?></strong><br>
                                    <small style="color: #718096;"><?= date('H:i', strtotime($t['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong>
                                        <?php if ($t['plan_name']): ?>
                                            <?= htmlspecialchars($t['plan_name']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($t['description'] ?? 'Paiement') ?>
                                        <?php endif; ?>
                                    </strong><br>
                                    <?php if ($t['stripe_invoice_id']): ?>
                                        <small style="color: #718096;"><?= htmlspecialchars($t['stripe_invoice_id']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="amount"><?= number_format($t['amount'], 2) ?> €</span>
                                </td>
                                <td>
                                    <?php
                                    $status_class = 'status-' . $t['status'];
                                    $status_labels = [
                                        'succeeded' => '✅ Réussi',
                                        'failed' => '❌ Échoué',
                                        'pending' => '⏳ En attente',
                                        'refunded' => '↩️ Remboursé'
                                    ];
                                    $status_label = $status_labels[$t['status']] ?? $t['status'];
                                    ?>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= $status_label ?>
                                    </span>
                                    <?php if ($t['failed_reason']): ?>
                                        <br><small style="color: #f56565;"><?= htmlspecialchars($t['failed_reason']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['status'] === 'succeeded' && $t['stripe_invoice_id']): ?>
                                        <a href="#" class="download-btn" onclick="alert('Intégration Stripe à finaliser pour téléchargement PDF'); return false;">
                                            📥 Télécharger
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #cbd5e0;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Informations complémentaires -->
        <div style="background: #ebf8ff; border: 2px solid #4299e1; border-radius: 15px; padding: 25px; margin-top: 40px;">
            <h3 style="margin-top: 0; color: #2c5282;">💡 Informations</h3>
            <ul style="color: #2d3748; line-height: 1.8; margin: 0;">
                <li>Toutes vos factures sont conservées pendant <strong>7 ans</strong> pour conformité comptable</li>
                <li>Les factures au format PDF sont disponibles immédiatement après chaque paiement</li>
                <li>Vous recevez également chaque facture par email</li>
                <li>Pour toute question sur une facture, contactez <a href="mailto:<?= SUPPORT_EMAIL ?>" style="color: #667eea; font-weight: 600;">notre support</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
