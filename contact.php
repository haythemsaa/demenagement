<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Contact';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        // En production : envoyer email + enregistrer en BDD
        $success = 'Votre message a bien été envoyé ! Nous vous répondrons dans les 24h.';

        // Reset form
        $name = $email = $phone = $subject = $message = '';
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .contact-page {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
            margin-top: 40px;
        }

        .contact-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 40px;
            border-radius: 20px;
        }

        .contact-info h2 {
            margin: 0 0 30px 0;
            font-size: 28px;
        }

        .info-item {
            display: flex;
            align-items: start;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-icon {
            font-size: 32px;
            flex-shrink: 0;
        }

        .info-content h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
        }

        .info-content p {
            margin: 0;
            opacity: 0.95;
            line-height: 1.6;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .social-link {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: 0.3s;
            cursor: pointer;
        }

        .social-link:hover {
            background: white;
            transform: translateY(-3px);
        }

        .contact-form {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .contact-form h2 {
            margin: 0 0 30px 0;
            font-size: 28px;
            color: #2d3748;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .required {
            color: #f56565;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .alert {
            padding: 18px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 15px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        .opening-hours {
            background: rgba(255,255,255,0.15);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .opening-hours h4 {
            margin: 0 0 15px 0;
        }

        .hours-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .faq-link {
            background: white;
            color: #667eea;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            margin-top: 30px;
            transition: 0.3s;
        }

        .faq-link:hover {
            background: #f7fafc;
            transform: translateX(5px);
        }

        @media (max-width: 968px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .contact-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="contact-page">
        <div style="text-align: center; margin-bottom: 50px;">
            <h1 style="font-size: 42px; margin-bottom: 15px; color: #2d3748;">📞 Contactez-Nous</h1>
            <p style="font-size: 18px; color: #718096;">Une question ? Besoin d'aide ? Notre équipe est là pour vous !</p>
        </div>

        <div class="contact-grid">
            <div class="contact-info">
                <h2>Nos Coordonnées</h2>

                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <h3>Téléphone</h3>
                        <p><strong><?= PHONE_NUMBER ?></strong></p>
                        <p>Du lundi au dimanche<br>8h00 - 20h00</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">✉️</div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p><strong><?= SITE_EMAIL ?></strong></p>
                        <p>Réponse sous 24h ouvrées</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">💬</div>
                    <div class="info-content">
                        <h3>Chat en Ligne</h3>
                        <p>Disponible du lundi au vendredi<br>9h00 - 18h00</p>
                    </div>
                </div>

                <div class="opening-hours">
                    <h4>⏰ Horaires d'Ouverture</h4>
                    <div class="hours-line">
                        <span>Lundi - Vendredi</span>
                        <strong>8h00 - 20h00</strong>
                    </div>
                    <div class="hours-line">
                        <span>Samedi</span>
                        <strong>9h00 - 19h00</strong>
                    </div>
                    <div class="hours-line">
                        <span>Dimanche</span>
                        <strong>10h00 - 18h00</strong>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" class="social-link">📘</a>
                    <a href="#" class="social-link">🐦</a>
                    <a href="#" class="social-link">📷</a>
                    <a href="#" class="social-link">💼</a>
                </div>

                <a href="/faq.php" class="faq-link">
                    ❓ Consultez notre FAQ
                </a>
            </div>

            <div class="contact-form">
                <h2>Envoyez-nous un message</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        ✅ <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        ⚠️ <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet <span class="required">*</span></label>
                            <input type="text" name="name" value="<?= e($name ?? '') ?>" required placeholder="Jean Dupont">
                        </div>

                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?= e($email ?? '') ?>" required placeholder="jean@exemple.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" name="phone" value="<?= e($phone ?? '') ?>" placeholder="06 12 34 56 78">
                        </div>

                        <div class="form-group">
                            <label>Sujet</label>
                            <select name="subject">
                                <option value="">Choisissez un sujet</option>
                                <option value="devis">Demande de devis</option>
                                <option value="question">Question générale</option>
                                <option value="reclamation">Réclamation</option>
                                <option value="partenariat">Partenariat déménageur</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Message <span class="required">*</span></label>
                        <textarea name="message" required placeholder="Décrivez votre demande en détail..."><?= e($message ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        📧 Envoyer le message
                    </button>

                    <p style="font-size: 13px; color: #718096; margin-top: 15px; text-align: center;">
                        En soumettant ce formulaire, vous acceptez notre
                        <a href="/politique-confidentialite.php" style="color: #667eea;">politique de confidentialité</a>
                    </p>
                </form>
            </div>
        </div>

        <div style="text-align: center; margin-top: 60px; padding: 50px; background: #f7fafc; border-radius: 20px;">
            <h2 style="margin-bottom: 15px;">🚚 Vous préférez recevoir des devis ?</h2>
            <p style="color: #718096; margin-bottom: 30px;">
                Gagnez du temps : recevez jusqu'à 6 devis gratuits de déménageurs professionnels en 3 minutes
            </p>
            <a href="/index.php#formulaire-devis" class="cta-button">
                Obtenir mes devis gratuits
            </a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
