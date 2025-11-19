<?php
/**
 * Email envoyé après chaque facturation mensuelle/annuelle
 *
 * Variables requises :
 * - $company_name : Nom de l'entreprise
 * - $contact_name : Nom du contact
 * - $plan_name : Nom du forfait
 * - $amount : Montant facturé
 * - $invoice_number : Numéro de facture
 * - $invoice_date : Date de facturation (format Y-m-d)
 * - $next_billing_date : Prochaine date de facturation (format Y-m-d)
 * - $leads_this_month : Nombre de leads reçus ce mois
 * - $invoice_pdf_url : URL du PDF de la facture
 */

$invoice_formatted = date('d/m/Y', strtotime($invoice_date));
$next_billing_formatted = date('d/m/Y', strtotime($next_billing_date));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture <?= $invoice_number ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f7fafc;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden;">

                    <!-- En-tête avec dégradé bleu -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center; color: white;">
                            <div style="font-size: 64px; margin-bottom: 15px;">💳</div>
                            <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: 700;">Paiement Effectué</h1>
                            <p style="margin: 0; font-size: 16px; opacity: 0.95;">Facture N° <?= htmlspecialchars($invoice_number) ?></p>
                        </td>
                    </tr>

                    <!-- Message principal -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Bonjour <strong><?= htmlspecialchars($contact_name) ?></strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Nous vous confirmons que le paiement de votre abonnement <strong><?= htmlspecialchars($plan_name) ?></strong> a bien été effectué le <strong><?= $invoice_formatted ?></strong>.
                            </p>

                            <!-- Détails de la facture -->
                            <div style="background: #edf2f7; border-radius: 15px; padding: 30px; margin: 30px 0;">
                                <h3 style="margin: 0 0 20px 0; color: #2d3748; font-size: 20px; text-align: center;">📄 Détails de la facture</h3>
                                <table width="100%" cellpadding="0" cellspacing="0" style="background: white; border-radius: 10px; overflow: hidden;">
                                    <tr>
                                        <td style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
                                            <div style="color: #718096; font-size: 13px;">Numéro de facture</div>
                                            <div style="color: #2d3748; font-size: 16px; font-weight: 600; margin-top: 5px;"><?= htmlspecialchars($invoice_number) ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
                                            <div style="color: #718096; font-size: 13px;">Date de facturation</div>
                                            <div style="color: #2d3748; font-size: 16px; font-weight: 600; margin-top: 5px;"><?= $invoice_formatted ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
                                            <div style="color: #718096; font-size: 13px;">Forfait</div>
                                            <div style="color: #2d3748; font-size: 16px; font-weight: 600; margin-top: 5px;"><?= htmlspecialchars($plan_name) ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 15px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <div style="color: rgba(255,255,255,0.9); font-size: 13px;">Montant TTC</div>
                                            <div style="color: white; font-size: 28px; font-weight: 700; margin-top: 5px;"><?= number_format($amount, 2) ?> €</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Bouton télécharger facture -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?= htmlspecialchars($invoice_pdf_url) ?>" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 30px; font-size: 18px; font-weight: 600; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);">
                                    📥 Télécharger la facture PDF
                                </a>
                            </div>

                            <!-- Statistiques du mois écoulé -->
                            <div style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%); color: white; padding: 30px; margin: 40px 0; border-radius: 15px;">
                                <h3 style="margin: 0 0 20px 0; font-size: 20px; text-align: center;">📊 Votre activité ce mois-ci</h3>
                                <div style="text-align: center;">
                                    <div style="font-size: 72px; font-weight: 700; margin: 20px 0;"><?= $leads_this_month ?></div>
                                    <div style="font-size: 18px; opacity: 0.95;">
                                        lead<?= $leads_this_month > 1 ? 's' : '' ?> reçu<?= $leads_this_month > 1 ? 's' : '' ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations sur le prochain paiement -->
                            <div style="background: #ebf8ff; border: 2px solid #4299e1; border-radius: 10px; padding: 25px; margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; color: #2c5282; font-size: 18px;">📅 Prochain prélèvement</h3>
                                <p style="margin: 0; color: #2d3748; font-size: 15px; line-height: 1.6;">
                                    Votre prochain paiement de <strong><?= number_format($amount, 2) ?> €</strong> sera effectué automatiquement le <strong><?= $next_billing_formatted ?></strong>.
                                </p>
                            </div>

                            <!-- Informations pratiques -->
                            <div style="margin: 40px 0;">
                                <h3 style="margin: 0 0 20px 0; color: #2d3748; font-size: 20px;">💡 Bon à savoir</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">🔄</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Renouvellement automatique</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Votre abonnement se renouvelle sans intervention de votre part</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">⚙️</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Gestion de l'abonnement</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Vous pouvez modifier ou annuler votre abonnement à tout moment depuis votre espace</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">📧</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Conservation des factures</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Toutes vos factures sont archivées dans votre espace déménageur</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">📞</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Support comptabilité</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Des questions sur votre facturation ? Notre équipe est disponible</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Accès dashboard -->
                            <div style="text-align: center; margin: 40px 0;">
                                <a href="<?= APP_URL ?>/demenageur/abonnement.php" style="display: inline-block; background: #edf2f7; color: #667eea; text-decoration: none; padding: 14px 30px; border-radius: 10px; font-size: 16px; font-weight: 600; border: 2px solid #667eea;">
                                    💼 Gérer mon abonnement
                                </a>
                            </div>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Merci de votre confiance et à très bientôt sur <?= SITE_NAME ?> !
                            </p>

                            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Cordialement,<br>
                                <strong>L'équipe <?= SITE_NAME ?></strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Informations légales -->
                    <tr>
                        <td style="background-color: #f7fafc; padding: 30px; border-top: 1px solid #e2e8f0;">
                            <div style="background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                                <p style="margin: 0 0 10px 0; color: #2d3748; font-size: 13px; line-height: 1.6;">
                                    <strong>Informations de facturation</strong>
                                </p>
                                <p style="margin: 0; color: #718096; font-size: 12px; line-height: 1.6;">
                                    <?= SITE_NAME ?><br>
                                    123 Avenue Example, 75001 Paris, France<br>
                                    SIRET : XXX XXX XXX XXXXX<br>
                                    TVA : FR XX XXX XXX XXX<br>
                                    Email : <?= SUPPORT_EMAIL ?>
                                </p>
                            </div>
                            <div style="text-align: center;">
                                <p style="margin: 0 0 15px 0; color: #718096; font-size: 14px;">
                                    Questions sur cette facture ?
                                </p>
                                <p style="margin: 0 0 20px 0;">
                                    <a href="mailto:<?= SUPPORT_EMAIL ?>" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                        Contacter le support
                                    </a>
                                </p>
                                <div style="margin: 20px 0; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                    <p style="margin: 0; color: #a0aec0; font-size: 12px;">
                                        © <?= date('Y') ?> <?= SITE_NAME ?> - Tous droits réservés<br>
                                        Cet email a été envoyé à <strong><?= $company_name ?></strong>
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
