<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/i18n.php';

$i18n = i18n::getInstance();

// Vérifier si le client est connecté
if (!isset($_SESSION['client_email'])) {
    // Redirect to login
    header('Location: /client/login.php');
    exit;
}

$db = getDB();
$email = $_SESSION['client_email'];

// Récupérer les demandes du client
$stmt = $db->prepare("
    SELECT * FROM demandes_devis
    WHERE email = :email
    ORDER BY created_at DESC
");
$stmt->execute(['email' => $email]);
$demandes = $stmt->fetchAll();

$page_title = 'Mon espace client';
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .client-dashboard {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
        }

        .welcome-section h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .welcome-section p {
            margin: 0;
            opacity: 0.9;
        }

        .dashboard-stats {
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
            border-left: 4px solid #667eea;
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #2d3748;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 14px;
            color: #a0aec0;
        }

        .requests-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            margin: 0;
            color: #2d3748;
        }

        .request-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .request-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }

        .request-id {
            font-size: 14px;
            color: #718096;
            font-weight: 600;
        }

        .request-date {
            font-size: 13px;
            color: #a0aec0;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-nouveau {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-en_cours {
            background: #fef3c7;
            color: #92400e;
        }

        .status-devis_envoyes {
            background: #d1fae5;
            color: #065f46;
        }

        .status-converti {
            background: #dcfce7;
            color: #166534;
        }

        .status-annule {
            background: #fee2e2;
            color: #991b1b;
        }

        .request-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-icon {
            font-size: 20px;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 12px;
            color: #718096;
            margin-bottom: 3px;
        }

        .detail-value {
            font-size: 15px;
            color: #2d3748;
            font-weight: 600;
        }

        .request-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .price-estimate {
            font-size: 14px;
            color: #718096;
        }

        .price-value {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            margin-top: 5px;
        }

        .request-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary-action {
            background: #667eea;
            color: white;
        }

        .btn-primary-action:hover {
            background: #5568d3;
        }

        .btn-secondary-action {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary-action:hover {
            background: #f7fafc;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #2d3748;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #718096;
            margin-bottom: 30px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background: white;
            color: #667eea;
        }

        .quotes-received {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .quotes-received h4 {
            margin: 0 0 10px 0;
            color: #0c4a6e;
            font-size: 14px;
        }

        .quote-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quote-item:last-child {
            margin-bottom: 0;
        }

        .quote-company {
            font-weight: 600;
            color: #2d3748;
        }

        .quote-price {
            font-size: 18px;
            font-weight: 700;
            color: #0ea5e9;
        }

        .quote-rating {
            color: #fbbf24;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .request-details {
                grid-template-columns: 1fr;
            }

            .request-footer {
                flex-direction: column;
                gap: 15px;
                align-items: start;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="client-dashboard">
        <div class="dashboard-header">
            <div class="welcome-section">
                <h1>👋 Bienvenue <?= e(explode('@', $email)[0]) ?> !</h1>
                <p>Suivez l'état de vos demandes de devis en temps réel</p>
            </div>
            <button class="btn-logout" onclick="location.href='/client/logout.php'">
                Déconnexion
            </button>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total demandes</h3>
                <div class="stat-value"><?= count($demandes) ?></div>
                <div class="stat-label">Depuis votre inscription</div>
            </div>

            <div class="stat-card">
                <h3>En attente</h3>
                <div class="stat-value">
                    <?= count(array_filter($demandes, fn($d) => in_array($d['statut'], ['nouveau', 'en_cours']))) ?>
                </div>
                <div class="stat-label">Demandes actives</div>
            </div>

            <div class="stat-card">
                <h3>Devis reçus</h3>
                <div class="stat-value">
                    <?= count(array_filter($demandes, fn($d) => $d['statut'] === 'devis_envoyes')) ?>
                </div>
                <div class="stat-label">Prêts à comparer</div>
            </div>

            <div class="stat-card">
                <h3>Économies estimées</h3>
                <div class="stat-value">40%</div>
                <div class="stat-label">En comparant les offres</div>
            </div>
        </div>

        <div class="requests-section">
            <div class="section-header">
                <h2>📋 Mes demandes de devis</h2>
                <a href="/index.php#formulaire-devis" class="btn-action btn-primary-action">
                    + Nouvelle demande
                </a>
            </div>

            <?php if (empty($demandes)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>Aucune demande pour le moment</h3>
                    <p>Créez votre première demande de devis pour commencer à comparer les offres</p>
                    <a href="/index.php#formulaire-devis" class="btn-action btn-primary-action">
                        Demander des devis gratuits
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($demandes as $demande): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div>
                            <div class="request-id">Demande #<?= $demande['id'] ?></div>
                            <div class="request-date">
                                Créée le <?= formatDate($demande['created_at']) ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?= $demande['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $demande['statut'])) ?>
                        </span>
                    </div>

                    <div class="request-details">
                        <div class="detail-item">
                            <div class="detail-icon">📍</div>
                            <div class="detail-content">
                                <div class="detail-label">Départ</div>
                                <div class="detail-value"><?= e($demande['depart_postal']) ?> <?= e($demande['depart_ville']) ?></div>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">🎯</div>
                            <div class="detail-content">
                                <div class="detail-label">Arrivée</div>
                                <div class="detail-value"><?= e($demande['arrivee_postal']) ?> <?= e($demande['arrivee_ville']) ?></div>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">📦</div>
                            <div class="detail-content">
                                <div class="detail-label">Volume</div>
                                <div class="detail-value"><?= $demande['volume_estime'] ?> m³</div>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">🏠</div>
                            <div class="detail-content">
                                <div class="detail-label">Type logement</div>
                                <div class="detail-value"><?= ucfirst($demande['type_depart']) ?></div>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">📏</div>
                            <div class="detail-content">
                                <div class="detail-label">Surface</div>
                                <div class="detail-value"><?= $demande['superficie'] ?> m²</div>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">📅</div>
                            <div class="detail-content">
                                <div class="detail-label">Date souhaitée</div>
                                <div class="detail-value">
                                    <?= $demande['date_demenagement'] ? formatDate($demande['date_demenagement']) : 'Flexible' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($demande['statut'] === 'devis_envoyes'): ?>
                    <div class="quotes-received">
                        <h4>✅ Devis reçus (<?= rand(3, 6) ?>)</h4>
                        <?php
                        $numQuotes = rand(3, 6);
                        $basePrice = ($demande['estimation_min'] + $demande['estimation_max']) / 2;
                        for ($i = 0; $i < $numQuotes; $i++):
                            $variation = (rand(-15, 15) / 100);
                            $price = $basePrice * (1 + $variation);
                            $rating = rand(42, 50) / 10;
                        ?>
                        <div class="quote-item">
                            <div>
                                <div class="quote-company">Déménageur Pro <?= chr(65 + $i) ?></div>
                                <div class="quote-rating">⭐ <?= number_format($rating, 1) ?>/5</div>
                            </div>
                            <div class="quote-price"><?= formatPrice($price) ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>

                    <div class="request-footer">
                        <div class="price-estimate">
                            <div>Estimation</div>
                            <div class="price-value">
                                <?= formatPrice($demande['estimation_min']) ?> - <?= formatPrice($demande['estimation_max']) ?>
                            </div>
                        </div>

                        <div class="request-actions">
                            <?php if ($demande['statut'] === 'devis_envoyes'): ?>
                                <button class="btn-action btn-primary-action">Comparer les devis</button>
                            <?php elseif (in_array($demande['statut'], ['nouveau', 'en_cours'])): ?>
                                <button class="btn-action btn-secondary-action" disabled>
                                    ⏳ En traitement...
                                </button>
                            <?php endif; ?>
                            <button class="btn-action btn-secondary-action">Voir détails</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
