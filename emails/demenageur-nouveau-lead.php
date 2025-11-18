<?php
/**
 * Template Email: Nouveau Lead pour Déménageur
 *
 * Variables disponibles:
 * - $demenageur: tableau avec infos du déménageur
 * - $lead: tableau avec infos du lead
 * - $quote_request: tableau avec détails de la demande
 * - $match_score: score de pertinence sur 100
 * - $lead_url: URL pour voir le lead
 */

$subject = "🎯 Nouveau lead déménagement #{$quote_request['id']} - " . $quote_request['postal_code_from'] . " → " . $quote_request['postal_code_to'];

$moving_date_formatted = $quote_request['moving_date']
    ? date('d/m/Y', strtotime($quote_request['moving_date']))
    : 'À définir';

$services = json_decode($quote_request['services'] ?? '[]', true);
$services_text = !empty($services) ? implode(', ', $services) : 'Aucun service supplémentaire';

$urgency_labels = [
    'normal' => 'Normal',
    'urgent' => '⚠️ Urgent',
    'flexible' => 'Flexible'
];
$urgency_text = $urgency_labels[$quote_request['urgency']] ?? 'Normal';

// Calcul du potentiel de revenu
$estimated_revenue = round($quote_request['estimated_price'] * 1.2); // +20% marge
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            margin: 0;
            padding: 0;
            background: #f7fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header .lead-id {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            font-size: 14px;
        }
        .alert-urgent {
            background: #fed7d7;
            color: #9b2c2c;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            border-left: 4px solid #fc8181;
        }
        .content {
            padding: 30px;
        }
        .match-score {
            background: #f0fff4;
            border: 2px solid #9ae6b4;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .match-score .score {
            font-size: 48px;
            font-weight: bold;
            color: #38a169;
            margin: 0;
        }
        .match-score .label {
            color: #2f855a;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .info-item {
            background: #f7fafc;
            padding: 15px;
            border-radius: 8px;
        }
        .info-item .label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
        }
        .route {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .route .location {
            display: inline-block;
            padding: 10px 20px;
            background: #f7fafc;
            border-radius: 5px;
            font-weight: bold;
        }
        .route .arrow {
            display: inline-block;
            margin: 0 15px;
            font-size: 24px;
            color: #667eea;
        }
        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .details-table td:first-child {
            font-weight: 600;
            color: #4a5568;
            width: 40%;
        }
        .cta-section {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 18px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6b4091 100%);
        }
        .urgency-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .urgency-badge.urgent {
            background: #fed7d7;
            color: #9b2c2c;
        }
        .revenue-box {
            background: #f0fff4;
            border-left: 4px solid #48bb78;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .revenue-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #38a169;
        }
        .tips {
            background: #ebf8ff;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin: 20px 0;
        }
        .tips h3 {
            margin-top: 0;
            color: #2c5282;
        }
        .tips ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .tips li {
            color: #2d3748;
            margin: 8px 0;
        }
        .footer {
            background: #2d3748;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
            opacity: 0.8;
        }
        .footer a {
            color: #90cdf4;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎯 Nouveau Lead Déménagement</h1>
            <div class="lead-id">Lead #<?= $quote_request['id'] ?></div>
        </div>

        <?php if ($quote_request['urgency'] === 'urgent'): ?>
            <div class="alert-urgent">
                ⚠️ URGENT - Le client souhaite déménager rapidement !
            </div>
        <?php endif; ?>

        <!-- Content -->
        <div class="content">
            <!-- Score de pertinence -->
            <div class="match-score">
                <p class="score"><?= round($match_score) ?>/100</p>
                <p class="label">Score de pertinence pour votre profil</p>
                <p style="font-size: 13px; color: #718096; margin-top: 10px;">
                    Ce lead correspond parfaitement à votre zone et vos services
                </p>
            </div>

            <!-- Informations principales -->
            <h2 style="margin-bottom: 20px;">📋 Détails de la Demande</h2>

            <!-- Trajet -->
            <div class="route">
                <div class="location">
                    📍 <?= htmlspecialchars($quote_request['postal_code_from']) ?> - <?= htmlspecialchars($quote_request['city_from']) ?>
                </div>
                <div class="arrow">➔</div>
                <div class="location">
                    📍 <?= htmlspecialchars($quote_request['postal_code_to']) ?> - <?= htmlspecialchars($quote_request['city_to']) ?>
                </div>
            </div>

            <!-- Grid d'infos clés -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Volume</div>
                    <div class="value"><?= round($quote_request['total_volume'], 1) ?> m³</div>
                </div>
                <div class="info-item">
                    <div class="label">Date souhaitée</div>
                    <div class="value"><?= $moving_date_formatted ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Étage départ</div>
                    <div class="value">
                        <?= $quote_request['floor_from'] ?>e
                        <?= $quote_request['elevator_from'] ? '(avec ✓)' : '(sans ✗)' ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">Étage arrivée</div>
                    <div class="value">
                        <?= $quote_request['floor_to'] ?>e
                        <?= $quote_request['elevator_to'] ? '(avec ✓)' : '(sans ✗)' ?>
                    </div>
                </div>
            </div>

            <!-- Détails supplémentaires -->
            <table class="details-table">
                <tr>
                    <td>Client</td>
                    <td><strong><?= htmlspecialchars($quote_request['name']) ?></strong></td>
                </tr>
                <tr>
                    <td>Services demandés</td>
                    <td><?= htmlspecialchars($services_text) ?></td>
                </tr>
                <tr>
                    <td>Urgence</td>
                    <td><span class="urgency-badge <?= $quote_request['urgency'] === 'urgent' ? 'urgent' : '' ?>"><?= $urgency_text ?></span></td>
                </tr>
                <?php if (!empty($quote_request['comments'])): ?>
                    <tr>
                        <td>Commentaires client</td>
                        <td><em><?= htmlspecialchars($quote_request['comments']) ?></em></td>
                    </tr>
                <?php endif; ?>
            </table>

            <!-- Potentiel de revenu -->
            <div class="revenue-box">
                <div style="font-size: 14px; color: #2f855a; margin-bottom: 5px;">💰 Revenu estimé</div>
                <div class="amount"><?= number_format($estimated_revenue, 0, ',', ' ') ?> €</div>
                <div style="font-size: 13px; color: #4a5568; margin-top: 5px;">
                    Basé sur notre analyse du marché pour ce type de déménagement
                </div>
            </div>

            <!-- CTA Principal -->
            <div class="cta-section">
                <h3 style="margin-top: 0;">⏰ Répondez rapidement pour maximiser vos chances !</h3>
                <p style="color: #718096; margin-bottom: 20px;">
                    Les déménageurs qui répondent en moins de 2h ont <strong>3x plus de chances</strong> d'être choisis
                </p>
                <a href="<?= $lead_url ?>" class="cta-button">
                    📧 Voir le lead et envoyer mon devis
                </a>
                <p style="font-size: 13px; color: #718096; margin-top: 15px;">
                    Ce lead expire dans <strong>7 jours</strong>
                </p>
            </div>

            <!-- Conseils -->
            <div class="tips">
                <h3>💡 Conseils pour remporter ce lead</h3>
                <ul>
                    <li><strong>Répondez dans les 2h</strong> - La rapidité est un facteur clé</li>
                    <li><strong>Soyez précis</strong> - Détaillez votre devis et vos services</li>
                    <li><strong>Ajoutez une touche personnelle</strong> - Mentionnez votre expérience locale</li>
                    <li><strong>Proposez plusieurs options</strong> - Budget standard et premium</li>
                    <li><strong>Rassurez le client</strong> - Mettez en avant vos assurances et garanties</li>
                </ul>
            </div>

            <!-- Informations complémentaires -->
            <div style="background: #f7fafc; padding: 20px; border-radius: 10px; margin-top: 30px;">
                <h4 style="margin-top: 0;">ℹ️ Informations Utiles</h4>
                <p style="margin: 5px 0;"><strong>Nombre de déménageurs contactés :</strong> 3-4 professionnels</p>
                <p style="margin: 5px 0;"><strong>Coût de ce lead :</strong> <?= number_format($lead['lead_cost'], 2, ',', ' ') ?> € (débité uniquement si vous envoyez un devis)</p>
                <p style="margin: 5px 0;"><strong>Expiration :</strong> <?= date('d/m/Y à H:i', strtotime($lead['expires_at'])) ?></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>🚚 <?= SITE_NAME ?></strong></p>
            <p>Plateforme de mise en relation déménageurs-clients</p>
            <p style="margin-top: 15px;">
                <a href="<?= SITE_URL ?>/demenageur/dashboard.php">Mon Dashboard</a> |
                <a href="<?= SITE_URL ?>/demenageur/profil.php">Mon Profil</a> |
                <a href="<?= SITE_URL ?>/demenageur/abonnement.php">Mon Abonnement</a>
            </p>
            <p style="font-size: 12px; margin-top: 20px;">
                Vous recevez cet email car vous êtes inscrit comme déménageur professionnel sur notre plateforme.<br>
                <a href="<?= SITE_URL ?>/demenageur/preferences.php">Gérer mes préférences email</a>
            </p>
        </div>
    </div>
</body>
</html>
