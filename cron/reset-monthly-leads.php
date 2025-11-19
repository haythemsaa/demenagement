<?php
/**
 * Cron Job - Reset Compteur Leads Mensuels
 *
 * À exécuter tous les 1er du mois à 00:01
 * Cron : 1 0 1 * * php /var/www/demenageur.com/cron/reset-monthly-leads.php
 *
 * Actions :
 * - Reset leads_used_this_month à 0 pour tous les abonnements actifs
 * - Met à jour leads_reset_date
 * - Génère un rapport mensuel pour chaque déménageur
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Log de l'exécution
$log_file = __DIR__ . '/../logs/cron-reset-leads.log';
$log_dir = dirname($log_file);

if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function log_cron($message) {
    global $log_file;
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents($log_file, "$timestamp $message\n", FILE_APPEND);
    echo "$timestamp $message\n";
}

log_cron("=== DÉBUT RESET LEADS MENSUELS ===");

try {
    $db = Database::getInstance()->getConnection();

    // ============================================
    // 1. RESET COMPTEURS POUR ABONNEMENTS ACTIFS
    // ============================================

    $stmt = $db->prepare("
        UPDATE demenageur_subscriptions
        SET leads_used_this_month = 0,
            leads_reset_date = NOW()
        WHERE status IN ('active', 'trial')
    ");
    $stmt->execute();
    $count_reset = $stmt->rowCount();

    log_cron("✓ Compteurs reset pour $count_reset abonnements");

    // ============================================
    // 2. GÉNÉRER RAPPORTS MENSUELS
    // ============================================

    // Récupérer tous les déménageurs actifs
    $stmt = $db->query("
        SELECT
            d.id, d.email, d.company_name, d.contact_name,
            sp.name as plan_name,
            sp.price_monthly,
            ds.leads_used_this_month as leads_last_month,
            (SELECT COUNT(*) FROM demenageur_leads dl
             WHERE dl.demenageur_id = d.id
             AND dl.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
            ) as leads_received_last_month,
            (SELECT COUNT(*) FROM demenageur_leads dl
             WHERE dl.demenageur_id = d.id
             AND dl.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
             AND dl.status = 'won'
            ) as leads_won_last_month,
            (SELECT COUNT(*) FROM demenageur_leads dl
             WHERE dl.demenageur_id = d.id
             AND dl.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
             AND dl.viewed_at IS NOT NULL
            ) as leads_viewed_last_month
        FROM demenageurs d
        INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
        INNER JOIN subscription_plans sp ON ds.plan_id = sp.id
        WHERE ds.status IN ('active', 'trial')
        AND d.status = 'active'
    ");

    $count_reports = 0;
    while ($dem = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Calculer statistiques
        $taux_ouverture = $dem['leads_received_last_month'] > 0
            ? round(($dem['leads_viewed_last_month'] / $dem['leads_received_last_month']) * 100, 1)
            : 0;

        $taux_conversion = $dem['leads_received_last_month'] > 0
            ? round(($dem['leads_won_last_month'] / $dem['leads_received_last_month']) * 100, 1)
            : 0;

        // Préparer email de rapport mensuel
        $email_subject = "📊 Rapport mensuel " . date('F Y', strtotime('-1 month'));

        $email_html = "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px; }
                .stats { background: #f7fafc; padding: 20px; margin: 20px 0; border-radius: 10px; }
                .stat-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
                .stat-value { font-size: 24px; font-weight: bold; color: #667eea; }
                .footer { text-align: center; color: #718096; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📊 Rapport Mensuel</h1>
                    <p>" . date('F Y', strtotime('-1 month')) . "</p>
                </div>

                <p>Bonjour <strong>{$dem['contact_name']}</strong>,</p>

                <p>Voici le récapitulatif de votre activité du mois dernier sur " . SITE_NAME . ".</p>

                <div class='stats'>
                    <div class='stat-item'>
                        <span>Leads reçus</span>
                        <span class='stat-value'>{$dem['leads_received_last_month']}</span>
                    </div>
                    <div class='stat-item'>
                        <span>Leads consultés</span>
                        <span class='stat-value'>{$dem['leads_viewed_last_month']}</span>
                    </div>
                    <div class='stat-item'>
                        <span>Leads convertis</span>
                        <span class='stat-value'>{$dem['leads_won_last_month']}</span>
                    </div>
                    <div class='stat-item'>
                        <span>Taux d'ouverture</span>
                        <span class='stat-value'>{$taux_ouverture}%</span>
                    </div>
                    <div class='stat-item'>
                        <span>Taux de conversion</span>
                        <span class='stat-value'>{$taux_conversion}%</span>
                    </div>
                </div>

                <p>Votre forfait <strong>{$dem['plan_name']}</strong> à {$dem['price_monthly']}€/mois.</p>

                <p>Votre compteur de leads a été remis à zéro pour ce nouveau mois.</p>

                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . APP_URL . "/demenageur/dashboard.php' style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px;'>
                        Voir mon dashboard
                    </a>
                </p>

                <div class='footer'>
                    <p>© " . date('Y') . " " . SITE_NAME . "</p>
                    <p>Cet email a été envoyé à <strong>{$dem['company_name']}</strong></p>
                </div>
            </div>
        </body>
        </html>";

        // Envoyer le rapport
        if (send_email($dem['email'], $email_subject, $email_html)) {
            log_cron("✓ Rapport mensuel envoyé à {$dem['email']} (ID: {$dem['id']})");
            $count_reports++;
        } else {
            log_cron("✗ Échec envoi rapport à {$dem['email']} (ID: {$dem['id']})");
        }
    }

    log_cron("Total rapports mensuels envoyés : $count_reports");

    // ============================================
    // 3. STATISTIQUES GLOBALES
    // ============================================

    log_cron("=== FIN RESET LEADS MENSUELS ===");
    log_cron("RÉSUMÉ : $count_reset compteurs reset, $count_reports rapports envoyés");

} catch (Exception $e) {
    log_cron("ERREUR : " . $e->getMessage());
    log_cron("Trace : " . $e->getTraceAsString());

    // Notifier l'admin
    if (defined('ADMIN_EMAIL')) {
        send_email(
            ADMIN_EMAIL,
            'ERREUR Cron - Reset Leads Mensuels',
            "<h1>Erreur dans le cron</h1><p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString() . "</pre>"
        );
    }

    exit(1);
}

exit(0);
