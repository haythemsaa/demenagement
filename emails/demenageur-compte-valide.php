<?php
/**
 * Template Email: Compte déménageur validé
 */

$subject = "🎉 Votre compte " . SITE_NAME . " est validé ! Commencez à recevoir des leads";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #2d3748; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0 0 10px 0; font-size: 32px; }
        .content { padding: 40px 30px; }
        .cta-button { display: inline-block; padding: 18px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: bold; margin: 20px 0; }
        .features { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .feature { background: #f7fafc; padding: 20px; border-radius: 10px; text-align: center; }
        .feature-icon { font-size: 40px; margin-bottom: 10px; }
        .footer { background: #2d3748; color: white; padding: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="font-size: 60px; margin-bottom: 20px;">🎉</div>
            <h1>Compte Validé !</h1>
            <p style="font-size: 18px; opacity: 0.95;">Vous pouvez maintenant recevoir vos premiers leads</p>
        </div>

        <div class="content">
            <p>Bonjour <strong><?= htmlspecialchars($demenageur['contact_name']) ?></strong>,</p>

            <p>Excellente nouvelle ! Votre compte <strong><?= htmlspecialchars($demenageur['company_name']) ?></strong> a été validé par notre équipe.</p>

            <div style="background: #d1fae5; border: 2px solid #9ae6b4; border-radius: 10px; padding: 25px; text-align: center; margin: 30px 0;">
                <div style="font-size: 48px; margin-bottom: 10px;">🎁</div>
                <h3 style="margin: 0 0 10px 0; color: #065f46;">Essai Gratuit Activé</h3>
                <p style="margin: 0; color: #047857; font-size: 18px;">
                    <strong>30 jours gratuits + 5 leads offerts</strong><br>
                    <span style="font-size: 14px;">Profitez-en pour tester notre plateforme</span>
                </p>
            </div>

            <h3>🚀 Démarrez dès maintenant</h3>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">📊</div>
                    <strong>Accédez à votre dashboard</strong><br>
                    <span style="font-size: 14px; color: #718096;">Suivez vos stats en temps réel</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎯</div>
                    <strong>Recevez des leads</strong><br>
                    <span style="font-size: 14px; color: #718096;">Qualifiés pour votre zone</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">📧</div>
                    <strong>Envoyez vos devis</strong><br>
                    <span style="font-size: 14px; color: #718096;">En quelques clics</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <strong>Augmentez votre CA</strong><br>
                    <span style="font-size: 14px; color: #718096;">Jusqu'à +40%</span>
                </div>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="<?= SITE_URL ?>/demenageur/dashboard.php" class="cta-button">
                    🚀 Accéder à mon dashboard
                </a>
            </div>

            <h3>💡 Conseils pour bien démarrer</h3>

            <ol style="line-height: 1.8;">
                <li><strong>Répondez rapidement</strong> - Les déménageurs qui répondent en moins de 2h ont 3x plus de chances</li>
                <li><strong>Soignez vos devis</strong> - Détaillez vos services, garanties et disponibilités</li>
                <li><strong>Proposez plusieurs options</strong> - Budget standard + Premium pour maximiser vos chances</li>
                <li><strong>Mettez en avant vos atouts</strong> - Certifications, avis clients, années d'expérience</li>
                <li><strong>Suivez vos statistiques</strong> - Optimisez votre taux de conversion via le dashboard</li>
            </ol>

            <div style="background: #fff5f5; border-left: 4px solid #f56565; padding: 20px; border-radius: 5px; margin: 30px 0;">
                <strong>⏰ N'oubliez pas !</strong><br>
                <p style="margin: 10px 0 0 0;">
                    Votre période d'essai gratuite expire le <strong><?= date('d/m/Y', strtotime('+30 days')) ?></strong>.<br>
                    Pensez à choisir votre plan avant cette date pour continuer à recevoir des leads.
                </p>
            </div>

            <p style="text-align: center; color: #718096;">
                Besoin d'aide ? Notre équipe est là pour vous accompagner !<br>
                <a href="/contact.php" style="color: #667eea; font-weight: 600;">Contactez le support</a>
            </p>
        </div>

        <div class="footer">
            <p><strong>🚚 <?= SITE_NAME ?></strong></p>
            <p>La plateforme de génération de leads pour déménageurs</p>
            <p style="font-size: 12px; margin-top: 20px; opacity: 0.8;">
                Votre compte est maintenant actif. Bonnes ventes !
            </p>
        </div>
    </div>
</body>
</html>
