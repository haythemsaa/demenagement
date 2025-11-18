<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'Services Spécialisés';
?>
<!DOCTYPE html>
<html lang="<?= $i18n->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .services-page {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .page-intro {
            text-align: center;
            margin-bottom: 60px;
        }

        .page-intro h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .page-intro p {
            font-size: 18px;
            color: #718096;
            max-width: 800px;
            margin: 0 auto;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .service-header {
            padding: 40px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .service-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .service-header h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .service-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .service-body {
            padding: 30px;
        }

        .service-description {
            color: #718096;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .service-features {
            list-style: none;
            padding: 0;
            margin: 0 0 25px 0;
        }

        .service-features li {
            padding: 12px 0;
            display: flex;
            align-items: start;
            gap: 12px;
            color: #2d3748;
        }

        .feature-check {
            color: #48bb78;
            font-weight: 700;
            font-size: 18px;
        }

        .service-price {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }

        .price-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 8px;
        }

        .price-value {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
        }

        .service-cta {
            width: 100%;
            padding: 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .service-cta:hover {
            background: #5568d3;
        }

        .specialty-section {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 60px;
        }

        .specialty-section h2 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            color: #2d3748;
        }

        .specialty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .specialty-item {
            background: #f7fafc;
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid #667eea;
            transition: 0.3s;
        }

        .specialty-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .specialty-item h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .specialty-item p {
            margin: 0;
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }

        .guarantees {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 20px;
            padding: 50px;
            margin-bottom: 60px;
        }

        .guarantees h2 {
            text-align: center;
            color: #92400e;
            margin-bottom: 40px;
        }

        .guarantee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .guarantee-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }

        .guarantee-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .guarantee-item h4 {
            margin: 0 0 10px 0;
            color: #92400e;
        }

        .guarantee-item p {
            margin: 0;
            color: #78350f;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .page-intro h1 {
                font-size: 32px;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .specialty-section,
            .guarantees {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="services-page">
        <div class="page-intro">
            <h1>🎯 Services Spécialisés</h1>
            <p>Des solutions expertes pour tous vos besoins de déménagement, même les plus complexes. Nos partenaires sont formés et équipés pour gérer vos biens les plus précieux.</p>
        </div>

        <div class="services-grid">
            <!-- Déménagement Piano -->
            <div class="service-card">
                <div class="service-header">
                    <div class="service-icon">🎹</div>
                    <h3>Déménagement de Piano</h3>
                    <p>Transport sécurisé d'instruments de musique</p>
                </div>
                <div class="service-body">
                    <div class="service-description">
                        Le déménagement d'un piano nécessite un savoir-faire spécifique et un équipement adapté. Nos déménageurs spécialisés garantissent un transport en toute sécurité.
                    </div>
                    <ul class="service-features">
                        <li>
                            <span class="feature-check">✓</span>
                            Déménageurs formés au transport de pianos
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Équipement de manutention spécialisé
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Protection maximale avec housses renforcées
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Assurance tous risques incluse
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Transport vertical par fenêtre si nécessaire
                        </li>
                    </ul>
                    <div class="service-price">
                        <div class="price-label">À partir de</div>
                        <div class="price-value">300 €</div>
                    </div>
                    <button class="service-cta" onclick="location.href='index.php#formulaire-devis'">
                        Demander un devis
                    </button>
                </div>
            </div>

            <!-- Coffre-fort -->
            <div class="service-card">
                <div class="service-header" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                    <div class="service-icon">🔒</div>
                    <h3>Transport de Coffre-Fort</h3>
                    <p>Manutention sécurisée d'objets lourds</p>
                </div>
                <div class="service-body">
                    <div class="service-description">
                        Déplacer un coffre-fort requiert force, technique et précision. Nos équipes spécialisées utilisent du matériel professionnel pour un transport en toute sécurité.
                    </div>
                    <ul class="service-features">
                        <li>
                            <span class="feature-check">✓</span>
                            Équipe renforcée (3-4 personnes)
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Matériel de levage professionnel
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Protection des sols et murs
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Expertise jusqu'à 500 kg
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Confidentialité garantie
                        </li>
                    </ul>
                    <div class="service-price">
                        <div class="price-label">À partir de</div>
                        <div class="price-value">400 €</div>
                    </div>
                    <button class="service-cta" onclick="location.href='index.php#formulaire-devis'">
                        Demander un devis
                    </button>
                </div>
            </div>

            <!-- Garde-meubles -->
            <div class="service-card">
                <div class="service-header" style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);">
                    <div class="service-icon">📦</div>
                    <h3>Garde-Meubles</h3>
                    <p>Stockage sécurisé courte/longue durée</p>
                </div>
                <div class="service-body">
                    <div class="service-description">
                        Besoin de stocker vos meubles entre deux logements ? Nos espaces de stockage sécurisés vous offrent une solution flexible et économique.
                    </div>
                    <ul class="service-features">
                        <li>
                            <span class="feature-check">✓</span>
                            Entrepôts sécurisés 24/7
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Surveillance vidéo permanente
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Espaces climatisés
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Accès flexible sur rendez-vous
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Assurance multirisque incluse
                        </li>
                    </ul>
                    <div class="service-price">
                        <div class="price-label">À partir de</div>
                        <div class="price-value">150 € /mois</div>
                    </div>
                    <button class="service-cta" onclick="location.href='index.php#formulaire-devis'">
                        Demander un devis
                    </button>
                </div>
            </div>

            <!-- Déménagement International -->
            <div class="service-card">
                <div class="service-header" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                    <div class="service-icon">✈️</div>
                    <h3>Déménagement International</h3>
                    <p>Expertisen déménagement à l'étranger</p>
                </div>
                <div class="service-body">
                    <div class="service-description">
                        Un déménagement à l'étranger demande une organisation rigoureuse. Nous gérons toute la logistique pour vous, des formalités douanières au transport.
                    </div>
                    <ul class="service-features">
                        <li>
                            <span class="feature-check">✓</span>
                            Gestion des formalités douanières
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Transport maritime ou aérien
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Emballage export professionnel
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Suivi en temps réel de votre envoi
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Assistance multilingue
                        </li>
                    </ul>
                    <div class="service-price">
                        <div class="price-label">À partir de</div>
                        <div class="price-value">2 500 €</div>
                    </div>
                    <button class="service-cta" onclick="location.href='index.php#formulaire-devis'">
                        Demander un devis
                    </button>
                </div>
            </div>

            <!-- Déménagement Entreprise -->
            <div class="service-card">
                <div class="service-header" style="background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);">
                    <div class="service-icon">🏢</div>
                    <h3>Déménagement Entreprise</h3>
                    <p>Solutions professionnelles sur mesure</p>
                </div>
                <div class="service-body">
                    <div class="service-description">
                        Minimisez l'interruption de votre activité avec nos solutions de déménagement d'entreprise clés en main, planifiées selon vos contraintes.
                    </div>
                    <ul class="service-features">
                        <li>
                            <span class="feature-check">✓</span>
                            Intervention weekend/hors horaires
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Transport de matériel informatique
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Démontage/remontage mobilier bureau
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Coordinateur projet dédié
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Service de nettoyage inclus
                        </li>
                    </ul>
                    <div class="service-price">
                        <div class="price-label">Sur devis</div>
                        <div class="price-value">Contactez-nous</div>
                    </div>
                    <button class="service-cta" onclick="location.href='index.php#formulaire-devis'">
                        Demander un devis
                    </button>
                </div>
            </div>

            <!-- Objets d'art -->
            <div class="service-card">
                <div class="service-header" style="background: linear-gradient(135deg, #ed64a6 0%, #d53f8c 100%);">
                    <div class="service-icon">🎨</div>
                    <h3>Œuvres d'Art & Antiquités</h3>
                    <p>Protection maximale pour objets précieux</p>
                </div>
                <div class="service-body">
                    <div class="service-description">
                        Vos œuvres d'art méritent une attention particulière. Nos spécialistes utilisent des techniques museum pour garantir leur intégrité.
                    </div>
                    <ul class="service-features">
                        <li>
                            <span class="feature-check">✓</span>
                            Emballage caisse sur mesure
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Matériaux conservation musée
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Véhicules suspensions renforcées
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Assurance valeur déclarée
                        </li>
                        <li>
                            <span class="feature-check">✓</span>
                            Expertise et constat d'état
                        </li>
                    </ul>
                    <div class="service-price">
                        <div class="price-label">Sur devis</div>
                        <div class="price-value">Contactez-nous</div>
                    </div>
                    <button class="service-cta" onclick="location.href='index.php#formulaire-devis'">
                        Demander un devis
                    </button>
                </div>
            </div>
        </div>

        <div class="specialty-section">
            <h2>🛠️ Autres Services Complémentaires</h2>
            <div class="specialty-grid">
                <div class="specialty-item">
                    <h4>🧰 Montage/Démontage</h4>
                    <p>Démontage et remontage professionnel de tous vos meubles, cuisine équipée, literie, etc.</p>
                </div>
                <div class="specialty-item">
                    <h4>📦 Fourniture Cartons</h4>
                    <p>Livraison de cartons de déménagement de qualité avec papier bulle et scotch professionnel.</p>
                </div>
                <div class="specialty-item">
                    <h4>🧹 Nettoyage</h4>
                    <p>Service de nettoyage complet de votre ancien logement pour récupérer votre caution.</p>
                </div>
                <div class="specialty-item">
                    <h4>🚚 Monte-Charge</h4>
                    <p>Location de monte-meubles pour les déménagements en étage sans ascenseur.</p>
                </div>
                <div class="specialty-item">
                    <h4>📝 Aide Administrative</h4>
                    <p>Assistance pour vos démarches de changement d'adresse auprès des administrations.</p>
                </div>
                <div class="specialty-item">
                    <h4>🐕 Animaux</h4>
                    <p>Transport sécurisé de vos animaux de compagnie vers votre nouveau domicile.</p>
                </div>
            </div>
        </div>

        <div class="guarantees">
            <h2>🛡️ Nos Garanties</h2>
            <div class="guarantee-grid">
                <div class="guarantee-item">
                    <div class="guarantee-icon">✅</div>
                    <h4>Professionnels Certifiés</h4>
                    <p>Tous nos partenaires sont certifiés et assurés professionnellement</p>
                </div>
                <div class="guarantee-item">
                    <div class="guarantee-icon">🔒</div>
                    <h4>Assurance Complète</h4>
                    <p>Couverture tous risques pour vos biens pendant tout le transport</p>
                </div>
                <div class="guarantee-item">
                    <div class="guarantee-icon">💰</div>
                    <h4>Prix Transparent</h4>
                    <p>Aucun frais caché, le prix du devis est le prix final</p>
                </div>
                <div class="guarantee-item">
                    <div class="guarantee-icon">⭐</div>
                    <h4>Satisfaction Garantie</h4>
                    <p>Plus de 96% de clients satisfaits recommandent nos services</p>
                </div>
            </div>
        </div>

        <div style="text-align: center; padding: 50px 0;">
            <h2 style="margin-bottom: 20px;">Prêt à démarrer votre projet ?</h2>
            <p style="color: #718096; margin-bottom: 30px;">Obtenez jusqu'à 6 devis gratuits de déménageurs spécialisés</p>
            <a href="index.php#formulaire-devis" class="cta-button">
                Obtenir mes devis gratuits
            </a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
