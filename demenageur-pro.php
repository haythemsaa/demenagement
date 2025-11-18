<?php
session_start();
require_once 'config/config.php';
require_once 'classes/Database.php';

// Récupérer les plans d'abonnement
$db = Database::getInstance()->getConnection();
$stmt = $db->query("
    SELECT * FROM subscription_plans
    WHERE is_active = TRUE
    ORDER BY display_order ASC
");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Déménageurs Professionnels - Recevez des Leads Qualifiés';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .pro-page {
            margin-top: 80px;
        }

        .hero-pro {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }

        .hero-pro h1 {
            font-size: 56px;
            margin: 0 0 20px 0;
        }

        .hero-pro p {
            font-size: 24px;
            opacity: 0.95;
            max-width: 800px;
            margin: 0 auto 40px;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 50px;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat .number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .hero-stat .label {
            opacity: 0.9;
        }

        .hero-cta {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .btn-hero {
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255,255,255,0.3);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section {
            padding: 80px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 42px;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .section-subtitle {
            text-align: center;
            font-size: 18px;
            color: #718096;
            margin-bottom: 60px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }

        .benefit-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: 0.3s;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .benefit-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .benefit-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .benefit-card p {
            color: #718096;
            line-height: 1.6;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }

        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            position: relative;
            transition: 0.3s;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .pricing-card.featured {
            border: 3px solid #667eea;
            transform: scale(1.05);
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            right: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .plan-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .plan-price {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin: 20px 0;
        }

        .plan-price .currency {
            font-size: 24px;
        }

        .plan-price .period {
            font-size: 16px;
            color: #718096;
            font-weight: normal;
        }

        .plan-description {
            color: #718096;
            margin-bottom: 30px;
            min-height: 60px;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .plan-features li {
            padding: 12px 0;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }

        .plan-features li::before {
            content: '✓ ';
            color: #48bb78;
            font-weight: bold;
            margin-right: 10px;
        }

        .plan-cta {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .plan-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .how-it-works {
            background: #f7fafc;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin: 60px 0;
        }

        .step-card {
            text-align: center;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
            margin: 0 auto 20px;
        }

        .step-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .step-card p {
            color: #718096;
            line-height: 1.6;
        }

        .testimonials {
            background: white;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }

        .testimonial-card {
            background: #f7fafc;
            padding: 30px;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }

        .testimonial-text {
            font-style: italic;
            color: #4a5568;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .author-info .name {
            font-weight: 600;
            color: #2d3748;
        }

        .author-info .company {
            font-size: 14px;
            color: #718096;
        }

        .faq-section {
            background: #f7fafc;
        }

        .faq-list {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .faq-question {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .faq-answer {
            color: #718096;
            line-height: 1.6;
        }

        .final-cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }

        .final-cta h2 {
            font-size: 42px;
            margin-bottom: 20px;
        }

        .final-cta p {
            font-size: 20px;
            opacity: 0.95;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .hero-pro h1 {
                font-size: 36px;
            }

            .hero-pro p {
                font-size: 18px;
            }

            .hero-stats {
                flex-direction: column;
                gap: 30px;
            }

            .pricing-card.featured {
                transform: none;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="pro-page">
        <!-- Hero Section -->
        <div class="hero-pro">
            <h1>🚚 Développez Votre Activité de Déménagement</h1>
            <p>Recevez des leads qualifiés dans votre zone géographique et boostez votre chiffre d'affaires</p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="number">500+</div>
                    <div class="label">Déménageurs partenaires</div>
                </div>
                <div class="hero-stat">
                    <div class="number">12 000+</div>
                    <div class="label">Leads par mois</div>
                </div>
                <div class="hero-stat">
                    <div class="number">4.7/5</div>
                    <div class="label">Satisfaction</div>
                </div>
            </div>

            <div class="hero-cta">
                <a href="/demenageur/inscription.php" class="btn-hero btn-primary">
                    Créer mon compte gratuit
                </a>
                <a href="#tarifs" class="btn-hero btn-secondary">
                    Voir les tarifs
                </a>
            </div>
        </div>

        <!-- Avantages -->
        <div class="section">
            <div class="container">
                <h2 class="section-title">✨ Pourquoi Rejoindre Notre Réseau ?</h2>
                <p class="section-subtitle">
                    Nous sélectionnons pour vous les meilleurs leads en fonction de votre zone, vos capacités et vos spécialités
                </p>

                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="benefit-icon">🎯</div>
                        <h3>Leads Ultra-Qualifiés</h3>
                        <p>Recevez uniquement des leads correspondant à votre zone de couverture et vos services. Notre algorithme intelligent vous envoie les 3-4 meilleures opportunités.</p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">⚡</div>
                        <h3>Notifications Instantanées</h3>
                        <p>Soyez alerté en temps réel par email, SMS et dans votre dashboard. Répondez rapidement et augmentez vos chances de conversion.</p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">📊</div>
                        <h3>Dashboard Complet</h3>
                        <p>Suivez vos leads, vos devis envoyés, votre taux de conversion et vos statistiques. Analysez votre performance en un coup d'œil.</p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">💰</div>
                        <h3>Paiement au Résultat</h3>
                        <p>Pas d'engagement, pas de frais cachés. Vous payez uniquement pour les leads que vous recevez, selon votre abonnement.</p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">🏆</div>
                        <h3>Visibilité Accrue</h3>
                        <p>Profitez de notre trafic de 50 000+ visiteurs/mois. Nous investissons massivement en publicité pour générer des demandes.</p>
                    </div>

                    <div class="benefit-card">
                        <div class="benefit-icon">🤝</div>
                        <h3>Support Dédié</h3>
                        <p>Une équipe à votre écoute 7j/7 pour vous accompagner. Formation gratuite pour optimiser votre taux de conversion.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarifs -->
        <div class="section" id="tarifs" style="background: #f7fafc;">
            <div class="container">
                <h2 class="section-title">💳 Nos Tarifs Transparents</h2>
                <p class="section-subtitle">
                    Choisissez le plan qui correspond à votre activité. Changez ou annulez à tout moment.
                </p>

                <div class="pricing-grid">
                    <?php foreach ($plans as $plan):
                        $features = json_decode($plan['features_list'], true);
                        $is_popular = $plan['slug'] === 'pro';
                    ?>
                        <div class="pricing-card <?= $is_popular ? 'featured' : '' ?>">
                            <?php if ($is_popular): ?>
                                <div class="popular-badge">⭐ Le plus populaire</div>
                            <?php endif; ?>

                            <div class="plan-name"><?= htmlspecialchars($plan['name']) ?></div>

                            <div class="plan-price">
                                <?php if ($plan['price_monthly'] > 0): ?>
                                    <span class="currency">€</span><?= number_format($plan['price_monthly'], 0) ?>
                                    <span class="period">/mois</span>
                                <?php else: ?>
                                    <span>Gratuit</span>
                                <?php endif; ?>
                            </div>

                            <div class="plan-description">
                                <?= htmlspecialchars($plan['description']) ?>
                            </div>

                            <ul class="plan-features">
                                <?php foreach ($features as $feature): ?>
                                    <li><?= htmlspecialchars($feature) ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="/demenageur/inscription.php?plan=<?= $plan['slug'] ?>" class="plan-cta">
                                <?= $plan['slug'] === 'free' ? 'Essayer gratuitement' : 'Choisir ce plan' ?>
                            </a>

                            <?php if ($plan['price_yearly'] > 0): ?>
                                <p style="text-align: center; margin-top: 15px; color: #48bb78; font-size: 14px;">
                                    <strong>Économisez <?= round((1 - ($plan['price_yearly'] / ($plan['price_monthly'] * 12))) * 100) ?>%</strong> avec le paiement annuel
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="text-align: center; margin-top: 40px; color: #718096;">
                    <p>💡 <strong>Besoin d'une solution personnalisée ?</strong></p>
                    <p>Contactez-nous pour un plan Entreprise sur mesure adapté à vos besoins spécifiques.</p>
                    <a href="/contact.php" style="color: #667eea; font-weight: 600;">Nous contacter →</a>
                </div>
            </div>
        </div>

        <!-- Comment ça marche -->
        <div class="section how-it-works">
            <div class="container">
                <h2 class="section-title">🔄 Comment Ça Marche ?</h2>
                <p class="section-subtitle">
                    Un processus simple et automatisé pour maximiser votre efficacité
                </p>

                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3>Inscrivez-vous</h3>
                        <p>Créez votre compte en 3 minutes. Définissez votre zone de couverture, vos services et vos capacités.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3>Recevez des leads</h3>
                        <p>Notre algorithme intelligent sélectionne les leads qui correspondent parfaitement à votre profil et vous les envoie instantanément.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Envoyez vos devis</h3>
                        <p>Consultez les détails complets du déménagement et envoyez votre devis personnalisé directement via notre plateforme.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Remportez le contrat</h3>
                        <p>Le client compare les offres et choisit. Vous êtes notifié instantanément et pouvez finaliser votre vente.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Témoignages -->
        <div class="section testimonials">
            <div class="container">
                <h2 class="section-title">💬 Ils Nous Font Confiance</h2>
                <p class="section-subtitle">
                    Découvrez ce que nos partenaires déménageurs pensent de notre service
                </p>

                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Depuis que nous utilisons cette plateforme, notre chiffre d'affaires a augmenté de 40%. Les leads sont de qualité et correspondent vraiment à notre zone."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">👨‍💼</div>
                            <div class="author-info">
                                <div class="name">Jean-Marc Dupont</div>
                                <div class="company">DéménaPro Paris</div>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Le système de notification en temps réel est parfait. Je réponds en moins de 30 minutes et mon taux de conversion est excellent."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">👩‍💼</div>
                            <div class="author-info">
                                <div class="name">Sophie Martin</div>
                                <div class="company">TransDem Lyon</div>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Le rapport qualité/prix est imbattable. Le plan Pro est rapidement rentabilisé dès le premier contrat remporté chaque mois."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">👨‍💼</div>
                            <div class="author-info">
                                <div class="name">Pierre Lefebvre</div>
                                <div class="company">Déménagement Sud</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="section faq-section">
            <div class="container">
                <h2 class="section-title">❓ Questions Fréquentes</h2>

                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">Combien de déménageurs reçoivent chaque lead ?</div>
                        <div class="faq-answer">
                            Nous envoyons chaque lead à 3-4 déménageurs maximum, sélectionnés selon leur pertinence géographique et leurs services. Vous n'êtes jamais en compétition avec des dizaines d'entreprises.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Comment fonctionne le matching intelligent ?</div>
                        <div class="faq-answer">
                            Notre algorithme analyse plus de 10 critères : zone géographique, capacité, spécialités, réputation, taux de réponse, niveau d'abonnement, etc. Chaque déménageur reçoit un score de pertinence pour chaque lead.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Puis-je essayer gratuitement ?</div>
                        <div class="faq-answer">
                            Oui ! Le plan Gratuit vous permet de tester la plateforme pendant 30 jours avec 5 leads offerts. Aucune carte bancaire requise.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Que se passe-t-il si je dépasse mon quota de leads ?</div>
                        <div class="faq-answer">
                            Vous pouvez acheter des leads supplémentaires au tarif unitaire de votre plan (8€ à 15€ selon le plan), ou upgrader votre abonnement à tout moment.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Y a-t-il un engagement de durée ?</div>
                        <div class="faq-answer">
                            Non, aucun engagement. Vous pouvez annuler votre abonnement à tout moment depuis votre dashboard.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Comment puis-je maximiser mes chances de remporter les leads ?</div>
                        <div class="faq-answer">
                            Répondez rapidement (< 2h), envoyez un devis détaillé et personnalisé, mettez en avant vos atouts (assurances, certifications, avis clients), et proposez plusieurs options tarifaires.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Final -->
        <div class="final-cta">
            <h2>🚀 Prêt à Développer Votre Activité ?</h2>
            <p>Rejoignez les 500+ déménageurs qui font confiance à notre plateforme</p>
            <div class="hero-cta">
                <a href="/demenageur/inscription.php" class="btn-hero btn-primary">
                    Créer mon compte gratuitement
                </a>
                <a href="/contact.php" class="btn-hero btn-secondary">
                    Poser une question
                </a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
