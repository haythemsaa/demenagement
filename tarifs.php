<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Grille Tarifaire';
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .pricing-page {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .page-intro {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-intro h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .page-intro p {
            font-size: 18px;
            color: #718096;
            max-width: 700px;
            margin: 0 auto;
        }

        .pricing-calculator {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 50px;
        }

        .calculator-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        .calculator-input {
            background: rgba(255,255,255,0.15);
            padding: 20px;
            border-radius: 12px;
        }

        .calculator-input label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .calculator-input select,
        .calculator-input input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }

        .estimate-result {
            background: rgba(255,255,255,0.2);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }

        .estimate-result h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
        }

        .estimate-price {
            font-size: 48px;
            font-weight: 700;
            margin: 20px 0;
        }

        .pricing-tables {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .pricing-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .pricing-header {
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .pricing-header.eco {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .pricing-header.premium {
            background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        }

        .pricing-header h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .pricing-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .price-tag {
            font-size: 48px;
            font-weight: 700;
            margin: 20px 0;
        }

        .price-unit {
            font-size: 16px;
            opacity: 0.8;
        }

        .pricing-features {
            padding: 30px;
        }

        .pricing-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pricing-features li {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pricing-features li:last-child {
            border-bottom: none;
        }

        .feature-icon {
            color: #48bb78;
            font-weight: 700;
        }

        .feature-icon.no {
            color: #f56565;
        }

        .detailed-pricing {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .detailed-pricing h2 {
            margin: 0 0 30px 0;
            color: #2d3748;
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
        }

        .price-table thead {
            background: #f7fafc;
        }

        .price-table th,
        .price-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .price-table th {
            font-weight: 700;
            color: #2d3748;
        }

        .price-table tr:hover {
            background: #f7fafc;
        }

        .price-highlight {
            color: #667eea;
            font-weight: 700;
        }

        .factors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .factor-card {
            background: #f7fafc;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }

        .factor-card h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .factor-card p {
            margin: 0;
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }

        .savings-tips {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 40px;
            margin-top: 40px;
        }

        .savings-tips h2 {
            margin: 0 0 25px 0;
            color: #92400e;
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .tip-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .tip-item h4 {
            margin: 0 0 10px 0;
            color: #92400e;
        }

        .tip-item p {
            margin: 0;
            color: #78350f;
            font-size: 14px;
        }

        @media (max-width: 968px) {
            .calculator-grid {
                grid-template-columns: 1fr;
            }

            .page-intro h1 {
                font-size: 32px;
            }

            .price-table {
                font-size: 14px;
            }

            .price-table th,
            .price-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="pricing-page">
        <div class="page-intro">
            <h1>💰 Tarifs Transparents & Compétitifs</h1>
            <p>Comparez les prix et choisissez la formule qui vous convient. Aucun frais caché, 100% transparent.</p>
        </div>

        <div class="pricing-calculator">
            <h2 style="margin-top: 0; text-align: center;">🧮 Estimez votre déménagement en temps réel</h2>
            <div class="calculator-grid">
                <div>
                    <div class="calculator-input">
                        <label>Type de logement</label>
                        <select id="housing-type" onchange="calculatePrice()">
                            <option value="15">Studio (15m²)</option>
                            <option value="30">T1 (30m²)</option>
                            <option value="50" selected>T2 (50m²)</option>
                            <option value="70">T3 (70m²)</option>
                            <option value="90">T4 (90m²)</option>
                            <option value="120">T5+ (120m²)</option>
                            <option value="150">Maison (150m²)</option>
                        </select>
                    </div>

                    <div class="calculator-input" style="margin-top: 20px;">
                        <label>Distance (km)</label>
                        <input type="number" id="distance" value="100" min="1" max="1000" onchange="calculatePrice()">
                    </div>

                    <div class="calculator-input" style="margin-top: 20px;">
                        <label>Services additionnels</label>
                        <div style="margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <input type="checkbox" id="packing" onchange="calculatePrice()" style="width: auto;">
                                Emballage professionnel (+20%)
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <input type="checkbox" id="storage" onchange="calculatePrice()" style="width: auto;">
                                Garde-meubles (1 mois) (+15%)
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="piano" onchange="calculatePrice()" style="width: auto;">
                                Transport piano (+300€)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="estimate-result">
                    <h3>Estimation de votre déménagement</h3>
                    <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Fourchette de prix</div>
                        <div class="estimate-price" id="price-estimate">800 € - 1 200 €</div>
                    </div>
                    <p style="font-size: 14px; opacity: 0.9; margin-bottom: 20px;">
                        Prix indicatif basé sur vos critères. Obtenez jusqu'à 6 devis personnalisés pour comparer.
                    </p>
                    <a href="index.php#formulaire-devis" class="cta-button" style="background: white; color: #667eea; display: inline-block;">
                        Obtenir mes devis gratuits
                    </a>
                </div>
            </div>
        </div>

        <h2 style="text-align: center; margin-bottom: 40px;">🎯 Nos Formules</h2>

        <div class="pricing-tables">
            <div class="pricing-table">
                <div class="pricing-header eco">
                    <h3>Formule Éco</h3>
                    <p>L'essentiel au meilleur prix</p>
                    <div class="price-tag">
                        -40%
                    </div>
                    <div class="price-unit">d'économies</div>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li><span class="feature-icon">✓</span> Transport de vos biens</li>
                        <li><span class="feature-icon">✓</span> Déménageurs professionnels</li>
                        <li><span class="feature-icon">✓</span> Assurance de base</li>
                        <li><span class="feature-icon">✓</span> Devis gratuit</li>
                        <li><span class="feature-icon no">✗</span> Emballage fourni</li>
                        <li><span class="feature-icon no">✗</span> Montage/démontage</li>
                        <li><span class="feature-icon no">✗</span> Garde-meubles</li>
                    </ul>
                    <a href="index.php#formulaire-devis" class="cta-button" style="margin-top: 20px; width: 100%; display: block; text-align: center;">
                        Choisir
                    </a>
                </div>
            </div>

            <div class="pricing-table">
                <div class="pricing-header">
                    <h3>Formule Standard</h3>
                    <p>Le bon compromis qualité-prix</p>
                    <div class="price-tag">
                        Recommandé
                    </div>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li><span class="feature-icon">✓</span> Tout de la formule Éco</li>
                        <li><span class="feature-icon">✓</span> Cartons fournis</li>
                        <li><span class="feature-icon">✓</span> Montage/démontage meubles</li>
                        <li><span class="feature-icon">✓</span> Protection mobilier</li>
                        <li><span class="feature-icon">✓</span> Assurance complète</li>
                        <li><span class="feature-icon no">✗</span> Emballage par l'équipe</li>
                        <li><span class="feature-icon no">✗</span> Garde-meubles</li>
                    </ul>
                    <a href="index.php#formulaire-devis" class="cta-button" style="margin-top: 20px; width: 100%; display: block; text-align: center;">
                        Choisir
                    </a>
                </div>
            </div>

            <div class="pricing-table">
                <div class="pricing-header premium">
                    <h3>Formule Premium</h3>
                    <p>Service tout inclus sans stress</p>
                    <div class="price-tag">
                        Confort
                    </div>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li><span class="feature-icon">✓</span> Tout de la formule Standard</li>
                        <li><span class="feature-icon">✓</span> Emballage professionnel</li>
                        <li><span class="feature-icon">✓</span> Nettoyage ancien logement</li>
                        <li><span class="feature-icon">✓</span> Garde-meubles 1 mois</li>
                        <li><span class="feature-icon">✓</span> Démarches administratives</li>
                        <li><span class="feature-icon">✓</span> Service conciergerie</li>
                        <li><span class="feature-icon">✓</span> Assurance tous risques</li>
                    </ul>
                    <a href="index.php#formulaire-devis" class="cta-button" style="margin-top: 20px; width: 100%; display: block; text-align: center;">
                        Choisir
                    </a>
                </div>
            </div>
        </div>

        <div class="detailed-pricing">
            <h2>📊 Grille Tarifaire Détaillée</h2>
            <p style="color: #718096; margin-bottom: 30px;">
                Prix moyens constatés en France pour un déménagement standard (formule Standard). Les prix peuvent varier selon la région et la saison.
            </p>

            <table class="price-table">
                <thead>
                    <tr>
                        <th>Type de logement</th>
                        <th>Volume (m³)</th>
                        <th>Local (&lt;50km)</th>
                        <th>Régional (50-200km)</th>
                        <th>Longue distance (&gt;200km)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Studio</strong></td>
                        <td>15-20 m³</td>
                        <td class="price-highlight">400 - 600 €</td>
                        <td class="price-highlight">600 - 900 €</td>
                        <td class="price-highlight">900 - 1 400 €</td>
                    </tr>
                    <tr>
                        <td><strong>T1 / F1</strong></td>
                        <td>20-25 m³</td>
                        <td class="price-highlight">500 - 750 €</td>
                        <td class="price-highlight">750 - 1 100 €</td>
                        <td class="price-highlight">1 100 - 1 700 €</td>
                    </tr>
                    <tr>
                        <td><strong>T2 / F2</strong></td>
                        <td>25-35 m³</td>
                        <td class="price-highlight">700 - 1 000 €</td>
                        <td class="price-highlight">1 000 - 1 500 €</td>
                        <td class="price-highlight">1 500 - 2 200 €</td>
                    </tr>
                    <tr>
                        <td><strong>T3 / F3</strong></td>
                        <td>35-45 m³</td>
                        <td class="price-highlight">900 - 1 400 €</td>
                        <td class="price-highlight">1 400 - 2 000 €</td>
                        <td class="price-highlight">2 000 - 3 000 €</td>
                    </tr>
                    <tr>
                        <td><strong>T4 / F4</strong></td>
                        <td>45-60 m³</td>
                        <td class="price-highlight">1 200 - 1 800 €</td>
                        <td class="price-highlight">1 800 - 2 600 €</td>
                        <td class="price-highlight">2 600 - 3 800 €</td>
                    </tr>
                    <tr>
                        <td><strong>T5+ / Maison</strong></td>
                        <td>60-100 m³</td>
                        <td class="price-highlight">1 800 - 2 800 €</td>
                        <td class="price-highlight">2 800 - 4 000 €</td>
                        <td class="price-highlight">4 000 - 6 000 €</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="detailed-pricing">
            <h2>🔍 Facteurs Influençant le Prix</h2>
            <div class="factors-grid">
                <div class="factor-card">
                    <h4>📏 Volume</h4>
                    <p>Plus le volume est important, plus le coût augmente. En moyenne 15€/m³ pour un déménagement local.</p>
                </div>
                <div class="factor-card">
                    <h4>📍 Distance</h4>
                    <p>Le kilométrage impacte directement le prix. Comptez environ 1-2€/km selon la distance totale.</p>
                </div>
                <div class="factor-card">
                    <h4>🏢 Étages</h4>
                    <p>Chaque étage sans ascenseur ajoute 50-100€ au devis final pour la manutention supplémentaire.</p>
                </div>
                <div class="factor-card">
                    <h4>📅 Période</h4>
                    <p>Déménager hors saison (octobre-mars) permet d'économiser 20-30% par rapport à l'été.</p>
                </div>
                <div class="factor-card">
                    <h4>📦 Services</h4>
                    <p>Emballage, montage, stockage : chaque service additionnel augmente le coût de 15-30%.</p>
                </div>
                <div class="factor-card">
                    <h4>🎯 Accès</h4>
                    <p>Un accès difficile (ruelle étroite, parking éloigné) peut ajouter 100-200€ au devis.</p>
                </div>
            </div>
        </div>

        <div class="savings-tips">
            <h2>💡 5 Astuces pour Économiser sur Votre Déménagement</h2>
            <div class="tips-grid">
                <div class="tip-item">
                    <h4>1. Comparez les Devis</h4>
                    <p>Obtenez au moins 3-6 devis pour comparer les prix et négocier. Économies moyennes : 300-500€.</p>
                </div>
                <div class="tip-item">
                    <h4>2. Déménagez Hors Saison</h4>
                    <p>Évitez juillet-août et les fins de mois. Préférez l'automne ou l'hiver pour des tarifs -30%.</p>
                </div>
                <div class="tip-item">
                    <h4>3. Faites le Tri</h4>
                    <p>Vendez ou donnez ce dont vous n'avez plus besoin. Moins de volume = moins cher !</p>
                </div>
                <div class="tip-item">
                    <h4>4. Emballez Vous-Même</h4>
                    <p>L'emballage par vos soins peut vous faire économiser 200-400€ selon le volume.</p>
                </div>
                <div class="tip-item">
                    <h4>5. Groupez le Transport</h4>
                    <p>Le groupage (partage du camion) réduit les coûts de 30-40% pour les longues distances.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function calculatePrice() {
            const surface = parseInt(document.getElementById('housing-type').value);
            const distance = parseInt(document.getElementById('distance').value);
            const packing = document.getElementById('packing').checked;
            const storage = document.getElementById('storage').checked;
            const piano = document.getElementById('piano').checked;

            // Calcul de base
            let basePrice = 500;
            let pricePerSqm = 8;
            let pricePerKm = 1.5;

            let total = basePrice + (surface * pricePerSqm) + (distance * pricePerKm);

            // Services additionnels
            if (packing) total *= 1.2;
            if (storage) total *= 1.15;
            if (piano) total += 300;

            // Fourchette +/- 20%
            const min = Math.round(total * 0.8);
            const max = Math.round(total * 1.2);

            document.getElementById('price-estimate').textContent =
                `${min.toLocaleString()} € - ${max.toLocaleString()} €`;
        }

        // Initialize
        calculatePrice();
    </script>
</body>
</html>
