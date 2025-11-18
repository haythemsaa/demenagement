<?php
/**
 * Classe pour l'envoi d'emails
 */

class Mailer {
    private $from;
    private $fromName;
    private $replyTo;

    public function __construct() {
        $this->from = EMAIL_FROM_ADDRESS;
        $this->fromName = EMAIL_FROM_NAME;
        $this->replyTo = EMAIL_REPLY_TO;
    }

    /**
     * Envoie un email
     */
    public function send($to, $subject, $body, $isHtml = true, $attachments = []) {
        $headers = $this->buildHeaders($isHtml);

        if (!empty($attachments)) {
            return $this->sendWithAttachments($to, $subject, $body, $headers, $attachments);
        }

        $success = mail($to, $subject, $body, implode("\r\n", $headers));

        if ($success) {
            logMessage("Email envoyé à $to - Sujet: $subject");
        } else {
            logMessage("Échec d'envoi d'email à $to - Sujet: $subject", 'error');
        }

        return $success;
    }

    /**
     * Construit les en-têtes de l'email
     */
    private function buildHeaders($isHtml = true) {
        $headers = [
            'MIME-Version: 1.0',
            'From: ' . $this->fromName . ' <' . $this->from . '>',
            'Reply-To: ' . $this->replyTo,
            'X-Mailer: PHP/' . phpversion()
        ];

        if ($isHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }

        return $headers;
    }

    /**
     * Envoie un email avec pièces jointes
     */
    private function sendWithAttachments($to, $subject, $body, $headers, $attachments) {
        $boundary = md5(time());

        $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";

        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $body . "\r\n\r\n";

        foreach ($attachments as $file) {
            if (file_exists($file)) {
                $filename = basename($file);
                $content = chunk_split(base64_encode(file_get_contents($file)));

                $message .= "--$boundary\r\n";
                $message .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n";
                $message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
                $message .= $content . "\r\n\r\n";
            }
        }

        $message .= "--$boundary--";

        return mail($to, $subject, $message, implode("\r\n", $headers));
    }

    /**
     * Envoie un email de devis
     */
    public function sendQuoteEmail($data, $devisId, $estimation) {
        $to = $data['email'];
        $subject = "Confirmation de votre demande de devis #" . $devisId;

        $body = $this->getQuoteEmailTemplate($data, $devisId, $estimation);

        return $this->send($to, $subject, $body);
    }

    /**
     * Envoie un email de notification admin
     */
    public function sendQuoteNotification($data, $devisId, $estimation) {
        $to = ADMIN_EMAIL;
        $subject = "Nouvelle demande de devis #" . $devisId;

        $body = $this->getQuoteNotificationTemplate($data, $devisId, $estimation);

        return $this->send($to, $subject, $body);
    }

    /**
     * Envoie un email de rappel
     */
    public function sendCallbackEmail($data, $rappelId) {
        $to = ADMIN_EMAIL;
        $subject = "Nouvelle demande de rappel #" . $rappelId;

        $body = $this->getCallbackEmailTemplate($data, $rappelId);

        return $this->send($to, $subject, $body);
    }

    /**
     * Template d'email de confirmation de devis
     */
    private function getQuoteEmailTemplate($data, $devisId, $estimation) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #2563eb; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Déménageur.com</h1>
        </div>
        <div class="content">
            <h2>Bonjour {$data['prenom']} {$data['nom']},</h2>
            <p>Nous avons bien reçu votre demande de devis pour votre déménagement.</p>

            <div class="info-box">
                <h3>📋 Récapitulatif de votre demande</h3>
                <p><strong>Numéro de demande :</strong> #{$devisId}</p>
                <p><strong>Départ :</strong> {$data['depart_address']}, {$data['depart_postal']}</p>
                <p><strong>Arrivée :</strong> {$data['arrivee_address']}, {$data['arrivee_postal']}</p>
                <p><strong>Date souhaitée :</strong> {$data['date_demenagement']}</p>
                <p><strong>Type de logement :</strong> {$data['type_depart']}</p>
                <p><strong>Superficie :</strong> {$data['superficie']} m²</p>
            </div>

