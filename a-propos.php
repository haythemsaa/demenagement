<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'À Propos';
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .about-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .hero-section {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            color: white;
            margin-bottom: 60px;
        }

        .hero-section h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 20px;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .stat-box {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-size: 56px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #2d3748;
            font-weight: 600;
            font-size: 18px;
        }

        .content-section {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }

        .content-section h2 {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 25px;
        }

        .content-section p {
            color: #4a5568;
            line-height: 1.8;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .value-card {
            text-align: center;
            padding: 30px;
        }

        .value-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .value-card h3 {
            font-size: 22px;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .value-card p {
            color: #718096;
            line-height: 1.6;
        }

        .timeline {
            position: relative;
            padding-left: 50px;
            margin-top: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #667eea;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -40px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #667eea;
            border: 4px solid white;
            box-shadow: 0 0 0 2px #667eea;
        }

        .timeline-year {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .timeline-content {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-member {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .member-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
        }

        .member-info {
            padding: 25px;
            text-align: center;
        }

        .member-name {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .member-role {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .member-bio {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }

        .partners-section {
            background: #f7fafc;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .partners-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .partner-logo {
            width: 120px;
            height: 80px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 32px;
            }

            .content-section {
                padding: 30px 20px;
            }

            .stat-number {
                font-size: 42px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="about-page">
        <div class="hero-section">
            <h1>À Propos de <?= SITE_NAME ?></h1>
            <p>La plateforme n°1 de comparaison de devis de déménagement en France et bientôt dans toute l'Europe</p>
        </div>

        <div class="stats-section">
            <div class="stat-box">
                <div class="stat-number">20+</div>
                <div class="stat-label">Ans d'expérience</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">500+</div>
                <div class="stat-label">Déménageurs partenaires</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">150K+</div>
                <div class="stat-label">Déménagements réalisés</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">4.7/5</div>
                <div class="stat-label">Satisfaction client</div>
            </div>
        </div>

        <div class="content-section">
            <h2>🎯 Notre Mission</h2>
            <p>
                Chez <?= SITE_NAME ?>, notre mission est simple mais ambitieuse : <strong>rendre le déménagement accessible, transparent et sans stress pour tous</strong>.
            </p>
            <p>
                Nous avons constaéque trouver un déménageur de confiance au meilleur prix était un parcours du combattant : manque de transparence tarifaire, difficulté à comparer les offres, crainte de tomber sur un prestataire peu fiable...
            </p>
            <p>
                C'est pourquoi nous avons créé la <strong>plateforme de comparaison la plus complète et innovante d'Europe</strong>, avec des outils révolutionnaires comme notre estimateur professionnel 100+ meubles, notre système de prix en temps réel, et notre espace client avec suivi personnalisé.
            </p>
        </div>

        <div class="content-section">
            <h2>💎 Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">✨</div>
                    <h3>Transparence</h3>
                    <p>Prix clairs, sans frais cachés. Nos outils vous donnent une estimation précise avant même de demander un devis.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Confiance</h3>
                    <p>Tous nos partenaires sont certifiés, assurés et notés par de vrais clients. Nous vérifions chaque déménageur.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🚀</div>
                    <h3>Innovation</h3>
                    <p>Nous investissons dans la technologie pour vous offrir les meilleurs outils du marché européen.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💚</div>
                    <h3>Engagement</h3>
                    <p>Nous encourageons les pratiques éco-responsables et soutenons les déménageurs engagés pour l'environnement.</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>📅 Notre Histoire</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2004</div>
                    <div class="timeline-content">
                        <h4>Création de Déménageur.com</h4>
                        <p>Lancement de la première plateforme de comparaison de devis de déménagement en France avec 50 partenaires.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2010</div>
                    <div class="timeline-content">
                        <h4>100,000 déménagements</h4>
                        <p>Franchissement du cap symbolique des 100,000 déménagements facilités et 200 partenaires actifs.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2015</div>
                    <div class="timeline-content">
                        <h4>Expansion internationale</h4>
                        <p>Ouverture aux déménagements internationaux et partenariats avec des acteurs européens majeurs.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-content">
                        <h4>Digitalisation complète</h4>
                        <p>Lancement de l'espace client en ligne, suivi en temps réel des devis, et première version mobile.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2024</div>
                    <div class="timeline-content">
                        <h4>Révolution technologique</h4>
                        <p>Lancement de l'estimateur professionnel 100+ meubles, prix en temps réel, et système multi-pays pour conquête européenne.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2025</div>
                    <div class="timeline-content">
                        <h4>Leader européen</h4>
                        <p>Objectif : devenir la référence européenne avec 15+ pays couverts et 1 million de déménagements facilités.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>👥 Notre Équipe</h2>
            <p style="margin-bottom: 30px;">
                Une équipe passionnée et expérimentée au service de votre déménagement
            </p>

            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">👨‍💼</div>
                    <div class="member-info">
                        <div class="member-name">Jean-Marc Dupont</div>
                        <div class="member-role">Fondateur & CEO</div>
                        <p class="member-bio">20 ans d'expérience dans le déménagement. Visionnaire de la digitalisation du secteur.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">👩‍💻</div>
                    <div class="member-info">
                        <div class="member-name">Sophie Martin</div>
                        <div class="member-role">CTO - Innovation</div>
                        <p class="member-bio">Experte en IA et Big Data. Créatrice de notre estimateur révolutionnaire.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">👨‍💼</div>
                    <div class="member-info">
                        <div class="member-name">Thomas Lefebvre</div>
                        <div class="member-role">Directeur Commercial</div>
                        <p class="member-bio">15 ans dans le réseau de déménageurs. Garant de la qualité de nos partenaires.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">👩‍💼</div>
                    <div class="member-info">
                        <div class="member-name">Marie Dubois</div>
                        <div class="member-role">Responsable Client</div>
                        <p class="member-bio">Votre interlocutrice privilégiée. Disponible 7j/7 pour vous accompagner.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="partners-section">
            <h2>🤝 Nos Partenaires de Confiance</h2>
            <p style="color: #718096; margin-bottom: 20px;">
                Nous travaillons avec les meilleurs acteurs du secteur
            </p>

            <div class="partners-logos">
                <div class="partner-logo">🚚</div>
                <div class="partner-logo">📦</div>
                <div class="partner-logo">🏢</div>
                <div class="partner-logo">🌍</div>
                <div class="partner-logo">⭐</div>
                <div class="partner-logo">🔒</div>
            </div>
        </div>

        <div style="text-align: center; padding: 60px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; color: white;">
            <h2 style="font-size: 36px; margin-bottom: 20px;">Prêt à déménager avec nous ?</h2>
            <p style="font-size: 18px; opacity: 0.95; margin-bottom: 30px;">
                Rejoignez les 150,000+ personnes qui nous ont fait confiance
            </p>
            <a href="/index.php#formulaire-devis" class="cta-button" style="background: white; color: #667eea;">
                Obtenir mes devis gratuits
            </a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
