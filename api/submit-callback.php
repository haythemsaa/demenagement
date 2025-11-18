<?php
/**
 * API pour soumettre une demande de rappel
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
        'nom' => trim($_POST['callback-nom'] ?? ''),
        'telephone' => trim($_POST['callback-tel'] ?? ''),
        'creneau' => trim($_POST['callback-creneau'] ?? ''),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];

    // Validation
    $errors = [];

    if (empty($data['nom'])) {
        $errors[] = "Le nom est obligatoire";
    }

    if (empty($data['telephone']) || !preg_match('/^[0-9]{10}$/', $data['telephone'])) {
        $errors[] = "Numéro de téléphone invalide (10 chiffres requis)";
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

    // Insérer la demande de rappel dans la base de données
    $sql = "INSERT INTO demandes_rappel (
        nom, telephone, creneau, ip_address, user_agent, statut, created_at
    ) VALUES (
        :nom, :telephone, :creneau, :ip_address, :user_agent, 'nouveau', NOW()
    )";

    $stmt = $db->prepare($sql);
    $stmt->execute($data);
    $rappelId = $db->lastInsertId();

    // Envoyer une notification par email avec la classe Mailer
    $mailer = new Mailer();
    $mailer->sendCallbackEmail($data, $rappelId);

    // Envoyer un SMS de confirmation si configuré
    // sendSMSConfirmation($data['telephone'], $rappelId);

    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Votre demande de rappel a été enregistrée !',
        'rappel_id' => $rappelId
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
 * Envoie une notification par email pour la demande de rappel
 */
function sendCallbackNotification($data, $rappelId) {
    $to = "admin@demenageur.com"; // À configurer
    $subject = "Nouvelle demande de rappel #" . $rappelId;

    $creneauText = match($data['creneau']) {
        'matin' => 'Matin (8h-12h)',
        'aprem' => 'Après-midi (12h-17h)',
        'soir' => 'Soir (17h-20h)',
        default => 'Non spécifié'
    };

    $message = "
    <html>
    <head><title>Nouvelle demande de rappel</title></head>
    <body>
        <h2>Nouvelle demande de rappel #$rappelId</h2>

        <p><strong>Nom :</strong> {$data['nom']}</p>
        <p><strong>Téléphone :</strong> {$data['telephone']}</p>
        <p><strong>Créneau souhaité :</strong> $creneauText</p>
        <p><strong>Date de demande :</strong> " . date('d/m/Y H:i') . "</p>

        <p><strong>Action requise :</strong> Rappeler ce client dans les meilleurs délais</p>
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

/**
 * Envoie un SMS de confirmation (nécessite une API SMS)
 */
function sendSMSConfirmation($telephone, $rappelId) {
    $message = "Demenageur.com: Votre demande de rappel #$rappelId a été enregistrée. Nous vous contacterons rapidement.";

    // Implémenter avec une API SMS (Twilio, OVH, etc.)
    // Exemple avec une API générique :
    /*
    $apiUrl = "https://api.sms-provider.com/send";
    $apiKey = "YOUR_API_KEY";

    $postData = [
        'to' => $telephone,
        'message' => $message,
        'from' => 'Demenageur'
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $apiKey]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    */
}
