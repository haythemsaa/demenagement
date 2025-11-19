<?php
/**
 * Email envoyé lors du rejet d'une candidature déménageur
 *
 * Variables requises :
 * - $company_name : Nom de l'entreprise
 * - $contact_name : Nom du contact
 * - $rejection_reason : Raison du rejet (optionnel)
 */

$rejection_reason = $rejection_reason ?? 'Votre dossier ne correspond pas aux critères requis pour rejoindre notre plateforme.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature non retenue</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f7fafc;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden;">

                    <!-- En-tête -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #718096 0%, #4a5568 100%); padding: 40px; text-align: center; color: white;">
                            <div style="font-size: 64px; margin-bottom: 15px;">📋</div>
                            <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: 700;">Réponse à votre candidature</h1>
                            <p style="margin: 0; font-size: 16px; opacity: 0.95;"><?= SITE_NAME ?></p>
                        </td>
                    </tr>

                    <!-- Message principal -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Bonjour <strong><?= htmlspecialchars($contact_name) ?></strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Nous avons bien reçu votre candidature pour rejoindre <?= SITE_NAME ?> en tant que déménageur professionnel.
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Après examen attentif de votre dossier, nous sommes au regret de vous informer que nous ne pouvons pas donner suite à votre demande pour le moment.
                            </p>

                            <!-- Raison du rejet -->
                            <div style="background: #edf2f7; border-left: 4px solid #718096; padding: 25px; margin: 30px 0; border-radius: 8px;">
                                <h3 style="margin: 0 0 15px 0; color: #2d3748; font-size: 18px;">📌 Motif</h3>
                                <p style="margin: 0; color: #4a5568; font-size: 15px; line-height: 1.6;">
                                    <?= htmlspecialchars($rejection_reason) ?>
                                </p>
                            </div>

                            <!-- Critères requis -->
                            <div style="margin: 40px 0;">
                                <h3 style="margin: 0 0 20px 0; color: #2d3748; font-size: 20px;">✅ Critères requis pour rejoindre notre plateforme</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">📄</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">SIRET valide et actif</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Numéro SIRET en cours de validité</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">🛡️</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Assurance professionnelle</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">RC Pro + Garantie décennale si stockage</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">🚚</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Flotte de véhicules</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Au minimum 1 véhicule utilitaire adapté</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">👥</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Équipe qualifiée</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Personnel formé aux techniques de déménagement</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0;">
                                            <div style="display: flex; align-items: start;">
                                                <span style="font-size: 20px; margin-right: 12px;">⭐</span>
                                                <div>
                                                    <strong style="color: #2d3748; font-size: 15px;">Réputation vérifiable</strong><br>
                                                    <span style="color: #718096; font-size: 13px;">Avis clients et références</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Possibilité de réessayer -->
                            <div style="background: #ebf8ff; border: 2px solid #4299e1; border-radius: 10px; padding: 25px; margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; color: #2c5282; font-size: 18px;">💡 Vous pouvez réessayer !</h3>
                                <p style="margin: 0 0 15px 0; color: #2d3748; font-size: 15px; line-height: 1.6;">
                                    Si vous estimez que votre situation a évolué ou que vous remplissez désormais tous les critères, vous pouvez soumettre une nouvelle candidature dans <strong>3 mois</strong>.
                                </p>
                                <p style="margin: 0; color: #2d3748; font-size: 15px; line-height: 1.6;">
                                    Nous examinerons votre nouveau dossier avec la même attention.
                                </p>
                            </div>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Nous vous remercions pour l'intérêt que vous portez à <?= SITE_NAME ?> et vous souhaitons beaucoup de succès dans le développement de votre activité.
                            </p>

                            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #2d3748;">
                                Cordialement,<br>
                                <strong>L'équipe <?= SITE_NAME ?></strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background-color: #f7fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 15px 0; color: #718096; font-size: 14px;">
                                Des questions sur cette décision ?
                            </p>
                            <p style="margin: 0 0 20px 0;">
                                <a href="mailto:<?= SUPPORT_EMAIL ?>" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                    Contactez notre équipe
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
