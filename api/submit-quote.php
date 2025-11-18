<?php
/**
 * API pour soumettre une demande de devis
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/Mailer.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer et valider les données du formulaire
    $data = [
        'depart_address' => trim($_POST['depart-address'] ?? ''),
        'depart_postal' => trim($_POST['depart-postal'] ?? ''),
        'arrivee_address' => trim($_POST['arrivee-address'] ?? ''),
        'arrivee_postal' => trim($_POST['arrivee-postal'] ?? ''),
        'type_depart' => trim($_POST['type-depart'] ?? ''),
        'superficie' => intval($_POST['superficie'] ?? 0),
        'pieces' => intval($_POST['pieces'] ?? 0),
        'etage_depart' => intval($_POST['etage-depart'] ?? 0),
        'ascenseur_depart' => trim($_POST['ascenseur-depart'] ?? 'non'),
        'etage_arrivee' => intval($_POST['etage-arrivee'] ?? 0),
        'ascenseur_arrivee' => trim($_POST['ascenseur-arrivee'] ?? 'non'),
        'monte_charge' => trim($_POST['monte-charge'] ?? 'non'),
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'date_demenagement' => trim($_POST['date-demenagement'] ?? ''),
        'commentaires' => trim($_POST['commentaires'] ?? ''),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];

    // Validation des champs obligatoires
    $errors = [];

    if (empty($data['depart_address'])) {
        $errors[] = "L'adresse de départ est obligatoire";
    }

    if (empty($data['depart_postal']) || !preg_match('/^[0-9]{5}$/', $data['depart_postal'])) {
        $errors[] = "Code postal de départ invalide";
    }

    if (empty($data['arrivee_address'])) {
        $errors[] = "L'adresse d'arrivée est obligatoire";
    }

    if (empty($data['arrivee_postal']) || !preg_match('/^[0-9]{5}$/', $data['arrivee_postal'])) {
        $errors[] = "Code postal d'arrivée invalide";
    }

    if (empty($data['type_depart'])) {
        $errors[] = "Le type de logement est obligatoire";
    }

    if ($data['superficie'] < 10) {
        $errors[] = "La superficie doit être d'au moins 10m²";
    }

    if ($data['pieces'] < 1) {
        $errors[] = "Le nombre de pièces est obligatoire";
    }

    if (empty($data['nom'])) {
        $errors[] = "Le nom est obligatoire";
    }

    if (empty($data['prenom'])) {
        $errors[] = "Le prénom est obligatoire";
    }

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }

    if (empty($data['telephone']) || !preg_match('/^[0-9]{10}$/', $data['telephone'])) {
        $errors[] = "Numéro de téléphone invalide";
    }

    // Si des erreurs, renvoyer les erreurs
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }

    // Connexion à la base de données
    $db = getDB();

    // Calculer une estimation de prix
    $estimation = calculateEstimation($data);

    // Insérer la demande dans la base de données
    $sql = "INSERT INTO demandes_devis (
        depart_address, depart_postal, arrivee_address, arrivee_postal,
        type_depart, superficie, pieces,
        etage_depart, ascenseur_depart, etage_arrivee, ascenseur_arrivee,
        monte_charge, nom, prenom, email, telephone,
        date_demenagement, commentaires,
        estimation_min, estimation_max, volume_estime,
        ip_address, user_agent, statut, created_at
    ) VALUES (
        :depart_address, :depart_postal, :arrivee_address, :arrivee_postal,
        :type_depart, :superficie, :pieces,
        :etage_depart, :ascenseur_depart, :etage_arrivee, :ascenseur_arrivee,
        :monte_charge, :nom, :prenom, :email, :telephone,
        :date_demenagement, :commentaires,
        :estimation_min, :estimation_max, :volume_estime,
        :ip_address, :user_agent, 'nouveau', NOW()
    )";

    $stmt = $db->prepare($sql);

    $params = array_merge($data, [
        'estimation_min' => $estimation['min'],
        'estimation_max' => $estimation['max'],
        'volume_estime' => $estimation['volume']
    ]);

    $stmt->execute($params);
    $devisId = $db->lastInsertId();

    // Envoyer les emails avec la classe Mailer
    $mailer = new Mailer();
    $mailer->sendQuoteEmail($data, $devisId, $estimation);
    $mailer->sendQuoteNotification($data, $devisId, $estimation);

    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Votre demande a été envoyée avec succès !',
        'devis_id' => $devisId,
        'estimation' => $estimation
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement de votre demande'
    ]);
    error_log("Erreur DB: " . $e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue'
    ]);
    error_log("Erreur: " . $e->getMessage());
}

/**
 * Calcule une estimation de prix pour le déménagement
 */
