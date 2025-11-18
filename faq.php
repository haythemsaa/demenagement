<?php
session_start();
require_once 'config/config.php';
require_once 'classes/i18n.php';
require_once 'includes/helpers.php';

$i18n = i18n::getInstance();
$page_title = 'FAQ - Questions Fréquentes';

// FAQ complète par catégorie
$faq_categories = [
    'Généralités' => [
        [
            'q' => 'Comment fonctionne votre service de comparaison ?',
            'a' => 'Notre service est 100% gratuit et sans engagement. Vous remplissez un seul formulaire en 3 minutes, et nous vous mettons en relation avec jusqu\'à 6 déménageurs professionnels certifiés qui vous envoient leurs meilleurs devis sous 24h. Vous comparez les offres et choisissez celle qui vous convient le mieux.'
        ],
        [
            'q' => 'Est-ce vraiment gratuit ?',
            'a' => 'Oui, absolument ! Notre service est entièrement gratuit pour vous. Nous sommes rémunérés par les déménageurs partenaires uniquement si vous choisissez l\'un d\'eux. Vous ne payez rien et n\'avez aucun engagement.'
        ],
        [
            'q' => 'Combien de devis vais-je recevoir ?',
            'a' => 'Vous recevrez entre 3 et 6 devis gratuits de déménageurs professionnels correspondant à vos critères. Le nombre exact dépend de votre localisation et de la disponibilité des partenaires dans votre région.'
        ],
        [
            'q' => 'Sous quel délai vais-je recevoir les devis ?',
            'a' => 'La plupart de nos partenaires répondent sous 1 à 4 heures. Vous recevrez tous vos devis dans les 24 heures suivant votre demande. Certains déménageurs peuvent vous contacter par téléphone pour préciser votre besoin.'
        ],
    ],

    'Prix & Tarifs' => [
        [
            'q' => 'Combien coûte un déménagement en moyenne ?',
            'a' => 'Le coût varie selon plusieurs facteurs : volume (15-100m³), distance (local/national/international), étages, services (emballage, montage). En moyenne : Studio 400-900€, T2 700-1,500€, T3 900-2,000€, T4+ 1,200-3,000€. Utilisez notre estimateur pro pour un prix précis !'
        ],
        [
            'q' => 'Qu\'est-ce qui influence le prix d\'un déménagement ?',
            'a' => '6 facteurs principaux : 1) Volume de vos biens (m³), 2) Distance entre les deux logements, 3) Étages sans ascenseur (+80€/étage), 4) Services additionnels (emballage, montage), 5) Période (haute saison +15%), 6) Accessibilité (monte-meubles, stationnement).'
        ],
        [
            'q' => 'Comment économiser sur mon déménagement ?',
            'a' => '5 astuces pour économiser : 1) Comparez 3-6 devis (économies : 300-500€), 2) Déménagez hors saison (oct-mars, -30%), 3) Faites le tri (vendez/donnez), 4) Emballez vous-même (-200-400€), 5) Optez pour le groupage longue distance (-30-40%).'
        ],
        [
            'q' => 'Y a-t-il des frais cachés ?',
            'a' => 'Avec nos partenaires certifiés, NON. Le devis doit mentionner TOUS les coûts. Vérifiez qu\'il inclut : transport, main d\'œuvre, carburant, assurance, TVA. Méfiez-vous des prix trop bas qui pourraient cacher des suppléments non annoncés.'
        ],
        [
            'q' => 'Quand dois-je payer ?',
            'a' => 'Généralement : acompte de 20-40% à la réservation, solde à la livraison. Ne payez JAMAIS 100% à l\'avance ! Privilégiez le paiement par virement ou carte bancaire (évitez le cash). Vérifiez les conditions d\'annulation avant de signer.'
        ],
    ],

    'Préparation' => [
        [
            'q' => 'Combien de temps à l\'avance dois-je préparer mon déménagement ?',
            'a' => 'Idéalement 4-6 semaines minimum. Timeline recommandée : J-30 : demander devis, J-21 : choisir déménageur + réserver, J-14 : commencer tri et emballage, J-7 : changements d\'adresse, J-1 : check final. Haute saison (juin-août) : prévoir 2-3 mois.'
        ],
        [
            'q' => 'Que dois-je emballer moi-même ?',
            'a' => 'Objets personnels sensibles : documents importants, bijoux, argent, médicaments, ordinateur/disques durs, objets de valeur sentimentale. Les déménageurs professionnels s\'occupent du reste (meubles, électroménager, vaisselle, vêtements).'
        ],
        [
            'q' => 'Combien de cartons me faut-il ?',
            'a' => 'Estimation : Studio (15-25 cartons), T2 (30-50), T3 (50-75), T4 (75-100), T5+ (100-150). Types : standards (livres, vêtements), penderies (vêtements suspendus), renforcés (livres lourds). Prévoyez 10-20% de plus que l\'estimation.'
        ],
        [
            'q' => 'Comment protéger mes objets fragiles ?',
            'a' => 'Utilisez : papier bulle pour verres/vaisselle, couvertures pour meubles/écrans, cartons renforcés + calage (papier journal), film étirable pour tiroirs. Marquez "FRAGILE" en gros. Les objets de valeur (art, antiquités) nécessitent un emballage professionnel.'
        ],
    ],

    'Le Jour J' => [
        [
            'q' => 'Combien de temps dure un déménagement ?',
            'a' => 'Dépend du volume et distance. Local : Studio/T1 (3-5h), T2/T3 (6-8h), T4+ (8-12h). Longue distance : ajoutez temps de trajet. Avec 2-3 déménageurs pros, un T3 standard prend une journée complète.'
        ],
        [
            'q' => 'Dois-je être présent pendant tout le déménagement ?',
            'a' => 'OUI, fortement recommandé. Vous devez : superviser le chargement, vérifier l\'état des biens, signer l\'inventaire contradictoire, être présent au départ ET à l\'arrivée, vérifier le déchargement. Si impossible, mandatez quelqu\'un de confiance.'
        ],
        [
            'q' => 'Que faire si un meuble est endommagé ?',
            'a' => '1) Notez IMMÉDIATEMENT sur l\'état des lieux contradictoire (avant signature), 2) Prenez des photos, 3) Contactez le déménageur dans les 48h par LRAR, 4) L\'assurance couvre selon les conditions (vérifiez votre contrat). L\'inventaire signé fait foi.'
        ],
        [
            'q' => 'Les déménageurs montent les meubles ?',
            'a' => 'Dépend de la formule : Basique (NON, transport uniquement), Standard (OUI, montage/démontage inclus), Premium (OUI + installation). Précisez dans le devis. Meubles complexes (cuisine équipée, dressing) peuvent nécessiter un supplément.'
        ],
    ],

    'Assurance & Sécurité' => [
        [
            'q' => 'Mes biens sont-ils assurés pendant le déménagement ?',
            'a' => 'OUI, les déménageurs pros ont une assurance obligatoire. 3 niveaux : Basique (valeur au poids, ~10€/kg), Standard (valeur déclarée), Premium (tous risques + objets précieux). Déclarez les objets de valeur AVANT le déménagement.'
        ],
        [
            'q' => 'Comment vérifier la fiabilité d\'un déménageur ?',
            'a' => '5 vérifications essentielles : 1) Inscription au registre du commerce, 2) Assurance professionnelle valide, 3) Avis clients (Google, Trustpilot), 4) Devis détaillé écrit, 5) Pas de paiement 100% avant prestation. Tous nos partenaires sont certifiés.'
        ],
        [
            'q' => 'Que couvre l\'assurance de base ?',
            'a' => 'L\'assurance légale minimale couvre selon le POIDS (environ 10€/kg). Exemple : TV 20kg cassée = 200€ max (même si elle vaut 1,500€). Pour une meilleure couverture, souscrivez assurance complémentaire ou premium (valeur réelle).'
        ],
        [
            'q' => 'Puis-je annuler mon déménagement ?',
            'a' => 'OUI, mais conditions variables : Gratuit 48-72h avant selon contrat, 50-100€ de frais si annulation tardive, Acompte généralement non remboursable. Vérifiez les conditions d\'annulation AVANT de signer. Préférez déménageurs avec annulation gratuite 48h.'
        ],
    ],

    'Services Spéciaux' => [
        [
            'q' => 'Faites-vous des déménagements internationaux ?',
            'a' => 'OUI ! Nos partenaires gèrent les déménagements vers l\'Europe et le monde entier. Services inclus : formalités douanières, transport maritime/aérien, emballage export, suivi GPS. Comptez 2,500-10,000€ selon destination et volume. Délai : 2-8 semaines.'
        ],
        [
            'q' => 'Pouvez-vous déménager un piano ?',
            'a' => 'OUI, avec équipes spécialisées. Piano droit : 300-500€, Piano à queue : 500-1,000€. Nécessite : déménageurs formés, sangles spéciales, protection renforcée, monte-charge si étages. L\'assurance spécifique est fortement recommandée.'
        ],
        [
            'q' => 'Proposez-vous du garde-meubles ?',
            'a' => 'OUI, nos partenaires offrent du stockage sécurisé. Tarifs : 100-300€/mois selon volume (box 5-20m³). Espaces climatisés, surveillance 24/7, assurance incluse, accès sur RDV. Idéal si délai entre départ et arrivée.'
        ],
        [
            'q' => 'Déménagez-vous les entreprises ?',
            'a' => 'OUI, déménagement professionnel sur mesure. Services : intervention weekend/nuit, transport matériel informatique, démontage/remontage mobilier bureau, coordinateur projet dédié, nettoyage. Devis personnalisé selon taille entreprise.'
        ],
    ],

    'Démarches Administratives' => [
        [
            'q' => 'Quels changements d\'adresse dois-je faire ?',
            'a' => 'Checklist complète : 1) Poste (réexpédition courrier), 2) CAF/Impôts/CPAM, 3) Banque/assurances, 4) Employeur/école enfants, 5) Abonnements (internet, électricité, eau, gaz), 6) Carte grise (1 mois max), 7) Liste électorale. Faites-le 2-4 semaines avant.'
        ],
        [
            'q' => 'Dois-je résilier mes abonnements ?',
            'a' => 'Internet/Box : préavis 10 jours (déménagement = motif légitime), Électricité/Gaz : demandez transfert OU résiliation + réabonnement, Eau : contactez fournisseur local, Téléphone : transférable partout. Pensez aux abonnements (salle sport, presse, Netflix).'
        ],
        [
            'q' => 'Comment récupérer ma caution ?',
            'a' => '1) Nettoyez à fond l\'ancien logement, 2) Réalisez état des lieux de sortie avec propriétaire, 3) Restituez TOUTES les clés, 4) Envoyez nouvelle adresse par LRAR. Caution = 2 mois max, restitution sous 1-2 mois. Gardez preuves de l\'état impeccable.'
        ],
        [
            'q' => 'Quand prévenir mon propriétaire ?',
            'a' => 'Préavis légal : Location vide (3 mois), Location meublée (1 mois), Zone tendue (1 mois). Envoyez LETTRE RECOMMANDÉE avec AR. Le préavis commence à réception de la lettre. Anticipez pour éviter double loyer !'
        ],
    ],

    'Conseils Pratiques' => [
        [
            'q' => 'Quelle est la meilleure période pour déménager ?',
            'a' => 'BASSE SAISON (octobre-mars) : -20-30% moins cher, disponibilité, calme. HAUTE SAISON (juin-août) : +15% cher, forte demande. À ÉVITER : fins de mois (rush), 1er du mois, vacances scolaires. IDÉAL : mardi-jeudi en milieu de mois hors vacances.'
        ],
        [
            'q' => 'Dois-je vider mes placards/tiroirs ?',
            'a' => 'OUI pour objets lourds/fragiles (livres, vaisselle, bocaux). NON pour vêtements légers dans commode (sauf si étages sans ascenseur). Les déménageurs fixent les tiroirs avec film étirable. Videz toujours : produits liquides, alimentaire périssable, objets valeur.'
        ],
        [
            'q' => 'Comment organiser le déballage ?',
            'a' => 'Numérotez cartons par PIÈCE (Cuisine-1, Cuisine-2). Déballez dans l\'ordre : 1) Chambres (matelas/literie), 2) Cuisine (essentiel repas), 3) Salle de bain (hygiène), 4) Salon, 5) Reste. Gardez un carton "PRIORITÉ" (draps, serviettes, ustensiles base, vêtements lendemain).'
        ],
        [
            'q' => 'Que faire des objets dont je ne veux plus ?',
            'a' => '4 options écologiques : 1) Vente (Leboncoin, Vinted, vide-grenier), 2) Don (Emmaüs, Croix-Rouge, Ressourcerie), 3) Recyclage (déchetterie), 4) Reprise (magasins pour électroménager). Faites-le 2-3 semaines AVANT = moins de volume = moins cher !'
        ],
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
        .faq-page {
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

        .search-box {
            max-width: 600px;
            margin: 30px auto;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 18px 50px 18px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            font-size: 16px;
            transition: 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #718096;
        }

        .quick-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .quick-link {
            padding: 8px 20px;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }

        .quick-link:hover {
            border-color: #667eea;
            background: white;
            color: #667eea;
        }

        .faq-category {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .category-header {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-item {
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 0;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            transition: 0.3s;
        }

        .faq-question:hover {
            color: #667eea;
        }

        .faq-icon {
            font-size: 24px;
            transition: transform 0.3s;
            color: #667eea;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: 10px;
            border-left: 3px solid #667eea;
            margin-top: 15px;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding-top: 15px;
        }

        .faq-answer p {
            color: #4a5568;
            line-height: 1.8;
            margin: 0;
        }

        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            margin-top: 50px;
        }

        .cta-section h2 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 32px;
            }

            .quick-links {
                gap: 10px;
            }

            .faq-question {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="faq-page">
        <div class="page-header">
            <h1>❓ Questions Fréquentes</h1>
            <p>Toutes les réponses à vos questions sur le déménagement</p>

            <div class="search-box">
                <input type="text" id="faq-search" placeholder="Rechercher une question..." onkeyup="searchFAQ()">
                <span class="search-icon">🔍</span>
            </div>

            <div class="quick-links">
                <?php foreach (array_keys($faq_categories) as $cat): ?>
                    <span class="quick-link" onclick="scrollToCategory('<?= sanitize_id($cat) ?>')">
                        <?= $cat ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_map('count', $faq_categories)) ?></div>
                <div class="stat-label">Questions répondues</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($faq_categories) ?></div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">95%</div>
                <div class="stat-label">Clients satisfaits</div>
            </div>
        </div>

        <?php foreach ($faq_categories as $category => $questions): ?>
        <div class="faq-category" id="cat-<?= sanitize_id($category) ?>">
            <h2 class="category-header">
                <?php
                $icons = [
                    'Généralités' => '📋',
                    'Prix & Tarifs' => '💰',
                    'Préparation' => '📦',
                    'Le Jour J' => '🚚',
                    'Assurance & Sécurité' => '🛡️',
                    'Services Spéciaux' => '⭐',
                    'Démarches Administratives' => '📝',
                    'Conseils Pratiques' => '💡',
                ];
                echo ($icons[$category] ?? '❓') . ' ' . $category;
                ?>
            </h2>

            <?php foreach ($questions as $index => $item): ?>
            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <span><?= e($item['q']) ?></span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p><?= nl2br(e($item['a'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <div class="cta-section">
            <h2>Vous n'avez pas trouvé votre réponse ?</h2>
            <p>Notre équipe est là pour vous aider ! Contactez-nous ou demandez vos devis gratuits</p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/contact.php" class="cta-button" style="background: white; color: #667eea;">
                    Nous contacter
                </a>
                <a href="/index.php#formulaire-devis" class="cta-button" style="background: rgba(255,255,255,0.2); border: 2px solid white;">
                    Demander des devis
                </a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleFAQ(element) {
            const wasActive = element.classList.contains('active');

            // Close all
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });

            // Open clicked one
            if (!wasActive) {
                element.classList.add('active');
            }
        }

        function scrollToCategory(catId) {
            const element = document.getElementById('cat-' + catId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function searchFAQ() {
            const searchTerm = document.getElementById('faq-search').value.toLowerCase();
            const items = document.querySelectorAll('.faq-item');

            items.forEach(item => {
                const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();

                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    if (searchTerm.length > 2) {
                        item.classList.add('active');
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide empty categories
            document.querySelectorAll('.faq-category').forEach(cat => {
                const visibleItems = cat.querySelectorAll('.faq-item[style="display: block"]').length;
                cat.style.display = visibleItems > 0 ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
<?php
function sanitize_id($str) {
    return str_replace([' ', '&', '/'], ['_', '_', '_'], strtolower($str));
}
?>
