<?php
/**
 * Fonctions helpers pour le site
 */

/**
 * Échappe une chaîne pour l'affichage HTML
 */
function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Alias pour escape()
 */
function e($string) {
    return escape($string);
}

/**
 * Redirige vers une URL
 */
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Retourne l'URL de base du site
 */
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host;

    return $path ? $baseUrl . '/' . ltrim($path, '/') : $baseUrl;
}

/**
 * Retourne l'URL actuelle
 */
function currentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Vérifie si la requête est en POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Vérifie si la requête est en GET
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Vérifie si la requête est en AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Récupère une valeur du POST avec valeur par défaut
 */
function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Récupère une valeur du GET avec valeur par défaut
 */
function get($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Récupère une valeur de la session
 */
function session($key, $default = null) {
    return $_SESSION[$key] ?? $default;
}

/**
 * Définit une valeur de session
 */
function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Supprime une valeur de session
 */
function unsetSession($key) {
    unset($_SESSION[$key]);
}

/**
 * Ajoute un message flash
 */
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Récupère et supprime un message flash
 */
function getFlash($type) {
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Vérifie si un message flash existe
 */
function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

/**
 * Génère un token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un token CSRF
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Valide une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numéro de téléphone français
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^0[1-9][0-9]{8}$/', $phone);
}

/**
 * Valide un code postal français
 */
function isValidPostalCode($postalCode) {
    return preg_match('/^[0-9]{5}$/', $postalCode);
}

/**
 * Formate un numéro de téléphone
 */
function formatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 10) {
        return substr($phone, 0, 2) . ' ' .
               substr($phone, 2, 2) . ' ' .
               substr($phone, 4, 2) . ' ' .
               substr($phone, 6, 2) . ' ' .
               substr($phone, 8, 2);
    }
    return $phone;
}

/**
 * Formate un prix
 */
function formatPrice($price, $currency = '€') {
    return number_format($price, 2, ',', ' ') . ' ' . $currency;
}

/**
 * Formate une date en français
 */
function formatDate($date, $format = 'd/m/Y') {
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    return $date->format($format);
}

/**
 * Formate une date et heure en français
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (is_string($datetime)) {
        $datetime = new DateTime($datetime);
    }
    return $datetime->format($format);
}

/**
 * Tronque un texte
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Génère un slug à partir d'un texte
 */
function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

/**
 * Enregistre un log
 */
function logMessage($message, $level = 'info', $context = []) {
    if (!LOG_ENABLED) {
        return;
    }

    $logFile = LOG_PATH . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
    $logLine = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }

    file_put_contents($logFile, $logLine, FILE_APPEND);
}

/**
 * Envoie une réponse JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Envoie une réponse JSON de succès
 */
function jsonSuccess($message = 'Succès', $data = []) {
    jsonResponse(array_merge(['success' => true, 'message' => $message], $data));
}

/**
 * Envoie une réponse JSON d'erreur
 */
function jsonError($message = 'Erreur', $statusCode = 400, $data = []) {
    jsonResponse(array_merge(['success' => false, 'message' => $message], $data), $statusCode);
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Récupère l'ID de l'utilisateur connecté
 */
function userId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Nettoie une chaîne de caractères
 */
function sanitizeString($string) {
    return trim(strip_tags($string));
}

/**
 * Génère un mot de passe aléatoire
 */
function generatePassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $password = '';
    $charactersLength = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $password;
}

/**
 * Hash un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Vérifie un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Récupère l'IP du client
 */
function getClientIp() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Récupère le User Agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

/**
 * Calcule le temps écoulé depuis une date
 */
function timeAgo($datetime) {
    if (is_string($datetime)) {
        $datetime = new DateTime($datetime);
    }

    $now = new DateTime();
    $diff = $now->diff($datetime);

    if ($diff->y > 0) {
        return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
    }
    if ($diff->m > 0) {
        return $diff->m . ' mois';
    }
    if ($diff->d > 0) {
        return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
    }
    if ($diff->h > 0) {
        return $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
    }
    if ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }

    return 'À l\'instant';
}

