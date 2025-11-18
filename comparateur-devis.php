<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Comparateur de Devis';

// Simulation de devis (en production, viendraient de la base de données)
$sample_quotes = [
    [
        'id' => 1,
        'company' => 'DéménaPro Expert',
        'logo' => '🚚',
        'rating' => 4.8,
        'reviews' => 234,
        'price' => 1850,
        'response_time' => '2h',
        'experience' => '15 ans',
        'insurance' => 'Tous risques',
        'services' => ['Emballage', 'Montage', 'Assurance premium'],
        'eco_friendly' => true,
        'availability' => 'Sous 2 semaines',
        'payment_terms' => '30% acompte',
        'cancellation' => 'Gratuit 48h avant',
        'pros' => ['Très professionnel', 'Équipe formée', 'Matériel moderne'],
        'cons' => ['Prix élevé'],
    ],
    [
        'id' => 2,
        'company' => 'MoveEasy Transport',
        'logo' => '📦',
        'rating' => 4.5,
        'reviews' => 156,
        'price' => 1450,
        'response_time' => '4h',
        'experience' => '8 ans',
        'insurance' => 'Standard',
        'services' => ['Transport', 'Assurance'],
        'eco_friendly' => false,
        'availability' => 'Sous 1 semaine',
        'payment_terms' => '20% acompte',
        'cancellation' => '50€ si annulation',
        'pros' => ['Bon rapport qualité-prix', 'Rapide'],
        'cons' => ['Emballage en supplément', 'Moins d\'expérience'],
    ],
    [
        'id' => 3,
        'company' => 'Budget Déménagement',
        'logo' => '💰',
        'rating' => 4.2,
        'reviews' => 89,
        'price' => 1150,
        'response_time' => '6h',
        'experience' => '5 ans',
        'insurance' => 'Basique',
        'services' => ['Transport uniquement'],
        'eco_friendly' => false,
        'availability' => 'Flexible',
        'payment_terms' => '50% acompte',
        'cancellation' => '100€ si annulation',
        'pros' => ['Prix très compétitif', 'Flexible'],
        'cons' => ['Services basiques', 'Assurance limitée', 'Peu d\'avis'],
    ],
    [
        'id' => 4,
        'company' => 'Premium Move Services',
        'logo' => '⭐',
        'rating' => 4.9,
        'reviews' => 412,
        'price' => 2200,
        'response_time' => '1h',
        'experience' => '20 ans',
        'insurance' => 'Premium + objets précieux',
        'services' => ['Emballage premium', 'Montage', 'Nettoyage', 'Garde-meubles', 'Conciergerie'],
        'eco_friendly' => true,
        'availability' => 'Sur mesure',
        'payment_terms' => '40% acompte',
        'cancellation' => 'Gratuit 72h avant',
        'pros' => ['Excellence reconnue', 'Service complet', 'Très fiable'],
        'cons' => ['Prix premium'],
    ],
    [
        'id' => 5,
        'company' => 'Eco Move Green',
        'logo' => '🌱',
        'rating' => 4.6,
        'reviews' => 178,
        'price' => 1650,
        'response_time' => '3h',
        'experience' => '10 ans',
        'insurance' => 'Standard éco',
        'services' => ['Emballage recyclable', 'Transport éco', 'Compensation CO2'],
        'eco_friendly' => true,
        'availability' => 'Sous 10 jours',
        'payment_terms' => '25% acompte',
        'cancellation' => 'Gratuit 24h avant',
        'pros' => ['Écologique', 'Bon prix', 'Engagement environnemental'],
        'cons' => ['Moins de services premium'],
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
        .comparator-page {
            max-width: 1600px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .filters-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }

        .view-btn {
            padding: 10px 25px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .view-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        /* Vue Liste */
        .quotes-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .quote-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .quote-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .quote-card.recommended {
            border: 3px solid #48bb78;
            position: relative;
        }

        .recommended-badge {
            position: absolute;
            top: -15px;
            right: 30px;
            background: #48bb78;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
        }

        .quote-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-logo {
            font-size: 32px;
        }

        .company-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .stars {
            color: #fbbf24;
            font-size: 18px;
        }

        .rating-number {
            font-weight: 700;
            color: #2d3748;
        }

        .review-count {
            color: #718096;
            font-size: 14px;
        }

        .company-meta {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #718096;
        }

        .price-block {
            text-align: right;
        }

        .price {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            line-height: 1;
        }

        .price-label {
            font-size: 14px;
            color: #718096;
            margin-top: 5px;
        }

        .quote-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
            padding: 20px;
            background: #f7fafc;
            border-radius: 10px;
        }

        .detail-item {
            display: flex;
            align-items: start;
            gap: 10px;
        }

        .detail-icon {
            font-size: 20px;
        }

        .detail-content strong {
            display: block;
            color: #2d3748;
            margin-bottom: 3px;
        }

        .detail-content span {
            color: #718096;
            font-size: 14px;
        }

        .services-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
        }

        .service-tag {
            background: #e6fffa;
            color: #047857;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .eco-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .pros-cons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .pros-cons h4 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .pros-cons ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pros-cons li {
            padding: 5px 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pros li {
            color: #065f46;
        }

        .cons li {
            color: #991b1b;
        }

        .quote-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-quote {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-select {
            background: #667eea;
            color: white;
        }

        .btn-select:hover {
            background: #5568d3;
        }

        .btn-details {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-compare {
            background: white;
            color: #2d3748;
            border: 2px solid #e2e8f0;
        }

        /* Vue Tableau */
        .quotes-table {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            display: none;
        }

        .quotes-table.active {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f7fafc;
            font-weight: 700;
            color: #2d3748;
        }

        tr:hover {
            background: #f7fafc;
        }

        .best-value {
            background: #d1fae5;
            color: #065f46;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .quote-header {
                flex-direction: column;
            }

            .price-block {
                text-align: left;
                margin-top: 15px;
            }

            .pros-cons {
                grid-template-columns: 1fr;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="comparator-page">
        <div class="page-header">
            <h1>⚖️ Comparateur de Devis Interactif</h1>
            <p>Comparez jusqu'à 6 devis côte à côte et trouvez la meilleure offre</p>
        </div>

        <div class="filters-section">
            <h3 style="margin-top: 0;">🔍 Filtres & Tri</h3>
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Trier par</label>
                    <select id="sort-by" onchange="sortQuotes()">
                        <option value="price-asc">Prix croissant</option>
                        <option value="price-desc">Prix décroissant</option>
                        <option value="rating">Meilleure note</option>
                        <option value="reviews">Plus d'avis</option>
                        <option value="response">Réponse rapide</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Budget maximum</label>
                    <select id="budget-filter" onchange="filterQuotes()">
                        <option value="">Tous les prix</option>
                        <option value="1200">Jusqu'à 1 200€</option>
                        <option value="1500">Jusqu'à 1 500€</option>
                        <option value="1800">Jusqu'à 1 800€</option>
                        <option value="2500">Jusqu'à 2 500€</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Note minimum</label>
                    <select id="rating-filter" onchange="filterQuotes()">
                        <option value="">Toutes les notes</option>
                        <option value="4.5">4.5+ ⭐</option>
                        <option value="4.7">4.7+ ⭐⭐</option>
                        <option value="4.8">4.8+ ⭐⭐⭐</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Services inclus</label>
                    <select id="services-filter" onchange="filterQuotes()">
                        <option value="">Tous</option>
                        <option value="emballage">Avec emballage</option>
                        <option value="montage">Avec montage</option>
                        <option value="eco">Éco-responsable</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="view-toggle">
            <button class="view-btn active" onclick="switchView('list')">📋 Vue Liste</button>
            <button class="view-btn" onclick="switchView('table')">📊 Vue Tableau</button>
        </div>

        <!-- Vue Liste -->
        <div class="quotes-list" id="list-view">
            <?php foreach ($sample_quotes as $index => $quote): ?>
            <div class="quote-card <?= $index === 0 ? 'recommended' : '' ?>" data-price="<?= $quote['price'] ?>" data-rating="<?= $quote['rating'] ?>" data-eco="<?= $quote['eco_friendly'] ? '1' : '0' ?>">
                <?php if ($index === 0): ?>
                <div class="recommended-badge">⭐ RECOMMANDÉ</div>
                <?php endif; ?>

                <div class="quote-header">
                    <div class="company-info">
                        <div class="company-name">
                            <span class="company-logo"><?= $quote['logo'] ?></span>
                            <?= e($quote['company']) ?>
                            <?php if ($quote['eco_friendly']): ?>
                                <span class="eco-badge">🌱 Éco</span>
                            <?php endif; ?>
                        </div>

                        <div class="company-rating">
                            <span class="stars">
                                <?= str_repeat('★', floor($quote['rating'])) ?>
                                <?= str_repeat('☆', 5 - floor($quote['rating'])) ?>
                            </span>
                            <span class="rating-number"><?= number_format($quote['rating'], 1) ?></span>
                            <span class="review-count">(<?= $quote['reviews'] ?> avis)</span>
                        </div>

                        <div class="company-meta">
                            <span>⏱️ Réponse : <?= $quote['response_time'] ?></span>
                            <span>📅 <?= $quote['experience'] ?> d'expérience</span>
                        </div>
                    </div>

                    <div class="price-block">
                        <div class="price"><?= number_format($quote['price']) ?> €</div>
                        <div class="price-label">Prix total TTC</div>
                    </div>
                </div>

                <div class="quote-details">
                    <div class="detail-item">
                        <span class="detail-icon">🛡️</span>
                        <div class="detail-content">
                            <strong>Assurance</strong>
                            <span><?= e($quote['insurance']) ?></span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-icon">📅</span>
                        <div class="detail-content">
                            <strong>Disponibilité</strong>
                            <span><?= e($quote['availability']) ?></span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-icon">💳</span>
                        <div class="detail-content">
                            <strong>Paiement</strong>
                            <span><?= e($quote['payment_terms']) ?></span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-icon">🔄</span>
                        <div class="detail-content">
                            <strong>Annulation</strong>
                            <span><?= e($quote['cancellation']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="services-list">
                    <?php foreach ($quote['services'] as $service): ?>
                        <span class="service-tag">✓ <?= e($service) ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="pros-cons">
                    <div class="pros">
                        <h4>✅ Points forts</h4>
                        <ul>
                            <?php foreach ($quote['pros'] as $pro): ?>
                                <li>• <?= e($pro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="cons">
                        <h4>⚠️ Points d'attention</h4>
                        <ul>
                            <?php foreach ($quote['cons'] as $con): ?>
                                <li>• <?= e($con) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="quote-actions">
                    <button class="btn-quote btn-select">Choisir cette offre</button>
                    <button class="btn-quote btn-details">Plus de détails</button>
                    <button class="btn-quote btn-compare">
                        <input type="checkbox" style="width: auto; margin-right: 5px;"> Comparer
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Vue Tableau -->
        <div class="quotes-table" id="table-view">
            <table>
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        <th>Note</th>
                        <th>Prix</th>
                        <th>Réponse</th>
                        <th>Services</th>
                        <th>Assurance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sample_quotes as $quote): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 24px;"><?= $quote['logo'] ?></span>
                                <strong><?= e($quote['company']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <div><?= $quote['rating'] ?> ⭐</div>
                            <div style="font-size: 12px; color: #718096;"><?= $quote['reviews'] ?> avis</div>
                        </td>
                        <td>
                            <strong style="font-size: 20px; color: #667eea;"><?= number_format($quote['price']) ?> €</strong>
                            <?php if ($quote['price'] < 1500): ?>
                                <div class="best-value">Meilleur prix</div>
                            <?php endif; ?>
                        </td>
                        <td><?= $quote['response_time'] ?></td>
                        <td><?= count($quote['services']) ?> services</td>
                        <td><?= e($quote['insurance']) ?></td>
                        <td>
                            <button class="btn-quote btn-select" style="padding: 10px 20px;">Choisir</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="text-align: center; margin-top: 50px; padding: 40px; background: #f7fafc; border-radius: 15px;">
            <h2>Besoin de plus de devis ?</h2>
            <p style="color: #718096; margin: 15px 0 30px;">Remplissez notre formulaire pour recevoir jusqu'à 6 devis personnalisés</p>
            <a href="/index.php#formulaire-devis" class="cta-button">Recevoir mes devis gratuits</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function switchView(view) {
            const listView = document.getElementById('list-view');
            const tableView = document.getElementById('table-view');
            const buttons = document.querySelectorAll('.view-btn');

            buttons.forEach(btn => btn.classList.remove('active'));

            if (view === 'list') {
                listView.style.display = 'flex';
                tableView.classList.remove('active');
                event.target.classList.add('active');
            } else {
                listView.style.display = 'none';
                tableView.classList.add('active');
                event.target.classList.add('active');
            }
        }

        function sortQuotes() {
            const sortBy = document.getElementById('sort-by').value;
            const container = document.querySelector('.quotes-list');
            const cards = Array.from(container.querySelectorAll('.quote-card'));

            cards.sort((a, b) => {
                switch(sortBy) {
                    case 'price-asc':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-desc':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'rating':
                        return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                    default:
                        return 0;
                }
            });

            cards.forEach(card => container.appendChild(card));
        }

        function filterQuotes() {
            const budget = document.getElementById('budget-filter').value;
            const rating = document.getElementById('rating-filter').value;
            const cards = document.querySelectorAll('.quote-card');

            cards.forEach(card => {
                let show = true;

                if (budget && parseFloat(card.dataset.price) > parseFloat(budget)) {
                    show = false;
                }

                if (rating && parseFloat(card.dataset.rating) < parseFloat(rating)) {
                    show = false;
                }

                card.style.display = show ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
