<?php
/**
 * Système d'internationalisation (i18n)
 * Gestion multi-langues et multi-pays
 */

class i18n {
    private static $instance = null;
    private $language = 'fr';
    private $country = 'FR';
    private $translations = [];
    private $countries = [];
    private $config = [];

    private function __construct() {
        $this->detectLanguage();
        $this->loadTranslations();
        $this->loadCountryConfig();
    }

    /**
     * Singleton
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Détecte la langue du navigateur ou utilise la langue par défaut
     */
    private function detectLanguage() {
        // Vérifier si la langue est en session
        if (isset($_SESSION['language'])) {
            $this->language = $_SESSION['language'];
        }
        // Vérifier si la langue est dans l'URL
        elseif (isset($_GET['lang'])) {
            $lang = strtolower(substr($_GET['lang'], 0, 2));
            if ($this->isLanguageAvailable($lang)) {
                $this->language = $lang;
                $_SESSION['language'] = $lang;
            }
        }
        // Détecter depuis le navigateur
        elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if ($this->isLanguageAvailable($lang)) {
                $this->language = $lang;
            }
        }

        // Détecter le pays
        if (isset($_SESSION['country'])) {
            $this->country = $_SESSION['country'];
        } elseif (isset($_GET['country'])) {
            $this->country = strtoupper($_GET['country']);
            $_SESSION['country'] = $this->country;
        }
    }

    /**
     * Vérifie si une langue est disponible
     */
    private function isLanguageAvailable($lang) {
        $availableLanguages = ['fr', 'en', 'es', 'de', 'it', 'pt', 'nl', 'pl'];
        return in_array($lang, $availableLanguages);
    }

    /**
     * Charge les traductions
     */
    private function loadTranslations() {
        $file = __DIR__ . '/../languages/' . $this->language . '.php';
        if (file_exists($file)) {
            $this->translations = require $file;
        } else {
            // Fallback sur le français
            $this->translations = require __DIR__ . '/../languages/fr.php';
        }
    }

    /**
     * Charge la configuration du pays
     */
    private function loadCountryConfig() {
        $file = __DIR__ . '/../config/countries/' . $this->country . '.php';
        if (file_exists($file)) {
            $this->config = require $file;
        } else {
            // Fallback sur France
            $this->config = require __DIR__ . '/../config/countries/FR.php';
        }
    }

    /**
     * Traduit une clé
     */
    public function translate($key, $params = []) {
        $keys = explode('.', $key);
        $value = $this->translations;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key; // Retourne la clé si traduction non trouvée
            }
        }

        // Remplacer les paramètres
        if (!empty($params) && is_string($value)) {
            foreach ($params as $param => $val) {
                $value = str_replace(':' . $param, $val, $value);
            }
        }

        return $value;
    }

    /**
     * Alias court pour translate
     */
    public function t($key, $params = []) {
        return $this->translate($key, $params);
    }

    /**
     * Obtient la langue actuelle
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Définit la langue
     */
    public function setLanguage($lang) {
        if ($this->isLanguageAvailable($lang)) {
            $this->language = $lang;
            $_SESSION['language'] = $lang;
            $this->loadTranslations();
        }
    }

    /**
     * Obtient le pays actuel
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Définit le pays
     */
    public function setCountry($country) {
        $this->country = strtoupper($country);
        $_SESSION['country'] = $this->country;
        $this->loadCountryConfig();
    }

    /**
     * Obtient une configuration du pays
     */
    public function getCountryConfig($key = null) {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Formate un prix selon le pays
     */
    public function formatPrice($amount) {
        $currency = $this->getCountryConfig('currency.code');
        $symbol = $this->getCountryConfig('currency.symbol');
        $decimals = $this->getCountryConfig('currency.decimals');
        $decimalSep = $this->getCountryConfig('currency.decimal_separator');
        $thousandSep = $this->getCountryConfig('currency.thousand_separator');
        $symbolPosition = $this->getCountryConfig('currency.symbol_position');

        $formatted = number_format($amount, $decimals, $decimalSep, $thousandSep);

        if ($symbolPosition === 'before') {
            return $symbol . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $symbol;
        }
    }

    /**
     * Formate une date selon le pays
     */
    public function formatDate($date, $format = null) {
        if ($format === null) {
            $format = $this->getCountryConfig('date_format');
        }

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        return $date->format($format);
    }

    /**
     * Obtient les langues disponibles
     */
    public function getAvailableLanguages() {
        return [
            'fr' => 'Français',
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'nl' => 'Nederlands',
            'pl' => 'Polski'
        ];
    }

    /**
     * Obtient la liste des pays disponibles
     */
    public function getAvailableCountries() {
        $countriesDir = __DIR__ . '/../config/countries/';
        $countries = [];

        if (is_dir($countriesDir)) {
            $files = glob($countriesDir . '*.php');
            foreach ($files as $file) {
                $code = basename($file, '.php');
                $config = require $file;
                $countries[$code] = $config['name'];
            }
        }

        return $countries;
    }
}

/**
 * Fonction helper globale pour la traduction
 */
function __($key, $params = []) {
    return i18n::getInstance()->translate($key, $params);
}

/**
 * Fonction helper pour obtenir la config pays
 */
function countryConfig($key = null) {
    return i18n::getInstance()->getCountryConfig($key);
}
