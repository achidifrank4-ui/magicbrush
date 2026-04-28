<?php
// admin/stocks.php — Suivi et alertes des stocks
require_once '../config/config.php';
require_once '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php'); exit;
}

// Mise à jour rapide du stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['product_id'])) {
    $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id")
        ->execute([':stock' => max(0, (int)$_POST['stock']), ':id' => (int)$_POST['product_id']]);
    header('Location: stocks.php?updated=1'); exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY stock ASC, nom ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks — Admin Magic Brush</title>
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
        <a href="commandes.php" class="admin-nav-link"><i class="ph ph-shopping-cart-simple"></i> Commandes</a>
        <a href="stocks.php" class="admin-nav-link active"><i class="ph ph-chart-bar"></i> Stocks</a>
        <hr style="border-color:rgba(255,255,255,0.1);margin:1rem 0;">
        <a href="../index.php" class="admin-nav-link"><i class="ph ph-arrow-left"></i> Retour au site</a>
    </nav>
</aside>

<main class="admin-main">
    <header class="admin-header"><h1>Gestion des Stocks</h1></header>

    <?php if (isset($_GET['updated'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">Stock mis à jour avec succès.</div>
    <?php endif; ?>

    <?php $critiques = array_filter($products, fn($p) => $p['stock'] < 5); ?>
    <?php if (!empty($critiques)): ?>
    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="ph-fill ph-warning" style="font-size: 1.5rem; color: #f39c12;"></i>
        <div>
            <strong><?= count($critiques) ?> produit(s) en stock critique</strong> (moins de 5 unités). Pensez à réapprovisionner.
        </div>
    </div>
    <?php endif; ?>

    <div class="admin-card">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Espèce</th>
                        <th>Stock actuel</th>
                        <th>Niveau</th>
                        <th>Modifier le stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p):
                        $level = $p['stock'] === 0 ? 'rupture' : ($p['stock'] < 5 ? 'critique' : ($p['stock'] < 20 ? 'moyen' : 'ok'));
                        $level_cfg = [
                            'rupture'  => ['label' => 'Rupture',   'color' => 'var(--color-error)',   'bar' => 0],
                            'critique' => ['label' => 'Critique',  'color' => '#e67e22',              'bar' => 15],
                            'moyen'    => ['label' => 'Moyen',     'color' => '#f39c12',              'bar' => 50],
                            'ok'       => ['label' => 'OK',        'color' => 'var(--color-success)', 'bar' => 100],
                        ][$level];
                    ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <img src="<?= htmlspecialchars($p['image_principale']) ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: var(--radius-sm);" onerror="this.src='https://placehold.co/40x40/F5F0E8/7A9E7E?text=?'">
                                <strong><?= htmlspecialchars($p['nom']) ?></strong>
                            </div>
                        </td>
                        <td><?= ucfirst($p['espece']) ?></td>
                        <td style="font-size: 1.2rem; font-weight: 700; color: <?= $level_cfg['color'] ?>;"><?= $p['stock'] ?></td>
                        <td>
                            <div style="min-width: 120px;">
                                <div style="height: 6px; background: var(--color-border); border-radius: 3px; overflow: hidden;">
                                    <div style="height: 100%; width: <?= $level_cfg['bar'] ?>%; background: <?= $level_cfg['color'] ?>; border-radius: 3px; transition: width 0.3s;"></div>
                                </div>
                                <span style="font-size: 0.8rem; font-weight: 600; color: <?= $level_cfg['color'] ?>; margin-top: 0.25rem; display: block;"><?= $level_cfg['label'] ?></span>
                            </div>
                        </td>
                        <td>
                            <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="number" name="stock" value="<?= $p['stock'] ?>" min="0" style="width: 70px; padding: 0.35rem 0.5rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                                <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Sauver</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
