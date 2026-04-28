<?php
// admin/produits.php — Gestion CRUD des produits
require_once '../config/config.php';
require_once '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php'); exit;
}

$message = '';
$edit_product = null;

// --- SUPPRESSION ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM products WHERE id = :id")->execute([':id' => (int)$_GET['delete']]);
    $message = '<p class="alert-success">Produit supprimé.</p>';
}

// --- CHARGEMENT POUR ÉDITION ---
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $s->execute([':id' => (int)$_GET['edit']]);
    $edit_product = $s->fetch();
}

// --- SAVE (Création ou Mise à jour) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        ':nom'         => trim($_POST['nom'] ?? ''),
        ':slug'        => preg_replace('/[^a-z0-9-]/', '-', strtolower(trim($_POST['nom'] ?? ''))),
        ':description' => trim($_POST['description'] ?? ''),
        ':prix'        => (float)($_POST['prix'] ?? 0),
        ':prix_barre'  => !empty($_POST['prix_barre']) ? (float)$_POST['prix_barre'] : null,
        ':stock'       => (int)($_POST['stock'] ?? 0),
        ':espece'      => $_POST['espece'] ?? 'universel',
        ':type_poil'   => $_POST['type_poil'] ?? 'tous',
        ':categorie_id'=> !empty($_POST['categorie_id']) ? (int)$_POST['categorie_id'] : null,
        ':image_principale' => trim($_POST['image_principale'] ?? ''),
    ];

    if (!empty($_POST['id'])) {
        $data[':id'] = (int)$_POST['id'];
        $sql = "UPDATE products SET nom=:nom, slug=:slug, description=:description, prix=:prix, prix_barre=:prix_barre, stock=:stock, espece=:espece, type_poil=:type_poil, categorie_id=:categorie_id, image_principale=:image_principale WHERE id=:id";
        $message = '<p class="alert-success">Produit mis à jour avec succès.</p>';
    } else {
        $sql = "INSERT INTO products (nom, slug, description, prix, prix_barre, stock, espece, type_poil, categorie_id, image_principale) VALUES (:nom, :slug, :description, :prix, :prix_barre, :stock, :espece, :type_poil, :categorie_id, :image_principale)";
        $message = '<p class="alert-success">Produit créé avec succès.</p>';
    }
    $pdo->prepare($sql)->execute($data);
    $edit_product = null;
    header('Location: produits.php?saved=1'); exit;
}

if (isset($_GET['saved'])) $message = '<p class="alert-success">Produit enregistré avec succès.</p>';

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$products   = $pdo->query("SELECT p.*, c.nom as cat_nom FROM products p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Produits — Admin Magic Brush</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="admin-brand"><i class="ph-fill ph-paw-print"></i> Magic Brush <span style="font-size:0.7rem;font-weight:400;opacity:0.7;">Back-office</span></div>
    <nav class="admin-nav">
        <a href="index.php" class="admin-nav-link"><i class="ph ph-squares-four"></i> Dashboard</a>
        <a href="produits.php" class="admin-nav-link active"><i class="ph ph-package"></i> Produits</a>
        <a href="commandes.php" class="admin-nav-link"><i class="ph ph-shopping-cart-simple"></i> Commandes</a>
        <a href="stocks.php" class="admin-nav-link"><i class="ph ph-chart-bar"></i> Stocks</a>
        <hr style="border-color:rgba(255,255,255,0.1);margin:1rem 0;">
        <a href="../index.php" class="admin-nav-link"><i class="ph ph-arrow-left"></i> Retour au site</a>
    </nav>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <h1>Gestion des Produits</h1>
        <a href="produits.php" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
            <i class="ph ph-plus"></i> Nouveau produit
        </a>
    </header>

    <?= $message ?>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">

        <!-- Formulaire Ajout / Édition -->
        <div class="admin-card" style="flex: 1; min-width: 350px;">
            <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;"><?= $edit_product ? 'Modifier le produit' : 'Ajouter un produit' ?></h2>
            <form method="POST" action="produits.php">
                <?php if ($edit_product): ?>
                <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                <?php endif; ?>

                <div class="admin-form-group">
                    <label>Nom du produit *</label>
                    <input type="text" name="nom" required value="<?= htmlspecialchars($edit_product['nom'] ?? '') ?>">
                </div>
                <div class="admin-form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="admin-form-group">
                        <label>Prix TTC (€) *</label>
                        <input type="number" name="prix" step="0.01" required value="<?= $edit_product['prix'] ?? '' ?>">
                    </div>
                    <div class="admin-form-group">
                        <label>Prix barré (€)</label>
                        <input type="number" name="prix_barre" step="0.01" value="<?= $edit_product['prix_barre'] ?? '' ?>">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="admin-form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" min="0" value="<?= $edit_product['stock'] ?? 0 ?>">
                    </div>
                    <div class="admin-form-group">
                        <label>Espèce *</label>
                        <select name="espece">
                            <?php foreach (['chien', 'chat', 'universel'] as $e): ?>
                            <option value="<?= $e ?>" <?= ($edit_product['espece'] ?? '') === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="admin-form-group">
                        <label>Type de poil</label>
                        <select name="type_poil">
                            <?php foreach (['tous', 'court', 'long', 'epais'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($edit_product['type_poil'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label>Catégorie</label>
                        <select name="categorie_id">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($edit_product['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>URL Image principale</label>
                    <input type="text" name="image_principale" value="<?= htmlspecialchars($edit_product['image_principale'] ?? '') ?>" placeholder="assets/img/uploads/...">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ph ph-floppy-disk"></i> <?= $edit_product ? 'Mettre à jour' : 'Créer le produit' ?>
                </button>
                <?php if ($edit_product): ?>
                <a href="produits.php" class="btn btn-outline" style="width: 100%; margin-top: 0.5rem; text-align: center;">Annuler</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des produits -->
        <div class="admin-card" style="flex: 2; min-width: 400px;">
            <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;"><?= count($products) ?> produit(s)</h2>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead><tr><th>Produit</th><th>Prix</th><th>Stock</th><th>Espèce</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="<?= htmlspecialchars($p['image_principale']) ?>" style="width: 40px; height: 40px; border-radius: var(--radius-sm); object-fit: cover;" onerror="this.src='https://placehold.co/40x40/F5F0E8/7A9E7E?text=?'">
                                    <div>
                                        <div style="font-weight: 500;"><?= htmlspecialchars($p['nom']) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--color-text-light);"><?= htmlspecialchars($p['cat_nom'] ?? '—') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><strong><?= number_format($p['prix'], 2, ',', ' ') ?> €</strong></td>
                            <td>
                                <span style="color: <?= $p['stock'] < 5 ? 'var(--color-error)' : 'var(--color-success)' ?>; font-weight: 600;">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td><?= ucfirst($p['espece']) ?></td>
                            <td style="white-space: nowrap;">
                                <a href="produits.php?edit=<?= $p['id'] ?>" style="color: var(--color-primary); margin-right: 0.75rem; font-size: 1.2rem;" title="Modifier"><i class="ph ph-pencil-simple"></i></a>
                                <a href="produits.php?delete=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')" style="color: var(--color-error); font-size: 1.2rem;" title="Supprimer"><i class="ph ph-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</body>
</html>
