<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

$success = '';
$error = '';
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = intval($_POST['step'] ?? 1);

    if ($step === 1) {
        // Étape 1: Informations entreprise
        $_SESSION['inscription_data'] = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'siret' => trim($_POST['siret'] ?? ''),
            'legal_form' => $_POST['legal_form'] ?? 'SARL',
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'mobile' => trim($_POST['mobile'] ?? ''),
        ];

        // Validation
        if (empty($_SESSION['inscription_data']['company_name']) ||
            empty($_SESSION['inscription_data']['siret']) ||
            empty($_SESSION['inscription_data']['email'])) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($_SESSION['inscription_data']['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (strlen($_SESSION['inscription_data']['siret']) !== 14) {
            $error = 'Le SIRET doit contenir 14 chiffres.';
        } else {
            // Vérifier si SIRET ou email déjà existant
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id FROM demenageurs WHERE siret = ? OR email = ?");
            $stmt->execute([$_SESSION['inscription_data']['siret'], $_SESSION['inscription_data']['email']]);
            if ($stmt->fetch()) {
                $error = 'Cette entreprise ou cet email est déjà inscrit.';
            } else {
                header('Location: inscription.php?step=2');
                exit;
            }
        }

    } elseif ($step === 2) {
        // Étape 2: Adresse et zones
        $_SESSION['inscription_data']['address'] = trim($_POST['address'] ?? '');
        $_SESSION['inscription_data']['postal_code'] = trim($_POST['postal_code'] ?? '');
        $_SESSION['inscription_data']['city'] = trim($_POST['city'] ?? '');
        $_SESSION['inscription_data']['coverage_zones'] = $_POST['coverage_zones'] ?? [];
        $_SESSION['inscription_data']['max_distance_km'] = intval($_POST['max_distance_km'] ?? 50);

        if (empty($_SESSION['inscription_data']['address']) ||
            empty($_SESSION['inscription_data']['postal_code']) ||
            empty($_SESSION['inscription_data']['city'])) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } else {
            header('Location: inscription.php?step=3');
            exit;
        }

    } elseif ($step === 3) {
        // Étape 3: Capacités et services
        $_SESSION['inscription_data']['fleet_size'] = intval($_POST['fleet_size'] ?? 1);
        $_SESSION['inscription_data']['staff_count'] = intval($_POST['staff_count'] ?? 2);
        $_SESSION['inscription_data']['specialties'] = $_POST['specialties'] ?? [];
        $_SESSION['inscription_data']['services_offered'] = $_POST['services_offered'] ?? [];
        $_SESSION['inscription_data']['insurance_amount'] = floatval($_POST['insurance_amount'] ?? 30000);
        $_SESSION['inscription_data']['password'] = $_POST['password'] ?? '';
        $_SESSION['inscription_data']['password_confirm'] = $_POST['password_confirm'] ?? '';

        // Validation
        if (empty($_SESSION['inscription_data']['password'])) {
            $error = 'Veuillez choisir un mot de passe.';
        } elseif (strlen($_SESSION['inscription_data']['password']) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($_SESSION['inscription_data']['password'] !== $_SESSION['inscription_data']['password_confirm']) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            // Créer le compte
            try {
                $db = Database::getInstance()->getConnection();

                $sql = "INSERT INTO demenageurs (
                    company_name, siret, legal_form, contact_name, email, password_hash,
                    phone, mobile, address, postal_code, city, country,
                    coverage_zones, max_distance_km, fleet_size, staff_count,
                    specialties, services_offered, insurance_amount,
                    status, verified
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'FR', ?, ?, ?, ?, ?, ?, ?, 'pending', FALSE)";

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $_SESSION['inscription_data']['company_name'],
                    $_SESSION['inscription_data']['siret'],
                    $_SESSION['inscription_data']['legal_form'],
                    $_SESSION['inscription_data']['contact_name'],
                    $_SESSION['inscription_data']['email'],
                    password_hash($_SESSION['inscription_data']['password'], PASSWORD_DEFAULT),
                    $_SESSION['inscription_data']['phone'],
                    $_SESSION['inscription_data']['mobile'],
                    $_SESSION['inscription_data']['address'],
                    $_SESSION['inscription_data']['postal_code'],
                    $_SESSION['inscription_data']['city'],
                    json_encode($_SESSION['inscription_data']['coverage_zones']),
                    $_SESSION['inscription_data']['max_distance_km'],
                    $_SESSION['inscription_data']['fleet_size'],
                    $_SESSION['inscription_data']['staff_count'],
                    json_encode($_SESSION['inscription_data']['specialties']),
                    json_encode($_SESSION['inscription_data']['services_offered']),
                    $_SESSION['inscription_data']['insurance_amount']
                ]);

                $demenageur_id = $db->lastInsertId();

                // Créer un abonnement gratuit par défaut
                $stmt = $db->prepare("
                    INSERT INTO demenageur_subscriptions
                    (demenageur_id, plan_id, start_date, end_date, billing_cycle, status, amount_paid)
                    SELECT ?, id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'monthly', 'active', 0.00
                    FROM subscription_plans WHERE slug = 'free' LIMIT 1
                ");
                $stmt->execute([$demenageur_id]);

                unset($_SESSION['inscription_data']);

                $_SESSION['success_message'] = 'Votre compte a été créé ! Votre demande est en cours de validation.';
                header('Location: login.php');
                exit;

            } catch (PDOException $e) {
                $error = 'Erreur lors de la création du compte: ' . $e->getMessage();
            }
        }
    }
}

