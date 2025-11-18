<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - Déménageur.com</title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .error-page {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }
        .error-content {
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        .error-description {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
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
    <div class="error-page">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1 class="error-message">Page non trouvée</h1>
            <p class="error-description">
                Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
            </p>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">Retour à l'accueil</a>
                <a href="/#devis" class="btn btn-secondary">Obtenir un devis</a>
                <a href="tel:0978450218" class="btn btn-secondary">Nous appeler</a>
            </div>
        </div>
    </div>
</body>
</html>
