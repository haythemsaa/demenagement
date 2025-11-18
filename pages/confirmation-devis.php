<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Récupérer l'ID du devis depuis l'URL
$devisId = get('id');

// Si pas d'ID, rediriger vers l'accueil
if (!$devisId) {
    redirect('/');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande envoyée avec succès - Déménageur.com</title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .confirmation-page {
            min-height: 60vh;
            padding: 4rem 0;
        }
        .confirmation-box {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            color: white;
        }
        .confirmation-title {
            color: var(--text-dark);
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .confirmation-message {
            color: var(--text-light);
            margin-bottom: 2rem;
            font-size: 1.125rem;
        }
        .reference-number {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }
        .next-steps {
            background: #eff6ff;
            padding: 2rem;
            border-radius: 8px;
            text-align: left;
            margin: 2rem 0;
        }
        .next-steps h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .next-steps ul {
            list-style: none;
            padding: 0;
        }
        .next-steps li {
            padding: 0.75rem 0;
            padding-left: 2rem;
            position: relative;
        }
        .next-steps li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #22c55e;
            font-weight: bold;
            font-size: 1.25rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.875rem 2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-dark);
        }
        .btn-secondary:hover {
            background: var(--border-color);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="/" style="color: inherit; text-decoration: none;">DÉMÉNAGEUR.COM</a></h1>
                </div>
            </div>
        </div>
    </header>

    <section class="confirmation-page">
        <div class="container">
            <div class="confirmation-box">
                <div class="success-icon">✓</div>

                <h1 class="confirmation-title">Demande envoyée avec succès !</h1>

                <p class="confirmation-message">
                    Votre demande de devis a bien été enregistrée. Nos déménageurs partenaires vont vous contacter très rapidement.
                </p>

                <div class="reference-number">
                    <strong>Numéro de référence :</strong> #<?= e($devisId) ?>
                </div>

                <div class="next-steps">
                    <h3>📋 Prochaines étapes</h3>
                    <ul>
                        <li>Vous allez recevoir un email de confirmation dans quelques instants</li>
                        <li>Jusqu'à <strong>6 déménageurs professionnels</strong> vont consulter votre demande</li>
                        <li>Vous recevrez leurs <strong>devis personnalisés sous 1 heure</strong> en moyenne</li>
                        <li>Tous les devis sont <strong>gratuits et sans engagement</strong></li>
                        <li>Comparez tranquillement et choisissez la meilleure offre</li>
                    </ul>
                </div>

                <div style="background: #fef3c7; padding: 1rem; border-radius: 6px; margin: 2rem 0;">
                    <p style="margin: 0; color: #92400e;">
                        <strong>💡 Conseil :</strong> Vérifiez votre boîte de réception (et vos spams) pour ne manquer aucun devis.
                    </p>
                </div>

                <div class="action-buttons">
                    <a href="/" class="btn btn-primary">Retour à l'accueil</a>
                    <a href="tel:0978450218" class="btn btn-secondary">Nous appeler : 09 78 45 02 18</a>
                </div>

                <p style="margin-top: 2rem; color: var(--text-light); font-size: 0.875rem;">
                    Besoin d'aide ? Notre service client est disponible 7j/7 de 8h à 20h
                </p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 Déménageur.com - Tous droits réservés</p>
            </div>
        </div>
    </footer>
</body>
</html>