            <div class="info-box">
                <h3>💰 Estimation préliminaire</h3>
                <p><strong>Prix estimé :</strong> {$estimation['min']}€ - {$estimation['max']}€</p>
                <p><strong>Volume estimé :</strong> {$estimation['volume']} m³</p>
                <p><em>Cette estimation est indicative. Les déménageurs vous fourniront des devis personnalisés.</em></p>
            </div>

            <div class="info-box">
                <h3>⏰ Prochaines étapes</h3>
                <p>✓ Vous allez recevoir jusqu'à <strong>6 devis</strong> de déménageurs professionnels</p>
                <p>✓ Délai moyen de réponse : <strong>1 heure</strong></p>
                <p>✓ Tous les devis sont <strong>gratuits et sans engagement</strong></p>
            </div>

            <p style="text-align: center;">
                <a href="tel:{$data['telephone']}" class="button">Besoin d'aide ? Appelez le 09 78 45 02 18</a>
            </p>
        </div>
        <div class="footer">
            <p>© 2024 Déménageur.com - Tous droits réservés</p>
            <p>Vous recevez cet email suite à votre demande de devis sur Déménageur.com</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Template d'email de notification admin pour devis
     */
    private function getQuoteNotificationTemplate($data, $devisId, $estimation) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #f3f4f6; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🆕 Nouvelle demande de devis #{$devisId}</h2>
        </div>
        <div class="content">
            <h3>Informations client</h3>
            <table>
                <tr><th>Nom</th><td>{$data['prenom']} {$data['nom']}</td></tr>
                <tr><th>Email</th><td>{$data['email']}</td></tr>
                <tr><th>Téléphone</th><td>{$data['telephone']}</td></tr>
            </table>

            <h3>Détails du déménagement</h3>
            <table>
                <tr><th>Départ</th><td>{$data['depart_address']}, {$data['depart_postal']}</td></tr>
                <tr><th>Arrivée</th><td>{$data['arrivee_address']}, {$data['arrivee_postal']}</td></tr>
                <tr><th>Type</th><td>{$data['type_depart']}</td></tr>
                <tr><th>Superficie</th><td>{$data['superficie']} m²</td></tr>
                <tr><th>Pièces</th><td>{$data['pieces']}</td></tr>
                <tr><th>Date souhaitée</th><td>{$data['date_demenagement']}</td></tr>
            </table>

            <h3>Estimation</h3>
            <table>
                <tr><th>Prix estimé</th><td>{$estimation['min']}€ - {$estimation['max']}€</td></tr>
                <tr><th>Volume</th><td>{$estimation['volume']} m³</td></tr>
                <tr><th>Distance</th><td>{$estimation['distance']} km</td></tr>
            </table>

            <h3>Commentaires</h3>
            <p>{$data['commentaires']}</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Template d'email pour demande de rappel
     */
    private function getCallbackEmailTemplate($data, $rappelId) {
        $creneauText = match($data['creneau']) {
            'matin' => 'Matin (8h-12h)',
            'aprem' => 'Après-midi (12h-17h)',
            'soir' => 'Soir (17h-20h)',
            default => 'Non spécifié'
        };

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f97316; color: white; padding: 20px; }
        .content { padding: 20px; }
        .alert { background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📞 Nouvelle demande de rappel #{$rappelId}</h2>
        </div>
        <div class="content">
            <div class="alert">
                <strong>⚠️ Action requise :</strong> Rappeler ce client rapidement
            </div>

            <p><strong>Nom :</strong> {$data['nom']}</p>
            <p><strong>Téléphone :</strong> <a href="tel:{$data['telephone']}">{$data['telephone']}</a></p>
            <p><strong>Créneau souhaité :</strong> {$creneauText}</p>
            <p><strong>Date de demande :</strong> " . date('d/m/Y H:i') . "</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
