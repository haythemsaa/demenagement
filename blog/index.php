<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/i18n.php';
require_once __DIR__ . '/../includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Blog & Guides Déménagement';

// Articles (en production, ces données viendraient de la base de données)
$articles = [
    [
        'id' => 1,
        'title' => 'Le Guide Complet du Déménagement Réussi',
        'slug' => 'guide-complet-demenagement',
        'excerpt' => 'Découvrez toutes les étapes essentielles pour organiser un déménagement sans stress, du tri à l\'installation dans votre nouveau logement.',
        'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=400&fit=crop',
        'category' => 'Guides',
        'date' => '2024-01-15',
        'reading_time' => '8 min',
        'author' => 'Marie Dubois',
        'views' => 1542
    ],
    [
        'id' => 2,
        'title' => 'Comment Économiser sur Votre Déménagement',
        'slug' => 'economiser-sur-demenagement',
        'excerpt' => '10 astuces concrètes pour réduire le coût de votre déménagement sans sacrifier la qualité du service.',
        'image' => 'https://images.unsplash.com/photo-1554224311-beee415c201f?w=800&h=400&fit=crop',
        'category' => 'Conseils',
        'date' => '2024-01-12',
        'reading_time' => '6 min',
        'author' => 'Thomas Martin',
        'views' => 2156
    ],
    [
        'id' => 3,
        'title' => 'Déménagement International : Ce qu\'il Faut Savoir',
        'slug' => 'demenagement-international',
        'excerpt' => 'Formalités, douanes, transport : tout ce que vous devez savoir pour réussir votre déménagement à l\'étranger.',
        'image' => 'https://images.unsplash.com/photo-1569163139394-de4798aa62b0?w=800&h=400&fit=crop',
        'category' => 'International',
        'date' => '2024-01-10',
        'reading_time' => '10 min',
        'author' => 'Sophie Laurent',
        'views' => 987
    ],
    [
        'id' => 4,
        'title' => 'Checklist Complète : Ne Rien Oublier',
        'slug' => 'checklist-demenagement',
        'excerpt' => 'Une checklist détaillée mois par mois pour ne rien oublier lors de votre déménagement.',
        'image' => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&h=400&fit=crop',
        'category' => 'Organisation',
        'date' => '2024-01-08',
        'reading_time' => '5 min',
        'author' => 'Marie Dubois',
        'views' => 3421
    ],
    [
        'id' => 5,
        'title' => 'Emballer ses Objets Fragiles : Les Bonnes Techniques',
        'slug' => 'emballer-objets-fragiles',
        'excerpt' => 'Astuces et techniques professionnelles pour protéger vos objets les plus précieux pendant le transport.',
        'image' => 'https://images.unsplash.com/photo-1600518464441-9154a4dea21b?w=800&h=400&fit=crop',
        'category' => 'Emballage',
        'date' => '2024-01-05',
        'reading_time' => '7 min',
        'author' => 'Alexandre Petit',
        'views' => 1876
    ],
    [
        'id' => 6,
        'title' => 'Déménager avec des Enfants : Guide Pratique',
        'slug' => 'demenager-avec-enfants',
        'excerpt' => 'Comment préparer vos enfants au déménagement et les aider à s\'adapter à leur nouvel environnement.',
        'image' => 'https://images.unsplash.com/photo-1560185127-6d79e96fa46e?w=800&h=400&fit=crop',
        'category' => 'Famille',
        'date' => '2024-01-02',
        'reading_time' => '6 min',
        'author' => 'Caroline Bernard',
        'views' => 1234
    ]
];

