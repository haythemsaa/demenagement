<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = __('volume_calculator.page_title');
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .calculator-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .calculator-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        .calculator-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .room-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .room-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .room-icon {
            font-size: 32px;
            width: 50px;
            text-align: center;
        }

        .room-details h4 {
            margin: 0 0 5px 0;
            color: #2d3748;
        }

        .room-details p {
            margin: 0;
            font-size: 13px;
            color: #718096;
        }

        .room-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            border-radius: 8px;
            padding: 5px;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            border: none;
            background: #667eea;
            color: white;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .quantity-btn:hover {
            background: #5568d3;
        }

        .quantity-display {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
            color: #2d3748;
        }

        .volume-badge {
            background: #e6fffa;
            color: #047857;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        .results-section {
            position: sticky;
            top: 100px;
        }

        .result-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .result-card h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
        }

        .total-volume {
            font-size: 48px;
            font-weight: 700;
            margin: 10px 0;
        }

        .volume-unit {
            font-size: 24px;
            opacity: 0.9;
        }

        .estimate-range {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .estimate-range p {
            margin: 5px 0;
            font-size: 14px;
        }

        .price-range {
            font-size: 24px;
            font-weight: 600;
            margin: 10px 0;
        }

        .quick-templates {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .template-btn {
            padding: 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }

        .template-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .template-btn.active {
            border-color: #667eea;
            background: #f7fafc;
        }

        .template-btn strong {
            display: block;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .template-btn span {
            display: block;
            font-size: 13px;
            color: #718096;
        }

        .category-header {
            background: #f7fafc;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 30px 0 20px;
        }

        .category-header h3 {
            margin: 0;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-full {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary-calc {
            background: #667eea;
            color: white;
        }

        .btn-primary-calc:hover {
            background: #5568d3;
        }

        .btn-secondary-calc {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary-calc:hover {
            background: #f7fafc;
        }

        @media (max-width: 968px) {
            .calculator-grid {
                grid-template-columns: 1fr;
            }

            .results-section {
                position: static;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="calculator-page">
        <h1>📦 <?= __('volume_calculator.title') ?></h1>
        <p class="subtitle"><?= __('volume_calculator.subtitle') ?></p>

        <div class="calculator-section">
            <h3>⚡ <?= __('volume_calculator.quick_estimate') ?></h3>
            <div class="quick-templates">
                <div class="template-btn" onclick="loadTemplate('studio')">
                    <strong>Studio</strong>
                    <span>15-20 m³</span>
                </div>
                <div class="template-btn" onclick="loadTemplate('t1')">
                    <strong>T1</strong>
                    <span>20-25 m³</span>
                </div>
                <div class="template-btn" onclick="loadTemplate('t2')">
                    <strong>T2</strong>
                    <span>25-35 m³</span>
                </div>
                <div class="template-btn" onclick="loadTemplate('t3')">
                    <strong>T3</strong>
                    <span>35-45 m³</span>
                </div>
                <div class="template-btn" onclick="loadTemplate('t4')">
                    <strong>T4</strong>
                    <span>45-60 m³</span>
                </div>
                <div class="template-btn" onclick="loadTemplate('t5')">
                    <strong>T5+</strong>
                    <span>60-80 m³</span>
                </div>
            </div>
        </div>

        <div class="calculator-grid">
            <div class="calculator-section">
                <h3>🛋️ <?= __('volume_calculator.detailed_calculation') ?></h3>

                <!-- Pièces principales -->
                <div class="category-header">
                    <h3>🏠 <?= __('volume_calculator.main_rooms') ?></h3>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🛏️</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.bedroom') ?></h4>
                            <p>~12 m³ par chambre</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('bedroom', -1)">−</button>
                            <span class="quantity-display" id="bedroom-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('bedroom', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="bedroom-volume">0</span> m³</span>
                    </div>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🛋️</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.living_room') ?></h4>
                            <p>~15 m³ par salon</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('living', -1)">−</button>
                            <span class="quantity-display" id="living-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('living', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="living-volume">0</span> m³</span>
                    </div>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🍳</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.kitchen') ?></h4>
                            <p>~10 m³ équipée</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('kitchen', -1)">−</button>
                            <span class="quantity-display" id="kitchen-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('kitchen', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="kitchen-volume">0</span> m³</span>
                    </div>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🚿</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.bathroom') ?></h4>
                            <p>~3 m³ par salle de bain</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('bathroom', -1)">−</button>
                            <span class="quantity-display" id="bathroom-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('bathroom', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="bathroom-volume">0</span> m³</span>
                    </div>
                </div>

                <!-- Espaces additionnels -->
                <div class="category-header">
                    <h3>📦 <?= __('volume_calculator.additional_spaces') ?></h3>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">📚</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.office') ?></h4>
                            <p>~8 m³ par bureau</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('office', -1)">−</button>
                            <span class="quantity-display" id="office-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('office', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="office-volume">0</span> m³</span>
                    </div>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🧺</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.cellar') ?></h4>
                            <p>~5 m³ par cave/grenier</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('cellar', -1)">−</button>
                            <span class="quantity-display" id="cellar-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('cellar', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="cellar-volume">0</span> m³</span>
                    </div>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🚗</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.garage') ?></h4>
                            <p>~10 m³ par garage</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('garage', -1)">−</button>
                            <span class="quantity-display" id="garage-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('garage', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="garage-volume">0</span> m³</span>
                    </div>
                </div>

                <!-- Objets spéciaux -->
                <div class="category-header">
                    <h3>🎹 <?= __('volume_calculator.special_items') ?></h3>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🎹</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.piano') ?></h4>
                            <p>~2-4 m³</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('piano', -1)">−</button>
                            <span class="quantity-display" id="piano-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('piano', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="piano-volume">0</span> m³</span>
                    </div>
                </div>

                <div class="room-item">
                    <div class="room-info">
                        <div class="room-icon">🔒</div>
                        <div class="room-details">
                            <h4><?= __('volume_calculator.safe') ?></h4>
                            <p>~1-2 m³</p>
                        </div>
                    </div>
                    <div class="room-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity('safe', -1)">−</button>
                            <span class="quantity-display" id="safe-qty">0</span>
                            <button class="quantity-btn" onclick="updateQuantity('safe', 1)">+</button>
                        </div>
                        <span class="volume-badge"><span id="safe-volume">0</span> m³</span>
                    </div>
                </div>
            </div>

            <!-- Résultats -->
            <div class="results-section">
                <div class="result-card">
                    <h3>📊 <?= __('volume_calculator.your_estimate') ?></h3>
                    <div class="total-volume">
                        <span id="total-volume">0</span>
                        <span class="volume-unit">m³</span>
                    </div>

                    <div class="estimate-range">
                        <p><?= __('volume_calculator.estimated_cost') ?></p>
                        <div class="price-range" id="price-range">0 € - 0 €</div>
                        <p style="font-size: 12px; margin-top: 10px; opacity: 0.8;">
                            <?= __('volume_calculator.price_info') ?>
                        </p>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-full btn-primary-calc" onclick="requestQuotes()">
                        <?= __('volume_calculator.get_quotes') ?>
                    </button>
                </div>

                <div class="action-buttons">
                    <button class="btn-full btn-secondary-calc" onclick="resetCalculator()">
                        <?= __('volume_calculator.reset') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        const rooms = {
            bedroom: { volume: 12, qty: 0 },
            living: { volume: 15, qty: 0 },
            kitchen: { volume: 10, qty: 0 },
            bathroom: { volume: 3, qty: 0 },
            office: { volume: 8, qty: 0 },
            cellar: { volume: 5, qty: 0 },
            garage: { volume: 10, qty: 0 },
            piano: { volume: 3, qty: 0 },
            safe: { volume: 1.5, qty: 0 }
        };

        const templates = {
            studio: { bedroom: 0, living: 1, kitchen: 1, bathroom: 1 },
            t1: { bedroom: 1, living: 1, kitchen: 1, bathroom: 1 },
            t2: { bedroom: 2, living: 1, kitchen: 1, bathroom: 1 },
            t3: { bedroom: 3, living: 1, kitchen: 1, bathroom: 1, office: 0 },
            t4: { bedroom: 4, living: 1, kitchen: 1, bathroom: 2, office: 1 },
            t5: { bedroom: 5, living: 1, kitchen: 1, bathroom: 2, office: 1, cellar: 1 }
        };

        function updateQuantity(room, delta) {
            rooms[room].qty = Math.max(0, rooms[room].qty + delta);
            document.getElementById(room + '-qty').textContent = rooms[room].qty;
            document.getElementById(room + '-volume').textContent =
                (rooms[room].qty * rooms[room].volume).toFixed(1);
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            for (let room in rooms) {
                total += rooms[room].qty * rooms[room].volume;
            }

            document.getElementById('total-volume').textContent = Math.round(total);

            // Calcul estimation prix (base: 500€ + 15€/m³)
            const basePrice = 500;
            const pricePerM3 = 15;
            const minPrice = basePrice + (total * (pricePerM3 - 3));
            const maxPrice = basePrice + (total * (pricePerM3 + 3));

            document.getElementById('price-range').textContent =
                `${Math.round(minPrice).toLocaleString()} € - ${Math.round(maxPrice).toLocaleString()} €`;
        }

        function loadTemplate(type) {
            // Reset
            for (let room in rooms) {
                rooms[room].qty = 0;
            }

            // Apply template
            const template = templates[type];
            for (let room in template) {
                rooms[room].qty = template[room];
            }

            // Update UI
            for (let room in rooms) {
                document.getElementById(room + '-qty').textContent = rooms[room].qty;
                document.getElementById(room + '-volume').textContent =
                    (rooms[room].qty * rooms[room].volume).toFixed(1);
            }

            // Highlight active template
            document.querySelectorAll('.template-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.template-btn').classList.add('active');

            calculateTotal();
        }

        function resetCalculator() {
            for (let room in rooms) {
                rooms[room].qty = 0;
                document.getElementById(room + '-qty').textContent = 0;
                document.getElementById(room + '-volume').textContent = 0;
            }
            document.querySelectorAll('.template-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            calculateTotal();
        }

        function requestQuotes() {
            const volume = document.getElementById('total-volume').textContent;
            sessionStorage.setItem('estimatedVolume', volume);
            window.location.href = 'index.php#formulaire-devis';
        }

        // Initialize
        calculateTotal();
    </script>
</body>
</html>
