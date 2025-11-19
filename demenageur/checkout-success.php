<?php
/**
 * Page de confirmation après paiement réussi
 */

session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

if (!isset($_SESSION['demenageur_id'])) {
    header('Location: login.php');
    exit;
}

$session_id = $_GET['session_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .success-page {
            max-width: 800px;
            margin: 100px auto;
            padding: 0 20px;
            text-align: center;
        }

        .success-icon {
            font-size: 120px;
            margin-bottom: 30px;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .success-card {
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .success-card h1 {
            font-size: 36px;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .success-card p {
            font-size: 18px;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            margin: 10px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 16px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            margin: 10px;
            border: 2px solid #667eea;
            transition: 0.3s;
        }

        .btn-secondary:hover {
            background: #f7fafc;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 50px 0;
            text-align: left;
        }

        .feature-item {
            background: #f7fafc;
            padding: 25px;
            border-radius: 15px;
        }

        .feature-item .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .feature-item h3 {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .feature-item p {
            font-size: 14px;
            color: #718096;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="success-page">
        <div class="success-card">
            <div class="success-icon">🎉</div>

            <h1>Abonnement Activé avec Succès !</h1>

            <p>
                Félicitations ! Votre paiement a été effectué et votre abonnement est maintenant actif.<br>
                Vous allez commencer à recevoir des leads qualifiés dès maintenant.
            </p>

            <p style="background: #d1fae5; padding: 20px; border-radius: 10px; color: #065f46; font-weight: 600;">
                ✓ Un email de confirmation a été envoyé à votre adresse
            </p>

            <div style="margin: 40px 0;">
                <a href="dashboard.php" class="btn-primary">
                    🎯 Accéder au Dashboard
                </a>
                <a href="abonnement.php" class="btn-secondary">
                    💼 Gérer mon abonnement
                </a>
            </div>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="icon">📧</div>
                    <h3>Notifications</h3>
                    <p>Recevez les nouveaux leads par email instantanément</p>
                </div>
                <div class="feature-item">
                    <div class="icon">📊</div>
                    <h3>Statistiques</h3>
                    <p>Suivez vos performances en temps réel</p>
                </div>
                <div class="feature-item">
                    <div class="icon">⚡</div>
                    <h3>Priorité</h3>
                    <p>Apparaissez en premier dans les résultats</p>
                </div>
                <div class="feature-item">
                    <div class="icon">🎧</div>
                    <h3>Support 7j/7</h3>
                    <p>Notre équipe est là pour vous aider</p>
                </div>
            </div>

            <div style="background: #ebf8ff; padding: 25px; border-radius: 15px; margin-top: 40px;">
                <h3 style="color: #2c5282; margin-top: 0;">💡 Conseils pour démarrer</h3>
                <ul style="text-align: left; color: #2d3748; line-height: 1.8;">
                    <li>Complétez à 100% votre profil pour maximiser vos chances</li>
                    <li>Vérifiez vos zones de couverture</li>
                    <li>Activez les notifications pour ne manquer aucun lead</li>
                    <li>Répondez rapidement aux demandes (dans les 2 heures idéalement)</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Envoyer un événement analytics (si configuré)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'purchase', {
                'transaction_id': '<?= $session_id ?>',
                'value': 0,
                'currency': 'EUR'
            });
        }

        // Confetti animation (optionnel)
        setTimeout(() => {
            console.log('🎊 Paiement réussi !');
        }, 500);
    </script>
</body>
</html>
