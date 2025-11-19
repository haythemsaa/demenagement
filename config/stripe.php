<?php
/**
 * Configuration Stripe
 *
 * Pour utiliser Stripe, installez la bibliothèque via Composer:
 * composer require stripe/stripe-php
 */

// Clés Stripe (à configurer dans les variables d'environnement en production)
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: 'pk_test_VOTRE_CLE_PUBLIQUE_TEST');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_VOTRE_CLE_SECRETE_TEST');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: 'whsec_VOTRE_WEBHOOK_SECRET');

// Configuration Stripe
if (class_exists('\Stripe\Stripe')) {
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    \Stripe\Stripe::setApiVersion('2023-10-16');
}

/**
 * IDs des produits Stripe (à créer dans le dashboard Stripe)
 *
 * Créez ces produits dans Stripe avec les prix correspondants:
 * - Plan Basic: 79€/mois
 * - Plan Pro: 199€/mois
 * - Plan Premium: 399€/mois
 */
define('STRIPE_PRICE_IDS', [
    'basic_monthly' => 'price_XXXXX',    // À remplacer
    'basic_yearly' => 'price_XXXXX',     // À remplacer
    'pro_monthly' => 'price_XXXXX',      // À remplacer
    'pro_yearly' => 'price_XXXXX',       // À remplacer
    'premium_monthly' => 'price_XXXXX',  // À remplacer
    'premium_yearly' => 'price_XXXXX',   // À remplacer
]);

/**
 * URL de retour après paiement
 */
define('STRIPE_SUCCESS_URL', SITE_URL . '/demenageur/abonnement.php?session_id={CHECKOUT_SESSION_ID}');
define('STRIPE_CANCEL_URL', SITE_URL . '/demenageur/abonnement.php?cancelled=1');

/**
 * Webhooks endpoint
 */
define('STRIPE_WEBHOOK_URL', SITE_URL . '/webhooks/stripe.php');
