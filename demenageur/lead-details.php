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
$lead_id = intval($_GET['id'] ?? 0);

if (!$lead_id) {
    header('Location: leads.php');
    exit;
}

// Récupérer le lead avec toutes les infos
$stmt = $db->prepare("
    SELECT dl.*, qr.*,
           dl.id as lead_id, dl.status as lead_status, dl.sent_at as lead_sent_at,
           dl.lead_details as lead_data_json
    FROM demenageur_leads dl
    INNER JOIN quote_requests qr ON dl.quote_request_id = qr.id
    WHERE dl.id = ? AND dl.demenageur_id = ?
    LIMIT 1
");
$stmt->execute([$lead_id, $demenageur_id]);
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lead) {
    $_SESSION['error'] = 'Lead introuvable';
    header('Location: leads.php');
    exit;
}

// Marquer comme "viewed" si c'était "sent"
if ($lead['lead_status'] === 'sent') {
    $stmt = $db->prepare("
        UPDATE demenageur_leads
        SET status = 'viewed', viewed_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$lead_id]);
    $lead['lead_status'] = 'viewed';
}

$lead_data = json_decode($lead['lead_data_json'], true);
$match_score = $lead_data['match_score'] ?? 0;

$services = json_decode($lead['services'] ?? '[]', true);
$inventory = json_decode($lead['inventory'] ?? 'null', true);

$success = '';
$error = '';

// Traitement du formulaire de devis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quote'])) {
    $quote_amount = floatval($_POST['quote_amount'] ?? 0);
    $quote_message = trim($_POST['quote_message'] ?? '');

    if ($quote_amount <= 0) {
        $error = 'Veuillez indiquer un montant valide pour votre devis.';
    } elseif (empty($quote_message)) {
        $error = 'Veuillez ajouter un message pour le client.';
    } else {
        // Enregistrer le devis
        $quote_details = json_encode([
            'amount' => $quote_amount,
            'message' => $quote_message,
            'sent_at' => date('Y-m-d H:i:s')
        ]);

        $response_time_hours = round((time() - strtotime($lead['lead_sent_at'])) / 3600, 1);

        $stmt = $db->prepare("
            UPDATE demenageur_leads
            SET status = 'quoted',
                quoted_at = NOW(),
                quote_amount = ?,
                quote_details = ?,
                response_time_hours = ?
            WHERE id = ?
        ");
        $stmt->execute([$quote_amount, $quote_details, $response_time_hours, $lead_id]);

        // TODO: Envoyer email au client avec le nouveau devis

        $success = 'Votre devis a été envoyé avec succès au client !';
        $lead['lead_status'] = 'quoted';
        $lead['quote_amount'] = $quote_amount;
    }
}

