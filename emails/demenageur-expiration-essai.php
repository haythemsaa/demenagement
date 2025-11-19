<?php
/**
 * Email envoyé 7 jours avant l'expiration de l'essai gratuit
 *
 * Variables requises :
 * - $company_name : Nom de l'entreprise
 * - $contact_name : Nom du contact
 * - $expiration_date : Date d'expiration de l'essai (format Y-m-d)
 * - $leads_received : Nombre de leads reçus pendant l'essai
 * - $leads_converted : Nombre de leads convertis
 */

$expiration_formatted = date('d/m/Y', strtotime($expiration_date));
$days_remaining = max(0, floor((strtotime($expiration_date) - time()) / 86400));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre essai gratuit se termine bientôt</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f7fafc;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden;">

                    <!-- En-tête avec dégradé orange -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%); padding: 40px; text-align: center; color: white;">
                            <div style="font-size: 64px; margin-bottom: 15px;">⏰</div>
                            <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: 700;">Votre essai se termine dans <?= $days_remaining ?> jours</h1>
                            <p style="margin: 0; font-size: 16px; opacity: 0.95;">Ne perdez pas vos leads !</p>
                        </td>
                    </tr>

                    <!-- Message principal -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Bonjour <strong><?= htmlspecialchars($contact_name) ?></strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Votre période d'essai gratuit de 30 jours sur <strong><?= SITE_NAME ?></strong> arrive à expiration le <strong><?= $expiration_formatted ?></strong>.
                            </p>

                            <!-- Récapitulatif de l'essai -->
                            <div style="background: #edf2f7; border-left: 4px solid #f6ad55; padding: 25px; margin: 30px 0; border-radius: 8px;">
                                <h3 style="margin: 0 0 15px 0; color: #2d3748; font-size: 18px;">📊 Bilan de votre essai gratuit</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 10px 0; color: #2d3748; font-size: 16px;">
                                            <strong>Leads reçus :</strong>
                                        </td>
                                        <td align="right" style="padding: 10px 0; color: #f6ad55; font-size: 24px; font-weight: 700;">
                                            <?= $leads_received ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; color: #2d3748; font-size: 16px;">
                                            <strong>Leads convertis :</strong>
                                        </td>
                                        <td align="right" style="padding: 10px 0; color: #48bb78; font-size: 24px; font-weight: 700;">
                                            <?= $leads_converted ?>
                                        </td>
                                    </tr>
                                    <?php if ($leads_received > 0): ?>
                                    <tr>
                                        <td style="padding: 10px 0; color: #2d3748; font-size: 16px;">
                                            <strong>Taux de conversion :</strong>
                                        </td>
                                        <td align="right" style="padding: 10px 0; color: #667eea; font-size: 24px; font-weight: 700;">
                                            <?= round(($leads_converted / $leads_received) * 100, 1) ?>%
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Pour continuer à recevoir des leads qualifiés et développer votre activité, choisissez dès maintenant l'un de nos forfaits d'abonnement.
                            </p>

                            <!-- Bouton CTA -->
                            <div style="text-align: center; margin: 40px 0;">
                                <a href="<?= APP_URL ?>/demenageur/abonnement.php" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 30px; font-size: 18px; font-weight: 600; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);">
                                    💳 Choisir mon forfait
                                </a>
                            </div>

                            <!-- Avantages de l'abonnement -->
                            <div style="margin: 40px 0;">
                                <h3 style="margin: 0 0 20px 0; color: #2d3748; font-size: 20px; text-align: center;">🎯 Pourquoi s'abonner ?</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 24px; margin-right: 15px;">✓</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Leads qualifiés illimités</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Recevez tous les leads dans votre zone</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 24px; margin-right: 15px;">✓</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Priorité sur vos concurrents</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Apparaissez en premier dans les résultats</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 24px; margin-right: 15px;">✓</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Dashboard pro complet</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Suivez vos stats et performances</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 24px; margin-right: 15px;">✓</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Support prioritaire 7j/7</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Notre équipe à votre service</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Note importante -->
                            <div style="background: #fff5f5; border: 2px solid #fc8181; border-radius: 10px; padding: 20px; margin: 30px 0;">
                                <p style="margin: 0; color: #c53030; font-size: 14px; line-height: 1.6;">
                                    <strong>⚠️ Important :</strong> Après l'expiration de votre essai, vous ne recevrez plus de nouveaux leads tant qu'un abonnement n'est pas activé. Les leads en cours restent accessibles pendant 30 jours supplémentaires.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background-color: #f7fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 15px 0; color: #718096; font-size: 14px;">
                                Des questions ? Notre équipe est là pour vous aider
                            </p>
                            <p style="margin: 0 0 20px 0;">
                                <a href="mailto:<?= SUPPORT_EMAIL ?>" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                    <?= SUPPORT_EMAIL ?>
                                </a>
                            </p>
                            <div style="margin: 20px 0; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                <p style="margin: 0; color: #a0aec0; font-size: 12px;">
                                    © <?= date('Y') ?> <?= SITE_NAME ?> - Tous droits réservés<br>
                                    Cet email a été envoyé à <strong><?= $company_name ?></strong>
                                </p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
