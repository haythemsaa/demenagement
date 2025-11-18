<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Rediriger si déjà connecté
if (isset($_SESSION['admin_id'])) {
    redirect('/admin/dashboard.php');
}

$error = '';

if (isPost()) {
    $email = post('email');
    $password = post('password');

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM admins WHERE email = :email AND actif = 1");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && verifyPassword($password, $admin['password_hash'])) {
                // Connexion réussie
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['prenom'] . ' ' . $admin['nom'];
                $_SESSION['admin_role'] = $admin['role'];

                // Mettre à jour last_login
                $stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
                $stmt->execute(['id' => $admin['id']]);

                // Log de l'activité
                logMessage("Admin login: {$admin['email']}", 'info');

                redirect('/admin/dashboard.php');
            } else {
                $error = 'Email ou mot de passe incorrect';
                logMessage("Failed admin login attempt: $email", 'warning');
            }
        } catch (Exception $e) {
            $error = 'Erreur de connexion';
            logMessage("Login error: " . $e->getMessage(), 'error');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration - <?= SITE_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #333;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #dc2626;
        }

        .back-home {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-home a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-home a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Administration</h1>
            <p><?= SITE_NAME ?></p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus value="<?= e(post('email')) ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Se connecter</button>
        </form>

        <div class="back-home">
            <a href="/">← Retour au site</a>
        </div>
    </div>
</body>
</html>
