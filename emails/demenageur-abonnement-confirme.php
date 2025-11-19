<?php
/**
 * Email envoyé après confirmation d'un abonnement payant
 *
 * Variables requises :
 * - $company_name : Nom de l'entreprise
 * - $contact_name : Nom du contact
 * - $plan_name : Nom du forfait souscrit
 * - $price : Prix mensuel
 * - $billing_period : Période de facturation (monthly/yearly)
 * - $start_date : Date de début (format Y-m-d)
 * - $next_billing_date : Prochaine date de facturation (format Y-m-d)
 * - $leads_per_month : Nombre de leads par mois
 * - $invoice_url : URL de la facture (optionnel)
 */

$start_formatted = date('d/m/Y', strtotime($start_date));
$next_billing_formatted = date('d/m/Y', strtotime($next_billing_date));
$period_label = $billing_period === 'yearly' ? 'par an' : 'par mois';
$leads_label = $leads_per_month > 0 ? $leads_per_month . ' leads/mois' : 'Leads illimités';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement confirmé</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f7fafc;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden;">

                    <!-- En-tête avec dégradé vert -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); padding: 40px; text-align: center; color: white;">
                            <div style="font-size: 64px; margin-bottom: 15px;">🎉</div>
                            <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: 700;">Abonnement Confirmé !</h1>
                            <p style="margin: 0; font-size: 16px; opacity: 0.95;">Bienvenue dans votre forfait <?= htmlspecialchars($plan_name) ?></p>
                        </td>
                    </tr>

                    <!-- Message principal -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Bonjour <strong><?= htmlspecialchars($contact_name) ?></strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Félicitations ! Votre abonnement au forfait <strong><?= htmlspecialchars($plan_name) ?></strong> a été activé avec succès.
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Vous pouvez dès maintenant profiter de tous les avantages de votre forfait et commencer à recevoir des leads qualifiés dans votre zone de couverture.
                            </p>

                            <!-- Récapitulatif de l'abonnement -->
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; margin: 30px 0; border-radius: 15px; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);">
                                <h3 style="margin: 0 0 25px 0; font-size: 20px; text-align: center;">📋 Détails de votre abonnement</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">
                                            <div style="font-size: 13px; opacity: 0.9;">Forfait</div>
                                            <div style="font-size: 20px; font-weight: 700; margin-top: 5px;"><?= htmlspecialchars($plan_name) ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">
                                            <div style="font-size: 13px; opacity: 0.9;">Prix</div>
                                            <div style="font-size: 20px; font-weight: 700; margin-top: 5px;"><?= number_format($price, 2) ?> € <?= $period_label ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">
                                            <div style="font-size: 13px; opacity: 0.9;">Leads inclus</div>
                                            <div style="font-size: 20px; font-weight: 700; margin-top: 5px;"><?= $leads_label ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">
                                            <div style="font-size: 13px; opacity: 0.9;">Date de début</div>
                                            <div style="font-size: 18px; font-weight: 600; margin-top: 5px;"><?= $start_formatted ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0;">
                                            <div style="font-size: 13px; opacity: 0.9;">Prochain paiement</div>
                                            <div style="font-size: 18px; font-weight: 600; margin-top: 5px;"><?= $next_billing_formatted ?></div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <?php if (isset($invoice_url)): ?>
                            <!-- Lien facture -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?= htmlspecialchars($invoice_url) ?>" style="display: inline-block; background: #edf2f7; color: #667eea; text-decoration: none; padding: 14px 30px; border-radius: 10px; font-size: 16px; font-weight: 600; border: 2px solid #667eea;">
                                    📄 Télécharger la facture
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Ce qui change pour vous -->
                            <div style="margin: 40px 0;">
                                <h3 style="margin: 0 0 20px 0; color: #2d3748; font-size: 20px; text-align: center;">✨ Vos nouveaux avantages</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                                    <span style="font-size: 20px;">🎯</span>
                                                </div>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Leads qualifiés garantis</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Recevez des demandes de devis de vrais clients dans votre zone</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                                    <span style="font-size: 20px;">⚡</span>
                                                </div>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Priorité sur les nouveaux leads</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Soyez notifié en premier et gagnez en réactivité</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                                    <span style="font-size: 20px;">📊</span>
                                                </div>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Dashboard analytique complet</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Suivez vos performances et optimisez votre taux de conversion</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 0;">
                                            <div style="display: flex; align-items: start;">
                                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                                    <span style="font-size: 20px;">🎧</span>
                                                </div>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 16px;">Support prioritaire 7j/7</strong><br>
                                                    <span style="color: #718096; font-size: 14px;">Notre équipe dédiée répond à toutes vos questions</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Prochaines étapes -->
                            <div style="background: #ebf8ff; border: 2px solid #4299e1; border-radius: 10px; padding: 25px; margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; color: #2c5282; font-size: 18px;">🚀 Prochaines étapes</h3>
                                <ol style="margin: 0; padding-left: 20px; color: #2d3748; font-size: 15px; line-height: 1.8;">
                                    <li><strong>Complétez votre profil</strong> pour maximiser vos chances de conversion</li>
                                    <li><strong>Vérifiez vos zones de couverture</strong> dans les paramètres</li>
                                    <li><strong>Activez les notifications</strong> pour ne manquer aucun lead</li>
                                    <li><strong>Préparez vos modèles de réponse</strong> pour être plus réactif</li>
                                </ol>
                            </div>

                            <!-- Bouton CTA -->
                            <div style="text-align: center; margin: 40px 0;">
                                <a href="<?= APP_URL ?>/demenageur/dashboard.php" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 30px; font-size: 18px; font-weight: 600; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);">
                                    🎯 Accéder à mon dashboard
                                </a>
                            </div>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Nous sommes ravis de vous compter parmi nos partenaires et nous vous souhaitons beaucoup de succès !
                            </p>

                            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Bien cordialement,<br>
                                <strong>L'équipe <?= SITE_NAME ?></strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background-color: #f7fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 15px 0; color: #718096; font-size: 14px;">
                                <strong>Besoin d'aide ?</strong>
                            </p>
                            <p style="margin: 0 0 10px 0;">
                                <a href="mailto:<?= SUPPORT_EMAIL ?>" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                    <?= SUPPORT_EMAIL ?>
                                </a>
                            </p>
                            <p style="margin: 0 0 20px 0;">
                                <a href="<?= APP_URL ?>/demenageur/abonnement.php" style="color: #667eea; text-decoration: none;">
                                    Gérer mon abonnement
                                </a>
                            </p>
                            <div style="margin: 20px 0; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                <p style="margin: 0; color: #a0aec0; font-size: 12px;">
                                    © <?= date('Y') ?> <?= SITE_NAME ?> - Tous droits réservés<br>
                                    Cet email a été envoyé à <strong><?= $company_name ?></strong><br>
                                    Votre abonnement se renouvellera automatiquement le <?= $next_billing_formatted ?>
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
