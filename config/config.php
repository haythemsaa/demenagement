<?php
/**
 * Configuration générale du site
 */

// Environnement (development, staging, production)
define('ENVIRONMENT', 'development');

// Configuration du site
define('SITE_NAME', 'Déménageur.com');
define('SITE_URL', 'https://www.demenageur.com');
define('SITE_EMAIL', 'contact@demenageur.com');
define('ADMIN_EMAIL', 'admin@demenageur.com');

// Téléphones
define('PHONE_NUMBER', '09 78 45 02 18');
define('PHONE_DISPLAY', '09 78 45 02 18');

// Configuration des emails
define('EMAIL_FROM_NAME', 'Déménageur.com');
define('EMAIL_FROM_ADDRESS', 'noreply@demenageur.com');
define('EMAIL_REPLY_TO', 'contact@demenageur.com');

// Configuration SMTP (optionnel)
define('SMTP_ENABLED', false);
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls'); // tls ou ssl

// Configuration des sessions
define('SESSION_LIFETIME', 3600); // 1 heure en secondes
define('SESSION_NAME', 'DEMENAGEUR_SESSION');

// Configuration de sécurité
define('ENABLE_CSRF_PROTECTION', true);
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Configuration des uploads
define('UPLOAD_MAX_SIZE', 10485760); // 10 MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Configuration du cache
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 heure

// Configuration des logs
define('LOG_ENABLED', true);
define('LOG_PATH', __DIR__ . '/../logs/');
define('LOG_LEVEL', 'debug'); // debug, info, warning, error

// Google Analytics
define('GA_TRACKING_ID', 'UA-XXXXXXXXX-X'); // À remplacer

// reCAPTCHA (optionnel)
define('RECAPTCHA_ENABLED', false);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// API Keys
define('GOOGLE_MAPS_API_KEY', ''); // Pour calcul de distance précis
define('SMS_API_KEY', ''); // Pour envoi de SMS

// Configuration des tarifs (pour le simulateur)
define('BASE_PRICE', 500);
define('PRICE_PER_SQM', 8);
define('PRICE_PER_KM', 1.5);
define('FLOOR_COST_PER_LEVEL', 100);
define('LIFT_REQUIRED_COST', 250);
define('HOUSE_MULTIPLIER', 1.2);

// Formules de déménagement
define('FORMULA_ECO_MULTIPLIER', 0.6);
define('FORMULA_STANDARD_MULTIPLIER', 1.0);
define('FORMULA_PREMIUM_MULTIPLIER', 1.5);

// Délais et validité
define('QUOTE_VALIDITY_DAYS', 30);
define('CALLBACK_RESPONSE_TIME', 15); // minutes

// Pagination
define('ITEMS_PER_PAGE', 20);

// Messages flash
define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR', 'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO', 'info');

// Timezone
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs selon l'environnement
if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Autoload des classes (si besoin)
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start([
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_httponly' => true,
        'cookie_secure' => (ENVIRONMENT === 'production'),
        'use_strict_mode' => true
    ]);
}
