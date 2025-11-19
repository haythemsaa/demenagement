<?php
/**
 * Webhook Stripe - Gestion des événements de paiement
 *
 * Ce fichier reçoit les notifications de Stripe (webhooks) et met à jour
 * les abonnements en conséquence.
 *
 * URL du webhook à configurer dans Stripe : https://www.demenageur.com/webhooks/stripe.php
 *
 * Événements gérés :
 * - checkout.session.completed : Paiement initial réussi
 * - invoice.payment_succeeded : Paiement récurrent réussi
 * - invoice.payment_failed : Échec de paiement
 * - customer.subscription.updated : Modification d'abonnement
 * - customer.subscription.deleted : Annulation d'abonnement
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Charger autoload Composer pour Stripe SDK
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Log des webhooks pour debug
function log_webhook($event_type, $data) {
    $log_file = __DIR__ . '/../logs/stripe_webhooks.log';
    $log_dir = dirname($log_file);

    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $log_entry = date('[Y-m-d H:i:s]') . " [$event_type] " . json_encode($data) . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Récupérer le payload du webhook
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$endpoint_secret = STRIPE_WEBHOOK_SECRET;

try {
    // Vérifier la signature du webhook
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

    log_webhook($event->type, ['event_id' => $event->id]);

    $db = Database::getInstance()->getConnection();

    // Traiter l'événement selon son type
    switch ($event->type) {

        // Paiement initial réussi (checkout)
        case 'checkout.session.completed':
            $session = $event->data->object;

            // Récupérer les métadonnées
            $demenageur_id = $session->metadata->demenageur_id ?? null;
            $plan_id = $session->metadata->plan_id ?? null;

            if ($demenageur_id && $plan_id) {
                // Créer l'abonnement dans la base de données
                $stmt = $db->prepare("
                    INSERT INTO demenageur_subscriptions
                    (demenageur_id, plan_id, stripe_subscription_id, stripe_customer_id, status,
                     start_date, next_billing_date, auto_renew, created_at)
                    VALUES (?, ?, ?, ?, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), TRUE, NOW())
                ");
                $stmt->execute([
                    $demenageur_id,
                    $plan_id,
                    $session->subscription,
                    $session->customer
                ]);

                // Récupérer infos déménageur
                $stmt = $db->prepare("SELECT company_name, contact_name, email FROM demenageurs WHERE id = ?");
                $stmt->execute([$demenageur_id]);
                $demenageur = $stmt->fetch(PDO::FETCH_ASSOC);

                // Récupérer infos du plan
                $stmt = $db->prepare("SELECT * FROM subscription_plans WHERE id = ?");
                $stmt->execute([$plan_id]);
                $plan = $stmt->fetch(PDO::FETCH_ASSOC);

                // Envoyer email de confirmation
                $email_data = [
                    'company_name' => $demenageur['company_name'],
                    'contact_name' => $demenageur['contact_name'],
                    'plan_name' => $plan['name'],
                    'price' => $plan['price_monthly'],
                    'billing_period' => 'monthly',
                    'start_date' => date('Y-m-d'),
                    'next_billing_date' => date('Y-m-d', strtotime('+1 month')),
                    'leads_per_month' => $plan['leads_per_month'],
                    'invoice_url' => $session->invoice ?? null
                ];

                ob_start();
                extract($email_data);
                include __DIR__ . '/../emails/demenageur-abonnement-confirme.php';
                $email_html = ob_get_clean();

                send_email(
                    $demenageur['email'],
                    'Votre abonnement ' . $plan['name'] . ' est confirmé !',
                    $email_html
                );

                log_webhook('checkout.session.completed', [
                    'demenageur_id' => $demenageur_id,
                    'plan_id' => $plan_id,
                    'status' => 'success'
                ]);
            }
            break;

        // Paiement récurrent réussi
        case 'invoice.payment_succeeded':
            $invoice = $event->data->object;

            // Mettre à jour la date de prochaine facturation
            $stmt = $db->prepare("
                UPDATE demenageur_subscriptions
                SET next_billing_date = DATE_ADD(next_billing_date, INTERVAL 1 MONTH),
                    leads_used_this_month = 0,
                    leads_reset_date = NOW()
                WHERE stripe_subscription_id = ? AND status = 'active'
            ");
            $stmt->execute([$invoice->subscription]);

            // Récupérer l'abonnement
            $stmt = $db->prepare("
                SELECT ds.*, d.company_name, d.contact_name, d.email, sp.name as plan_name, sp.price_monthly
                FROM demenageur_subscriptions ds
                INNER JOIN demenageurs d ON ds.demenageur_id = d.id
                INNER JOIN subscription_plans sp ON ds.plan_id = sp.id
                WHERE ds.stripe_subscription_id = ?
            ");
            $stmt->execute([$invoice->subscription]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subscription) {
                // Compter les leads du mois écoulé
                $stmt = $db->prepare("
                    SELECT COUNT(*) as count FROM demenageur_leads
                    WHERE demenageur_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                ");
                $stmt->execute([$subscription['demenageur_id']]);
                $leads_count = $stmt->fetchColumn();

                // Envoyer email de facture
                $email_data = [
                    'company_name' => $subscription['company_name'],
                    'contact_name' => $subscription['contact_name'],
                    'plan_name' => $subscription['plan_name'],
                    'amount' => $subscription['price_monthly'],
                    'invoice_number' => $invoice->number,
                    'invoice_date' => date('Y-m-d', $invoice->created),
                    'next_billing_date' => $subscription['next_billing_date'],
                    'leads_this_month' => $leads_count,
                    'invoice_pdf_url' => $invoice->invoice_pdf ?? '#'
                ];

                ob_start();
                extract($email_data);
                include __DIR__ . '/../emails/demenageur-facture-mensuelle.php';
                $email_html = ob_get_clean();

                send_email(
                    $subscription['email'],
                    'Facture ' . $invoice->number . ' - ' . SITE_NAME,
                    $email_html
                );

                log_webhook('invoice.payment_succeeded', [
                    'subscription_id' => $invoice->subscription,
                    'amount' => $invoice->amount_paid / 100,
                    'status' => 'success'
                ]);
            }
            break;

        // Échec de paiement
        case 'invoice.payment_failed':
            $invoice = $event->data->object;

            // Marquer l'abonnement comme en retard de paiement
            $stmt = $db->prepare("
                UPDATE demenageur_subscriptions
                SET status = 'past_due',
                    payment_failed_count = payment_failed_count + 1
                WHERE stripe_subscription_id = ?
            ");
            $stmt->execute([$invoice->subscription]);

            // Si 3 échecs consécutifs, suspendre le compte
            $stmt = $db->prepare("
                SELECT ds.*, d.company_name, d.contact_name, d.email
                FROM demenageur_subscriptions ds
                INNER JOIN demenageurs d ON ds.demenageur_id = d.id
                WHERE ds.stripe_subscription_id = ?
            ");
            $stmt->execute([$invoice->subscription]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subscription && $subscription['payment_failed_count'] >= 3) {
                // Suspendre le déménageur
                $stmt = $db->prepare("UPDATE demenageurs SET status = 'suspended' WHERE id = ?");
                $stmt->execute([$subscription['demenageur_id']]);

                // Envoyer email de suspension
                $email_data = [
                    'company_name' => $subscription['company_name'],
                    'contact_name' => $subscription['contact_name'],
                    'suspension_reason' => 'Échec de paiement après 3 tentatives. Veuillez mettre à jour vos informations de paiement.',
                    'suspension_date' => date('Y-m-d H:i:s')
                ];

                ob_start();
                extract($email_data);
                include __DIR__ . '/../emails/demenageur-compte-suspendu.php';
                $email_html = ob_get_clean();

                send_email(
                    $subscription['email'],
                    'Compte suspendu - Échec de paiement',
                    $email_html
                );
            }

            log_webhook('invoice.payment_failed', [
                'subscription_id' => $invoice->subscription,
                'attempt' => $subscription['payment_failed_count'] ?? 0,
                'status' => 'failed'
            ]);
            break;

        // Modification d'abonnement
        case 'customer.subscription.updated':
            $subscription_obj = $event->data->object;

            // Mettre à jour le statut dans la base de données
            $status_map = [
                'active' => 'active',
                'past_due' => 'past_due',
                'canceled' => 'canceled',
                'incomplete' => 'pending',
                'trialing' => 'trial'
            ];

            $new_status = $status_map[$subscription_obj->status] ?? 'pending';

            $stmt = $db->prepare("
                UPDATE demenageur_subscriptions
                SET status = ?,
                    auto_renew = ?
                WHERE stripe_subscription_id = ?
            ");
            $stmt->execute([
                $new_status,
                !$subscription_obj->cancel_at_period_end,
                $subscription_obj->id
            ]);

            log_webhook('customer.subscription.updated', [
                'subscription_id' => $subscription_obj->id,
                'new_status' => $new_status
            ]);
            break;

        // Annulation d'abonnement
        case 'customer.subscription.deleted':
            $subscription_obj = $event->data->object;

            // Marquer l'abonnement comme annulé
            $stmt = $db->prepare("
                UPDATE demenageur_subscriptions
                SET status = 'canceled',
                    end_date = NOW(),
                    auto_renew = FALSE
                WHERE stripe_subscription_id = ?
            ");
            $stmt->execute([$subscription_obj->id]);

            // Désactiver le déménageur
            $stmt = $db->prepare("
                UPDATE demenageurs d
                INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
                SET d.status = 'inactive'
                WHERE ds.stripe_subscription_id = ?
            ");
            $stmt->execute([$subscription_obj->id]);

            log_webhook('customer.subscription.deleted', [
                'subscription_id' => $subscription_obj->id,
                'status' => 'canceled'
            ]);
            break;

        default:
            log_webhook('unhandled_event', ['type' => $event->type]);
    }

    // Répondre à Stripe avec succès
    http_response_code(200);
    echo json_encode(['status' => 'success', 'event' => $event->type]);

} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Signature invalide
    log_webhook('error', ['type' => 'signature_verification_failed', 'message' => $e->getMessage()]);
    http_response_code(400);
    echo json_encode(['error' => 'Invalid signature']);

} catch (\Exception $e) {
    // Autre erreur
    log_webhook('error', ['type' => 'general_error', 'message' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
