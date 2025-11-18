<?php require_once __DIR__ . '/../config/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - Déménageur.com</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="/" style="color: inherit; text-decoration: none;">DÉMÉNAGEUR.COM</a></h1>
                </div>
                <a href="/" class="btn-primary" style="padding: 0.5rem 1rem; text-decoration: none; border-radius: 6px;">Retour à l'accueil</a>
            </div>
        </div>
    </header>

    <section style="padding: 4rem 0;">
        <div class="container">
            <h1 style="margin-bottom: 2rem;">Mentions Légales</h1>

            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

                <h2>1. Éditeur du site</h2>
                <p>Le site www.demenageur.com est édité par :</p>
                <ul>
                    <li><strong>Raison sociale :</strong> Déménageur.com SARL</li>
                    <li><strong>Capital social :</strong> 50 000 €</li>
                    <li><strong>Siège social :</strong> 123 Avenue des Champs-Élysées, 75008 Paris, France</li>
                    <li><strong>SIRET :</strong> 123 456 789 00012</li>
                    <li><strong>N° TVA intracommunautaire :</strong> FR12 123456789</li>
                    <li><strong>Email :</strong> contact@demenageur.com</li>
                    <li><strong>Téléphone :</strong> 09 78 45 02 18</li>
                </ul>

                <h2>2. Directeur de publication</h2>
                <p>Le directeur de la publication du site est : M. Jean Dupont, Président de Déménageur.com SARL</p>

                <h2>3. Hébergement</h2>
                <p>Le site www.demenageur.com est hébergé par :</p>
                <ul>
                    <li><strong>Nom de l'hébergeur :</strong> OVH</li>
                    <li><strong>Adresse :</strong> 2 rue Kellermann, 59100 Roubaix, France</li>
                    <li><strong>Téléphone :</strong> 1007</li>
                    <li><strong>Site web :</strong> www.ovh.com</li>
                </ul>

                <h2>4. Propriété intellectuelle</h2>
                <p>L'ensemble du contenu de ce site (textes, images, vidéos, logos, graphismes, etc.) est la propriété exclusive de Déménageur.com SARL ou de ses partenaires.</p>
                <p>Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de Déménageur.com SARL.</p>

                <h2>5. Données personnelles</h2>
                <p>Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition aux données personnelles vous concernant.</p>
                <p>Pour exercer ces droits, vous pouvez nous contacter à l'adresse : contact@demenageur.com</p>
                <p>Pour plus d'informations, consultez notre <a href="/pages/politique-confidentialite.php">politique de confidentialité</a>.</p>

                <h2>6. Cookies</h2>
                <p>Le site www.demenageur.com utilise des cookies pour améliorer l'expérience utilisateur et réaliser des statistiques de visites.</p>
                <p>Vous pouvez paramétrer vos préférences de cookies à tout moment via notre bannière de gestion des cookies.</p>

                <h2>7. Limitation de responsabilité</h2>
                <p>Déménageur.com SARL met tout en œuvre pour offrir aux utilisateurs des informations et des outils disponibles et vérifiés, mais ne saurait être tenu pour responsable des erreurs, d'une absence de disponibilité des informations et/ou de la présence de virus sur son site.</p>

                <h2>8. Droit applicable et juridiction compétente</h2>
                <p>Les présentes mentions légales sont soumises au droit français. En cas de litige, et à défaut d'accord amiable, le litige sera porté devant les tribunaux français conformément aux règles de compétence en vigueur.</p>

                <h2>9. Contact</h2>
                <p>Pour toute question concernant les mentions légales, vous pouvez nous contacter :</p>
                <ul>
                    <li>Par email : contact@demenageur.com</li>
                    <li>Par téléphone : 09 78 45 02 18 (du lundi au dimanche, 8h-20h)</li>
                    <li>Par courrier : Déménageur.com SARL, 123 Avenue des Champs-Élysées, 75008 Paris</li>
                </ul>

                <p style="margin-top: 2rem; color: #6b7280; font-size: 0.875rem;">
                    <em>Dernière mise à jour : <?= date('d/m/Y') ?></em>
                </p>
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
