<?php
if (!isset($i18n)) {
    require_once __DIR__ . '/../classes/i18n.php';
    $i18n = i18n::getInstance();
}
?>
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-column">
                <h4>📦 <?= __('site.name') ?></h4>
                <p><?= __('footer.tagline') ?></p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">📘</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="LinkedIn">💼</a>
                </div>
            </div>

            <div class="footer-column">
                <h4>Services</h4>
                <ul>
                    <li><a href="/index.php#formulaire-devis">Devis gratuit</a></li>
                    <li><a href="/calculateur-volume.php">Calculateur de volume</a></li>
                    <li><a href="/tarifs.php">Grille tarifaire</a></li>
                    <li><a href="/services-specialises.php">Services spécialisés</a></li>
                    <li><a href="/client/login.php">Espace client</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Ressources</h4>
                <ul>
                    <li><a href="/blog/">Blog & Guides</a></li>
                    <li><a href="/avis-clients.php">Avis clients</a></li>
                    <li><a href="/admin/login.php">Espace administrateur</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Informations Légales</h4>
                <ul>
                    <li><a href="/mentions-legales.php">Mentions légales</a></li>
                    <li><a href="/cgu.php">CGU</a></li>
                    <li><a href="/politique-confidentialite.php">Politique de confidentialité</a></li>
                    <li><a href="#">Cookies</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Contact</h4>
                <ul>
                    <li>📞 <?= PHONE_NUMBER ?></li>
                    <li>✉️ <?= SITE_EMAIL ?></li>
                    <li>⏰ Lun-Dim : 8h-20h</li>
                </ul>
                <div class="footer-badges" style="margin-top: 20px;">
                    <span style="background: #48bb78; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; display: inline-block; margin: 2px;">✓ 100% Gratuit</span>
                    <span style="background: #4299e1; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; display: inline-block; margin: 2px;">✓ Certifié</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= __('site.name') ?>. <?= __('footer.rights') ?>.</p>
            <p>Comparateur de devis de déménagement gratuit et sans engagement</p>
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        color: white;
        padding: 60px 0 30px;
        margin-top: 80px;
    }

    .footer-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-column h4 {
        color: white;
        margin: 0 0 20px 0;
        font-size: 18px;
    }

    .footer-column p {
        color: #a0aec0;
        line-height: 1.6;
        font-size: 14px;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-column ul li {
        margin-bottom: 12px;
    }

    .footer-column ul li a {
        color: #cbd5e0;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .footer-column ul li a:hover {
        color: #667eea;
    }

    .footer-social {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .footer-social a {
        font-size: 24px;
        transition: transform 0.3s;
    }

    .footer-social a:hover {
        transform: scale(1.2);
    }

    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 30px;
        text-align: center;
    }

    .footer-bottom p {
        color: #a0aec0;
        margin: 5px 0;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