/**
 * Détermine le département à partir d'un code postal
 */
function getDepartmentFromPostalCode($postalCode) {
    if (strlen($postalCode) === 5) {
        $dept = substr($postalCode, 0, 2);

        // Cas spéciaux
        if ($dept === '20') {
            return in_array(substr($postalCode, 0, 3), ['200', '201']) ? '2A' : '2B';
        }

        return $dept;
    }

    return null;
}

/**
 * Liste des départements français
 */
function getDepartmentsList() {
    return [
        '01' => 'Ain', '02' => 'Aisne', '03' => 'Allier', '04' => 'Alpes-de-Haute-Provence',
        '05' => 'Hautes-Alpes', '06' => 'Alpes-Maritimes', '07' => 'Ardèche', '08' => 'Ardennes',
        '09' => 'Ariège', '10' => 'Aube', '11' => 'Aude', '12' => 'Aveyron',
        '13' => 'Bouches-du-Rhône', '14' => 'Calvados', '15' => 'Cantal', '16' => 'Charente',
        '17' => 'Charente-Maritime', '18' => 'Cher', '19' => 'Corrèze', '2A' => 'Corse-du-Sud',
        '2B' => 'Haute-Corse', '21' => 'Côte-d\'Or', '22' => 'Côtes-d\'Armor', '23' => 'Creuse',
        '24' => 'Dordogne', '25' => 'Doubs', '26' => 'Drôme', '27' => 'Eure',
        '28' => 'Eure-et-Loir', '29' => 'Finistère', '30' => 'Gard', '31' => 'Haute-Garonne',
        '32' => 'Gers', '33' => 'Gironde', '34' => 'Hérault', '35' => 'Ille-et-Vilaine',
        '36' => 'Indre', '37' => 'Indre-et-Loire', '38' => 'Isère', '39' => 'Jura',
        '40' => 'Landes', '41' => 'Loir-et-Cher', '42' => 'Loire', '43' => 'Haute-Loire',
        '44' => 'Loire-Atlantique', '45' => 'Loiret', '46' => 'Lot', '47' => 'Lot-et-Garonne',
        '48' => 'Lozère', '49' => 'Maine-et-Loire', '50' => 'Manche', '51' => 'Marne',
        '52' => 'Haute-Marne', '53' => 'Mayenne', '54' => 'Meurthe-et-Moselle', '55' => 'Meuse',
        '56' => 'Morbihan', '57' => 'Moselle', '58' => 'Nièvre', '59' => 'Nord',
        '60' => 'Oise', '61' => 'Orne', '62' => 'Pas-de-Calais', '63' => 'Puy-de-Dôme',
        '64' => 'Pyrénées-Atlantiques', '65' => 'Hautes-Pyrénées', '66' => 'Pyrénées-Orientales',
        '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '69' => 'Rhône', '70' => 'Haute-Saône',
        '71' => 'Saône-et-Loire', '72' => 'Sarthe', '73' => 'Savoie', '74' => 'Haute-Savoie',
        '75' => 'Paris', '76' => 'Seine-Maritime', '77' => 'Seine-et-Marne', '78' => 'Yvelines',
        '79' => 'Deux-Sèvres', '80' => 'Somme', '81' => 'Tarn', '82' => 'Tarn-et-Garonne',
        '83' => 'Var', '84' => 'Vaucluse', '85' => 'Vendée', '86' => 'Vienne',
        '87' => 'Haute-Vienne', '88' => 'Vosges', '89' => 'Yonne', '90' => 'Territoire de Belfort',
        '91' => 'Essonne', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis', '94' => 'Val-de-Marne',
        '95' => 'Val-d\'Oise', '971' => 'Guadeloupe', '972' => 'Martinique', '973' => 'Guyane',
        '974' => 'La Réunion', '976' => 'Mayotte'
    ];
}
