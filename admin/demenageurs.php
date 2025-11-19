<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Database.php';

// Vérifier si admin connecté
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();

$success = '';
$error = '';

// Actions sur les déménageurs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $demenageur_id = intval($_POST['demenageur_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'approve') {
        // Valider le déménageur
        $stmt = $db->prepare("
            UPDATE demenageurs
            SET status = 'active', verified = TRUE, verification_date = NOW(), verified_by = ?
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['admin_id'], $demenageur_id]);

        // TODO: Envoyer email de validation
        $success = 'Déménageur validé avec succès.';

    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE demenageurs SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$demenageur_id]);

        $success = 'Déménageur rejeté.';

    } elseif ($action === 'suspend') {
        $stmt = $db->prepare("UPDATE demenageurs SET status = 'suspended' WHERE id = ?");
        $stmt->execute([$demenageur_id]);

        $success = 'Déménageur suspendu.';

    } elseif ($action === 'activate') {
        $stmt = $db->prepare("UPDATE demenageurs SET status = 'active' WHERE id = ?");
        $stmt->execute([$demenageur_id]);

        $success = 'Déménageur réactivé.';
    }
}

// Récupérer tous les déménageurs
$filter = $_GET['filter'] ?? 'all';

$where_clause = '';
if ($filter === 'pending') {
    $where_clause = "WHERE d.status = 'pending'";
} elseif ($filter === 'active') {
    $where_clause = "WHERE d.status = 'active'";
} elseif ($filter === 'suspended') {
    $where_clause = "WHERE d.status = 'suspended'";
}

$sql = "
    SELECT d.*, sp.name as plan_name, ds.status as subscription_status
    FROM demenageurs d
    LEFT JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id AND ds.status = 'active'
    LEFT JOIN subscription_plans sp ON ds.plan_id = sp.id
    {$where_clause}
    ORDER BY d.created_at DESC
";

$stmt = $db->query($sql);
$demenageurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => 0,
    'pending' => 0,
    'active' => 0,
    'suspended' => 0,
    'rejected' => 0
];

$stmt = $db->query("SELECT status, COUNT(*) as count FROM demenageurs GROUP BY status");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Déménageurs - Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
        }

        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-btn {
            padding: 10px 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: #2d3748;
            transition: 0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            border-color: #667eea;
            color: #667eea;
        }

        .demenageurs-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .demenageurs-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .demenageurs-table th {
            background: #f7fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
        }

        .demenageurs-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .demenageurs-table tr:hover {
            background: #f7fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-suspended {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-rejected {
            background: #f3f4f6;
            color: #4b5563;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
            transition: 0.3s;
        }

        .btn-approve {
            background: #48bb78;
            color: white;
        }

        .btn-reject {
            background: #f56565;
            color: white;
        }

        .btn-suspend {
            background: #ed8936;
            color: white;
        }

        .btn-view {
            background: #667eea;
            color: white;
        }

        .action-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="admin-container">
        <h1>Gestion des Déménageurs</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div class="stat-label">Total déménageurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #fbbf24;"><?= $stats['pending'] ?? 0 ?></div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #48bb78;"><?= $stats['active'] ?? 0 ?></div>
                <div class="stat-label">Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #f56565;"><?= $stats['suspended'] ?? 0 ?></div>
                <div class="stat-label">Suspendus</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <a href="?filter=all" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
                Tous (<?= $stats['total'] ?>)
            </a>
            <a href="?filter=pending" class="filter-btn <?= $filter === 'pending' ? 'active' : '' ?>">
                En attente (<?= $stats['pending'] ?? 0 ?>)
            </a>
            <a href="?filter=active" class="filter-btn <?= $filter === 'active' ? 'active' : '' ?>">
                Actifs (<?= $stats['active'] ?? 0 ?>)
            </a>
            <a href="?filter=suspended" class="filter-btn <?= $filter === 'suspended' ? 'active' : '' ?>">
                Suspendus (<?= $stats['suspended'] ?? 0 ?>)
            </a>
        </div>

        <!-- Table -->
        <div class="demenageurs-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Entreprise</th>
                        <th>SIRET</th>
                        <th>Contact</th>
                        <th>Ville</th>
                        <th>Plan</th>
                        <th>Statut</th>
                        <th>Inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($demenageurs)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #718096;">
                                Aucun déménageur trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($demenageurs as $dem): ?>
                            <tr>
                                <td><strong>#<?= $dem['id'] ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($dem['company_name']) ?></strong><br>
                                    <small style="color: #718096;"><?= htmlspecialchars($dem['legal_form']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($dem['siret']) ?></td>
                                <td>
                                    <?= htmlspecialchars($dem['contact_name']) ?><br>
                                    <small style="color: #718096;"><?= htmlspecialchars($dem['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($dem['city']) ?> (<?= htmlspecialchars($dem['postal_code']) ?>)</td>
                                <td>
                                    <?= $dem['plan_name'] ? htmlspecialchars($dem['plan_name']) : '-' ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $dem['status'] ?>">
                                        <?= ucfirst($dem['status']) ?>
                                    </span>
                                    <?php if ($dem['verified']): ?>
                                        <span style="color: #48bb78;">✓</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($dem['created_at'])) ?></td>
                                <td>
                                    <?php if ($dem['status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="demenageur_id" value="<?= $dem['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="action-btn btn-approve"
                                                onclick="return confirm('Valider ce déménageur ?')">
                                                ✓ Valider
                                            </button>
                                            <button type="submit" name="action" value="reject" class="action-btn btn-reject"
                                                onclick="return confirm('Rejeter ce déménageur ?')">
                                                ✗ Rejeter
                                            </button>
                                        </form>
                                    <?php elseif ($dem['status'] === 'active'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="demenageur_id" value="<?= $dem['id'] ?>">
                                            <button type="submit" name="action" value="suspend" class="action-btn btn-suspend"
                                                onclick="return confirm('Suspendre ce déménageur ?')">
                                                Suspendre
                                            </button>
                                        </form>
                                    <?php elseif ($dem['status'] === 'suspended'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="demenageur_id" value="<?= $dem['id'] ?>">
                                            <button type="submit" name="action" value="activate" class="action-btn btn-approve"
                                                onclick="return confirm('Réactiver ce déménageur ?')">
                                                Réactiver
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="demenageur-details.php?id=<?= $dem['id'] ?>" class="action-btn btn-view">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
