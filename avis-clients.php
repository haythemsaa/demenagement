<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Avis Clients';

// Témoignages (en production, ces données viendraient de la base de données)
$testimonials = [
    [
        'name' => 'Marie Dubois',
        'location' => 'Paris',
        'rating' => 5,
        'date' => '2024-01-15',
        'photo' => 'https://i.pravatar.cc/150?img=1',
        'moving' => 'Paris → Lyon',
        'comment' => 'Service exceptionnel ! J\'ai reçu 5 devis en moins de 24h et j\'ai économisé plus de 800€ en comparant les offres. Les déménageurs étaient professionnels et ponctuels.',
        'verified' => true,
        'helpful' => 45
    ],
    [
        'name' => 'Thomas Martin',
        'location' => 'Marseille',
        'rating' => 5,
        'date' => '2024-01-10',
        'photo' => 'https://i.pravatar.cc/150?img=12',
        'moving' => 'Marseille → Bordeaux',
        'comment' => 'Excellente plateforme de comparaison. Le processus est simple et rapide. J\'ai pu choisir le meilleur rapport qualité-prix parmi plusieurs offres compétitives.',
        'verified' => true,
        'helpful' => 38
    ],
    [
        'name' => 'Sophie Laurent',
        'location' => 'Lyon',
        'rating' => 4,
        'date' => '2024-01-08',
        'photo' => 'https://i.pravatar.cc/150?img=5',
        'moving' => 'Lyon → Toulouse',
        'comment' => 'Très satisfaite du service. Les devis sont arrivés rapidement et les déménageurs recommandés étaient très compétents. Une petite amélioration sur le suivi serait appréciée.',
        'verified' => true,
        'helpful' => 32
    ],
    [
        'name' => 'Jean-Pierre Rousseau',
        'location' => 'Nantes',
        'rating' => 5,
        'date' => '2024-01-05',
        'photo' => 'https://i.pravatar.cc/150?img=14',
        'moving' => 'Nantes → Rennes',
        'comment' => 'Déménagement parfait de A à Z ! Grâce à cette plateforme, j\'ai trouvé un déménageur sérieux et économique. Je recommande vivement !',
        'verified' => true,
        'helpful' => 51
    ],
    [
        'name' => 'Caroline Bernard',
        'location' => 'Toulouse',
        'rating' => 5,
        'date' => '2024-01-02',
        'photo' => 'https://i.pravatar.cc/150?img=9',
        'moving' => 'Toulouse → Montpellier',
        'comment' => 'Processus très fluide et transparent. J\'apprécie particulièrement la possibilité de comparer plusieurs offres sans engagement. Économies réalisées : 650€ !',
        'verified' => true,
        'helpful' => 42
    ],
    [
        'name' => 'Alexandre Petit',
        'location' => 'Strasbourg',
        'rating' => 4,
        'date' => '2023-12-28',
        'photo' => 'https://i.pravatar.cc/150?img=13',
        'moving' => 'Strasbourg → Nancy',
        'comment' => 'Bon service global. Les devis reçus étaient détaillés et les prix compétitifs. Un déménageur m\'a contacté très rapidement.',
        'verified' => true,
        'helpful' => 27
    ],
    [
        'name' => 'Isabelle Moreau',
        'location' => 'Lille',
        'rating' => 5,
        'date' => '2023-12-25',
        'photo' => 'https://i.pravatar.cc/150?img=10',
        'moving' => 'Lille → Bruxelles (BE)',
        'comment' => 'Déménagement international facilité ! J\'ai obtenu des devis de déménageurs spécialisés en moins de 48h. Service client très réactif aux questions.',
        'verified' => true,
        'helpful' => 39
    ],
    [
        'name' => 'François Girard',
        'location' => 'Nice',
        'rating' => 5,
        'date' => '2023-12-20',
        'photo' => 'https://i.pravatar.cc/150?img=15',
        'moving' => 'Nice → Monaco',
        'comment' => 'Impressionné par la qualité des partenaires. Tous les déménageurs étaient certifiés et assurés. Déménagement réalisé sans accroc !',
        'verified' => true,
        'helpful' => 34
    ]
];

