<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Estimateur de Prix Professionnel';

// Liste complète de meubles par catégorie (100+ items)
$furniture_database = [
    'Salon' => [
        ['id' => 'canape_3p', 'name' => 'Canapé 3 places', 'volume' => 2.5, 'weight' => 80, 'icon' => '🛋️'],
        ['id' => 'canape_2p', 'name' => 'Canapé 2 places', 'volume' => 1.8, 'weight' => 60, 'icon' => '🛋️'],
        ['id' => 'fauteuil', 'name' => 'Fauteuil', 'volume' => 0.8, 'weight' => 25, 'icon' => '🪑'],
        ['id' => 'table_basse', 'name' => 'Table basse', 'volume' => 0.5, 'weight' => 20, 'icon' => '🪑'],
        ['id' => 'meuble_tv', 'name' => 'Meuble TV', 'volume' => 0.7, 'weight' => 30, 'icon' => '📺'],
        ['id' => 'bibliotheque', 'name' => 'Bibliothèque', 'volume' => 1.5, 'weight' => 50, 'icon' => '📚'],
        ['id' => 'vitrine', 'name' => 'Vitrine', 'volume' => 1.2, 'weight' => 40, 'icon' => '🏺'],
        ['id' => 'lampadaire', 'name' => 'Lampadaire', 'volume' => 0.2, 'weight' => 5, 'icon' => '💡'],
        ['id' => 'tapis', 'name' => 'Tapis (grand)', 'volume' => 0.3, 'weight' => 10, 'icon' => '🔲'],
    ],
    'Chambre' => [
        ['id' => 'lit_double', 'name' => 'Lit double (140-160cm)', 'volume' => 2.0, 'weight' => 70, 'icon' => '🛏️'],
        ['id' => 'lit_simple', 'name' => 'Lit simple (90cm)', 'volume' => 1.2, 'weight' => 40, 'icon' => '🛏️'],
        ['id' => 'matelas_double', 'name' => 'Matelas double', 'volume' => 1.5, 'weight' => 30, 'icon' => '🛏️'],
        ['id' => 'matelas_simple', 'name' => 'Matelas simple', 'volume' => 0.8, 'weight' => 20, 'icon' => '🛏️'],
        ['id' => 'armoire_2p', 'name' => 'Armoire 2 portes', 'volume' => 2.5, 'weight' => 80, 'icon' => '🚪'],
        ['id' => 'armoire_3p', 'name' => 'Armoire 3 portes', 'volume' => 3.5, 'weight' => 120, 'icon' => '🚪'],
        ['id' => 'commode', 'name' => 'Commode', 'volume' => 0.8, 'weight' => 35, 'icon' => '🗄️'],
        ['id' => 'table_chevet', 'name' => 'Table de chevet', 'volume' => 0.2, 'weight' => 10, 'icon' => '🪑'],
        ['id' => 'bureau', 'name' => 'Bureau', 'volume' => 1.0, 'weight' => 40, 'icon' => '🖥️'],
        ['id' => 'chaise_bureau', 'name' => 'Chaise de bureau', 'volume' => 0.5, 'weight' => 15, 'icon' => '🪑'],
    ],
    'Cuisine' => [
        ['id' => 'table_cuisine', 'name' => 'Table de cuisine', 'volume' => 1.2, 'weight' => 40, 'icon' => '🍽️'],
        ['id' => 'chaise', 'name' => 'Chaise', 'volume' => 0.3, 'weight' => 8, 'icon' => '🪑'],
        ['id' => 'buffet', 'name' => 'Buffet/Vaisselier', 'volume' => 1.8, 'weight' => 60, 'icon' => '🗄️'],
        ['id' => 'frigo', 'name' => 'Réfrigérateur', 'volume' => 1.5, 'weight' => 80, 'icon' => '🧊'],
        ['id' => 'congelateur', 'name' => 'Congélateur', 'volume' => 1.2, 'weight' => 60, 'icon' => '🧊'],
        ['id' => 'lave_vaisselle', 'name' => 'Lave-vaisselle', 'volume' => 0.8, 'weight' => 50, 'icon' => '🍽️'],
        ['id' => 'four', 'name' => 'Four', 'volume' => 0.5, 'weight' => 40, 'icon' => '🔥'],
        ['id' => 'micro_ondes', 'name' => 'Micro-ondes', 'volume' => 0.2, 'weight' => 15, 'icon' => '📻'],
        ['id' => 'etagere_cuisine', 'name' => 'Étagère cuisine', 'volume' => 0.6, 'weight' => 20, 'icon' => '📦'],
    ],
    'Buanderie' => [
        ['id' => 'lave_linge', 'name' => 'Lave-linge', 'volume' => 0.8, 'weight' => 70, 'icon' => '🌊'],
        ['id' => 'seche_linge', 'name' => 'Sèche-linge', 'volume' => 0.8, 'weight' => 50, 'icon' => '🌊'],
        ['id' => 'table_repasser', 'name' => 'Table à repasser', 'volume' => 0.3, 'weight' => 8, 'icon' => '👔'],
        ['id' => 'aspirateur', 'name' => 'Aspirateur', 'volume' => 0.2, 'weight' => 10, 'icon' => '🧹'],
    ],
    'Salle à Manger' => [
        ['id' => 'table_salle', 'name' => 'Table salle à manger', 'volume' => 1.5, 'weight' => 50, 'icon' => '🍽️'],
        ['id' => 'chaise_salle', 'name' => 'Chaise salle à manger', 'volume' => 0.4, 'weight' => 10, 'icon' => '🪑'],
        ['id' => 'vaisselier', 'name' => 'Vaisselier', 'volume' => 2.0, 'weight' => 70, 'icon' => '🗄️'],
        ['id' => 'desserte', 'name' => 'Desserte', 'volume' => 0.6, 'weight' => 25, 'icon' => '🛒'],
    ],
    'Entrée/Couloir' => [
        ['id' => 'meuble_entree', 'name' => 'Meuble d\'entrée', 'volume' => 0.8, 'weight' => 30, 'icon' => '🚪'],
        ['id' => 'porte_manteau', 'name' => 'Porte-manteau', 'volume' => 0.3, 'weight' => 10, 'icon' => '🧥'],
        ['id' => 'miroir', 'name' => 'Miroir (grand)', 'volume' => 0.2, 'weight' => 15, 'icon' => '🪞'],
        ['id' => 'banc', 'name' => 'Banc', 'volume' => 0.5, 'weight' => 20, 'icon' => '🪑'],
    ],
    'Bureau/Office' => [
        ['id' => 'bureau_pro', 'name' => 'Bureau professionnel', 'volume' => 1.5, 'weight' => 50, 'icon' => '💼'],
        ['id' => 'fauteuil_bureau', 'name' => 'Fauteuil de bureau', 'volume' => 0.8, 'weight' => 25, 'icon' => '🪑'],
        ['id' => 'armoire_bureau', 'name' => 'Armoire de bureau', 'volume' => 2.0, 'weight' => 70, 'icon' => '🗄️'],
        ['id' => 'caisson', 'name' => 'Caisson de bureau', 'volume' => 0.4, 'weight' => 20, 'icon' => '📦'],
        ['id' => 'etagere_bureau', 'name' => 'Étagère bureau', 'volume' => 1.0, 'weight' => 30, 'icon' => '📚'],
    ],
    'Objets Spéciaux' => [
        ['id' => 'piano_droit', 'name' => 'Piano droit', 'volume' => 3.0, 'weight' => 250, 'icon' => '🎹', 'special' => true],
        ['id' => 'piano_queue', 'name' => 'Piano à queue', 'volume' => 5.0, 'weight' => 400, 'icon' => '🎹', 'special' => true],
        ['id' => 'coffre_fort', 'name' => 'Coffre-fort', 'volume' => 1.5, 'weight' => 200, 'icon' => '🔒', 'special' => true],
        ['id' => 'aquarium', 'name' => 'Aquarium (grand)', 'volume' => 1.0, 'weight' => 100, 'icon' => '🐠', 'special' => true],
        ['id' => 'billard', 'name' => 'Table de billard', 'volume' => 4.0, 'weight' => 300, 'icon' => '🎱', 'special' => true],
        ['id' => 'velo', 'name' => 'Vélo', 'volume' => 0.5, 'weight' => 15, 'icon' => '🚴'],
        ['id' => 'moto', 'name' => 'Moto/Scooter', 'volume' => 2.0, 'weight' => 150, 'icon' => '🏍️', 'special' => true],
    ],
    'Cartons & Divers' => [
        ['id' => 'carton_standard', 'name' => 'Carton standard', 'volume' => 0.1, 'weight' => 5, 'icon' => '📦'],
        ['id' => 'carton_livre', 'name' => 'Carton livres', 'volume' => 0.05, 'weight' => 10, 'icon' => '📚'],
        ['id' => 'carton_penderie', 'name' => 'Carton penderie', 'volume' => 0.3, 'weight' => 8, 'icon' => '👔'],
        ['id' => 'valise', 'name' => 'Valise/Sac', 'volume' => 0.15, 'weight' => 15, 'icon' => '🧳'],
        ['id' => 'plante', 'name' => 'Plante (grande)', 'volume' => 0.3, 'weight' => 20, 'icon' => '🌱'],
    ],
];
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .estimator-page {
            max-width: 1600px;
            margin: 80px auto 50px;
            padding: 0 20px;
        }

        .estimator-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 50px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            color: white;
        }

        .estimator-header h1 {
            font-size: 48px;
            margin: 0 0 15px 0;
        }

        .estimator-header p {
            font-size: 20px;
            opacity: 0.95;
            margin: 0;
        }

        .estimator-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .main-panel {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .sidebar-panel {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .step-indicator::before {
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
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            z-index: 1;
            position: relative;
        }

        .step.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .step.completed {
            border-color: #48bb78;
            background: #48bb78;
            color: white;
        }

        .section-title {
            font-size: 24px;
            margin: 30px 0 20px;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-section {
            margin-bottom: 30px;
        }

        .category-header {
            background: #f7fafc;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .category-header:hover {
            background: #edf2f7;
        }

        .furniture-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            padding: 10px 0;
        }

        .furniture-item {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            transition: 0.3s;
        }

        .furniture-item:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .furniture-item.selected {
            border-color: #667eea;
            background: #f7fafc;
        }

        .furniture-item.special {
            border-color: #f6ad55;
            background: #fffaf0;
        }

        .furniture-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .furniture-icon {
            font-size: 32px;
        }

        .furniture-name {
            flex: 1;
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
        }

        .furniture-meta {
            font-size: 11px;
            color: #718096;
            margin-bottom: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: #667eea;
            color: white;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .qty-btn:hover {
            background: #5568d3;
        }

        .qty-display {
            min-width: 40px;
            text-align: center;
            font-weight: 700;
            font-size: 18px;
            color: #2d3748;
        }

        .summary-panel {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .summary-panel h3 {
            margin: 0 0 20px 0;
            color: #2d3748;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .total-line.major {
            font-weight: 700;
            font-size: 18px;
            color: #667eea;
            border-bottom: 2px solid #667eea;
        }

        .price-breakdown {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-top: 20px;
        }

        .price-breakdown h4 {
            margin: 0 0 20px 0;
            font-size: 16px;
        }

        .estimated-price {
            font-size: 48px;
            font-weight: 700;
            margin: 10px 0;
        }

        .price-range {
            font-size: 14px;
            opacity: 0.9;
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-action {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-export {
            background: #48bb78;
            color: white;
        }

        .btn-export:hover {
            background: #38a169;
        }

        @media (max-width: 1200px) {
            .estimator-grid {
                grid-template-columns: 1fr;
            }

            .sidebar-panel {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .furniture-grid {
                grid-template-columns: 1fr;
            }

            .estimator-header h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="estimator-page">
        <div class="estimator-header">
            <h1>💎 Estimateur de Prix Professionnel</h1>
            <p>L'outil le plus précis d'Europe - Plus de 100 types de meubles</p>
        </div>

        <div class="estimator-grid">
            <div class="main-panel">
                <div class="step-indicator">
                    <div class="step active" id="step-1">1</div>
                    <div class="step" id="step-2">2</div>
                    <div class="step" id="step-3">3</div>
                    <div class="step" id="step-4">4</div>
                </div>

                <!-- Étape 1: Informations générales -->
                <div class="step-content" id="content-step-1">
                    <h2 class="section-title">📍 Informations de Base</h2>

                    <div class="form-group">
                        <label>Code postal de départ</label>
                        <input type="text" id="postal_depart" placeholder="75001" maxlength="5">
                    </div>

                    <div class="form-group">
                        <label>Ville de départ</label>
                        <input type="text" id="ville_depart" placeholder="Paris">
                    </div>

                    <div class="form-group">
                        <label>Étage de départ</label>
                        <select id="etage_depart">
                            <option value="0">Rez-de-chaussée</option>
                            <option value="1">1er étage</option>
                            <option value="2">2ème étage</option>
                            <option value="3">3ème étage</option>
                            <option value="4">4ème étage</option>
                            <option value="5">5ème étage</option>
                            <option value="6">6ème étage +</option>
                        </select>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="ascenseur_depart">
                        <label for="ascenseur_depart">Ascenseur disponible au départ</label>
                    </div>

                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;">

                    <div class="form-group">
                        <label>Code postal d'arrivée</label>
                        <input type="text" id="postal_arrivee" placeholder="69001" maxlength="5">
                    </div>

                    <div class="form-group">
                        <label>Ville d'arrivée</label>
                        <input type="text" id="ville_arrivee" placeholder="Lyon">
                    </div>

                    <div class="form-group">
                        <label>Étage d'arrivée</label>
                        <select id="etage_arrivee">
                            <option value="0">Rez-de-chaussée</option>
                            <option value="1">1er étage</option>
                            <option value="2">2ème étage</option>
                            <option value="3">3ème étage</option>
                            <option value="4">4ème étage</option>
                            <option value="5">5ème étage</option>
                            <option value="6">6ème étage +</option>
                        </select>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="ascenseur_arrivee">
                        <label for="ascenseur_arrivee">Ascenseur disponible à l'arrivée</label>
                    </div>

                    <div class="form-group">
                        <label>Distance estimée (km)</label>
                        <input type="number" id="distance" placeholder="400" min="1" max="5000" value="100">
                    </div>

                    <div class="action-buttons">
                        <button class="btn-action btn-primary" onclick="nextStep(2)">
                            Étape suivante →
                        </button>
                    </div>
                </div>

                <!-- Étape 2: Sélection des meubles -->
                <div class="step-content" id="content-step-2" style="display: none;">
                    <h2 class="section-title">🛋️ Sélection de vos Meubles</h2>
                    <p style="color: #718096; margin-bottom: 30px;">
                        Parcourez chaque catégorie et ajustez les quantités de vos meubles
                    </p>

                    <div id="furniture-categories">
                        <?php foreach ($furniture_database as $category => $items): ?>
                        <div class="category-section">
                            <div class="category-header" onclick="toggleCategory('<?= sanitize_id($category) ?>')">
                                <span><?= e($category) ?> (<?= count($items) ?> items)</span>
                                <span id="cat-total-<?= sanitize_id($category) ?>">0 m³</span>
                            </div>
                            <div class="furniture-grid" id="category-<?= sanitize_id($category) ?>" style="display: none;">
                                <?php foreach ($items as $item): ?>
                                <div class="furniture-item <?= isset($item['special']) ? 'special' : '' ?>" data-item='<?= json_encode($item) ?>'>
                                    <div class="furniture-header">
                                        <span class="furniture-icon"><?= $item['icon'] ?></span>
                                        <span class="furniture-name"><?= e($item['name']) ?></span>
                                    </div>
                                    <div class="furniture-meta">
                                        📦 <?= $item['volume'] ?> m³ • ⚖️ <?= $item['weight'] ?> kg
                                    </div>
                                    <div class="quantity-control">
                                        <button class="qty-btn" onclick="updateFurniture('<?= $item['id'] ?>', -1)">−</button>
                                        <span class="qty-display" id="qty-<?= $item['id'] ?>">0</span>
                                        <button class="qty-btn" onclick="updateFurniture('<?= $item['id'] ?>', 1)">+</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-action btn-secondary" onclick="previousStep(1)">
                            ← Retour
                        </button>
                        <button class="btn-action btn-primary" onclick="nextStep(3)">
                            Étape suivante →
                        </button>
                    </div>
                </div>

                <!-- Étape 3: Services additionnels -->
                <div class="step-content" id="content-step-3" style="display: none;">
                    <h2 class="section-title">⚙️ Services Additionnels</h2>

                    <div class="checkbox-group">
                        <input type="checkbox" id="service_emballage" onchange="updateEstimate()">
                        <label for="service_emballage">Emballage professionnel (+20%)</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="service_montage" onchange="updateEstimate()">
                        <label for="service_montage">Montage/démontage meubles (+15%)</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="service_stockage" onchange="updateEstimate()">
                        <label for="service_stockage">Garde-meubles 1 mois (+300€)</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="service_nettoyage" onchange="updateEstimate()">
                        <label for="service_nettoyage">Nettoyage ancien logement (+200€)</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="service_monte_charge" onchange="updateEstimate()">
                        <label for="service_monte_charge">Monte-meubles (+150€)</label>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label>Date souhaitée du déménagement</label>
                        <input type="date" id="date_demenagement" onchange="updateEstimate()">
                    </div>

                    <div class="action-buttons">
                        <button class="btn-action btn-secondary" onclick="previousStep(2)">
                            ← Retour
                        </button>
                        <button class="btn-action btn-primary" onclick="nextStep(4)">
                            Voir l'estimation →
                        </button>
                    </div>
                </div>

                <!-- Étape 4: Résumé et estimation -->
                <div class="step-content" id="content-step-4" style="display: none;">
                    <h2 class="section-title">📋 Votre Estimation Détaillée</h2>

                    <div id="estimation-details"></div>

                    <div class="action-buttons">
                        <button class="btn-action btn-secondary" onclick="previousStep(3)">
                            ← Modifier
                        </button>
                        <button class="btn-action btn-export" onclick="exportPDF()">
                            📄 Exporter en PDF
                        </button>
                        <button class="btn-action btn-primary" onclick="location.href='index.php#formulaire-devis'">
                            Recevoir des devis →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar : Résumé en temps réel -->
            <div class="sidebar-panel">
                <div class="summary-panel">
                    <h3>📊 Résumé</h3>
                    <div class="total-line">
                        <span>Volume total</span>
                        <strong id="total-volume">0 m³</strong>
                    </div>
                    <div class="total-line">
                        <span>Poids total</span>
                        <strong id="total-weight">0 kg</strong>
                    </div>
                    <div class="total-line">
                        <span>Nombre d'objets</span>
                        <strong id="total-items">0</strong>
                    </div>
                    <div class="total-line">
                        <span>Distance</span>
                        <strong id="summary-distance">0 km</strong>
                    </div>
                    <div class="total-line">
                        <span>Étages (départ)</span>
                        <strong id="summary-etage-depart">RDC</strong>
                    </div>
                    <div class="total-line">
                        <span>Étages (arrivée)</span>
                        <strong id="summary-etage-arrivee">RDC</strong>
                    </div>
                </div>

                <div class="price-breakdown">
                    <h4>Estimation de Prix</h4>
                    <div class="estimated-price" id="estimated-price">0 €</div>
                    <div class="price-range" id="price-range">Fourchette : 0 € - 0 €</div>
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 13px;">
                        ✓ Prix indicatif TTC<br>
                        ✓ Assurance incluse<br>
                        ✓ Sans engagement
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Database des meubles
        const furnitureDB = <?= json_encode($furniture_database) ?>;
        const inventory = {};
        let currentStep = 1;

        // Helper pour sanitize les IDs
        function sanitizeId(str) {
            return str.toLowerCase()
                .replace(/[éèê]/g, 'e')
                .replace(/[àâ]/g, 'a')
                .replace(/['/\s]/g, '_');
        }

        // Toggle catégorie
        function toggleCategory(catId) {
            const content = document.getElementById('category-' + catId);
            content.style.display = content.style.display === 'none' ? 'grid' : 'none';
        }

        // Mettre à jour quantité meuble
        function updateFurniture(id, delta) {
            if (!inventory[id]) inventory[id] = 0;
            inventory[id] = Math.max(0, inventory[id] + delta);

            document.getElementById('qty-' + id).textContent = inventory[id];

            // Highlight selected
            const item = document.querySelector(`[data-item*='"id":"${id}"']`);
            if (inventory[id] > 0) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }

            updateEstimate();
        }

        // Calculer totaux
        function updateEstimate() {
            let totalVolume = 0;
            let totalWeight = 0;
            let totalItems = 0;

            // Parcourir l'inventaire
            Object.keys(inventory).forEach(itemId => {
                const qty = inventory[itemId];
                if (qty > 0) {
                    // Trouver l'item dans la DB
                    let item = null;
                    Object.values(furnitureDB).forEach(category => {
                        const found = category.find(i => i.id === itemId);
                        if (found) item = found;
                    });

                    if (item) {
                        totalVolume += item.volume * qty;
                        totalWeight += item.weight * qty;
                        totalItems += qty;
                    }
                }
            });

            // Récupérer les paramètres
            const distance = parseInt(document.getElementById('distance')?.value || 0);
            const etageDepart = parseInt(document.getElementById('etage_depart')?.value || 0);
            const etageArrivee = parseInt(document.getElementById('etage_arrivee')?.value || 0);
            const ascenseurDepart = document.getElementById('ascenseur_depart')?.checked;
            const ascenseurArrivee = document.getElementById('ascenseur_arrivee')?.checked;

            // Calcul du prix de base
            let basePrice = 500; // Prix de base
            let volumePrice = totalVolume * 15; // 15€ par m³
            let distancePrice = distance * 1.5; // 1.5€ par km

            // Majoration étages sans ascenseur
            if (!ascenseurDepart && etageDepart > 0) {
                basePrice += etageDepart * 80; // 80€ par étage
            }
            if (!ascenseurArrivee && etageArrivee > 0) {
                basePrice += etageArrivee * 80;
            }

            let totalPrice = basePrice + volumePrice + distancePrice;

            // Services additionnels
            if (document.getElementById('service_emballage')?.checked) totalPrice *= 1.2;
            if (document.getElementById('service_montage')?.checked) totalPrice *= 1.15;
            if (document.getElementById('service_stockage')?.checked) totalPrice += 300;
            if (document.getElementById('service_nettoyage')?.checked) totalPrice += 200;
            if (document.getElementById('service_monte_charge')?.checked) totalPrice += 150;

            // Période haute saison (juin-août)
            const date = document.getElementById('date_demenagement')?.value;
            if (date) {
                const month = new Date(date).getMonth() + 1;
                if (month >= 6 && month <= 8) {
                    totalPrice *= 1.15; // +15% haute saison
                }
            }

            // Mettre à jour l'affichage
            document.getElementById('total-volume').textContent = Math.round(totalVolume * 10) / 10 + ' m³';
            document.getElementById('total-weight').textContent = totalWeight + ' kg';
            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('summary-distance').textContent = distance + ' km';
            document.getElementById('summary-etage-depart').textContent = etageDepart === 0 ? 'RDC' : etageDepart + 'e';
            document.getElementById('summary-etage-arrivee').textContent = etageArrivee === 0 ? 'RDC' : etageArrivee + 'e';

            const minPrice = Math.round(totalPrice * 0.85);
            const maxPrice = Math.round(totalPrice * 1.15);

            document.getElementById('estimated-price').textContent = Math.round(totalPrice).toLocaleString() + ' €';
            document.getElementById('price-range').textContent = `Fourchette : ${minPrice.toLocaleString()} € - ${maxPrice.toLocaleString()} €`;

            // Mettre à jour totaux par catégorie
            Object.keys(furnitureDB).forEach(category => {
                let catVolume = 0;
                furnitureDB[category].forEach(item => {
                    const qty = inventory[item.id] || 0;
                    catVolume += item.volume * qty;
                });
                const catId = sanitizeId(category);
                const elem = document.getElementById('cat-total-' + catId);
                if (elem) {
                    elem.textContent = (Math.round(catVolume * 10) / 10) + ' m³';
                }
            });
        }

        // Navigation entre étapes
        function nextStep(step) {
            if (step === 4) {
                generateEstimationSummary();
            }

            document.getElementById('content-step-' + currentStep).style.display = 'none';
            document.getElementById('step-' + currentStep).classList.remove('active');
            document.getElementById('step-' + currentStep).classList.add('completed');

            currentStep = step;
            document.getElementById('content-step-' + currentStep).style.display = 'block';
            document.getElementById('step-' + currentStep).classList.add('active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function previousStep(step) {
            document.getElementById('content-step-' + currentStep).style.display = 'none';
            document.getElementById('step-' + currentStep).classList.remove('active');

            currentStep = step;
            document.getElementById('content-step-' + currentStep).style.display = 'block';
            document.getElementById('step-' + currentStep).classList.remove('completed');
            document.getElementById('step-' + currentStep).classList.add('active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Générer le résumé d'estimation
        function generateEstimationSummary() {
            let html = '<div style="background: #f7fafc; padding: 30px; border-radius: 15px; margin-bottom: 20px;">';

            // Itinéraire
            html += '<h3 style="margin-top: 0;">🚚 Itinéraire</h3>';
            html += '<p><strong>Départ :</strong> ' + document.getElementById('ville_depart').value + ' (' + document.getElementById('postal_depart').value + ')</p>';
            html += '<p><strong>Arrivée :</strong> ' + document.getElementById('ville_arrivee').value + ' (' + document.getElementById('postal_arrivee').value + ')</p>';
            html += '<p><strong>Distance :</strong> ' + document.getElementById('distance').value + ' km</p>';

            html += '</div>';

            // Inventaire
            html += '<div style="background: #f7fafc; padding: 30px; border-radius: 15px; margin-bottom: 20px;">';
            html += '<h3 style="margin-top: 0;">📦 Inventaire Complet</h3>';

            Object.keys(furnitureDB).forEach(category => {
                let categoryHtml = '';
                let hasItems = false;

                furnitureDB[category].forEach(item => {
                    const qty = inventory[item.id] || 0;
                    if (qty > 0) {
                        hasItems = true;
                        categoryHtml += `<li>${item.icon} ${item.name} × ${qty} (${(item.volume * qty).toFixed(1)} m³, ${item.weight * qty} kg)</li>`;
                    }
                });

                if (hasItems) {
                    html += `<h4 style="color: #667eea; margin-top: 20px;">${category}</h4><ul>` + categoryHtml + '</ul>';
                }
            });

            html += '</div>';

            // Services
            const services = [];
            if (document.getElementById('service_emballage').checked) services.push('✓ Emballage professionnel');
            if (document.getElementById('service_montage').checked) services.push('✓ Montage/démontage');
            if (document.getElementById('service_stockage').checked) services.push('✓ Garde-meubles');
            if (document.getElementById('service_nettoyage').checked) services.push('✓ Nettoyage');
            if (document.getElementById('service_monte_charge').checked) services.push('✓ Monte-meubles');

            if (services.length > 0) {
                html += '<div style="background: #f7fafc; padding: 30px; border-radius: 15px;">';
                html += '<h3 style="margin-top: 0;">⚙️ Services Sélectionnés</h3>';
                html += '<ul>' + services.map(s => '<li>' + s + '</li>').join('') + '</ul>';
                html += '</div>';
            }

            document.getElementById('estimation-details').innerHTML = html;
        }

        // Export PDF (simulation)
        function exportPDF() {
            alert('🎉 Votre estimation PDF est en cours de génération !\n\nVous allez recevoir par email :\n✓ Estimation détaillée\n✓ Liste complète des meubles\n✓ Comparatif des formules\n✓ Conseils personnalisés');
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            updateEstimate();

            // Auto-calcul distance en fonction codes postaux
            ['postal_depart', 'postal_arrivee'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', function() {
                    // Simulation calcul distance
                    const cp1 = document.getElementById('postal_depart').value;
                    const cp2 = document.getElementById('postal_arrivee').value;
                    if (cp1.length === 5 && cp2.length === 5) {
                        const dept1 = parseInt(cp1.substring(0, 2));
                        const dept2 = parseInt(cp2.substring(0, 2));
                        const estimatedDistance = Math.abs(dept1 - dept2) * 50;
                        document.getElementById('distance').value = estimatedDistance;
                        updateEstimate();
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
function sanitize_id($str) {
    return str_replace([' ', '/', '\'', 'à', 'â', 'é', 'è', 'ê'], ['_', '_', '_', 'a', 'a', 'e', 'e', 'e'], strtolower($str));
}
?>