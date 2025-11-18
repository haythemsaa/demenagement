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

// Récupérer abonnement actuel
$stmt = $db->prepare("
    SELECT ds.*, sp.*, ds.id as subscription_id, sp.name as plan_name
    FROM demenageur_subscriptions ds
    INNER JOIN subscription_plans sp ON ds.plan_id = sp.id
    WHERE ds.demenageur_id = ? AND ds.status = 'active'
    LIMIT 1
");
$stmt->execute([$demenageur_id]);
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer tous les plans
$stmt = $db->query("SELECT * FROM subscription_plans WHERE is_active = TRUE ORDER BY display_order");
$all_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Abonnement - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .abonnement-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .current-plan {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
        }

        .current-plan h2 {
            margin: 0 0 20px 0;
            font-size: 28px;
        }

        .plan-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .plan-info-item {
            background: rgba(255,255,255,0.15);
            padding: 20px;
            border-radius: 10px;
        }

        .plan-info-item .value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .plan-info-item .label {
            opacity: 0.9;
            font-size: 14px;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .plan-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .plan-card.current {
            border: 3px solid #667eea;
        }

        .plan-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .plan-price {
            font-size: 42px;
            font-weight: 700;
            color: #667eea;
            margin: 20px 0;
        }

        .plan-price .currency {
            font-size: 20px;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .plan-features li {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .plan-features li::before {
            content: '✓ ';
            color: #48bb78;
            font-weight: bold;
            margin-right: 8px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-current {
            background: #e2e8f0;
            color: #718096;
            cursor: default;
        }

        .usage-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
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
                <a href="abonnement.php" style="color: #667eea; font-weight: 600; text-decoration: none;">Abonnement</a>
                <a href="logout.php" style="color: #f56565; text-decoration: none;">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="abonnement-page">
        <h1 style="font-size: 36px; margin-bottom: 30px;">💳 Mon Abonnement</h1>

        <!-- Abonnement actuel -->
        <div class="current-plan">
            <h2>Plan Actuel : <?= htmlspecialchars($subscription['plan_name']) ?></h2>
            <p>
                <?php if ($subscription['auto_renew']): ?>
                    Renouvellement automatique le <?= date('d/m/Y', strtotime($subscription['next_billing_date'])) ?>
                <?php else: ?>
                    Expire le <?= date('d/m/Y', strtotime($subscription['end_date'])) ?>
                <?php endif; ?>
            </p>

            <div class="plan-info-grid">
                <div class="plan-info-item">
                    <div class="value"><?= $subscription['leads_per_month'] > 0 ? $subscription['leads_per_month'] : '∞' ?></div>
                    <div class="label">Leads par mois</div>
                </div>
                <div class="plan-info-item">
                    <div class="value"><?= $subscription['leads_used_this_month'] ?></div>
                    <div class="label">Leads utilisés ce mois</div>
                </div>
                <div class="plan-info-item">
                    <div class="value"><?= $subscription['leads_per_month'] > 0 ? max(0, $subscription['leads_per_month'] - $subscription['leads_used_this_month']) : '∞' ?></div>
                    <div class="label">Leads restants</div>
                </div>
                <div class="plan-info-item">
                    <div class="value"><?= number_format($subscription['price_monthly'], 0) ?> €</div>
                    <div class="label">Par mois</div>
                </div>
            </div>
        </div>

        <!-- Utilisation du mois -->
        <?php if ($subscription['leads_per_month'] > 0): ?>
            <div class="usage-section">
                <h3>📊 Utilisation du mois</h3>
                <p><?= $subscription['leads_used_this_month'] ?> / <?= $subscription['leads_per_month'] ?> leads utilisés</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= min(100, ($subscription['leads_used_this_month'] / $subscription['leads_per_month']) * 100) ?>%"></div>
                </div>
                <p style="font-size: 14px; color: #718096; margin-top: 10px;">
                    Réinitialisation le <?= date('d/m/Y', strtotime($subscription['leads_reset_date'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Tous les plans -->
        <h2 style="font-size: 28px; margin: 60px 0 30px 0;">🎯 Changer de Plan</h2>
        <div class="plans-grid">
            <?php foreach ($all_plans as $plan):
                $is_current = $plan['id'] == $subscription['plan_id'];
                $features = json_decode($plan['features_list'], true);
            ?>
                <div class="plan-card <?= $is_current ? 'current' : '' ?>">
                    <div class="plan-name"><?= htmlspecialchars($plan['name']) ?></div>
                    <div class="plan-price">
                        <?php if ($plan['price_monthly'] > 0): ?>
                            <span class="currency">€</span><?= number_format($plan['price_monthly'], 0) ?><span style="font-size: 16px; font-weight: normal; color: #718096;">/mois</span>
                        <?php else: ?>
                            <span style="font-size: 32px;">Gratuit</span>
                        <?php endif; ?>
                    </div>

                    <ul class="plan-features">
                        <?php foreach (array_slice($features, 0, 5) as $feature): ?>
                            <li><?= htmlspecialchars($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($is_current): ?>
                        <button class="btn btn-current">Plan actuel</button>
                    <?php else: ?>
                        <a href="#" class="btn btn-primary" onclick="alert('Intégration Stripe à venir'); return false;">
                            <?= $plan['price_monthly'] > $subscription['price_monthly'] ? 'Upgrader' : 'Downgrader' ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 60px; color: #718096;">
            <p>Besoin d'aide ? <a href="/contact.php" style="color: #667eea; font-weight: 600;">Contactez notre support</a></p>
        </div>
    </div>
</body>
</html>
