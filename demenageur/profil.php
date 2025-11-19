<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

if (!isset($_SESSION['demenageur_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$demenageur_id = $_SESSION['demenageur_id'];

$success = '';
$error = '';

// Récupérer les informations actuelles
$stmt = $db->prepare("SELECT * FROM demenageurs WHERE id = ?");
$stmt->execute([$demenageur_id]);
$demenageur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demenageur) {
    header('Location: logout.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_info'])) {
        // Mise à jour des informations générales
        $company_name = trim($_POST['company_name'] ?? '');
        $contact_name = trim($_POST['contact_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $postal_code = trim($_POST['postal_code'] ?? '');
        $city = trim($_POST['city'] ?? '');

        if (empty($company_name) || empty($contact_name) || empty($email) || empty($phone)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } else {
            // Vérifier si l'email existe déjà pour un autre déménageur
            $stmt = $db->prepare("SELECT id FROM demenageurs WHERE email = ? AND id != ?");
            $stmt->execute([$email, $demenageur_id]);
            if ($stmt->fetch()) {
                $error = 'Cet email est déjà utilisé par un autre déménageur.';
            } else {
                $stmt = $db->prepare("
                    UPDATE demenageurs
                    SET company_name = ?, contact_name = ?, email = ?, phone = ?, mobile = ?,
                        address = ?, postal_code = ?, city = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $company_name, $contact_name, $email, $phone, $mobile,
                    $address, $postal_code, $city, $demenageur_id
                ]);

                $_SESSION['demenageur_name'] = $company_name;
                $_SESSION['demenageur_email'] = $email;

                $success = 'Vos informations ont été mises à jour avec succès.';

                // Recharger les données
                $stmt = $db->prepare("SELECT * FROM demenageurs WHERE id = ?");
                $stmt->execute([$demenageur_id]);
                $demenageur = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    elseif (isset($_POST['update_zones'])) {
        // Mise à jour des zones de couverture
        $coverage_zones = $_POST['coverage_zones'] ?? [];
        $max_distance_km = intval($_POST['max_distance_km'] ?? 50);

        $stmt = $db->prepare("
            UPDATE demenageurs
            SET coverage_zones = ?, max_distance_km = ?
            WHERE id = ?
        ");
        $stmt->execute([
            json_encode($coverage_zones),
            $max_distance_km,
            $demenageur_id
        ]);

        $success = 'Vos zones de couverture ont été mises à jour.';

        $stmt = $db->prepare("SELECT * FROM demenageurs WHERE id = ?");
        $stmt->execute([$demenageur_id]);
        $demenageur = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    elseif (isset($_POST['update_services'])) {
        // Mise à jour des services et capacités
        $fleet_size = intval($_POST['fleet_size'] ?? 1);
        $staff_count = intval($_POST['staff_count'] ?? 2);
        $specialties = $_POST['specialties'] ?? [];
        $services_offered = $_POST['services_offered'] ?? [];
        $insurance_amount = floatval($_POST['insurance_amount'] ?? 30000);

        $stmt = $db->prepare("
            UPDATE demenageurs
            SET fleet_size = ?, staff_count = ?, specialties = ?, services_offered = ?, insurance_amount = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $fleet_size,
            $staff_count,
            json_encode($specialties),
            json_encode($services_offered),
            $insurance_amount,
            $demenageur_id
        ]);

        $success = 'Vos services et capacités ont été mis à jour.';

        $stmt = $db->prepare("SELECT * FROM demenageurs WHERE id = ?");
        $stmt->execute([$demenageur_id]);
        $demenageur = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    elseif (isset($_POST['update_password'])) {
        // Changement de mot de passe
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Veuillez remplir tous les champs.';
        } elseif (!password_verify($current_password, $demenageur['password_hash'])) {
            $error = 'Mot de passe actuel incorrect.';
        } elseif (strlen($new_password) < 8) {
            $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Les nouveaux mots de passe ne correspondent pas.';
        } else {
            $stmt = $db->prepare("UPDATE demenageurs SET password_hash = ? WHERE id = ?");
            $stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $demenageur_id]);

            $success = 'Votre mot de passe a été changé avec succès.';
        }
    }
}

$coverage_zones = json_decode($demenageur['coverage_zones'] ?? '[]', true);
$specialties = json_decode($demenageur['specialties'] ?? '[]', true);
$services_offered = json_decode($demenageur['services_offered'] ?? '[]', true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .profil-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
        }

        .tab {
            padding: 15px 30px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #718096;
            transition: 0.3s;
        }

        .tab:hover {
            color: #667eea;
        }

        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .card h2 {
            margin: 0 0 25px 0;
            font-size: 24px;
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
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-item input[type="checkbox"] {
            width: auto;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .alert {
            padding: 18px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
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

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .tabs {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <nav style="background: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: fixed; top: 0; left: 0; right: 0; z-index: 1000;">
        <div style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <a href="/index.php" style="font-size: 20px; font-weight: bold; color: #667eea; text-decoration: none;">
                🚚 <?= SITE_NAME ?>
            </a>
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="dashboard.php" style="color: #2d3748; text-decoration: none;">Tableau de bord</a>
                <a href="leads.php" style="color: #2d3748; text-decoration: none;">Mes Leads</a>
                <a href="profil.php" style="color: #667eea; font-weight: 600; text-decoration: none;">Mon Profil</a>
                <a href="abonnement.php" style="color: #2d3748; text-decoration: none;">Abonnement</a>
                <a href="logout.php" style="color: #f56565; text-decoration: none;">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="profil-page">
        <h1 style="font-size: 36px; margin-bottom: 10px;">👤 Mon Profil</h1>
        <p style="color: #718096; margin-bottom: 30px;">
            Statut :
            <span class="status-badge status-<?= $demenageur['status'] ?>">
                <?= ucfirst($demenageur['status']) ?>
                <?= $demenageur['verified'] ? ' ✓ Vérifié' : '' ?>
            </span>
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('info')">📋 Informations</button>
            <button class="tab" onclick="switchTab('zones')">📍 Zones de Couverture</button>
            <button class="tab" onclick="switchTab('services')">🚛 Services & Capacités</button>
            <button class="tab" onclick="switchTab('security')">🔐 Sécurité</button>
        </div>

        <!-- Tab 1: Informations générales -->
        <div id="tab-info" class="tab-content active">
            <div class="card">
                <h2>Informations Générales</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Nom de l'entreprise <span class="required">*</span></label>
                        <input type="text" name="company_name" value="<?= htmlspecialchars($demenageur['company_name']) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>SIRET</label>
                            <input type="text" value="<?= htmlspecialchars($demenageur['siret']) ?>" readonly style="background: #f7fafc;">
                            <small style="color: #718096;">Le SIRET ne peut pas être modifié</small>
                        </div>

                        <div class="form-group">
                            <label>Forme juridique</label>
                            <input type="text" value="<?= htmlspecialchars($demenageur['legal_form']) ?>" readonly style="background: #f7fafc;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nom du contact principal <span class="required">*</span></label>
                        <input type="text" name="contact_name" value="<?= htmlspecialchars($demenageur['contact_name']) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?= htmlspecialchars($demenageur['email']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Téléphone fixe <span class="required">*</span></label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($demenageur['phone']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Téléphone mobile</label>
                        <input type="tel" name="mobile" value="<?= htmlspecialchars($demenageur['mobile'] ?? '') ?>">
                    </div>

                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;">

                    <h3 style="margin-bottom: 20px;">Adresse du siège social</h3>

                    <div class="form-group">
                        <label>Adresse <span class="required">*</span></label>
                        <input type="text" name="address" value="<?= htmlspecialchars($demenageur['address']) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Code postal <span class="required">*</span></label>
                            <input type="text" name="postal_code" value="<?= htmlspecialchars($demenageur['postal_code']) ?>" required maxlength="5">
                        </div>

                        <div class="form-group">
                            <label>Ville <span class="required">*</span></label>
                            <input type="text" name="city" value="<?= htmlspecialchars($demenageur['city']) ?>" required>
                        </div>
                    </div>

                    <button type="submit" name="update_info" class="btn btn-primary">
                        💾 Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab 2: Zones de couverture -->
        <div id="tab-zones" class="tab-content">
            <div class="card">
                <h2>Zones de Couverture</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Départements et codes postaux couverts</label>
                        <small style="color: #718096; display: block; margin-bottom: 15px;">
                            Sélectionnez toutes les zones où vous intervenez
                        </small>
                        <div class="checkbox-group">
                            <?php
                            $all_zones = [
                                '75' => 'Paris (75)', '92' => 'Hauts-de-Seine (92)', '93' => 'Seine-Saint-Denis (93)',
                                '94' => 'Val-de-Marne (94)', '95' => 'Val-d\'Oise (95)', '78' => 'Yvelines (78)',
                                '91' => 'Essonne (91)', '77' => 'Seine-et-Marne (77)', '69' => 'Rhône (69)',
                                '13' => 'Bouches-du-Rhône (13)', '33' => 'Gironde (33)', '59' => 'Nord (59)',
                                '31' => 'Haute-Garonne (31)', '44' => 'Loire-Atlantique (44)', '06' => 'Alpes-Maritimes (06)',
                                '67' => 'Bas-Rhin (67)', '35' => 'Ille-et-Vilaine (35)', '34' => 'Hérault (34)'
                            ];
                            foreach ($all_zones as $code => $label):
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="coverage_zones[]" value="<?= $code ?>" id="zone_<?= $code ?>"
                                        <?= in_array($code, $coverage_zones) ? 'checked' : '' ?>>
                                    <label for="zone_<?= $code ?>" style="margin: 0; font-weight: normal;"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Distance maximale d'intervention (km)</label>
                        <input type="number" name="max_distance_km" value="<?= $demenageur['max_distance_km'] ?>" min="10" max="500">
                        <small style="color: #718096; display: block; margin-top: 5px;">
                            Rayon autour de votre siège social
                        </small>
                    </div>

                    <button type="submit" name="update_zones" class="btn btn-primary">
                        💾 Enregistrer les zones
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab 3: Services & Capacités -->
        <div id="tab-services" class="tab-content">
            <div class="card">
                <h2>Services & Capacités</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre de véhicules <span class="required">*</span></label>
                            <input type="number" name="fleet_size" value="<?= $demenageur['fleet_size'] ?>" min="1" required>
                        </div>

                        <div class="form-group">
                            <label>Nombre d'employés <span class="required">*</span></label>
                            <input type="number" name="staff_count" value="<?= $demenageur['staff_count'] ?>" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Spécialités</label>
                        <div class="checkbox-group">
                            <?php
                            $all_specialties = [
                                'piano' => '🎹 Piano',
                                'oeuvres_art' => '🖼️ Œuvres d\'art',
                                'international' => '🌍 International',
                                'entreprise' => '🏢 Entreprises',
                                'garde_meuble' => '📦 Garde-meuble'
                            ];
                            foreach ($all_specialties as $key => $label):
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="specialties[]" value="<?= $key ?>" id="spec_<?= $key ?>"
                                        <?= in_array($key, $specialties) ? 'checked' : '' ?>>
                                    <label for="spec_<?= $key ?>" style="margin: 0; font-weight: normal;"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Services proposés</label>
                        <div class="checkbox-group">
                            <?php
                            $all_services = [
                                'emballage' => '📦 Emballage',
                                'montage' => '🔧 Montage meubles',
                                'demontage' => '🔨 Démontage',
                                'stockage' => '🏪 Stockage',
                                'lift' => '🏗️ Monte-meuble',
                                'nettoyage' => '🧹 Nettoyage'
                            ];
                            foreach ($all_services as $key => $label):
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="services_offered[]" value="<?= $key ?>" id="serv_<?= $key ?>"
                                        <?= in_array($key, $services_offered) ? 'checked' : '' ?>>
                                    <label for="serv_<?= $key ?>" style="margin: 0; font-weight: normal;"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Montant de l'assurance professionnelle (€)</label>
                        <input type="number" name="insurance_amount" value="<?= $demenageur['insurance_amount'] ?>" min="10000" step="1000">
                        <small style="color: #718096; display: block; margin-top: 5px;">
                            Montant de garantie en responsabilité civile
                        </small>
                    </div>

                    <button type="submit" name="update_services" class="btn btn-primary">
                        💾 Enregistrer les services
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab 4: Sécurité -->
        <div id="tab-security" class="tab-content">
            <div class="card">
                <h2>Changer le Mot de Passe</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Mot de passe actuel <span class="required">*</span></label>
                        <input type="password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label>Nouveau mot de passe <span class="required">*</span></label>
                        <input type="password" name="new_password" required minlength="8">
                        <small style="color: #718096; display: block; margin-top: 5px;">
                            Minimum 8 caractères
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Confirmer le nouveau mot de passe <span class="required">*</span></label>
                        <input type="password" name="confirm_password" required minlength="8">
                    </div>

                    <button type="submit" name="update_password" class="btn btn-primary">
                        🔒 Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab
            document.getElementById('tab-' + tabName).classList.add('active');

            // Add active class to clicked tab button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