$data = $_SESSION['inscription_data'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Déménageur - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .inscription-page {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .steps-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .steps-progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #718096;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .step.active .step-number {
            background: #667eea;
            color: white;
        }

        .step.completed .step-number {
            background: #48bb78;
            color: white;
        }

        .step-label {
            font-size: 14px;
            color: #718096;
        }

        .step.active .step-label {
            color: #2d3748;
            font-weight: 600;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-card h2 {
            margin: 0 0 30px 0;
            color: #2d3748;
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
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }

        .btn-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .alert {
            padding: 18px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        .info-box {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="inscription-page">
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 36px; margin-bottom: 10px;">🚚 Inscription Déménageur</h1>
            <p style="color: #718096;">Rejoignez notre réseau de professionnels et recevez des leads qualifiés</p>
        </div>

        <!-- Progress Steps -->
        <div class="steps-progress">
            <div class="step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">
                <div class="step-number">1</div>
                <div class="step-label">Entreprise</div>
            </div>
            <div class="step <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">
                <div class="step-number">2</div>
                <div class="step-label">Zone</div>
            </div>
            <div class="step <?= $step >= 3 ? 'active' : '' ?>">
                <div class="step-number">3</div>
                <div class="step-label">Services</div>
            </div>
        </div>

        <div class="form-card">
            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <!-- ÉTAPE 1: Informations Entreprise -->
                <h2>📋 Informations de l'Entreprise</h2>

                <div class="info-box">
                    ℹ️ <strong>Compte gratuit pendant 30 jours</strong> - Testez notre plateforme avec 5 leads offerts !
                </div>

                <form method="POST">
                    <input type="hidden" name="step" value="1">

                    <div class="form-group">
                        <label>Nom de l'entreprise <span class="required">*</span></label>
                        <input type="text" name="company_name" value="<?= htmlspecialchars($data['company_name'] ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>SIRET <span class="required">*</span></label>
                            <input type="text" name="siret" value="<?= htmlspecialchars($data['siret'] ?? '') ?>" required maxlength="14" placeholder="12345678901234">
                            <small style="color: #718096;">14 chiffres sans espaces</small>
                        </div>

                        <div class="form-group">
                            <label>Forme juridique <span class="required">*</span></label>
                            <select name="legal_form" required>
                                <option value="SARL" <?= ($data['legal_form'] ?? '') === 'SARL' ? 'selected' : '' ?>>SARL</option>
                                <option value="SAS" <?= ($data['legal_form'] ?? '') === 'SAS' ? 'selected' : '' ?>>SAS</option>
                                <option value="SASU" <?= ($data['legal_form'] ?? '') === 'SASU' ? 'selected' : '' ?>>SASU</option>
                                <option value="EURL" <?= ($data['legal_form'] ?? '') === 'EURL' ? 'selected' : '' ?>>EURL</option>
                                <option value="EI" <?= ($data['legal_form'] ?? '') === 'EI' ? 'selected' : '' ?>>EI</option>
                                <option value="SA" <?= ($data['legal_form'] ?? '') === 'SA' ? 'selected' : '' ?>>SA</option>
                                <option value="AUTRE">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nom du contact principal <span class="required">*</span></label>
                        <input type="text" name="contact_name" value="<?= htmlspecialchars($data['contact_name'] ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Téléphone fixe <span class="required">*</span></label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($data['phone'] ?? '') ?>" required placeholder="01 23 45 67 89">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Téléphone mobile</label>
                        <input type="tel" name="mobile" value="<?= htmlspecialchars($data['mobile'] ?? '') ?>" placeholder="06 12 34 56 78">
                    </div>

                    <div class="btn-actions">
                        <a href="/demenageur-pro.php" class="btn btn-secondary">Retour</a>
                        <button type="submit" class="btn btn-primary">Suivant →</button>
                    </div>
                </form>

            <?php elseif ($step === 2): ?>
                <!-- ÉTAPE 2: Adresse et Zones de Couverture -->
                <h2>📍 Zone de Couverture</h2>

                <form method="POST">
                    <input type="hidden" name="step" value="2">

                    <div class="form-group">
                        <label>Adresse du siège social <span class="required">*</span></label>
                        <input type="text" name="address" value="<?= htmlspecialchars($data['address'] ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Code postal <span class="required">*</span></label>
                            <input type="text" name="postal_code" value="<?= htmlspecialchars($data['postal_code'] ?? '') ?>" required maxlength="5">
                        </div>

                        <div class="form-group">
                            <label>Ville <span class="required">*</span></label>
                            <input type="text" name="city" value="<?= htmlspecialchars($data['city'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Zones de couverture (départements/codes postaux) <span class="required">*</span></label>
                        <small style="color: #718096; display: block; margin-bottom: 10px;">
                            Sélectionnez les zones où vous intervenez. Plus vous couvrez de zones, plus vous recevrez de leads.
                        </small>
                        <div class="checkbox-group">
                            <?php
                            $common_zones = [
                                '75' => 'Paris (75)',
                                '92' => 'Hauts-de-Seine (92)',
                                '93' => 'Seine-Saint-Denis (93)',
                                '94' => 'Val-de-Marne (94)',
                                '95' => 'Val-d\'Oise (95)',
                                '78' => 'Yvelines (78)',
                                '91' => 'Essonne (91)',
                                '77' => 'Seine-et-Marne (77)',
                                '69' => 'Rhône (69)',
                                '13' => 'Bouches-du-Rhône (13)',
                                '33' => 'Gironde (33)',
                                '59' => 'Nord (59)',
                                '31' => 'Haute-Garonne (31)',
                                '44' => 'Loire-Atlantique (44)',
                            ];
                            $selected_zones = $data['coverage_zones'] ?? [];
                            foreach ($common_zones as $code => $label):
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="coverage_zones[]" value="<?= $code ?>" id="zone_<?= $code ?>"
                                        <?= in_array($code, $selected_zones) ? 'checked' : '' ?>>
                                    <label for="zone_<?= $code ?>" style="margin: 0; font-weight: normal;"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Distance maximale d'intervention (km)</label>
                        <input type="number" name="max_distance_km" value="<?= $data['max_distance_km'] ?? 50 ?>" min="10" max="500">
                        <small style="color: #718096;">Rayon autour de votre siège social</small>
                    </div>

                    <div class="btn-actions">
                        <a href="inscription.php?step=1" class="btn btn-secondary">← Précédent</a>
                        <button type="submit" class="btn btn-primary">Suivant →</button>
                    </div>
                </form>

            <?php elseif ($step === 3): ?>
                <!-- ÉTAPE 3: Capacités et Mot de Passe -->
                <h2>🚛 Capacités et Services</h2>

                <form method="POST">
                    <input type="hidden" name="step" value="3">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre de véhicules <span class="required">*</span></label>
                            <input type="number" name="fleet_size" value="<?= $data['fleet_size'] ?? 1 ?>" min="1" required>
                        </div>

                        <div class="form-group">
                            <label>Nombre d'employés <span class="required">*</span></label>
                            <input type="number" name="staff_count" value="<?= $data['staff_count'] ?? 2 ?>" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Spécialités</label>
                        <div class="checkbox-group">
                            <?php
                            $specialties_list = [
                                'piano' => '🎹 Piano',
                                'oeuvres_art' => '🖼️ Œuvres d\'art',
                                'international' => '🌍 International',
                                'entreprise' => '🏢 Entreprises',
                                'garde_meuble' => '📦 Garde-meuble'
                            ];
                            $selected_specialties = $data['specialties'] ?? [];
                            foreach ($specialties_list as $key => $label):
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="specialties[]" value="<?= $key ?>" id="spec_<?= $key ?>"
                                        <?= in_array($key, $selected_specialties) ? 'checked' : '' ?>>
                                    <label for="spec_<?= $key ?>" style="margin: 0; font-weight: normal;"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Services proposés</label>
                        <div class="checkbox-group">
                            <?php
                            $services_list = [
                                'emballage' => '📦 Emballage',
                                'montage' => '🔧 Montage meubles',
                                'demontage' => '🔨 Démontage',
                                'stockage' => '🏪 Stockage',
                                'lift' => '🏗️ Monte-meuble',
                                'nettoyage' => '🧹 Nettoyage'
                            ];
                            $selected_services = $data['services_offered'] ?? [];
                            foreach ($services_list as $key => $label):
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="services_offered[]" value="<?= $key ?>" id="serv_<?= $key ?>"
                                        <?= in_array($key, $selected_services) ? 'checked' : '' ?>>
                                    <label for="serv_<?= $key ?>" style="margin: 0; font-weight: normal;"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Montant de l'assurance professionnelle (€)</label>
                        <input type="number" name="insurance_amount" value="<?= $data['insurance_amount'] ?? 30000 ?>" min="10000" step="1000">
                        <small style="color: #718096;">Montant de garantie en responsabilité civile</small>
                    </div>

                    <hr style="margin: 30px 0; border: none; border-top: 2px solid #e2e8f0;">

                    <h3 style="margin-bottom: 20px;">🔐 Sécurité du Compte</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mot de passe <span class="required">*</span></label>
                            <input type="password" name="password" required minlength="8">
                            <small style="color: #718096;">Minimum 8 caractères</small>
                        </div>

                        <div class="form-group">
                            <label>Confirmer le mot de passe <span class="required">*</span></label>
                            <input type="password" name="password_confirm" required minlength="8">
                        </div>
                    </div>

                    <div class="btn-actions">
                        <a href="inscription.php?step=2" class="btn btn-secondary">← Précédent</a>
                        <button type="submit" class="btn btn-primary">✅ Créer mon compte</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <p style="color: #718096;">
                Vous avez déjà un compte ?
                <a href="login.php" style="color: #667eea; font-weight: 600;">Se connecter</a>
            </p>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
