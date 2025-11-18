<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

// Si déjà connecté, rediriger vers dashboard
if (isset($_SESSION['demenageur_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT d.*, ds.id as subscription_id, ds.status as subscription_status, sp.name as plan_name
            FROM demenageurs d
            LEFT JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id AND ds.status = 'active'
            LEFT JOIN subscription_plans sp ON ds.plan_id = sp.id
            WHERE d.email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $demenageur = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$demenageur) {
            $error = 'Email ou mot de passe incorrect.';
        } elseif (!password_verify($password, $demenageur['password_hash'])) {
            $error = 'Email ou mot de passe incorrect.';
        } elseif ($demenageur['status'] === 'suspended') {
            $error = 'Votre compte est suspendu. Contactez le support.';
        } elseif ($demenageur['status'] === 'rejected') {
            $error = 'Votre demande d\'inscription a été rejetée.';
        } elseif ($demenageur['status'] === 'pending') {
            $error = 'Votre compte est en attente de validation. Nous vous contacterons sous 48h.';
        } else {
            // Connexion réussie
            $_SESSION['demenageur_id'] = $demenageur['id'];
            $_SESSION['demenageur_name'] = $demenageur['company_name'];
            $_SESSION['demenageur_email'] = $demenageur['email'];
            $_SESSION['demenageur_plan'] = $demenageur['plan_name'];

            // Update last login
            $stmt = $db->prepare("UPDATE demenageurs SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$demenageur['id']]);

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Déménageur - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .login-page {
            max-width: 500px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .login-card {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .login-card h1 {
            text-align: center;
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .login-card .subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 30px;
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

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: auto;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            color: #718096;
            position: relative;
            z-index: 1;
        }

        .signup-link {
            text-align: center;
            color: #718096;
        }

        .signup-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 18px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 15px;
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

        .features-list {
            background: #f7fafc;
            padding: 30px;
            border-radius: 15px;
            margin-top: 40px;
        }

        .features-list h3 {
            margin: 0 0 20px 0;
            color: #2d3748;
            text-align: center;
        }

        .features-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .features-list li {
            padding: 10px 0;
            color: #4a5568;
        }

        .features-list li::before {
            content: '✓ ';
            color: #48bb78;
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="login-page">
        <div class="login-card">
            <h1>🚚 Espace Déménageur</h1>
            <p class="subtitle">Connectez-vous pour accéder à vos leads</p>

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required placeholder="votreemail@exemple.com">
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember" style="margin: 0; font-weight: normal;">Se souvenir de moi</label>
                    </div>
                    <a href="mot-de-passe-oublie.php" class="forgot-link">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>
            </form>

            <div class="divider">
                <span>OU</span>
            </div>

            <div class="signup-link">
                Pas encore de compte ?<br>
                <a href="inscription.php">Créer un compte déménageur</a>
            </div>
        </div>

        <div class="features-list">
            <h3>✨ Pourquoi rejoindre notre plateforme ?</h3>
            <ul>
                <li>Recevez des leads qualifiés dans votre zone</li>
                <li>Testez gratuitement pendant 30 jours</li>
                <li>Dashboard complet avec statistiques</li>
                <li>Répondez aux devis en quelques clics</li>
                <li>Augmentez votre visibilité locale</li>
                <li>Support dédié 7j/7</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/demenageur-pro.php" style="color: #667eea; font-weight: 600;">
                ← Retour à la page déménageurs professionnels
            </a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
