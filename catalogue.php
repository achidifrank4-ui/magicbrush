<?php
$page_title = 'Catalogue Magic Brush — Toutes nos brosses';
require_once 'includes/header.php';
require_once 'includes/db.php';

$espece    = in_array($_GET['espece'] ?? '', ['chien', 'chat']) ? $_GET['espece'] : null;
$type_poil = in_array($_GET['type_poil'] ?? '', ['court', 'long', 'epais']) ? $_GET['type_poil'] : null;
$tri       = in_array($_GET['tri'] ?? '', ['prix_asc', 'prix_desc', 'nouveau']) ? $_GET['tri'] : 'popular';
$page      = max(1, (int)($_GET['page'] ?? 1));
$per_page  = 9;
$offset    = ($page - 1) * $per_page;

$titre_catalogue = "Notre Catalogue";
if ($espece === 'chien') $titre_catalogue = "Brosses pour Chiens";
if ($espece === 'chat')  $titre_catalogue = "Brosses pour Chats";

$where = []; $params = [];
if ($espece)    { $where[] = "espece = :espece";       $params[':espece']    = $espece; }
if ($type_poil) { $where[] = "type_poil = :type_poil"; $params[':type_poil'] = $type_poil; }
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";
$order_sql = match($tri) { 'prix_asc'=>"ORDER BY prix ASC", 'prix_desc'=>"ORDER BY prix DESC", 'nouveau'=>"ORDER BY created_at DESC", default=>"ORDER BY id ASC" };

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products $where_sql");
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

$stmt = $pdo->prepare("SELECT * FROM products $where_sql $order_sql LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

function filterUrl($overrides = []) {
    $p = array_filter(array_merge($_GET, $overrides));
    return '?' . http_build_query($p);
}
?>
<div style="background: var(--color-bg); padding: var(--spacing-lg) 0; text-align: center;">
    <div class="container">
        <h1><?= htmlspecialchars($titre_catalogue) ?></h1>
        <p style="color: var(--color-text-light); max-width: 600px; margin: 0 auto;">Découvrez notre collection de brosses premium, adaptées à chaque type de pelage.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: var(--spacing-lg);">

            <!-- Sidebar Filtres -->
            <aside style="flex: 1; min-width: 240px; max-width: 280px;">
                <form method="GET" action="catalogue.php">
                    <div style="background: var(--color-white); padding: var(--spacing-md); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); position: sticky; top: 100px;">
                        <h3 style="font-size: 1.2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 1rem;">Filtres</h3>
                        <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">Animal</h4>
                        <?php foreach (['' => 'Tous', 'chien' => 'Chiens', 'chat' => 'Chats'] as $val => $lbl): ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; cursor: pointer;">
                            <input type="radio" name="espece" value="<?= $val ?>" <?= ($espece ?? '') === $val ? 'checked' : '' ?> style="accent-color: var(--color-primary);"> <?= $lbl ?>
                        </label>
                        <?php endforeach; ?>

                        <h4 style="font-size: 1rem; margin: 1rem 0 0.5rem;">Type de poil</h4>
                        <?php foreach (['court' => 'Court', 'long' => 'Long', 'epais' => 'Épais'] as $val => $lbl): ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="type_poil" value="<?= $val ?>" <?= $type_poil === $val ? 'checked' : '' ?> style="accent-color: var(--color-primary);"> <?= $lbl ?>
                        </label>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Appliquer</button>
                        <?php if ($espece || $type_poil): ?>
                        <a href="catalogue.php" class="btn btn-outline" style="width: 100%; margin-top: 0.5rem; text-align: center;">Réinitialiser</a>
                        <?php endif; ?>
                    </div>
                </form>
            </aside>

            <!-- Grille Produits -->
            <div style="flex: 3; min-width: 300px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-md);">
                    <p><strong><?= $total_products ?></strong> produit(s) trouvé(s)</p>
                    <form method="GET">
                        <?php if ($espece): ?><input type="hidden" name="espece" value="<?= htmlspecialchars($espece) ?>"><?php endif; ?>
                        <select name="tri" onchange="this.form.submit()" style="padding: 0.5rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                            <option value="popular" <?= $tri==='popular'?'selected':'' ?>>Popularité</option>
                            <option value="prix_asc" <?= $tri==='prix_asc'?'selected':'' ?>>Prix croissant</option>
                            <option value="prix_desc" <?= $tri==='prix_desc'?'selected':'' ?>>Prix décroissant</option>
                            <option value="nouveau" <?= $tri==='nouveau'?'selected':'' ?>>Nouveautés</option>
                        </select>
                    </form>
                </div>

                <?php if (empty($products)): ?>
                <div style="text-align: center; padding: 4rem 0; color: var(--color-text-light);">
                    <i class="ph ph-magnifying-glass" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                    <h3>Aucun produit trouvé</h3>
                    <a href="catalogue.php" class="btn btn-outline" style="margin-top: 1.5rem;">Voir tout le catalogue</a>
                </div>
                <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $prod): ?>
                    <div class="product-card">
                        <?php if ($prod['prix_barre']): ?><span class="product-badge">Promo</span><?php endif; ?>
                        <div class="product-img-wrapper">
                            <img src="<?= htmlspecialchars($prod['image_principale']) ?>"
                                 alt="<?= htmlspecialchars($prod['nom']) ?>"
                                 onerror="this.src='https://placehold.co/400x400/F5F0E8/7A9E7E?text=<?= urlencode($prod['nom']) ?>'">
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= htmlspecialchars(ucfirst($prod['espece'])) ?></div>
                            <h3 class="product-title"><a href="produit.php?id=<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nom']) ?></a></h3>
                            <div>
                                <?php if ($prod['prix_barre']): ?>
                                <span style="text-decoration: line-through; color: var(--color-text-light); font-size: 0.9rem;"><?= number_format($prod['prix_barre'], 2, ',', ' ') ?> €</span>
                                <?php endif; ?>
                                <div class="product-price"><?= number_format($prod['prix'], 2, ',', ' ') ?> €</div>
                            </div>
                            <button class="btn btn-primary js-add-to-cart" style="width: 100%; margin-top: 0.75rem;"
                                data-id="<?= $prod['id'] ?>" data-name="<?= htmlspecialchars($prod['nom']) ?>"
                                data-price="<?= $prod['prix'] ?>" data-img="<?= htmlspecialchars($prod['image_principale']) ?>"
                                <?= $prod['stock'] <= 0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                <?= $prod['stock'] > 0 ? 'Ajouter au panier' : 'Rupture de stock' ?>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: var(--spacing-lg);">
                    <?php if ($page > 1): ?><a href="<?= filterUrl(['page' => $page-1]) ?>" class="btn btn-outline" style="padding: 0.5rem 1rem;"><i class="ph ph-caret-left"></i></a><?php endif; ?>
                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="<?= filterUrl(['page' => $p]) ?>" class="btn <?= $p===$page?'btn-primary':'btn-outline' ?>" style="padding: 0.5rem 1rem;"><?= $p ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?><a href="<?= filterUrl(['page' => $page+1]) ?>" class="btn btn-outline" style="padding: 0.5rem 1rem;"><i class="ph ph-caret-right"></i></a><?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