$categories = ['Tous', 'Guides', 'Conseils', 'Organisation', 'International', 'Emballage', 'Famille'];
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .blog-page {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .blog-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .blog-header h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .blog-header p {
            font-size: 18px;
            color: #718096;
            max-width: 700px;
            margin: 0 auto;
        }

        .categories-filter {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .category-btn {
            padding: 10px 25px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            color: #2d3748;
        }

        .category-btn:hover,
        .category-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .article-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
            cursor: pointer;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .article-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .article-content {
            padding: 25px;
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .article-category {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
        }

        .article-reading-time {
            color: #718096;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .article-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .article-excerpt {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .article-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #a0aec0;
        }

        .article-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .article-stats {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .featured-article {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .featured-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .featured-content {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .featured-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
            width: fit-content;
        }

        .featured-content h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .featured-content p {
            font-size: 16px;
            line-height: 1.7;
            opacity: 0.95;
            margin-bottom: 30px;
        }

        .newsletter-section {
            background: linear-gradient(135deg, #f7fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            margin-top: 50px;
        }

        .newsletter-section h2 {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .newsletter-section p {
            color: #718096;
            margin-bottom: 30px;
        }

        .newsletter-form {
            display: flex;
            gap: 15px;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-form input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #cbd5e0;
            border-radius: 10px;
            font-size: 16px;
        }

        .newsletter-form button {
            padding: 15px 35px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .newsletter-form button:hover {
            background: #5568d3;
        }

        @media (max-width: 968px) {
            .blog-header h1 {
                font-size: 32px;
            }

            .articles-grid {
                grid-template-columns: 1fr;
            }

            .featured-article {
                grid-template-columns: 1fr;
            }

            .featured-content {
                padding: 30px;
            }

            .featured-content h2 {
                font-size: 24px;
            }

            .newsletter-form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="blog-page">
        <div class="blog-header">
            <h1>📚 Blog & Guides Déménagement</h1>
            <p>Conseils d'experts, astuces pratiques et guides complets pour réussir votre déménagement</p>
        </div>

        <div class="categories-filter">
            <?php foreach ($categories as $cat): ?>
                <button class="category-btn <?= $cat === 'Tous' ? 'active' : '' ?>">
                    <?= $cat ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="articles-grid">
            <!-- Article mis en avant -->
            <div class="featured-article">
                <img src="<?= $articles[0]['image'] ?>" alt="<?= e($articles[0]['title']) ?>" class="featured-image">
                <div class="featured-content">
                    <span class="featured-badge">⭐ ARTICLE VEDETTE</span>
                    <h2><?= e($articles[0]['title']) ?></h2>
                    <p><?= e($articles[0]['excerpt']) ?></p>
                    <a href="/blog/article.php?slug=<?= $articles[0]['slug'] ?>" class="cta-button" style="width: fit-content; background: white; color: #667eea;">
                        Lire l'article →
                    </a>
                </div>
            </div>

            <!-- Autres articles -->
            <?php foreach (array_slice($articles, 1) as $article): ?>
            <div class="article-card" onclick="location.href='/blog/article.php?slug=<?= $article['slug'] ?>'">
                <img src="<?= $article['image'] ?>" alt="<?= e($article['title']) ?>" class="article-image">
                <div class="article-content">
                    <div class="article-meta">
                        <span class="article-category"><?= $article['category'] ?></span>
                        <span class="article-reading-time">
                            ⏱️ <?= $article['reading_time'] ?>
                        </span>
                    </div>
                    <h3 class="article-title"><?= e($article['title']) ?></h3>
                    <p class="article-excerpt"><?= e($article['excerpt']) ?></p>
                    <div class="article-footer">
                        <div class="article-author">
                            👤 <?= e($article['author']) ?>
                        </div>
                        <div class="article-stats">
                            👁️ <?= number_format($article['views']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="newsletter-section">
            <h2>📧 Restez Informé</h2>
            <p>Recevez nos meilleurs conseils et astuces déménagement directement dans votre boîte mail</p>
            <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Merci pour votre inscription !');">
                <input type="email" placeholder="Votre adresse email" required>
                <button type="submit">S'abonner</button>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
        // Filtrage par catégorie
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Logique de filtrage ici
            });
        });
    </script>
</body>
</html>