// Traitement décliner le lead
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decline_lead'])) {
    $decline_reason = trim($_POST['decline_reason'] ?? '');

    $stmt = $db->prepare("
        UPDATE demenageur_leads
        SET status = 'declined'
        WHERE id = ?
    ");
    $stmt->execute([$lead_id]);

    header('Location: leads.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead #<?= $lead_id ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .lead-details-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-block;
        }

        .lead-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
        }

        .lead-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .lead-meta {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .score-badge {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: 700;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin: 0 0 25px 0;
            font-size: 22px;
            color: #2d3748;
        }

        .info-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
        }

        .info-value {
            color: #2d3748;
        }

        .route-visual {
            background: #f7fafc;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .route-location {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 10px;
            display: inline-block;
            min-width: 200px;
        }

        .route-location .postal {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .route-location .city {
            color: #4a5568;
            font-size: 14px;
        }

        .route-location .address {
            color: #718096;
            font-size: 12px;
            margin-top: 5px;
        }

        .route-arrow {
            font-size: 36px;
            color: #667eea;
            margin: 0 20px;
        }

        .highlight-box {
            background: #f0fff4;
            border: 2px solid #9ae6b4;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .highlight-box .amount {
            font-size: 36px;
            font-weight: 700;
            color: #38a169;
            margin-bottom: 5px;
        }

        .highlight-box .label {
            color: #2f855a;
            font-size: 14px;
        }

        .quote-form {
            background: #f7fafc;
            padding: 30px;
            border-radius: 15px;
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .btn-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .alert {
            padding: 18px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-sent { background: #fef3c7; color: #92400e; }
        .status-viewed { background: #dbeafe; color: #1e40af; }
        .status-quoted { background: #e0e7ff; color: #3730a3; }
        .status-won { background: #d1fae5; color: #065f46; }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .inventory-item {
            background: #f7fafc;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .inventory-item .icon {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .inventory-item .name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .inventory-item .qty {
            color: #667eea;
            font-size: 20px;
            font-weight: 700;
        }

        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .route-visual {
                display: flex;
                flex-direction: column;
            }

            .route-arrow {
                transform: rotate(90deg);
                margin: 20px 0;
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

    <div class="lead-details-page">
        <a href="leads.php" class="back-link">← Retour à mes leads</a>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Header du lead -->
        <div class="lead-header">
            <h1>Lead #<?= $lead_id ?> - <?= htmlspecialchars($lead['name']) ?></h1>
            <div class="lead-meta">
                <div class="meta-item">
                    <span>📅</span>
                    <span>Reçu le <?= date('d/m/Y à H:i', strtotime($lead['lead_sent_at'])) ?></span>
                </div>
                <div class="meta-item">
                    <span>⏰</span>
                    <span>Expire le <?= date('d/m/Y', strtotime($lead['expires_at'])) ?></span>
                </div>
                <div class="meta-item">
                    <div class="score-badge">
                        Score de pertinence : <?= round($match_score) ?>/100
                    </div>
                </div>
                <div class="meta-item">
                    <span class="status-badge status-<?= $lead['lead_status'] ?>">
                        <?php
                        $status_labels = [
                            'sent' => 'Nouveau',
                            'viewed' => 'Vu',
                            'quoted' => 'Devis envoyé',
                            'won' => 'Gagné',
                            'lost' => 'Perdu'
                        ];
                        echo $status_labels[$lead['lead_status']] ?? $lead['lead_status'];
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Trajet visualisé -->
        <div class="card">
            <h2>📍 Trajet du Déménagement</h2>
            <div class="route-visual">
                <div class="route-location">
                    <div class="postal"><?= htmlspecialchars($lead['postal_code_from']) ?></div>
                    <div class="city"><?= htmlspecialchars($lead['city_from']) ?></div>
                    <div class="address"><?= htmlspecialchars($lead['address_from']) ?></div>
                    <div style="margin-top: 15px; font-size: 13px;">
                        🏢 Étage <?= $lead['floor_from'] ?> <?= $lead['elevator_from'] ? '(avec ascenseur ✓)' : '(sans ascenseur ✗)' ?>
                    </div>
                </div>

                <div class="route-arrow">→</div>

                <div class="route-location">
                    <div class="postal"><?= htmlspecialchars($lead['postal_code_to']) ?></div>
                    <div class="city"><?= htmlspecialchars($lead['city_to']) ?></div>
                    <div class="address"><?= htmlspecialchars($lead['address_to']) ?></div>
                    <div style="margin-top: 15px; font-size: 13px;">
                        🏢 Étage <?= $lead['floor_to'] ?> <?= $lead['elevator_to'] ? '(avec ascenseur ✓)' : '(sans ascenseur ✗)' ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Détails principaux -->
            <div>
                <div class="card">
                    <h2>📋 Détails du Déménagement</h2>

                    <div class="info-row">
                        <div class="info-label">Volume estimé</div>
                        <div class="info-value"><strong><?= round($lead['total_volume'], 1) ?> m³</strong></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Date souhaitée</div>
                        <div class="info-value"><?= $lead['moving_date'] ? date('d/m/Y', strtotime($lead['moving_date'])) : 'À définir' ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Urgence</div>
                        <div class="info-value">
                            <?php
                            $urgency_labels = ['normal' => 'Normal', 'urgent' => '⚠️ Urgent', 'flexible' => 'Flexible'];
                            echo $urgency_labels[$lead['urgency']] ?? 'Normal';
                            ?>
                        </div>
                    </div>

                    <?php if (!empty($services)): ?>
                        <div class="info-row">
                            <div class="info-label">Services demandés</div>
                            <div class="info-value"><?= implode(', ', $services) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($lead['comments'])): ?>
                        <div class="info-row">
                            <div class="info-label">Commentaires</div>
                            <div class="info-value"><em><?= nl2br(htmlspecialchars($lead['comments'])) ?></em></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Inventaire si disponible -->
                <?php if ($inventory && is_array($inventory) && count($inventory) > 0): ?>
                    <div class="card" style="margin-top: 30px;">
                        <h2>📦 Inventaire Détaillé</h2>
                        <div class="inventory-grid">
                            <?php foreach ($inventory as $item_id => $qty):
                                if ($qty > 0):
                                    // Simplification - afficher item_id et quantité
                                    $item_name = ucfirst(str_replace('_', ' ', $item_id));
                            ?>
                                <div class="inventory-item">
                                    <div class="icon">📦</div>
                                    <div class="name"><?= htmlspecialchars($item_name) ?></div>
                                    <div class="qty">×<?= $qty ?></div>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Informations client -->
            <div>
                <div class="card">
                    <h2>👤 Informations Client</h2>

                    <div class="info-row">
                        <div class="info-label">Nom</div>
                        <div class="info-value"><strong><?= htmlspecialchars($lead['name']) ?></strong></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <a href="mailto:<?= htmlspecialchars($lead['email']) ?>" style="color: #667eea;">
                                <?= htmlspecialchars($lead['email']) ?>
                            </a>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">
                            <a href="tel:<?= htmlspecialchars($lead['phone']) ?>" style="color: #667eea;">
                                <?= htmlspecialchars($lead['phone']) ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Potentiel de revenu -->
                <div class="highlight-box" style="margin-top: 20px;">
                    <div class="label">💰 Potentiel de revenu</div>
                    <div class="amount"><?= number_format($lead['estimated_price'] * 1.2, 0, ',', ' ') ?> €</div>
                    <div style="font-size: 13px; color: #4a5568; margin-top: 5px;">
                        Basé sur l'estimation client de <?= number_format($lead['estimated_price'], 0, ',', ' ') ?> €
                    </div>
                </div>

                <div style="background: #ebf8ff; border-left: 4px solid #4299e1; padding: 20px; border-radius: 10px; margin-top: 20px;">
                    <strong>💡 Conseil</strong><br>
                    <p style="margin: 10px 0 0 0; color: #2d3748; font-size: 14px; line-height: 1.6;">
                        Les déménageurs qui répondent en moins de 2h ont <strong>3x plus de chances</strong> d'être choisis.
                        Soyez précis, professionnel et proposez plusieurs options tarifaires.
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulaire de devis -->
        <?php if ($lead['lead_status'] !== 'quoted' && $lead['lead_status'] !== 'won' && $lead['lead_status'] !== 'lost'): ?>
            <div class="card">
                <h2>📧 Envoyer Votre Devis</h2>

                <form method="POST" class="quote-form">
                    <div class="form-group">
                        <label>Montant de votre devis (€) <span style="color: #f56565;">*</span></label>
                        <input type="number" name="quote_amount" step="0.01" min="1" required
                               placeholder="Ex: 1850.00">
                        <small style="color: #718096; display: block; margin-top: 5px;">
                            Client attend environ <?= number_format($lead['estimated_price'], 0, ',', ' ') ?> €
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Message au client <span style="color: #f56565;">*</span></label>
                        <textarea name="quote_message" required placeholder="Décrivez votre offre en détail..."></textarea>
                        <small style="color: #718096; display: block; margin-top: 5px;">
                            Détaillez vos services, garanties, disponibilités et différenciants
                        </small>
                    </div>

                    <div class="btn-actions">
                        <button type="submit" name="submit_quote" class="btn btn-primary">
                            📧 Envoyer mon devis
                        </button>
                    </div>
                </form>

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;">

                <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir décliner ce lead ?');">
                    <input type="hidden" name="decline_reason" value="Non disponible">
                    <button type="submit" name="decline_lead" class="btn btn-danger">
                        ❌ Décliner ce lead
                    </button>
                </form>
            </div>
        <?php elseif ($lead['lead_status'] === 'quoted'): ?>
            <div class="card" style="background: #f0fff4; border: 2px solid #9ae6b4;">
                <h2>✅ Devis Envoyé</h2>
                <p>Vous avez envoyé votre devis de <strong><?= number_format($lead['quote_amount'], 2, ',', ' ') ?> €</strong> le <?= date('d/m/Y à H:i', strtotime($lead['quoted_at'])) ?>.</p>
                <p style="margin-top: 15px; color: #2f855a;">
                    Le client va comparer les offres et prendra sa décision sous 48-72h. Vous serez notifié de sa réponse.
                </p>
            </div>
        <?php elseif ($lead['lead_status'] === 'won'): ?>
            <div class="card" style="background: #d1fae5; border: 2px solid #34d399;">
                <h2>🎉 Félicitations ! Vous avez remporté ce lead</h2>
                <p>Le client a choisi votre offre. Contactez-le rapidement pour finaliser les détails.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
