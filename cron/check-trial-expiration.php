<?php
/**
 * Cron Job - Vérification Expiration Essais Gratuits
 *
 * À exécuter quotidiennement à 9h du matin
 * Cron : 0 9 * * * php /var/www/demenageur.com/cron/check-trial-expiration.php
 *
 * Actions :
 * - Envoie email J-7 avant expiration
 * - Envoie email J-1 avant expiration
 * - Désactive les comptes expirés
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Créer un log de l'exécution
$log_file = __DIR__ . '/../logs/cron-trial-expiration.log';
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

log_cron("=== DÉBUT VÉRIFICATION EXPIRATIONS ESSAIS ===");

try {
    $db = Database::getInstance()->getConnection();

    // ============================================
    // 1. ALERTES J-7 (7 jours avant expiration)
    // ============================================

    $stmt = $db->query("
        SELECT
            d.id, d.email, d.company_name, d.contact_name,
            ds.end_date,
            (SELECT COUNT(*) FROM demenageur_leads dl WHERE dl.demenageur_id = d.id) as leads_received,
            (SELECT COUNT(*) FROM demenageur_leads dl WHERE dl.demenageur_id = d.id AND dl.status = 'won') as leads_converted
        FROM demenageurs d
        INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
        WHERE ds.status = 'trial'
        AND DATEDIFF(ds.end_date, NOW()) = 7
        AND d.status = 'active'
    ");

    $count_j7 = 0;
    while ($dem = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Préparer les variables pour l'email
        $email_data = [
            'company_name' => $dem['company_name'],
            'contact_name' => $dem['contact_name'],
            'expiration_date' => $dem['end_date'],
            'leads_received' => $dem['leads_received'],
            'leads_converted' => $dem['leads_converted']
        ];

        // Générer l'email
        ob_start();
        extract($email_data);
        include __DIR__ . '/../emails/demenageur-expiration-essai.php';
        $email_html = ob_get_clean();

        // Envoyer l'email
        if (send_email(
            $dem['email'],
            'Votre essai gratuit se termine dans 7 jours - ' . SITE_NAME,
            $email_html
        )) {
            log_cron("✓ Email J-7 envoyé à {$dem['email']} (ID: {$dem['id']})");
            $count_j7++;
        } else {
            log_cron("✗ Échec envoi email J-7 à {$dem['email']} (ID: {$dem['id']})");
        }
    }

    log_cron("Total emails J-7 envoyés : $count_j7");

    // ============================================
    // 2. ALERTES J-1 (dernier jour)
    // ============================================

    $stmt = $db->query("
        SELECT
            d.id, d.email, d.company_name, d.contact_name,
            ds.end_date,
            (SELECT COUNT(*) FROM demenageur_leads dl WHERE dl.demenageur_id = d.id) as leads_received,
            (SELECT COUNT(*) FROM demenageur_leads dl WHERE dl.demenageur_id = d.id AND dl.status = 'won') as leads_converted
        FROM demenageurs d
        INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
        WHERE ds.status = 'trial'
        AND DATEDIFF(ds.end_date, NOW()) = 1
        AND d.status = 'active'
    ");

    $count_j1 = 0;
    while ($dem = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Même processus mais avec un message plus urgent
        $email_data = [
            'company_name' => $dem['company_name'],
            'contact_name' => $dem['contact_name'],
            'expiration_date' => $dem['end_date'],
            'leads_received' => $dem['leads_received'],
            'leads_converted' => $dem['leads_converted']
        ];

        ob_start();
        extract($email_data);
        include __DIR__ . '/../emails/demenageur-expiration-essai.php';
        $email_html = ob_get_clean();

        if (send_email(
            $dem['email'],
            '⚠️ DERNIER JOUR - Votre essai gratuit expire demain',
            $email_html
        )) {
            log_cron("✓ Email J-1 envoyé à {$dem['email']} (ID: {$dem['id']})");
            $count_j1++;
        } else {
            log_cron("✗ Échec envoi email J-1 à {$dem['email']} (ID: {$dem['id']})");
        }
    }

    log_cron("Total emails J-1 envoyés : $count_j1");

    // ============================================
    // 3. DÉSACTIVATION DES ESSAIS EXPIRÉS
    // ============================================

    $stmt = $db->query("
        SELECT
            d.id, d.email, d.company_name
        FROM demenageurs d
        INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
        WHERE ds.status = 'trial'
        AND ds.end_date < NOW()
        AND d.status = 'active'
    ");

    $count_expired = 0;
    while ($dem = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Marquer l'abonnement comme expiré
        $update = $db->prepare("
            UPDATE demenageur_subscriptions
            SET status = 'canceled', end_date = NOW()
            WHERE demenageur_id = ? AND status = 'trial'
        ");
        $update->execute([$dem['id']]);

        // Désactiver le déménageur
        $update2 = $db->prepare("
            UPDATE demenageurs
            SET status = 'inactive'
            WHERE id = ?
        ");
        $update2->execute([$dem['id']]);

        log_cron("✓ Essai expiré désactivé : {$dem['email']} (ID: {$dem['id']})");
        $count_expired++;

        // Envoyer un email informatif (optionnel)
        // TODO: Créer un template "Essai expiré"
    }

    log_cron("Total essais expirés désactivés : $count_expired");

    // ============================================
    // 4. STATISTIQUES FINALES
    // ============================================

    $total_actions = $count_j7 + $count_j1 + $count_expired;
    log_cron("=== FIN VÉRIFICATION EXPIRATIONS ESSAIS ===");
    log_cron("RÉSUMÉ : $count_j7 emails J-7, $count_j1 emails J-1, $count_expired expirés");

    // Notifier l'admin si beaucoup d'expirations
    if ($count_expired > 10) {
        $admin_email = ADMIN_EMAIL ?? 'admin@demenageur.com';
        send_email(
            $admin_email,
            "ALERTE : $count_expired essais gratuits ont expiré aujourd'hui",
            "<h1>Expiration Massive d'Essais</h1><p>$count_expired déménageurs ont vu leur essai gratuit expirer aujourd'hui. Considérez une campagne de réactivation.</p>"
        );
    }

} catch (Exception $e) {
    log_cron("ERREUR : " . $e->getMessage());
    log_cron("Trace : " . $e->getTraceAsString());

    // Notifier l'admin en cas d'erreur
    if (defined('ADMIN_EMAIL')) {
        send_email(
            ADMIN_EMAIL,
            'ERREUR Cron - Vérification Expirations Essais',
            "<h1>Erreur dans le cron</h1><p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString() . "</pre>"
        );
    }

    exit(1);
}

exit(0);
