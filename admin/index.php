<?php
// admin/index.php — Dashboard Back-office Magic Brush
require_once '../config/config.php';
require_once '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Sécurité : accès admin uniquement
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php?redirect=admin');
    exit;
}

// Stats pour le dashboard
$stats = [
    'commandes_total'  => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'commandes_att'    => $pdo->query("SELECT COUNT(*) FROM orders WHERE statut = 'en_attente'")->fetchColumn(),
    'ca_total'         => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE statut != 'annulee'")->fetchColumn(),
    'produits'         => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'clients'          => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'stock_bas'        => $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 5")->fetchColumn(),
];

// Dernières commandes
$recent_orders = $pdo->query("SELECT o.id, o.statut, o.total, o.created_at, o.adresse_livraison FROM orders o ORDER BY o.created_at DESC LIMIT 8")->fetchAll();

// Statut → badge color
function statutBadge($statut) {
    return match($statut) {
        'en_attente' => ['label' => 'En attente', 'color' => '#f39c12'],
        'payee'      => ['label' => 'Payée',      'color' => 'var(--color-primary)'],
        'expediee'   => ['label' => 'Expédiée',   'color' => '#3498db'],
        'livree'     => ['label' => 'Livrée',     'color' => 'var(--color-success)'],
        'annulee'    => ['label' => 'Annulée',    'color' => 'var(--color-error)'],
        default      => ['label' => $statut,       'color' => '#999'],
    };
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Magic Brush</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="admin-body">

<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="admin-brand">
        <i class="ph-fill ph-paw-print"></i> Magic Brush
        <span style="font-size: 0.7rem; font-weight: 400; opacity: 0.7; display: block;">Back-office</span>
    </div>
    <nav class="admin-nav">
        <a href="index.php" class="admin-nav-link active"><i class="ph ph-squares-four"></i> Dashboard</a>
        <a href="produits.php" class="admin-nav-link"><i class="ph ph-package"></i> Produits</a>
        <a href="commandes.php" class="admin-nav-link"><i class="ph ph-shopping-cart-simple"></i> Commandes</a>
        <a href="stocks.php" class="admin-nav-link"><i class="ph ph-chart-bar"></i> Stocks</a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
        <a href="../index.php" class="admin-nav-link"><i class="ph ph-arrow-left"></i> Retour au site</a>
    </nav>
</aside>

<!-- Contenu principal -->
<main class="admin-main">
    <header class="admin-header">
        <h1>Tableau de bord</h1>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-text-light); font-size: 0.9rem;">
            <i class="ph ph-user-circle"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrateur') ?>
        </div>
    </header>

    <!-- KPIs -->
    <div class="admin-kpi-grid">
        <div class="admin-kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));">
                <i class="ph-fill ph-shopping-cart-simple"></i>
            </div>
            <div>
                <div class="kpi-value"><?= $stats['commandes_total'] ?></div>
                <div class="kpi-label">Commandes totales</div>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <i class="ph-fill ph-clock"></i>
            </div>
            <div>
                <div class="kpi-value"><?= $stats['commandes_att'] ?></div>
                <div class="kpi-label">En attente</div>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, var(--color-secondary), var(--color-secondary-light));">
                <i class="ph-fill ph-currency-eur"></i>
            </div>
            <div>
                <div class="kpi-value"><?= number_format($stats['ca_total'], 0, ',', ' ') ?> €</div>
                <div class="kpi-label">Chiffre d'affaires</div>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                <i class="ph-fill ph-users"></i>
            </div>
            <div>
                <div class="kpi-value"><?= $stats['clients'] ?></div>
                <div class="kpi-label">Clients</div>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                <i class="ph-fill ph-package"></i>
            </div>
            <div>
                <div class="kpi-value"><?= $stats['produits'] ?></div>
                <div class="kpi-label">Produits actifs</div>
            </div>
        </div>
        <?php if ($stats['stock_bas'] > 0): ?>
        <div class="admin-kpi-card" style="border-left: 4px solid var(--color-error);">
            <div class="kpi-icon" style="background: linear-gradient(135deg, var(--color-error), #c0392b);">
                <i class="ph-fill ph-warning"></i>
            </div>
            <div>
                <div class="kpi-value" style="color: var(--color-error);"><?= $stats['stock_bas'] ?></div>
                <div class="kpi-label">Stock(s) critiques</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Dernières commandes -->
    <div class="admin-card" style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.3rem;">Dernières commandes</h2>
            <a href="commandes.php" style="color: var(--color-secondary); font-size: 0.9rem; font-weight: 500;">Tout voir →</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client / Adresse</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                    <?php $badge = statutBadge($order['statut']); ?>
                    <tr>
                        <td><strong>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($order['adresse_livraison']) ?></td>
                        <td><strong><?= number_format($order['total'], 2, ',', ' ') ?> €</strong></td>
                        <td>
                            <span style="background: <?= $badge['color'] ?>22; color: <?= $badge['color'] ?>; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                                <?= $badge['label'] ?>
                            </span>
                        </td>
                        <td style="color: var(--color-text-light); font-size: 0.9rem;"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><a href="commandes.php?edit=<?= $order['id'] ?>" style="color: var(--color-primary); font-size: 0.9rem;">Gérer</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>
