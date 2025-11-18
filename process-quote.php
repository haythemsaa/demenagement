<?php
/**
 * Traitement d'une demande de devis
 *
 * Ce fichier:
 * 1. Enregistre la demande en BDD
 * 2. Trouve automatiquement les 3-4 meilleurs déménageurs
 * 3. Envoie le lead aux déménageurs sélectionnés
 * 4. Envoie un email de confirmation au client
 */

session_start();
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/DemenageurMatcher.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Récupération et validation des données
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),

        'postal_code_from' => trim($_POST['postal_code_from'] ?? ''),
        'city_from' => trim($_POST['city_from'] ?? ''),
        'address_from' => trim($_POST['address_from'] ?? ''),
        'floor_from' => intval($_POST['floor_from'] ?? 0),
        'elevator_from' => isset($_POST['elevator_from']) ? 1 : 0,

        'postal_code_to' => trim($_POST['postal_code_to'] ?? ''),
        'city_to' => trim($_POST['city_to'] ?? ''),
        'address_to' => trim($_POST['address_to'] ?? ''),
        'floor_to' => intval($_POST['floor_to'] ?? 0),
        'elevator_to' => isset($_POST['elevator_to']) ? 1 : 0,

        'moving_date' => $_POST['moving_date'] ?? null,
        'total_volume' => floatval($_POST['total_volume'] ?? 0),
        'estimated_price' => floatval($_POST['estimated_price'] ?? 0),

        'services' => $_POST['services'] ?? [],
        'inventory' => $_POST['inventory'] ?? null,
        'comments' => trim($_POST['comments'] ?? ''),
        'urgency' => $_POST['urgency'] ?? 'normal',
    ];

    // Validations
    if (empty($data['name']) || empty($data['email']) || empty($data['phone'])) {
        throw new Exception('Nom, email et téléphone sont obligatoires');
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Adresse email invalide');
    }

    if (empty($data['postal_code_from']) || empty($data['postal_code_to'])) {
        throw new Exception('Les codes postaux de départ et d\'arrivée sont obligatoires');
    }

    // Convertir services en JSON
    $services_json = json_encode($data['services']);

    // Convertir inventory en JSON si présent
    $inventory_json = $data['inventory'] ? json_encode($data['inventory']) : null;

    // 1. ENREGISTRER LA DEMANDE DE DEVIS
    $sql = "
        INSERT INTO quote_requests (
            name, email, phone,
            postal_code_from, city_from, address_from, floor_from, elevator_from,
            postal_code_to, city_to, address_to, floor_to, elevator_to,
            moving_date, total_volume, estimated_price,
            services, inventory, comments, urgency,
            status, ip_address
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', ?)
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['postal_code_from'],
        $data['city_from'],
        $data['address_from'],
        $data['floor_from'],
        $data['elevator_from'],
        $data['postal_code_to'],
        $data['city_to'],
        $data['address_to'],
        $data['floor_to'],
        $data['elevator_to'],
        $data['moving_date'],
        $data['total_volume'],
        $data['estimated_price'],
        $services_json,
        $inventory_json,
        $data['comments'],
        $data['urgency'],
        $_SERVER['REMOTE_ADDR']
    ]);

    $quote_request_id = $db->lastInsertId();

    // 2. MATCHING INTELLIGENT DES DÉMÉNAGEURS
    $matcher = new DemenageurMatcher();

    // Préparer les données pour le matching
    $quote_data_for_matching = array_merge($data, [
        'id' => $quote_request_id,
        // Ajouter latitude/longitude si disponibles (géocodage à implémenter)
        'latitude' => null,
        'longitude' => null
    ]);

    // Trouver les 3-4 meilleurs déménageurs
    $matched_demenageurs = $matcher->findBestMatches($quote_data_for_matching);

    $demenageurs_count = count($matched_demenageurs);

    // 3. ENVOYER LES LEADS AUX DÉMÉNAGEURS
    $send_results = ['sent' => 0, 'failed' => 0];

    if ($demenageurs_count > 0) {
        $send_results = $matcher->sendLeadsToMatches($quote_request_id, $matched_demenageurs);
    }

    // 4. ENVOYER EMAIL DE CONFIRMATION AU CLIENT
    $client_email_sent = sendClientConfirmationEmail($data['email'], $data['name'], $quote_request_id, $demenageurs_count);

    // 5. CRÉER UN COMPTE CLIENT (optionnel)
    if (!empty($data['create_account']) && !empty($data['password'])) {
        createClientAccount($data);
    }

    // 6. METTRE À JOUR LE STATUT
    $stmt = $db->prepare("
        UPDATE quote_requests
        SET status = 'sent_to_movers', quotes_count = ?
        WHERE id = ?
    ");
    $stmt->execute([$demenageurs_count, $quote_request_id]);

    // Réponse de succès
    echo json_encode([
        'success' => true,
        'quote_id' => $quote_request_id,
        'message' => 'Votre demande a été envoyée avec succès !',
        'demenageurs_contacted' => $demenageurs_count,
        'details' => [
            'leads_sent' => $send_results['sent'],
            'leads_failed' => $send_results['failed'],
            'estimated_response_time' => '24-48h'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Envoie un email de confirmation au client
 */
function sendClientConfirmationEmail($email, $name, $quote_id, $demenageurs_count) {
    $subject = "Confirmation de votre demande de devis - " . SITE_NAME;

    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; }
            .highlight { background: white; padding: 20px; border-left: 4px solid #667eea; margin: 20px 0; }
            .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { background: #2d3748; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Demande bien reçue !</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$name}</strong>,</p>

                <p>Nous avons bien reçu votre demande de devis de déménagement.</p>

                <div class='highlight'>
                    <h3>📊 Votre demande en chiffres</h3>
                    <ul>
                        <li><strong>{$demenageurs_count} déménageurs professionnels</strong> ont été contactés</li>
                        <li>Réponse attendue sous <strong>24-48h</strong></li>
                        <li>Numéro de demande : <strong>#{$quote_id}</strong></li>
                    </ul>
                </div>

                <p><strong>Que va-t-il se passer maintenant ?</strong></p>
                <ol>
                    <li>Les déménageurs vont étudier votre demande</li>
                    <li>Vous recevrez leurs devis par email et SMS</li>
                    <li>Vous pourrez comparer et choisir la meilleure offre</li>
                </ol>

                <div style='text-align: center;'>
                    <a href='" . SITE_URL . "/client/suivi-devis.php?id={$quote_id}' class='button'>
                        Suivre ma demande en ligne
                    </a>
                </div>

                <p><strong>💡 Conseil :</strong> Créez votre espace client pour suivre vos devis en temps réel et comparer facilement les offres.</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " " . SITE_NAME . " - La comparaison de devis simplifiée</p>
                <p>Email : " . SITE_EMAIL . " | Tél : " . PHONE_NUMBER . "</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . SITE_NAME . ' <' . SITE_EMAIL . '>',
        'Reply-To: ' . SITE_EMAIL
    ];

    return mail($email, $subject, $message, implode("\r\n", $headers));
}

/**
 * Crée un compte client automatiquement
 */
function createClientAccount($data) {
    $db = Database::getInstance()->getConnection();

    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM clients WHERE email = ?");
    $stmt->execute([$data['email']]);

    if ($stmt->fetch()) {
        return false; // Email déjà utilisé
    }

    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = "
        INSERT INTO clients (name, email, phone, password_hash, status)
        VALUES (?, ?, ?, ?, 'active')
    ";

    $stmt = $db->prepare($sql);
    return $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $password_hash
    ]);
}
