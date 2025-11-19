<?php
/**
 * Cron Job - Nettoyage Leads Expirés
 *
 * À exécuter quotidiennement à 2h du matin
 * Cron : 0 2 * * * php /var/www/demenageur.com/cron/cleanup-expired-leads.php
 *
 * Actions :
 * - Marque comme expirés les leads de plus de 30 jours sans réponse
 * - Archive les anciennes demandes de devis
 * - Nettoie les sessions expirées
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';

// Log de l'exécution
$log_file = __DIR__ . '/../logs/cron-cleanup.log';
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

log_cron("=== DÉBUT NETTOYAGE SYSTÈME ===");

try {
    $db = Database::getInstance()->getConnection();

    // Récupérer la durée de validité des leads (par défaut 30 jours)
    $lead_expiry_days = 30;
    $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'lead_expiry_days'");
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lead_expiry_days = (int)$row['setting_value'];
    }

    // ============================================
    // 1. EXPIRATION DES LEADS NON TRAITÉS
    // ============================================

    $stmt = $db->prepare("
        UPDATE demenageur_leads
        SET status = 'expired'
        WHERE status IN ('new', 'viewed')
        AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $stmt->execute([$lead_expiry_days]);
    $count_expired = $stmt->rowCount();

    log_cron("✓ $count_expired leads marqués comme expirés");

    // ============================================
    // 2. ARCHIVAGE DEMANDES DE DEVIS ANCIENNES
    // ============================================

    $stmt = $db->prepare("
        UPDATE quote_requests
        SET status = 'completed'
        WHERE status IN ('pending', 'assigned')
        AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    ");
    $stmt->execute();
    $count_archived = $stmt->rowCount();

    log_cron("✓ $count_archived demandes de devis archivées");

    // ============================================
    // 3. ARCHIVAGE DEMANDES DE RAPPEL ANCIENNES
    // ============================================

    $stmt = $db->prepare("
        UPDATE callback_requests
        SET status = 'cancelled'
        WHERE status = 'pending'
        AND created_at < DATE_SUB(NOW(), INTERVAL 14 DAY)
    ");
    $stmt->execute();
    $count_callbacks = $stmt->rowCount();

    log_cron("✓ $count_callbacks demandes de rappel annulées");

    // ============================================
    // 4. NETTOYAGE TOKENS EXPIRÉS
    // ============================================

    // Tokens de reset de mot de passe
    $stmt = $db->prepare("
        UPDATE users
        SET reset_token = NULL, reset_token_expires = NULL
        WHERE reset_token_expires < NOW()
    ");
    $stmt->execute();
    $count_tokens = $stmt->rowCount();

    log_cron("✓ $count_tokens tokens de reset nettoyés");

    // Tokens de vérification email (> 7 jours)
    $stmt = $db->prepare("
        UPDATE users
        SET verification_token = NULL
        WHERE verification_token IS NOT NULL
        AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
        AND email_verified = 0
    ");
    $stmt->execute();
    $count_verif = $stmt->rowCount();

    log_cron("✓ $count_verif tokens de vérification nettoyés");

    // ============================================
    // 5. NETTOYAGE LOGS ANCIENS
    // ============================================

    // Suppression logs d'activité > 90 jours
    $stmt = $db->prepare("
        DELETE FROM activity_logs
        WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    ");
    $stmt->execute();
    $count_logs = $stmt->rowCount();

    log_cron("✓ $count_logs logs d'activité supprimés");

    // ============================================
    // 6. NETTOYAGE SESSIONS PHP (fichiers)
    // ============================================

    $session_path = session_save_path();
    if (empty($session_path)) {
        $session_path = sys_get_temp_dir();
    }

    $count_sessions = 0;
    if (is_dir($session_path) && is_writable($session_path)) {
        $files = glob($session_path . '/sess_*');
        $now = time();
        $session_lifetime = 7200; // 2 heures par défaut

        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file) > $session_lifetime)) {
                if (unlink($file)) {
                    $count_sessions++;
                }
            }
        }
    }

    log_cron("✓ $count_sessions fichiers de session nettoyés");

    // ============================================
    // 7. NETTOYAGE FICHIERS TEMPORAIRES
    // ============================================

    $temp_dir = __DIR__ . '/../temp';
    $count_temp = 0;

    if (is_dir($temp_dir)) {
        $files = glob($temp_dir . '/*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file) > 86400)) { // > 24h
                if (unlink($file)) {
                    $count_temp++;
                }
            }
        }
    }

    log_cron("✓ $count_temp fichiers temporaires supprimés");

    // ============================================
    // 8. STATISTIQUES SYSTÈME
    // ============================================

    // Compter les déménageurs actifs
    $stmt = $db->query("SELECT COUNT(*) FROM demenageurs WHERE status = 'active'");
    $active_demenageurs = $stmt->fetchColumn();

    // Compter les abonnements actifs
    $stmt = $db->query("SELECT COUNT(*) FROM demenageur_subscriptions WHERE status IN ('active', 'trial')");
    $active_subscriptions = $stmt->fetchColumn();

    // Compter les leads du jour
    $stmt = $db->query("SELECT COUNT(*) FROM demenageur_leads WHERE DATE(created_at) = CURDATE()");
    $leads_today = $stmt->fetchColumn();

    log_cron("--- Statistiques Système ---");
    log_cron("Déménageurs actifs : $active_demenageurs");
    log_cron("Abonnements actifs : $active_subscriptions");
    log_cron("Leads aujourd'hui : $leads_today");

    // ============================================
    // 9. OPTIMISATION BASE DE DONNÉES
    // ============================================

    // Optimiser les tables principales (à faire avec prudence)
    $tables = [
        'demenageur_leads',
        'quote_requests',
        'activity_logs',
        'transactions'
    ];

    foreach ($tables as $table) {
        try {
            $db->exec("OPTIMIZE TABLE `$table`");
            log_cron("✓ Table $table optimisée");
        } catch (Exception $e) {
            log_cron("⚠ Échec optimisation table $table : " . $e->getMessage());
        }
    }

    // ============================================
    // RÉSUMÉ FINAL
    // ============================================

    log_cron("=== FIN NETTOYAGE SYSTÈME ===");
    log_cron("RÉSUMÉ :");
    log_cron("  - $count_expired leads expirés");
    log_cron("  - $count_archived demandes archivées");
    log_cron("  - $count_callbacks rappels annulés");
    log_cron("  - $count_tokens tokens nettoyés");
    log_cron("  - $count_logs logs supprimés");
    log_cron("  - $count_sessions sessions nettoyées");
    log_cron("  - $count_temp fichiers temp supprimés");

    $total_cleaned = $count_expired + $count_archived + $count_callbacks + $count_tokens + $count_logs + $count_sessions + $count_temp;
    log_cron("TOTAL : $total_cleaned éléments nettoyés");

} catch (Exception $e) {
    log_cron("ERREUR : " . $e->getMessage());
    log_cron("Trace : " . $e->getTraceAsString());

    // Notifier l'admin
    if (defined('ADMIN_EMAIL')) {
        require_once __DIR__ . '/../includes/helpers.php';
        send_email(
            ADMIN_EMAIL,
            'ERREUR Cron - Nettoyage Système',
            "<h1>Erreur dans le cron</h1><p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString() . "</pre>"
        );
    }

    exit(1);
}

exit(0);
