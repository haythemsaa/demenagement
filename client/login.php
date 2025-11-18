<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez entrer une adresse email valide.';
    } else {
        $db = getDB();

        // Vérifier si l'email existe dans les demandes
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM demandes_devis WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            // Email trouvé, créer la session
            $_SESSION['client_email'] = $email;

            // Envoyer un lien de connexion par email (simulation)
            $success = 'Un lien de connexion a été envoyé à votre adresse email.';

            // Redirection immédiate pour la démo
            header('Location: /client/dashboard.php');
            exit;
        } else {
            $error = 'Aucune demande trouvée pour cet email. Veuillez d\'abord créer une demande de devis.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
            padding: 50px 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            margin: 0 0 10px 0;
            color: #2d3748;
            font-size: 28px;
        }

        .login-header p {
            color: #718096;
            margin: 0;
        }

        .login-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .login-footer p {
            color: #718096;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .login-footer a:hover {
            color: #5568d3;
        }

        .info-box {
            background: #f0f9ff;
            border: 2px solid #bae6fd;
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
        }

        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #0c4a6e;
            line-height: 1.6;
        }

        .info-box strong {
            color: #075985;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1>Espace Client</h1>
                <p>Accédez à vos demandes de devis</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    ⚠️ <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ <?= e($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="votre@email.com"
                        required
                        value="<?= e($_POST['email'] ?? '') ?>"
                    >
                </div>

                <button type="submit" class="btn-login">
                    Se connecter
                </button>
            </form>

            <div class="info-box">
                <p>
                    <strong>💡 Astuce :</strong> Entrez l'email que vous avez utilisé lors de votre demande de devis.
                    Vous recevrez un lien de connexion sécurisé pour accéder à votre espace.
                </p>
            </div>

            <div class="login-footer">
                <p>Première visite ?</p>
                <a href="/index.php#formulaire-devis">Créer une demande de devis gratuite</a>
            </div>
        </div>
    </div>
</body>
</html>
