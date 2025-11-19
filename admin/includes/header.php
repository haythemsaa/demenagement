<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?>Administration - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>⚙️ Admin</h2>
                <p><?= SITE_NAME ?></p>
            </div>

            <nav class="sidebar-nav">
                <a href="/admin/dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Tableau de bord</span>
                </a>

                <a href="/admin/quotes.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'quotes.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📝</span>
                    <span class="nav-text">Demandes de devis</span>
                </a>

                <a href="/admin/callbacks.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'callbacks.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📞</span>
                    <span class="nav-text">Demandes de rappel</span>
                </a>

                <a href="/admin/demenageurs.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'demenageurs.php' ? 'active' : '' ?>">
                    <span class="nav-icon">🚚</span>
                    <span class="nav-text">Déménageurs Pros</span>
                </a>

                <a href="/admin/movers.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'movers.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Partenaires</span>
                </a>

                <a href="/admin/countries.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'countries.php' ? 'active' : '' ?>">
                    <span class="nav-icon">🌍</span>
                    <span class="nav-text">Pays & Régions</span>
                </a>

                <a href="/admin/settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Paramètres</span>
                </a>

                <a href="/admin/users.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Utilisateurs</span>
                </a>

                <div class="nav-separator"></div>

                <a href="/" class="nav-item" target="_blank">
                    <span class="nav-icon">🌐</span>
                    <span class="nav-text">Voir le site</span>
                </a>

                <a href="/admin/logout.php" class="nav-item nav-danger">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Déconnexion</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <p><strong><?= e($_SESSION['admin_name']) ?></strong></p>
                <p><small><?= e($_SESSION['admin_role']) ?></small></p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn-menu" onclick="toggleSidebar()">☰</button>
                </div>
                <div class="topbar-right">
                    <span class="user-info">👤 <?= e($_SESSION['admin_name']) ?></span>
                </div>
            </div>

            <div class="content-area">
