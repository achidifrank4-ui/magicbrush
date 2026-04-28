<?php
// admin/commandes.php — Gestion et suivi des commandes
require_once '../config/config.php';
require_once '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php'); exit;
}

// Mise à jour du statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_id']) && !empty($_POST['statut'])) {
    $valid_statuts = ['en_attente', 'payee', 'expediee', 'livree', 'annulee'];
    if (in_array($_POST['statut'], $valid_statuts)) {
        $pdo->prepare("UPDATE orders SET statut = :statut WHERE id = :id")
            ->execute([':statut' => $_POST['statut'], ':id' => (int)$_POST['order_id']]);
    }
    header('Location: commandes.php?updated=1'); exit;
}

$filter_statut = $_GET['statut'] ?? '';
$valid_statuts = ['en_attente', 'payee', 'expediee', 'livree', 'annulee'];

$where = $filter_statut && in_array($filter_statut, $valid_statuts) ? "WHERE o.statut = :statut" : "";
$params = $filter_statut && in_array($filter_statut, $valid_statuts) ? [':statut' => $filter_statut] : [];

$stmt = $pdo->prepare("SELECT o.*, u.email, u.prenom, u.nom as user_nom FROM orders o LEFT JOIN users u ON o.user_id = u.id $where ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

function statutBadge($s) {
    return match($s) {
        'en_attente' => ['label' => 'En attente', 'color' => '#f39c12'],
        'payee'      => ['label' => 'Payée',      'color' => 'var(--color-primary)'],
        'expediee'   => ['label' => 'Expédiée',   'color' => '#3498db'],
        'livree'     => ['label' => 'Livrée',     'color' => 'var(--color-success)'],
        'annulee'    => ['label' => 'Annulée',    'color' => 'var(--color-error)'],
        default      => ['label' => $s,            'color' => '#999'],
    };
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes — Admin Magic Brush</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="admin-brand"><i class="ph-fill ph-paw-print"></i> Magic Brush <span style="font-size:0.7rem;font-weight:400;opacity:0.7;">Back-office</span></div>
    <nav class="admin-nav">
        <a href="index.php" class="admin-nav-link"><i class="ph ph-squares-four"></i> Dashboard</a>
        <a href="produits.php" class="admin-nav-link"><i class="ph ph-package"></i> Produits</a>
        <a href="commandes.php" class="admin-nav-link active"><i class="ph ph-shopping-cart-simple"></i> Commandes</a>
        <a href="stocks.php" class="admin-nav-link"><i class="ph ph-chart-bar"></i> Stocks</a>
        <hr style="border-color:rgba(255,255,255,0.1);margin:1rem 0;">
        <a href="../index.php" class="admin-nav-link"><i class="ph ph-arrow-left"></i> Retour au site</a>
    </nav>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <h1>Gestion des Commandes</h1>
    </header>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert-success">Statut mis à jour.</div>
    <?php endif; ?>

    <!-- Filtres par statut -->
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <a href="commandes.php" class="btn <?= !$filter_statut ? 'btn-primary' : 'btn-outline' ?>" style="font-size: 0.85rem; padding: 0.4rem 1rem;">Toutes</a>
        <?php foreach (['en_attente' => 'En attente', 'payee' => 'Payées', 'expediee' => 'Expédiées', 'livree' => 'Livrées', 'annulee' => 'Annulées'] as $val => $lbl): ?>
        <a href="commandes.php?statut=<?= $val ?>" class="btn <?= $filter_statut === $val ? 'btn-primary' : 'btn-outline' ?>" style="font-size: 0.85rem; padding: 0.4rem 1rem;"><?= $lbl ?></a>
        <?php endforeach; ?>
    </div>

    <div class="admin-card">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Adresse</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Changer statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <?php $badge = statutBadge($order['statut']); ?>
                    <tr>
                        <td><strong>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                        <td>
                            <?php if ($order['email']): ?>
                            <div style="font-weight: 500;"><?= htmlspecialchars($order['prenom'] . ' ' . $order['user_nom']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--color-text-light);"><?= htmlspecialchars($order['email']) ?></div>
                            <?php else: ?>
                            <span style="color: var(--color-text-light);">Invité</span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.9rem;"><?= htmlspecialchars($order['adresse_livraison']) ?></td>
                        <td><strong><?= number_format($order['total'], 2, ',', ' ') ?> €</strong></td>
                        <td>
                            <span style="background: <?= $badge['color'] ?>22; color: <?= $badge['color'] ?>; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                <?= $badge['label'] ?>
                            </span>
                        </td>
                        <td style="color: var(--color-text-light); font-size: 0.85rem; white-space: nowrap;"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="statut" style="padding: 0.3rem 0.5rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 0.85rem; font-family: var(--font-body);">
                                    <?php foreach (['en_attente' => 'En attente', 'payee' => 'Payée', 'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée'] as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $order['statut'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.75rem; font-size: 0.85rem;"><i class="ph ph-check"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--color-text-light);">Aucune commande trouvée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