// Statistiques globales
$stats = [
    'total_reviews' => 4782,
    'average_rating' => 4.7,
    'satisfaction_rate' => 96,
    'would_recommend' => 94
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
        .reviews-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-header h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .rating-summary {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .overall-rating {
            text-align: center;
        }

        .rating-number {
            font-size: 72px;
            font-weight: 700;
            color: #667eea;
            line-height: 1;
        }

        .rating-stars {
            font-size: 32px;
            color: #fbbf24;
            margin: 10px 0;
        }

        .rating-count {
            color: #718096;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .stat-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-value {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
        }

        .filters-section {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .filter-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .filter-btn:hover {
            border-color: #667eea;
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .testimonial-header {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .testimonial-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
        }

        .testimonial-info {
            flex: 1;
        }

        .testimonial-name {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .testimonial-verified {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }

        .testimonial-location {
            color: #718096;
            font-size: 14px;
            margin-top: 5px;
        }

        .testimonial-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .stars {
            color: #fbbf24;
            font-size: 20px;
        }

        .testimonial-date {
            color: #a0aec0;
            font-size: 13px;
        }

        .testimonial-moving {
            background: #f7fafc;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .testimonial-comment {
            line-height: 1.7;
            color: #4a5568;
            margin-bottom: 20px;
        }

        .testimonial-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .helpful-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f7fafc;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            color: #718096;
            font-size: 14px;
            transition: 0.3s;
        }

        .helpful-btn:hover {
            background: #e2e8f0;
            color: #667eea;
        }

        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 50px 0;
            flex-wrap: wrap;
        }

        .trust-badge {
            text-align: center;
        }

        .badge-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .badge-text {
            color: #2d3748;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 32px;
            }

            .rating-number {
                font-size: 48px;
            }

            .testimonial-header {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="reviews-page">
        <div class="page-header">
            <h1>⭐ Ce que disent nos clients</h1>
            <p>Des milliers de clients satisfaits nous font confiance pour leur déménagement</p>

            <div class="rating-summary">
                <div class="overall-rating">
                    <div class="rating-number"><?= number_format($stats['average_rating'], 1) ?></div>
                    <div class="rating-stars">★★★★★</div>
                    <div class="rating-count">Basé sur <?= number_format($stats['total_reviews']) ?> avis</div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value"><?= $stats['satisfaction_rate'] ?>%</div>
                <div class="stat-label">Taux de satisfaction</div>
            </div>

            <div class="stat-box">
                <div class="stat-value"><?= $stats['would_recommend'] ?>%</div>
                <div class="stat-label">Recommanderaient</div>
            </div>

            <div class="stat-box">
                <div class="stat-value">24h</div>
                <div class="stat-label">Délai moyen de réponse</div>
            </div>

            <div class="stat-box">
                <div class="stat-value">40%</div>
                <div class="stat-label">Économies moyennes</div>
            </div>
        </div>

        <div class="trust-badges">
            <div class="trust-badge">
                <div class="badge-icon">✅</div>
                <div class="badge-text">100% Gratuit</div>
            </div>
            <div class="trust-badge">
                <div class="badge-icon">🔒</div>
                <div class="badge-text">Sécurisé & Confidentiel</div>
            </div>
            <div class="trust-badge">
                <div class="badge-icon">🏆</div>
                <div class="badge-text">Déménageurs Certifiés</div>
            </div>
            <div class="trust-badge">
                <div class="badge-icon">💰</div>
                <div class="badge-text">Meilleurs Prix</div>
            </div>
        </div>

        <div class="filters-section">
            <span style="color: #718096; font-weight: 600;">Filtrer par :</span>
            <button class="filter-btn active">Tous les avis</button>
            <button class="filter-btn">5 étoiles</button>
            <button class="filter-btn">4 étoiles</button>
            <button class="filter-btn">Vérifié uniquement</button>
            <button class="filter-btn">Plus récents</button>
        </div>

        <div class="testimonials-list">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-card">
                <div class="testimonial-header">
                    <img src="<?= $testimonial['photo'] ?>" alt="<?= e($testimonial['name']) ?>" class="testimonial-photo">
                    <div class="testimonial-info">
                        <div class="testimonial-name">
                            <?= e($testimonial['name']) ?>
                            <?php if ($testimonial['verified']): ?>
                                <span class="testimonial-verified">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.854 6.354-4 4a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 9.293l3.646-3.647a.5.5 0 0 1 .708.708z"/>
                                    </svg>
                                    Vérifié
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="testimonial-location">📍 <?= e($testimonial['location']) ?></div>
                    </div>
                </div>

                <div class="testimonial-rating">
                    <div class="stars">
                        <?= str_repeat('★', $testimonial['rating']) ?>
                        <?= str_repeat('☆', 5 - $testimonial['rating']) ?>
                    </div>
                    <div class="testimonial-date">
                        <?= formatDate($testimonial['date']) ?>
                    </div>
                </div>

                <div class="testimonial-moving">
                    🚚 <?= e($testimonial['moving']) ?>
                </div>

                <div class="testimonial-comment">
                    "<?= e($testimonial['comment']) ?>"
                </div>

                <div class="testimonial-footer">
                    <button class="helpful-btn">
                        👍 Utile (<?= $testimonial['helpful'] ?>)
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 50px;">
            <a href="index.php#formulaire-devis" class="cta-button">
                Obtenez vos devis gratuits
            </a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Filtres interactifs
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Logique de filtrage ici
            });
        });

        // Bouton "Utile"
        document.querySelectorAll('.helpful-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const count = parseInt(this.textContent.match(/\d+/)[0]);
                this.innerHTML = `👍 Utile (${count + 1})`;
                this.style.color = '#667eea';
                this.disabled = true;
            });
        });
    </script>
</body>
</html>
