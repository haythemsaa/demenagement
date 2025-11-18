<?php
if (!isset($i18n)) {
    require_once __DIR__ . '/../classes/i18n.php';
    $i18n = i18n::getInstance();
}
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="/index.php" class="logo">
            <strong><?= __('site.name') ?></strong>
            <span><?= __('site.tagline') ?></span>
        </a>
        <ul class="nav-menu">
            <li><a href="/index.php"><?= __('menu.home') ?></a></li>
            <li><a href="/estimateur-pro.php">💎 Estimateur Pro</a></li>
            <li><a href="/tarifs.php">Tarifs</a></li>
            <li><a href="/comparateur-devis.php">Comparateur</a></li>
            <li><a href="/services-specialises.php">Services</a></li>
            <li><a href="/avis-clients.php">Avis</a></li>
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/faq.php">FAQ</a></li>
            <li><a href="/demenageur-pro.php" style="color: #764ba2; font-weight: 600;">👔 Pros</a></li>
            <li><a href="/client/login.php" class="nav-login">Espace Client</a></li>
            <li><a href="/index.php#formulaire-devis" class="nav-cta">Devis Gratuit</a></li>
        </ul>
        <div class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

<style>
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        padding: 15px 0;
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        text-decoration: none;
        font-size: 24px;
        color: #667eea;
        display: flex;
        flex-direction: column;
    }

    .logo span {
        font-size: 12px;
        color: #718096;
        font-weight: normal;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 25px;
        align-items: center;
    }

    .nav-menu li a {
        text-decoration: none;
        color: #2d3748;
        font-weight: 500;
        transition: color 0.3s;
        font-size: 15px;
    }

    .nav-menu li a:hover {
        color: #667eea;
    }

    .nav-login {
        color: #667eea !important;
        font-weight: 600 !important;
    }

    .nav-cta {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600 !important;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .nav-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        gap: 5px;
        cursor: pointer;
    }

    .mobile-menu-toggle span {
        width: 25px;
        height: 3px;
        background: #2d3748;
        border-radius: 3px;
        transition: 0.3s;
    }

    @media (max-width: 968px) {
        .nav-menu {
            position: fixed;
            top: 70px;
            left: -100%;
            flex-direction: column;
            background: white;
            width: 100%;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
            gap: 15px;
        }

        .nav-menu.active {
            left: 0;
        }

        .mobile-menu-toggle {
            display: flex;
        }

        .nav-menu li {
            width: 100%;
            text-align: center;
        }

        .nav-menu li a {
            display: block;
            padding: 10px;
        }
    }
</style>

<script>
    function toggleMobileMenu() {
        document.querySelector('.nav-menu').classList.toggle('active');
    }
</script>
