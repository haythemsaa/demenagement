<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/i18n.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}

$page_title = 'Gestion des pays et régions';
$i18n = i18n::getInstance();

// Récupérer tous les pays disponibles
$countriesDir = __DIR__ . '/../config/countries/';
$countries = [];

if (is_dir($countriesDir)) {
    $files = glob($countriesDir . '*.php');
    foreach ($files as $file) {
        $code = basename($file, '.php');
        $config = require $file;
        $countries[$code] = $config;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="countries-page">
    <div class="page-header">
        <h1>🌍 Gestion des pays et régions</h1>
        <a href="/admin/countries-add.php" class="btn btn-primary">+ Ajouter un pays</a>
    </div>

    <div class="section-card">
        <p>Gérez les pays disponibles et leurs configurations spécifiques (devise, format, tarifs, régions...)</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Pays</th>
                    <th>Langue</th>
                    <th>Devise</th>
                    <th>Régions</th>
                    <th>Format date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($countries as $code => $country): ?>
                <tr>
                    <td><strong><?= e($code) ?></strong></td>
                    <td><?= e($country['name']) ?></td>
                    <td><?= e(strtoupper($country['language'])) ?></td>
                    <td>
                        <?= e($country['currency']['symbol']) ?> <?= e($country['currency']['code']) ?>
                    </td>
                    <td>
                        <?= count($country['regions']) ?> région(s)
                    </td>
                    <td>
                        <code><?= e($country['date_format']) ?></code>
                    </td>
                    <td>
                        <a href="/admin/countries-edit.php?code=<?= $code ?>" class="btn btn-sm btn-secondary">Modifier</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Détails d'un pays (exemple avec FR) -->
    <?php if (isset($countries['FR'])): $fr = $countries['FR']; ?>
    <div class="section-card">
        <h2>Exemple: Configuration France</h2>

        <div class="form-grid">
            <div>
                <h3>💰 Devise</h3>
                <ul>
                    <li><strong>Code:</strong> <?= $fr['currency']['code'] ?></li>
                    <li><strong>Symbole:</strong> <?= $fr['currency']['symbol'] ?></li>
                    <li><strong>Décimales:</strong> <?= $fr['currency']['decimals'] ?></li>
                    <li><strong>Position:</strong> <?= $fr['currency']['symbol_position'] ?></li>
                </ul>
            </div>

            <div>
                <h3>📅 Formats</h3>
                <ul>
                    <li><strong>Date:</strong> <code><?= $fr['date_format'] ?></code></li>
                    <li><strong>Téléphone:</strong> <?= $fr['phone']['format'] ?></li>
                    <li><strong>Code postal:</strong> <?= $fr['postal_code']['placeholder'] ?></li>
                </ul>
            </div>

            <div>
                <h3>📏 Unités</h3>
                <ul>
                    <li><strong>Distance:</strong> <?= $fr['units']['distance'] ?></li>
                    <li><strong>Surface:</strong> <?= $fr['units']['area'] ?></li>
                    <li><strong>Volume:</strong> <?= $fr['units']['volume'] ?></li>
                </ul>
            </div>

            <div>
                <h3>💵 Tarification</h3>
                <ul>
                    <li><strong>Prix de base:</strong> <?= $fr['pricing']['base_price'] ?>€</li>
                    <li><strong>Prix/m²:</strong> <?= $fr['pricing']['price_per_sqm'] ?>€</li>
                    <li><strong>Prix/km:</strong> <?= $fr['pricing']['price_per_km'] ?>€</li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
