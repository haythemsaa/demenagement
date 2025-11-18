<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';

$departement = '75';
$nomDepartement = 'Paris';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déménageurs à Paris (75) - Comparez jusqu'à 6 devis gratuits</title>
    <meta name="description" content="Trouvez les meilleurs déménageurs à Paris. Comparez gratuitement jusqu'à 6 devis de professionnels certifiés. Service gratuit et sans engagement.">
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="/" style="color: inherit; text-decoration: none;">DÉMÉNAGEUR.COM</a></h1>
                </div>
                <a href="/#devis" class="btn-primary" style="padding: 0.5rem 1rem; text-decoration: none; border-radius: 6px;">Obtenir un devis</a>
            </div>
        </div>
    </header>

    <section style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; padding: 3rem 0; text-align: center;">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Déménageurs à <?= e($nomDepartement) ?> (<?= e($departement) ?>)</h1>
            <p style="font-size: 1.25rem; opacity: 0.95;">Comparez gratuitement jusqu'à 6 devis de déménageurs professionnels</p>
        </div>
    </section>

    <section style="padding: 4rem 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
                <div>
                    <h2>Déménager à Paris en toute sérénité</h2>
                    <p>Paris, capitale française, est une ville dynamique où de nombreux déménagements ont lieu chaque année. Que vous déménagiez d'un studio dans le Marais vers un appartement familial à Montmartre, ou que vous quittiez Paris pour la banlieue, trouver le bon déménageur est essentiel.</p>

                    <h3>Pourquoi choisir un déménageur professionnel à Paris ?</h3>
                    <ul>
                        <li><strong>Expertise locale :</strong> Connaissance parfaite des arrondissements parisiens</li>
                        <li><strong>Gestion des contraintes :</strong> Stationnement difficile, étages sans ascenseur</li>
                        <li><strong>Autorisation de stationnement :</strong> Gestion des autorisations nécessaires</li>
                        <li><strong>Matériel adapté :</strong> Monte-meubles, diables, protections</li>
                        <li><strong>Assurance :</strong> Protection de vos biens pendant le transport</li>
                    </ul>

                    <h3>Nos déménageurs partenaires à Paris</h3>
                    <p>Nous travaillons avec des déménageurs professionnels certifiés et expérimentés sur Paris et l'Île-de-France :</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0;">
                        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <h4>Déménagements Express Paris</h4>
                            <p><strong>Note :</strong> ⭐⭐⭐⭐⭐ 4.5/5 (127 avis)</p>
                            <p><strong>Zones :</strong> Paris et toute l'Île-de-France</p>
                            <p>Spécialiste du déménagement en Île-de-France depuis 15 ans</p>
                        </div>

                        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <h4>Paris Déménagement Pro</h4>
                            <p><strong>Note :</strong> ⭐⭐⭐⭐ 4.3/5 (89 avis)</p>
                            <p><strong>Zones :</strong> Paris 75, 77, 78, 91, 92, 93, 94, 95</p>
                            <p>Service clé en main et formules économiques disponibles</p>
                        </div>
                    </div>

                    <h3>Tarifs déménagement à Paris</h3>
                    <p>Le coût d'un déménagement à Paris varie selon plusieurs facteurs :</p>

                    <table style="width: 100%; border-collapse: collapse; margin: 1.5rem 0;">
                        <thead>
                            <tr style="background: var(--primary-color); color: white;">
                                <th style="padding: 1rem; text-align: left;">Type de logement</th>
                                <th style="padding: 1rem; text-align: left;">Volume</th>
                                <th style="padding: 1rem; text-align: left;">Prix moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;">Studio (25m²)</td>
                                <td style="padding: 1rem;">15-20 m³</td>
                                <td style="padding: 1rem;"><strong>500€ - 900€</strong></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;">2 pièces (45m²)</td>
                                <td style="padding: 1rem;">25-30 m³</td>
                                <td style="padding: 1rem;"><strong>700€ - 1 200€</strong></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;">3 pièces (65m²)</td>
                                <td style="padding: 1rem;">35-40 m³</td>
                                <td style="padding: 1rem;"><strong>900€ - 1 600€</strong></td>
                            </tr>
                            <tr>
                                <td style="padding: 1rem;">4 pièces et + (90m²+)</td>
                                <td style="padding: 1rem;">50+ m³</td>
                                <td style="padding: 1rem;"><strong>1 500€ - 3 000€</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <p><em>* Prix indicatifs pour un déménagement local à Paris. Le prix final dépend de nombreux facteurs.</em></p>

                    <h3>Conseils pour votre déménagement à Paris</h3>
                    <ul>
                        <li><strong>Réservez une autorisation de stationnement</strong> auprès de la mairie de votre arrondissement (prévoir 2 semaines)</li>
                        <li><strong>Évitez les périodes de pointe</strong> : fin de mois et période estivale (juin-septembre)</li>
                        <li><strong>Préparez vos cartons</strong> en amont pour réduire les coûts</li>
                        <li><strong>Vérifiez les assurances</strong> proposées par le déménageur</li>
                        <li><strong>Prévenez votre syndic</strong> et réservez l'ascenseur si nécessaire</li>
                    </ul>

                    <h3>Questions fréquentes sur le déménagement à Paris</h3>

                    <div style="margin: 2rem 0;">
                        <details style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <summary style="font-weight: 600; cursor: pointer;">Combien coûte un déménagement à Paris ?</summary>
                            <p>Le coût varie de 500€ pour un studio à plus de 3000€ pour un grand appartement. Demandez plusieurs devis pour comparer.</p>
                        </details>

                        <details style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <summary style="font-weight: 600; cursor: pointer;">Comment obtenir une autorisation de stationnement ?</summary>
                            <p>Contactez la mairie de votre arrondissement au moins 2 semaines avant. Certains déménageurs proposent de s'en charger.</p>
                        </details>

                        <details style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <summary style="font-weight: 600; cursor: pointer;">Quelle est la meilleure période pour déménager ?</summary>
                            <p>Privilégiez les mois de mars à mai et septembre à novembre. Évitez juillet-août et les fins de mois.</p>
                        </details>
                    </div>
                </div>

                <div>
                    <div style="position: sticky; top: 100px;">
                        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <h3 style="margin-bottom: 1rem;">Obtenez vos devis gratuits</h3>
                            <p style="color: var(--text-light); margin-bottom: 1.5rem;">Comparez jusqu'à 6 devis de déménageurs à Paris</p>

                            <ul style="list-style: none; padding: 0; margin-bottom: 1.5rem;">
                                <li style="padding: 0.5rem 0;">✓ Réponse sous 1h</li>
                                <li style="padding: 0.5rem 0;">✓ Gratuit et sans engagement</li>
                                <li style="padding: 0.5rem 0;">✓ Déménageurs certifiés</li>
                                <li style="padding: 0.5rem 0;">✓ Économisez jusqu'à 40%</li>
                            </ul>

                            <a href="/#devis" style="display: block; text-align: center; padding: 1rem; background: var(--primary-color); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s;">Comparer les devis</a>

                            <p style="text-align: center; margin-top: 1rem; color: var(--text-light); font-size: 0.875rem;">
                                Ou appelez-nous au<br>
                                <a href="tel:0978450218" style="color: var(--primary-color); font-weight: 600; font-size: 1.125rem;">09 78 45 02 18</a>
                            </p>
                        </div>

                        <div style="background: #eff6ff; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
                            <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">💡 Le saviez-vous ?</h4>
                            <p style="font-size: 0.875rem; margin: 0;">En moyenne, nos utilisateurs économisent <strong>35%</strong> en comparant plusieurs devis de déménageurs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 Déménageur.com - Tous droits réservés</p>
                <p>
                    <a href="/pages/mentions-legales.php" style="color: inherit;">Mentions légales</a> |
                    <a href="/pages/cgu.php" style="color: inherit;">CGU</a> |
                    <a href="/pages/politique-confidentialite.php" style="color: inherit;">Politique de confidentialité</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
