<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déménageur - Comparez jusqu'à 6 devis de déménageurs | Économisez jusqu'à 40%</title>
    <meta name="description" content="Comparez gratuitement jusqu'à 6 devis de déménageurs professionnels. Service gratuit, rapide et sans engagement. Réponse en 1h.">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>DÉMÉNAGEUR.COM</h1>
                    <p class="tagline">Plus de 20 ans d'expérience</p>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="#devis">Devis gratuit</a></li>
                        <li><a href="#comment-ca-marche">Comment ça marche</a></li>
                        <li><a href="#prix">Prix</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
                <div class="header-phone">
                    <span class="phone-icon">📞</span>
                    <div>
                        <p class="phone-label">Service client 7j/7</p>
                        <a href="tel:0978450218" class="phone-number">09 78 45 02 18</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Comparez jusqu'à 6 devis de déménageurs</h2>
                <p class="hero-subtitle">Économisez jusqu'à 40% sur votre déménagement</p>
                <ul class="hero-benefits">
                    <li>✓ Gratuit et sans engagement</li>
                    <li>✓ Réponse en 1 heure</li>
                    <li>✓ Déménageurs professionnels certifiés</li>
                    <li>✓ Partout en France et à l'international</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Formulaire de devis -->
    <section id="devis" class="quote-form-section">
        <div class="container">
            <div class="quote-form-wrapper">
                <h3>Obtenez vos devis gratuits en 3 étapes</h3>
                <form id="quoteForm" class="quote-form">
                    <!-- Étape 1: Adresses -->
                    <div class="form-step active" id="step1">
                        <h4>Étape 1/3 : Vos adresses</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="depart-address">Adresse de départ *</label>
                                <input type="text" id="depart-address" name="depart-address" placeholder="Ex: 123 Rue de la Paix" required>
                            </div>
                            <div class="form-group">
                                <label for="depart-postal">Code postal *</label>
                                <input type="text" id="depart-postal" name="depart-postal" placeholder="Ex: 75001" pattern="[0-9]{5}" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="arrivee-address">Adresse d'arrivée *</label>
                                <input type="text" id="arrivee-address" name="arrivee-address" placeholder="Ex: 456 Avenue des Champs" required>
                            </div>
                            <div class="form-group">
                                <label for="arrivee-postal">Code postal *</label>
                                <input type="text" id="arrivee-postal" name="arrivee-postal" placeholder="Ex: 69001" pattern="[0-9]{5}" required>
                            </div>
                        </div>
                        <button type="button" class="btn-next" onclick="nextStep(2)">Suivant</button>
                    </div>

                    <!-- Étape 2: Détails du logement -->
                    <div class="form-step" id="step2">
                        <h4>Étape 2/3 : Détails de votre logement</h4>
                        <div class="form-group">
                            <label>Type de logement de départ *</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="type-depart" value="appartement" required>
                                    <span>Appartement</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="type-depart" value="maison">
                                    <span>Maison</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="superficie">Superficie (m²) *</label>
                                <input type="number" id="superficie" name="superficie" min="10" max="500" required>
                            </div>
                            <div class="form-group">
                                <label for="pieces">Nombre de pièces *</label>
                                <select id="pieces" name="pieces" required>
                                    <option value="">Sélectionner</option>
                                    <option value="1">Studio</option>
                                    <option value="2">2 pièces</option>
                                    <option value="3">3 pièces</option>
                                    <option value="4">4 pièces</option>
                                    <option value="5">5 pièces et +</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="etage-depart">Étage de départ</label>
                                <input type="number" id="etage-depart" name="etage-depart" min="0" max="50" value="0">
                            </div>
                            <div class="form-group">
                                <label for="ascenseur-depart">Ascenseur disponible ?</label>
                                <select id="ascenseur-depart" name="ascenseur-depart">
                                    <option value="non">Non</option>
                                    <option value="oui">Oui</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="etage-arrivee">Étage d'arrivée</label>
                                <input type="number" id="etage-arrivee" name="etage-arrivee" min="0" max="50" value="0">
                            </div>
                            <div class="form-group">
                                <label for="ascenseur-arrivee">Ascenseur disponible ?</label>
                                <select id="ascenseur-arrivee" name="ascenseur-arrivee">
                                    <option value="non">Non</option>
                                    <option value="oui">Oui</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Monte-charge nécessaire ?</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="monte-charge" value="non" checked>
                                    <span>Non</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="monte-charge" value="oui">
                                    <span>Oui</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button type="button" class="btn-prev" onclick="prevStep(1)">Précédent</button>
                            <button type="button" class="btn-next" onclick="nextStep(3)">Suivant</button>
                        </div>
                    </div>

                    <!-- Étape 3: Contact et date -->
                    <div class="form-step" id="step3">
                        <h4>Étape 3/3 : Vos coordonnées</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" placeholder="0612345678" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="date-demenagement">Date souhaitée du déménagement</label>
                            <input type="date" id="date-demenagement" name="date-demenagement" min="">
                        </div>
                        <div class="form-group">
                            <label for="commentaires">Informations complémentaires</label>
                            <textarea id="commentaires" name="commentaires" rows="4" placeholder="Objets fragiles, parking difficile, etc."></textarea>
                        </div>
                        <div class="form-buttons">
                            <button type="button" class="btn-prev" onclick="prevStep(2)">Précédent</button>
                            <button type="submit" class="btn-submit">Recevoir mes devis gratuits</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Simulateur de volume et d'économies -->
    <section id="simulateur" class="simulator-section">
        <div class="container">
            <h3>Simulez votre déménagement et vos économies</h3>
            <p class="section-subtitle">Estimez le coût de votre déménagement en quelques clics</p>
            <div class="simulator">
                <div class="simulator-inputs">
                    <div class="form-group">
                        <label for="sim-superficie">Superficie (m²)</label>
                        <input type="range" id="sim-superficie" min="20" max="300" value="70" step="10">
                        <span class="range-value" id="superficie-value">70 m²</span>
                    </div>
                    <div class="form-group">
                        <label for="sim-distance">Distance (km)</label>
                        <input type="range" id="sim-distance" min="10" max="1000" value="100" step="10">
                        <span class="range-value" id="distance-value">100 km</span>
                    </div>
                    <div class="form-group">
                        <label>Type de logement</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="sim-type" value="appartement" checked>
                                <span>Appartement</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="sim-type" value="maison">
                                <span>Maison</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Formule</label>
                        <select id="sim-formule">
                            <option value="eco">Éco (location camion)</option>
                            <option value="standard" selected>Standard (aide au chargement)</option>
                            <option value="premium">Clé en main (tout inclus)</option>
                        </select>
                    </div>
                </div>
                <div class="simulator-results">
                    <div class="result-box">
                        <h4>Estimation du coût</h4>
                        <div class="price-estimate">
                            <span class="price-range" id="price-range">800€ - 1 200€</span>
                            <p class="price-note">Prix indicatif hors options</p>
                        </div>
                    </div>
                    <div class="result-box savings">
                        <h4>Vos économies potentielles</h4>
                        <div class="savings-estimate">
                            <span class="savings-amount" id="savings-amount">jusqu'à 480€</span>
                            <p class="savings-note">En comparant 6 devis</p>
                        </div>
                    </div>
                    <div class="result-box volume">
                        <h4>Volume estimé</h4>
                        <div class="volume-estimate">
                            <span class="volume-amount" id="volume-amount">35 m³</span>
                            <p class="volume-note">Soit environ 1 camion</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comment ça marche -->
    <section id="comment-ca-marche" class="how-it-works">
        <div class="container">
            <h3>Comment ça marche ?</h3>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">📝</div>
                    <h4>Remplissez le formulaire</h4>
                    <p>Décrivez votre déménagement en 3 minutes : adresses, volume, date souhaitée</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">📨</div>
                    <h4>Recevez jusqu'à 6 devis</h4>
                    <p>Des déménageurs certifiés vous contactent sous 1h avec leurs meilleures offres</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">💰</div>
                    <h4>Comparez et économisez</h4>
                    <p>Choisissez l'offre qui vous convient et économisez jusqu'à 40%</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">🚚</div>
                    <h4>Déménagez sereinement</h4>
                    <p>Profitez d'un déménagement professionnel au meilleur prix</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Facteurs de prix -->
    <section id="prix" class="pricing-factors">
        <div class="container">
            <h3>Qu'est-ce qui influence le prix de votre déménagement ?</h3>
            <div class="factors-grid">
                <div class="factor-card">
                    <div class="factor-icon">📦</div>
                    <h4>Formule choisie</h4>
                    <ul>
                        <li><strong>Éco :</strong> Location de camion avec chauffeur (300-600€)</li>
                        <li><strong>Standard :</strong> Aide au chargement/déchargement (600-1500€)</li>
                        <li><strong>Clé en main :</strong> Emballage, transport, déballage (1500-4000€)</li>
                    </ul>
                </div>
                <div class="factor-card">
                    <div class="factor-icon">🏢</div>
                    <h4>Accessibilité</h4>
                    <ul>
                        <li>Étage sans ascenseur : +10-20% par étage</li>
                        <li>Monte-charge nécessaire : +150-300€</li>
                        <li>Parking difficile : +100-200€</li>
                        <li>Accès restreint : sur devis</li>
                    </ul>
                </div>
                <div class="factor-card">
                    <div class="factor-icon">🗺️</div>
                    <h4>Distance</h4>
                    <ul>
                        <li>Local (&lt;50km) : 400-1000€</li>
                        <li>Régional (50-200km) : 800-2000€</li>
                        <li>National (&gt;200km) : 1500-4000€</li>
                        <li>International : sur devis</li>
                    </ul>
                </div>
                <div class="factor-card">
                    <div class="factor-icon">📅</div>
                    <h4>Dates</h4>
                    <ul>
                        <li>Haute saison (juin-sept) : +20-30%</li>
                        <li>Fin de mois : +15-25%</li>
                        <li>Week-end : +10-15%</li>
                        <li>Semaine creuse : meilleurs tarifs</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Services additionnels -->
    <section id="services" class="additional-services">
        <div class="container">
            <h3>Nos services additionnels</h3>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🏠</div>
                    <h4>Estimation immobilière</h4>
                    <p>Estimez gratuitement la valeur de votre bien avant de déménager</p>
                    <a href="#" class="service-link">En savoir plus →</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">🏢</div>
                    <h4>Agences immobilières</h4>
                    <p>Trouvez les meilleures agences pour vendre ou louer votre logement</p>
                    <a href="#" class="service-link">Rechercher →</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">🎹</div>
                    <h4>Transport spécialisé</h4>
                    <p>Piano, œuvres d'art, objets fragiles : transport avec précaution</p>
                    <a href="#" class="service-link">Devis spécialisé →</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">🌍</div>
                    <h4>Déménagement international</h4>
                    <p>Partout en Europe et dans le monde avec des partenaires certifiés</p>
                    <a href="#" class="service-link">Devis international →</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">🏪</div>
                    <h4>Déménagement d'entreprise</h4>
                    <p>Solutions professionnelles pour bureaux, commerces et industries</p>
                    <a href="#" class="service-link">Devis entreprise →</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">📦</div>
                    <h4>Garde-meubles</h4>
                    <p>Stockage sécurisé de vos biens pendant votre transition</p>
                    <a href="#" class="service-link">Trouver un garde-meubles →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Couverture géographique -->
    <section id="couverture" class="geographic-coverage">
        <div class="container">
            <h3>Déménageurs partout en France</h3>
            <p class="section-subtitle">Des professionnels disponibles dans les 95 départements + DOM-TOM</p>
            <div class="regions-grid">
                <div class="region-card">
                    <h4>Île-de-France</h4>
                    <ul>
                        <li><a href="#">Paris (75)</a></li>
                        <li><a href="#">Hauts-de-Seine (92)</a></li>
                        <li><a href="#">Seine-Saint-Denis (93)</a></li>
                        <li><a href="#">Val-de-Marne (94)</a></li>
                        <li><a href="#">Seine-et-Marne (77)</a></li>
                        <li><a href="#">Yvelines (78)</a></li>
                        <li><a href="#">Essonne (91)</a></li>
                        <li><a href="#">Val-d'Oise (95)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Auvergne-Rhône-Alpes</h4>
                    <ul>
                        <li><a href="#">Rhône (69)</a></li>
                        <li><a href="#">Isère (38)</a></li>
                        <li><a href="#">Haute-Savoie (74)</a></li>
                        <li><a href="#">Savoie (73)</a></li>
                        <li><a href="#">Puy-de-Dôme (63)</a></li>
                        <li><a href="#">Loire (42)</a></li>
                        <li><a href="#">Ain (01)</a></li>
                        <li><a href="#">+ 5 départements</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Provence-Alpes-Côte d'Azur</h4>
                    <ul>
                        <li><a href="#">Bouches-du-Rhône (13)</a></li>
                        <li><a href="#">Var (83)</a></li>
                        <li><a href="#">Alpes-Maritimes (06)</a></li>
                        <li><a href="#">Vaucluse (84)</a></li>
                        <li><a href="#">Alpes-de-Haute-Provence (04)</a></li>
                        <li><a href="#">Hautes-Alpes (05)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Occitanie</h4>
                    <ul>
                        <li><a href="#">Haute-Garonne (31)</a></li>
                        <li><a href="#">Hérault (34)</a></li>
                        <li><a href="#">Gard (30)</a></li>
                        <li><a href="#">Aude (11)</a></li>
                        <li><a href="#">Pyrénées-Orientales (66)</a></li>
                        <li><a href="#">+ 8 départements</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Nouvelle-Aquitaine</h4>
                    <ul>
                        <li><a href="#">Gironde (33)</a></li>
                        <li><a href="#">Pyrénées-Atlantiques (64)</a></li>
                        <li><a href="#">Charente-Maritime (17)</a></li>
                        <li><a href="#">Landes (40)</a></li>
                        <li><a href="#">+ 8 départements</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Grand Est</h4>
                    <ul>
                        <li><a href="#">Bas-Rhin (67)</a></li>
                        <li><a href="#">Haut-Rhin (68)</a></li>
                        <li><a href="#">Moselle (57)</a></li>
                        <li><a href="#">Meurthe-et-Moselle (54)</a></li>
                        <li><a href="#">+ 6 départements</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Hauts-de-France</h4>
                    <ul>
                        <li><a href="#">Nord (59)</a></li>
                        <li><a href="#">Pas-de-Calais (62)</a></li>
                        <li><a href="#">Somme (80)</a></li>
                        <li><a href="#">Oise (60)</a></li>
                        <li><a href="#">Aisne (02)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Bretagne</h4>
                    <ul>
                        <li><a href="#">Ille-et-Vilaine (35)</a></li>
                        <li><a href="#">Finistère (29)</a></li>
                        <li><a href="#">Morbihan (56)</a></li>
                        <li><a href="#">Côtes-d'Armor (22)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Pays de la Loire</h4>
                    <ul>
                        <li><a href="#">Loire-Atlantique (44)</a></li>
                        <li><a href="#">Maine-et-Loire (49)</a></li>
                        <li><a href="#">Vendée (85)</a></li>
                        <li><a href="#">Sarthe (72)</a></li>
                        <li><a href="#">Mayenne (53)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Normandie</h4>
                    <ul>
                        <li><a href="#">Seine-Maritime (76)</a></li>
                        <li><a href="#">Calvados (14)</a></li>
                        <li><a href="#">Manche (50)</a></li>
                        <li><a href="#">Eure (27)</a></li>
                        <li><a href="#">Orne (61)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Centre-Val de Loire</h4>
                    <ul>
                        <li><a href="#">Loiret (45)</a></li>
                        <li><a href="#">Indre-et-Loire (37)</a></li>
                        <li><a href="#">Loir-et-Cher (41)</a></li>
                        <li><a href="#">Cher (18)</a></li>
                        <li><a href="#">+ 2 départements</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Bourgogne-Franche-Comté</h4>
                    <ul>
                        <li><a href="#">Côte-d'Or (21)</a></li>
                        <li><a href="#">Saône-et-Loire (71)</a></li>
                        <li><a href="#">Doubs (25)</a></li>
                        <li><a href="#">+ 5 départements</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>Corse</h4>
                    <ul>
                        <li><a href="#">Corse-du-Sud (2A)</a></li>
                        <li><a href="#">Haute-Corse (2B)</a></li>
                    </ul>
                </div>
                <div class="region-card">
                    <h4>DOM-TOM</h4>
                    <ul>
                        <li><a href="#">Guadeloupe (971)</a></li>
                        <li><a href="#">Martinique (972)</a></li>
                        <li><a href="#">Guyane (973)</a></li>
                        <li><a href="#">La Réunion (974)</a></li>
                        <li><a href="#">Mayotte (976)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Témoignages -->
    <section class="testimonials">
        <div class="container">
            <h3>Ils nous font confiance</h3>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"Service impeccable ! J'ai reçu 5 devis en moins de 2h et j'ai économisé 350€ sur mon déménagement de Lyon à Paris."</p>
                    <p class="author">- Sophie M., Paris</p>
                </div>
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"Les déménageurs étaient professionnels et très soigneux. Le processus de comparaison est vraiment simple et gratuit."</p>
                    <p class="author">- Marc D., Marseille</p>
                </div>
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"Excellent rapport qualité-prix. Le service client m'a aidé à choisir la meilleure offre pour mon budget."</p>
                    <p class="author">- Julie R., Toulouse</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact et rappel -->
    <section id="contact" class="contact-callback">
        <div class="container">
            <div class="callback-wrapper">
                <div class="callback-info">
                    <h3>Besoin d'aide ? Nous vous rappelons gratuitement</h3>
                    <ul class="callback-benefits">
                        <li>✓ Conseils personnalisés pour votre déménagement</li>
                        <li>✓ Aide au choix de la formule adaptée</li>
                        <li>✓ Réponse à toutes vos questions</li>
                        <li>✓ Service disponible 7j/7</li>
                    </ul>
                    <div class="contact-methods">
                        <div class="contact-method">
                            <span class="contact-icon">📞</span>
                            <div>
                                <p>Appelez-nous directement</p>
                                <a href="tel:0978450218" class="contact-phone">09 78 45 02 18</a>
                                <p class="contact-hours">(Lun-Dim 8h-20h)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="callback-form">
                    <h4>Demande de rappel gratuit</h4>
                    <form id="callbackForm">
                        <div class="form-group">
                            <input type="text" name="callback-nom" placeholder="Votre nom *" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="callback-tel" placeholder="Votre téléphone *" pattern="[0-9]{10}" required>
                        </div>
                        <div class="form-group">
                            <select name="callback-creneau">
                                <option value="">Choisir un créneau horaire</option>
                                <option value="matin">Matin (8h-12h)</option>
                                <option value="aprem">Après-midi (12h-17h)</option>
                                <option value="soir">Soir (17h-20h)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-callback">Me faire rappeler</button>
                        <p class="callback-note">Nous vous rappelons sous 15 minutes aux heures ouvrées</p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq">
        <div class="container">
            <h3>Questions fréquentes</h3>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Le service est-il vraiment gratuit ?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Oui, notre service de mise en relation est 100% gratuit et sans engagement. Vous ne payez que le déménageur que vous choisissez.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Combien de temps pour recevoir les devis ?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>En moyenne, vous recevez les premiers devis dans l'heure qui suit votre demande. Tous les devis arrivent généralement sous 24h.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Les déménageurs sont-ils certifiés ?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Tous nos partenaires sont des professionnels certifiés avec assurance responsabilité civile professionnelle et garantie décennale.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Puis-je annuler ma demande ?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Oui, vous pouvez annuler à tout moment sans frais. Il n'y a aucun engagement de votre part.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Quelle est la différence entre les formules ?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Formule Éco : vous louez un camion et faites le déménagement. Standard : aide professionnelle pour charger/décharger. Clé en main : tout est pris en charge (emballage, transport, installation).</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Déménageur.com</h4>
                    <p>Le leader de la comparaison de devis de déménagement depuis plus de 20 ans.</p>
                    <p>Plus de 500 000 déménagements réalisés</p>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#devis">Devis gratuit</a></li>
                        <li><a href="#simulateur">Simulateur</a></li>
                        <li><a href="#">Déménagement international</a></li>
                        <li><a href="#">Déménagement entreprise</a></li>
                        <li><a href="#">Garde-meubles</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Informations</h4>
                    <ul>
                        <li><a href="#">Qui sommes-nous ?</a></li>
                        <li><a href="#">Comment ça marche ?</a></li>
                        <li><a href="#">Nos partenaires</a></li>
                        <li><a href="#">Blog déménagement</a></li>
                        <li><a href="#">Guide du déménagement</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <ul>
                        <li><a href="tel:0978450218">☎ 09 78 45 02 18</a></li>
                        <li>Lun-Dim : 8h-20h</li>
                        <li><a href="mailto:contact@demenageur.com">contact@demenageur.com</a></li>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">CGU</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Déménageur.com - Tous droits réservés</p>
                <p>Comparateur de devis de déménagement gratuit et sans engagement</p>
            </div>
        </div>
    </footer>

    <!-- Bouton de retour en haut -->
    <button id="backToTop" class="back-to-top" title="Retour en haut">↑</button>

    <script src="script.js"></script>
</body>
</html>
