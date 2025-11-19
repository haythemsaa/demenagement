<?php
/**
 * Script d'Installation Automatique - Déménageur.com
 *
 * Ce script facilite l'installation initiale de l'application
 * IMPORTANT : Supprimez ce fichier après installation !
 */

// Désactiver l'affichage des erreurs en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = $_GET['step'] ?? 1;
$error = null;
$success = null;

// Vérifier si déjà installé
$config_file = __DIR__ . '/config/database.php';
if (file_exists($config_file) && $step == 1) {
    $warning = "⚠️ L'application semble déjà être installée. Si vous souhaitez réinstaller, supprimez d'abord le fichier config/database.php";
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Étape 1 : Configuration BDD
        $db_host = $_POST['db_host'] ?? '';
        $db_name = $_POST['db_name'] ?? '';
        $db_user = $_POST['db_user'] ?? '';
        $db_pass = $_POST['db_pass'] ?? '';

        // Tester la connexion
        try {
            $dsn = "mysql:host=$db_host;charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Créer la base de données si elle n'existe pas
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$db_name`");

            // Importer le schéma
            $schema_file = __DIR__ . '/database/schema.sql';
            if (file_exists($schema_file)) {
                $schema = file_get_contents($schema_file);
                $pdo->exec($schema);
            }

            // Importer les données
            $data_file = __DIR__ . '/database/data.sql';
            if (file_exists($data_file)) {
                $data = file_get_contents($data_file);
                $pdo->exec($data);
            }

            // Créer le fichier de configuration
            $config_content = "<?php\n";
            $config_content .= "// Configuration Base de Données\n";
            $config_content .= "define('DB_HOST', '$db_host');\n";
            $config_content .= "define('DB_NAME', '$db_name');\n";
            $config_content .= "define('DB_USER', '$db_user');\n";
            $config_content .= "define('DB_PASS', '$db_pass');\n";

            file_put_contents($config_file, $config_content);

            $_SESSION['install_step1'] = true;
            header('Location: install.php?step=2');
            exit;

        } catch (PDOException $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
        }
    }

    elseif ($step == 2) {
        // Étape 2 : Compte admin
        $admin_username = $_POST['admin_username'] ?? '';
        $admin_email = $_POST['admin_email'] ?? '';
        $admin_password = $_POST['admin_password'] ?? '';
        $admin_confirm = $_POST['admin_confirm'] ?? '';

        if ($admin_password !== $admin_confirm) {
            $error = "Les mots de passe ne correspondent pas";
        } elseif (strlen($admin_password) < 8) {
            $error = "Le mot de passe doit contenir au moins 8 caractères";
        } else {
            try {
                require_once __DIR__ . '/config/database.php';
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                // Supprimer l'admin par défaut
                $pdo->exec("DELETE FROM admin_users WHERE id = 1");

                // Créer le nouvel admin
                $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO admin_users (username, email, password_hash, full_name, role, is_active, created_at)
                    VALUES (?, ?, ?, ?, 'superadmin', 1, NOW())
                ");
                $stmt->execute([
                    $admin_username,
                    $admin_email,
                    $password_hash,
                    'Administrateur Principal'
                ]);

                header('Location: install.php?step=3');
                exit;

            } catch (PDOException $e) {
                $error = "Erreur : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Déménageur.com</title>
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

        .install-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .install-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .install-body {
            padding: 40px;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #f7fafc;
            margin: 0 5px;
            border-radius: 10px;
            font-weight: 600;
            color: #718096;
        }

        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .step.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fca5a5;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #6ee7b7;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #fcd34d;
        }

        .success-icon {
            font-size: 80px;
            text-align: center;
            margin-bottom: 20px;
        }

        .checklist {
            list-style: none;
            padding: 0;
        }

        .checklist li {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .checklist li::before {
            content: '✅ ';
            margin-right: 10px;
        }

        code {
            background: #2d3748;
            color: #48bb78;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1>🚚 Installation</h1>
            <p>Déménageur.com v1.0</p>
        </div>

        <div class="install-body">
            <div class="steps">
                <div class="step <?= $step >= 1 ? 'active' : '' ?>">1. Base de données</div>
                <div class="step <?= $step >= 2 ? 'active' : '' ?>">2. Administrateur</div>
                <div class="step <?= $step >= 3 ? 'active' : '' ?>">3. Terminé</div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (isset($warning)): ?>
                <div class="alert alert-warning"><?= $warning ?></div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <h2 style="margin-bottom: 20px;">Configuration de la Base de Données</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Hôte MySQL</label>
                        <input type="text" name="db_host" value="localhost" required>
                    </div>
                    <div class="form-group">
                        <label>Nom de la base de données</label>
                        <input type="text" name="db_name" value="demenagement" required>
                    </div>
                    <div class="form-group">
                        <label>Utilisateur MySQL</label>
                        <input type="text" name="db_user" value="root" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe MySQL</label>
                        <input type="password" name="db_pass">
                    </div>
                    <button type="submit" class="btn">Suivant →</button>
                </form>

            <?php elseif ($step == 2): ?>
                <h2 style="margin-bottom: 20px;">Création du Compte Administrateur</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="admin_username" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="admin_email" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="admin_password" required minlength="8">
                        <small style="color: #718096;">Minimum 8 caractères</small>
                    </div>
                    <div class="form-group">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" name="admin_confirm" required>
                    </div>
                    <button type="submit" class="btn">Créer le compte →</button>
                </form>

            <?php elseif ($step == 3): ?>
                <div class="success-icon">🎉</div>
                <h2 style="text-align: center; margin-bottom: 20px;">Installation Terminée !</h2>
                <p style="text-align: center; color: #718096; margin-bottom: 30px;">
                    Votre plateforme Déménageur.com est maintenant prête à l'emploi.
                </p>

                <h3 style="margin-bottom: 15px;">Prochaines étapes :</h3>
                <ul class="checklist">
                    <li>Configurez le fichier <code>.env</code> avec vos clés Stripe</li>
                    <li>Configurez les emails SMTP dans <code>config/config.php</code></li>
                    <li>Installez les dépendances : <code>composer install</code></li>
                    <li>Configurez le webhook Stripe</li>
                    <li><strong>IMPORTANT : Supprimez le fichier install.php</strong></li>
                </ul>

                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <a href="/admin/login.php" class="btn" style="text-decoration: none; text-align: center;">
                        👨‍💼 Accéder à l'admin
                    </a>
                    <a href="/index.php" class="btn" style="text-decoration: none; text-align: center; background: #48bb78;">
                        🏠 Voir le site
                    </a>
                </div>

                <div class="alert alert-warning" style="margin-top: 20px;">
                    <strong>⚠️ SÉCURITÉ :</strong> Supprimez immédiatement le fichier <code>install.php</code> de votre serveur !
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
