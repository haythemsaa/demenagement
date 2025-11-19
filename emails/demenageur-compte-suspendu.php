<?php
/**
 * Email envoyé lors de la suspension d'un compte déménageur
 *
 * Variables requises :
 * - $company_name : Nom de l'entreprise
 * - $contact_name : Nom du contact
 * - $suspension_reason : Raison de la suspension
 * - $suspension_date : Date de la suspension (format Y-m-d)
 */

$suspension_formatted = date('d/m/Y à H:i', strtotime($suspension_date));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte suspendu</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f7fafc;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden;">

                    <!-- En-tête avec dégradé rouge -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #fc8181 0%, #f56565 100%); padding: 40px; text-align: center; color: white;">
                            <div style="font-size: 64px; margin-bottom: 15px;">🚫</div>
                            <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: 700;">Compte Suspendu</h1>
                            <p style="margin: 0; font-size: 16px; opacity: 0.95;">Action requise</p>
                        </td>
                    </tr>

                    <!-- Message principal -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Bonjour <strong><?= htmlspecialchars($contact_name) ?></strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Nous vous informons que votre compte <strong><?= htmlspecialchars($company_name) ?></strong> sur <?= SITE_NAME ?> a été <strong>suspendu</strong> le <?= $suspension_formatted ?>.
                            </p>

                            <!-- Alerte importante -->
                            <div style="background: #fff5f5; border: 3px solid #fc8181; border-radius: 10px; padding: 25px; margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; color: #c53030; font-size: 18px;">⚠️ Important</h3>
                                <p style="margin: 0; color: #742a2a; font-size: 15px; line-height: 1.6;">
                                    Pendant la durée de la suspension, vous <strong>ne recevrez plus de nouveaux leads</strong> et votre profil ne sera plus visible sur la plateforme.
                                </p>
                            </div>

                            <!-- Raison de la suspension -->
                            <div style="background: #edf2f7; border-left: 4px solid #fc8181; padding: 25px; margin: 30px 0; border-radius: 8px;">
                                <h3 style="margin: 0 0 15px 0; color: #2d3748; font-size: 18px;">📌 Motif de la suspension</h3>
                                <p style="margin: 0; color: #2d3748; font-size: 15px; line-height: 1.6;">
                                    <?= htmlspecialchars($suspension_reason) ?>
                                </p>
                            </div>

                            <!-- Conséquences de la suspension -->
                            <div style="margin: 40px 0;">
                                <h3 style="margin: 0 0 20px 0; color: #2d3748; font-size: 20px;">🔒 Accès limités pendant la suspension</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="color: #f56565; font-size: 20px; margin-right: 12px;">✗</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Nouveaux leads</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Aucun nouveau lead ne vous sera attribué</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="color: #f56565; font-size: 20px; margin-right: 12px;">✗</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Visibilité sur le site</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Votre profil est masqué des résultats de recherche</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="color: #48bb78; font-size: 20px; margin-right: 12px;">✓</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Leads en cours</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Vous conservez l'accès à vos leads existants</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="color: #48bb78; font-size: 20px; margin-right: 12px;">✓</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Connexion au dashboard</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Vous pouvez toujours accéder à votre espace</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Actions à entreprendre -->
                            <div style="background: #e6fffa; border: 2px solid #4fd1c5; border-radius: 10px; padding: 25px; margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; color: #234e52; font-size: 18px;">🔧 Comment réactiver votre compte ?</h3>
                                <p style="margin: 0 0 15px 0; color: #2d3748; font-size: 15px; line-height: 1.6;">
                                    Pour résoudre cette situation et réactiver votre compte, veuillez :
                                </p>
                                <ol style="margin: 0; padding-left: 20px; color: #2d3748; font-size: 15px; line-height: 1.8;">
                                    <li>Prendre connaissance du motif de suspension ci-dessus</li>
                                    <li>Contacter notre équipe par email ou téléphone</li>
                                    <li>Fournir les justificatifs ou corrections nécessaires</li>
                                    <li>Attendre la validation de notre équipe</li>
                                </ol>
                            </div>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Nous restons à votre disposition pour vous accompagner dans la résolution de cette situation.
                            </p>

                            <!-- Bouton contact -->
                            <div style="text-align: center; margin: 40px 0;">
                                <a href="mailto:<?= SUPPORT_EMAIL ?>" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 30px; font-size: 18px; font-weight: 600; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);">
                                    📧 Contacter le support
                                </a>
                            </div>

                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background-color: #f7fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 15px 0; color: #718096; font-size: 14px;">
                                <strong>Support <?= SITE_NAME ?></strong>
                            </p>
                            <p style="margin: 0 0 10px 0;">
                                <a href="mailto:<?= SUPPORT_EMAIL ?>" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                    <?= SUPPORT_EMAIL ?>
                                </a>
                            </p>
                            <p style="margin: 0 0 20px 0; color: #718096; font-size: 14px;">
                                Tel : 01 XX XX XX XX
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
