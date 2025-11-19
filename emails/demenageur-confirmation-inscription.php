<?php
/**
 * Template Email: Confirmation d'inscription déménageur
 *
 * Variables:
 * - $demenageur: array avec les infos du déménageur
 */

$subject = "Bienvenue sur " . SITE_NAME . " - Votre inscription est en cours de validation";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #2d3748; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 40px 30px; }
        .cta-button { display: inline-block; padding: 18px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: bold; margin: 20px 0; }
        .info-box { background: #f7fafc; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .footer { background: #2d3748; color: white; padding: 30px; text-align: center; }
        .timeline { margin: 30px 0; }
        .timeline-item { display: flex; gap: 20px; margin: 20px 0; }
        .timeline-number { width: 40px; height: 40px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Inscription Bien Reçue !</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong><?= htmlspecialchars($demenageur['contact_name']) ?></strong>,</p>

            <p>Nous avons bien reçu votre demande d'inscription pour <strong><?= htmlspecialchars($demenageur['company_name']) ?></strong> sur notre plateforme.</p>

            <div class="info-box">
                <h3 style="margin-top: 0;">📋 Récapitulatif de votre inscription</h3>
                <p><strong>Entreprise :</strong> <?= htmlspecialchars($demenageur['company_name']) ?></p>
                <p><strong>SIRET :</strong> <?= htmlspecialchars($demenageur['siret']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($demenageur['email']) ?></p>
                <p><strong>Zones de couverture :</strong> <?= count(json_decode($demenageur['coverage_zones'] ?? '[]', true)) ?> zone(s)</p>
                <p><strong>Plan :</strong> Gratuit (30 jours d'essai - 5 leads offerts)</p>
            </div>

            <h3>🔄 Que se passe-t-il maintenant ?</h3>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-number">1</div>
                    <div>
                        <strong>Validation de votre compte</strong><br>
                        <span style="color: #718096;">Notre équipe vérifie vos informations et documents (SIRET, assurance) sous 24-48h ouvrées.</span>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-number">2</div>
                    <div>
                        <strong>Activation</strong><br>
                        <span style="color: #718096;">Vous recevrez un email de confirmation dès que votre compte sera validé.</span>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-number">3</div>
                    <div>
                        <strong>Premiers leads</strong><br>
                        <span style="color: #718096;">Vous commencerez à recevoir des leads qualifiés dans votre zone de couverture.</span>
                    </div>
                </div>
            </div>

            <h3>💡 En attendant la validation</h3>

            <p>Voici quelques conseils pour optimiser votre profil :</p>
            <ul>
                <li>✅ Vérifiez que votre assurance RC Pro est à jour</li>
                <li>✅ Préparez des photos de vos réalisations</li>
                <li>✅ Réfléchissez à vos arguments de vente</li>
                <li>✅ Pensez à votre stratégie tarifaire (3 niveaux recommandés)</li>
            </ul>

            <div style="text-align: center; margin: 40px 0;">
                <a href="<?= SITE_URL ?>/demenageur/login.php" class="cta-button">
                    Accéder à mon compte
                </a>
            </div>

            <div style="background: #ebf8ff; border-left: 4px solid #4299e1; padding: 20px; border-radius: 5px; margin: 30px 0;">
                <strong>❓ Questions ?</strong><br>
                <p style="margin: 10px 0 0 0;">
                    Notre équipe est là pour vous aider :<br>
                    📧 Email : <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a><br>
                    📞 Téléphone : <?= PHONE_NUMBER ?><br>
                    ⏰ Du lundi au vendredi, 9h-18h
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>🚚 <?= SITE_NAME ?></strong></p>
            <p>La plateforme de génération de leads pour déménageurs</p>
            <p style="font-size: 12px; margin-top: 20px; opacity: 0.8;">
                Vous recevez cet email suite à votre inscription sur notre plateforme.
            </p>
        </div>
    </div>
</body>
</html>
