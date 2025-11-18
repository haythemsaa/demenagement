<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}

$page_title = 'Paramètres globaux';

include __DIR__ . '/includes/header.php';
?>

<div class="settings-page">
    <h1>⚙️ Paramètres globaux</h1>

    <div class="section-card">
        <h2>🌐 Paramètres du site</h2>

        <div class="form-grid">
            <div class="form-group">
                <label>Nom du site</label>
                <input type="text" value="<?= SITE_NAME ?>" readonly>
            </div>
            <div class="form-group">
                <label>URL du site</label>
                <input type="text" value="<?= SITE_URL ?>" readonly>
            </div>
            <div class="form-group">
                <label>Email principal</label>
                <input type="email" value="<?= SITE_EMAIL ?>" readonly>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" value="<?= PHONE_NUMBER ?>" readonly>
            </div>
        </div>

        <p class="mt-2"><small>Pour modifier ces paramètres, éditez le fichier <code>config/config.php</code></small></p>
    </div>

    <div class="section-card">
        <h2>📧 Configuration email</h2>

        <div class="form-grid">
            <div class="form-group">
                <label>Email expéditeur</label>
                <input type="text" value="<?= EMAIL_FROM_ADDRESS ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nom expéditeur</label>
                <input type="text" value="<?= EMAIL_FROM_NAME ?>" readonly>
            </div>
            <div class="form-group">
                <label>SMTP activé</label>
                <input type="text" value="<?= SMTP_ENABLED ? 'Oui' : 'Non' ?>" readonly>
            </div>
        </div>
    </div>

    <div class="section-card">
        <h2>🔐 Sécurité</h2>

        <div class="form-grid">
            <div class="form-group">
                <label>Protection CSRF</label>
                <input type="text" value="<?= ENABLE_CSRF_PROTECTION ? 'Activée' : 'Désactivée' ?>" readonly>
            </div>
            <div class="form-group">
                <label>Longueur min. mot de passe</label>
                <input type="text" value="<?= PASSWORD_MIN_LENGTH ?> caractères" readonly>
            </div>
            <div class="form-group">
                <label>Tentatives de connexion max</label>
                <input type="text" value="<?= MAX_LOGIN_ATTEMPTS ?>" readonly>
            </div>
            <div class="form-group">
                <label>Durée de session</label>
                <input type="text" value="<?= SESSION_LIFETIME / 60 ?> minutes" readonly>
            </div>
        </div>
    </div>

    <div class="section-card">
        <h2>💰 Tarification par défaut</h2>

        <div class="form-grid">
            <div class="form-group">
                <label>Prix de base</label>
                <input type="text" value="<?= BASE_PRICE ?>€" readonly>
            </div>
            <div class="form-group">
                <label>Prix par m²</label>
                <input type="text" value="<?= PRICE_PER_SQM ?>€" readonly>
            </div>
            <div class="form-group">
                <label>Prix par km</label>
                <input type="text" value="<?= PRICE_PER_KM ?>€" readonly>
            </div>
            <div class="form-group">
                <label>Coût monte-charge</label>
                <input type="text" value="<?= LIFT_REQUIRED_COST ?>€" readonly>
            </div>
        </div>
    </div>

    <div class="section-card">
        <h2>🌍 Langues disponibles</h2>

        <?php
        require_once __DIR__ . '/../classes/i18n.php';
        $i18n = i18n::getInstance();
        $languages = $i18n->getAvailableLanguages();
        ?>

        <div class="form-grid">
            <?php foreach ($languages as $code => $name): ?>
            <div>
                <strong><?= strtoupper($code) ?>:</strong> <?= $name ?>
                <?php if ($code === 'fr'): ?><span class="badge badge-converti">Par défaut</span><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section-card">
        <h2>📊 Informations système</h2>

        <div class="form-grid">
            <div class="form-group">
                <label>Version PHP</label>
                <input type="text" value="<?= PHP_VERSION ?>" readonly>
            </div>
            <div class="form-group">
                <label>Environnement</label>
                <input type="text" value="<?= ENVIRONMENT ?>" readonly>
            </div>
            <div class="form-group">
                <label>Logs activés</label>
                <input type="text" value="<?= LOG_ENABLED ? 'Oui' : 'Non' ?>" readonly>
            </div>
            <div class="form-group">
                <label>Cache activé</label>
                <input type="text" value="<?= CACHE_ENABLED ? 'Oui' : 'Non' ?>" readonly>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
