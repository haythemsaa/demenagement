<?php
/**
 * Page de checkout Stripe
 * Crée une session de paiement Stripe et redirige vers le checkout
 */

session_start();
require_once '../config/config.php';
require_once '../config/stripe.php';
require_once '../classes/Database.php';

// Vérifier l'authentification
if (!isset($_SESSION['demenageur_id'])) {
    header('Location: login.php');
    exit;
}

// Charger autoload Composer pour Stripe SDK
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Erreur : Stripe SDK non installé. Veuillez exécuter "composer install".');
}
require_once __DIR__ . '/../vendor/autoload.php';

$demenageur_id = $_SESSION['demenageur_id'];
$db = Database::getInstance()->getConnection();

// Récupérer les informations du déménageur
$stmt = $db->prepare("SELECT * FROM demenageurs WHERE id = ?");
$stmt->execute([$demenageur_id]);
$demenageur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demenageur) {
    die('Erreur : Déménageur introuvable.');
}

// Vérifier qu'un plan a été sélectionné
if (!isset($_GET['plan_id'])) {
    header('Location: abonnement.php?error=plan_required');
    exit;
}

$plan_id = (int)$_GET['plan_id'];

// Récupérer les détails du plan
$stmt = $db->prepare("SELECT * FROM subscription_plans WHERE id = ? AND is_active = TRUE");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    header('Location: abonnement.php?error=invalid_plan');
    exit;
}

// Déterminer le mode de facturation (mensuel par défaut)
$billing_mode = $_GET['mode'] ?? 'monthly';
$price_amount = $billing_mode === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];
$stripe_price_id = STRIPE_PRICE_IDS[strtolower($plan['slug']) . '_' . $billing_mode] ?? null;

if (!$stripe_price_id) {
    die('Erreur : Configuration Stripe incomplète pour ce plan.');
}

try {
    // Configurer Stripe
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Créer ou récupérer le customer Stripe
    $customer_id = null;

    // Vérifier si le déménageur a déjà un customer ID
    $stmt = $db->prepare("
        SELECT stripe_customer_id
        FROM demenageur_subscriptions
        WHERE demenageur_id = ? AND stripe_customer_id IS NOT NULL
        LIMIT 1
    ");
    $stmt->execute([$demenageur_id]);
    $existing_customer = $stmt->fetchColumn();

    if ($existing_customer) {
        $customer_id = $existing_customer;
    } else {
        // Créer un nouveau customer Stripe
        $customer = \Stripe\Customer::create([
            'email' => $demenageur['email'],
            'name' => $demenageur['company_name'],
            'metadata' => [
                'demenageur_id' => $demenageur_id,
                'company_name' => $demenageur['company_name'],
                'siret' => $demenageur['siret']
            ]
        ]);
        $customer_id = $customer->id;
    }

    // Créer une session de checkout Stripe
    $checkout_session = \Stripe\Checkout\Session::create([
        'customer' => $customer_id,
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => $stripe_price_id,
            'quantity' => 1,
        ]],
        'mode' => 'subscription',
        'success_url' => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => STRIPE_CANCEL_URL,
        'metadata' => [
            'demenageur_id' => $demenageur_id,
            'plan_id' => $plan_id,
            'billing_mode' => $billing_mode
        ],
        'subscription_data' => [
            'metadata' => [
                'demenageur_id' => $demenageur_id,
                'plan_id' => $plan_id
            ]
        ],
        'billing_address_collection' => 'required',
        'locale' => 'fr',
        'allow_promotion_codes' => true,
    ]);

    // Rediriger vers Stripe Checkout
    header('Location: ' . $checkout_session->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    // Erreur Stripe
    error_log('Stripe Error: ' . $e->getMessage());
    header('Location: abonnement.php?error=stripe_error&message=' . urlencode($e->getMessage()));
    exit;

} catch (\Exception $e) {
    // Autre erreur
    error_log('Checkout Error: ' . $e->getMessage());
    header('Location: abonnement.php?error=system_error');
    exit;
}