function calculateEstimation($data) {
    // Prix de base
    $basePrice = 500;

    // Prix selon la superficie
    $basePrice += $data['superficie'] * 8;

    // Calcul de la distance approximative (à améliorer avec une API de géolocalisation)
    $distance = estimateDistance($data['depart_postal'], $data['arrivee_postal']);
    $basePrice += $distance * 1.5;

    // Prix selon le type
    if ($data['type_depart'] === 'maison') {
        $basePrice *= 1.2;
    }

    // Coûts additionnels pour les étages sans ascenseur
    if ($data['ascenseur_depart'] === 'non' && $data['etage_depart'] > 0) {
        $basePrice += $data['etage_depart'] * 100;
    }

    if ($data['ascenseur_arrivee'] === 'non' && $data['etage_arrivee'] > 0) {
        $basePrice += $data['etage_arrivee'] * 100;
    }

    // Monte-charge
    if ($data['monte_charge'] === 'oui') {
        $basePrice += 250;
    }

    // Volume estimé
    $volume = round($data['superficie'] / 2);

    return [
        'min' => round($basePrice * 0.8),
        'max' => round($basePrice * 1.2),
        'volume' => $volume,
        'distance' => $distance
    ];
}

/**
 * Estime la distance entre deux codes postaux (approximatif)
 */
function estimateDistance($postalFrom, $postalTo) {
    // Simplification: distance basée sur la différence de codes postaux
    // Dans un vrai projet, utiliser une API de géolocalisation
    $dept1 = intval(substr($postalFrom, 0, 2));
    $dept2 = intval(substr($postalTo, 0, 2));

    $diff = abs($dept1 - $dept2);

    if ($diff === 0) {
        return 20; // Même département
    } elseif ($diff <= 2) {
        return 100; // Départements voisins
    } elseif ($diff <= 10) {
        return 300; // Région proche
    } else {
        return 600; // Longue distance
    }
}

/**
 * Envoie un email de notification aux administrateurs
 */
function sendNotificationEmail($data, $devisId, $estimation) {
    $to = "admin@demenageur.com"; // À configurer
    $subject = "Nouvelle demande de devis #" . $devisId;

    $message = "
    <html>
    <head><title>Nouvelle demande de devis</title></head>
    <body>
        <h2>Nouvelle demande de devis #$devisId</h2>

        <h3>Informations client :</h3>
        <p><strong>Nom :</strong> {$data['prenom']} {$data['nom']}</p>
        <p><strong>Email :</strong> {$data['email']}</p>
        <p><strong>Téléphone :</strong> {$data['telephone']}</p>

        <h3>Détails du déménagement :</h3>
        <p><strong>Départ :</strong> {$data['depart_address']}, {$data['depart_postal']}</p>
        <p><strong>Arrivée :</strong> {$data['arrivee_address']}, {$data['arrivee_postal']}</p>
        <p><strong>Type :</strong> {$data['type_depart']}</p>
        <p><strong>Superficie :</strong> {$data['superficie']} m²</p>
        <p><strong>Pièces :</strong> {$data['pieces']}</p>
        <p><strong>Date souhaitée :</strong> {$data['date_demenagement']}</p>

        <h3>Estimation :</h3>
        <p><strong>Prix estimé :</strong> {$estimation['min']}€ - {$estimation['max']}€</p>
        <p><strong>Volume estimé :</strong> {$estimation['volume']} m³</p>

        <p><strong>Commentaires :</strong> {$data['commentaires']}</p>
    </body>
    </html>
    ";

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Déménageur.com <noreply@demenageur.com>',
        'Reply-To: ' . $data['email']
    ];

    // Décommenter pour envoyer réellement l'email
    // mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Envoie un email de confirmation au client
 */
function sendConfirmationEmail($data, $devisId, $estimation) {
    $to = $data['email'];
    $subject = "Confirmation de votre demande de devis #" . $devisId;

    $message = "
    <html>
    <head><title>Confirmation de votre demande</title></head>
    <body>
        <h2>Bonjour {$data['prenom']} {$data['nom']},</h2>

        <p>Nous avons bien reçu votre demande de devis pour votre déménagement.</p>

        <h3>Récapitulatif de votre demande :</h3>
        <p><strong>Numéro de demande :</strong> #$devisId</p>
        <p><strong>Départ :</strong> {$data['depart_address']}, {$data['depart_postal']}</p>
        <p><strong>Arrivée :</strong> {$data['arrivee_address']}, {$data['arrivee_postal']}</p>
        <p><strong>Date souhaitée :</strong> {$data['date_demenagement']}</p>

        <h3>Estimation préliminaire :</h3>
        <p><strong>Prix estimé :</strong> {$estimation['min']}€ - {$estimation['max']}€</p>
        <p><strong>Volume estimé :</strong> {$estimation['volume']} m³</p>

        <p>Vous allez recevoir jusqu'à 6 devis de déménageurs professionnels dans l'heure qui suit.</p>

        <p>Pour toute question, contactez-nous au 09 78 45 02 18</p>

        <p>Cordialement,<br>L'équipe Déménageur.com</p>
    </body>
    </html>
    ";

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Déménageur.com <noreply@demenageur.com>'
    ];

    // Décommenter pour envoyer réellement l'email
    // mail($to, $subject, $message, implode("\r\n", $headers));
}
