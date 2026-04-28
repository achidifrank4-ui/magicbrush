<?php
require_once 'includes/db.php';

// Sécurité : ID valide uniquement
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: catalogue.php'); exit; }

// Récupérer le produit
$stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie_nom FROM products p LEFT JOIN categories c ON p.categorie_id = c.id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) { header('Location: catalogue.php'); exit; }

// Récupérer les images secondaires
$img_stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :id ORDER BY ordre ASC");
$img_stmt->execute([':id' => $id]);
$images = $img_stmt->fetchAll();

// Récupérer la note moyenne et le nombre d'avis
$review_stmt = $pdo->prepare("SELECT AVG(note) as avg_note, COUNT(*) as count FROM reviews WHERE product_id = :id");
$review_stmt->execute([':id' => $id]);
$review_data = $review_stmt->fetch();
$avg_note = round($review_data['avg_note'] ?? 0, 1);
$review_count = $review_data['count'];

// Produits similaires (même espèce)
$similar_stmt = $pdo->prepare("SELECT * FROM products WHERE espece = :espece AND id != :id LIMIT 3");
$similar_stmt->execute([':espece' => $product['espece'], ':id' => $id]);
$similar_products = $similar_stmt->fetchAll();

$page_title = htmlspecialchars($product['nom']) . ' — Magic Brush';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div style="background: var(--color-bg); padding: 1rem 0;">
    <div class="container" style="font-size: 0.9rem; color: var(--color-text-light);">
        <a href="index.php">Accueil</a> &rsaquo;
        <a href="catalogue.php?espece=<?= $product['espece'] ?>"><?= ucfirst($product['espece']) ?>s</a> &rsaquo;
        <span style="color: var(--color-text);"><?= htmlspecialchars($product['nom']) ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: var(--spacing-xl);">

            <!-- Galerie -->
            <div style="flex: 1; min-width: 300px;">
                <div style="border-radius: var(--radius-lg); overflow: hidden; background: var(--color-bg); margin-bottom: 1rem;">
                    <img src="<?= htmlspecialchars($product['image_principale']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>"
                         id="mainImage" style="width: 100%; aspect-ratio: 1/1; object-fit: cover;"
                         onerror="this.src='https://placehold.co/600x600/F5F0E8/7A9E7E?text=<?= urlencode($product['nom']) ?>'">
                </div>
                <?php if (!empty($images)): ?>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <?php foreach ($images as $img): ?>
                    <div onclick="document.getElementById('mainImage').src='<?= htmlspecialchars($img['url']) ?>'"
                         style="width: 72px; height: 72px; border-radius: var(--radius-sm); overflow: hidden; cursor: pointer; border: 2px solid var(--color-border);">
                        <img src="<?= htmlspecialchars($img['url']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Infos produit -->
            <div style="flex: 1; min-width: 300px;">
                <div style="font-size: 0.85rem; color: var(--color-text-light); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">
                    <?= htmlspecialchars($product['categorie_nom'] ?? ucfirst($product['espece'])) ?>
                </div>
                <h1 style="margin-bottom: 0.75rem;"><?= htmlspecialchars($product['nom']) ?></h1>

                <!-- Note -->
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="color: #f39c12; font-size: 1.1rem; display: flex;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?= $i <= $avg_note ? 'ph-fill ph-star' : ($i - 0.5 <= $avg_note ? 'ph-fill ph-star-half' : 'ph ph-star') ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <a href="#avis" style="color: var(--color-text-light); font-size: 0.9rem; text-decoration: underline;"><?= $review_count ?> avis</a>
                </div>

                <!-- Prix -->
                <div style="margin-bottom: 1.5rem;">
                    <?php if ($product['prix_barre']): ?>
                    <span style="text-decoration: line-through; color: var(--color-text-light); font-size: 1.1rem; margin-right: 0.5rem;"><?= number_format($product['prix_barre'], 2, ',', ' ') ?> €</span>
                    <?php endif; ?>
                    <span style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 700; color: var(--color-primary);"><?= number_format($product['prix'], 2, ',', ' ') ?> €</span>
                </div>

                <p style="color: var(--color-text-light); line-height: 1.8; margin-bottom: 2rem;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                <!-- Quantité -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Quantité</label>
                    <div style="display: inline-flex; align-items: center; border: 1px solid var(--color-border); border-radius: var(--radius-full);">
                        <button onclick="updateQty(-1)" style="background: none; border: none; width: 36px; height: 36px; cursor: pointer; font-size: 1.2rem;">-</button>
                        <input type="number" id="qty" value="1" min="1" max="<?= $product['stock'] ?>" style="width: 44px; text-align: center; border: none; font-family: var(--font-body); font-weight: 600; outline: none;">
                        <button onclick="updateQty(1)" style="background: none; border: none; width: 36px; height: 36px; cursor: pointer; font-size: 1.2rem;">+</button>
                    </div>
                </div>

                <button id="addToCartBtn" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-bottom: 1rem;"
                        <?= $product['stock'] <= 0 ? 'disabled style="opacity:0.5;"' : '' ?>
                        data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['nom']) ?>"
                        data-price="<?= $product['prix'] ?>" data-img="<?= htmlspecialchars($product['image_principale']) ?>">
                    <i class="ph ph-shopping-cart" style="margin-right: 0.5rem;"></i>
                    <?= $product['stock'] > 0 ? 'Ajouter au panier' : 'Rupture de stock' ?>
                </button>

                <div style="display: flex; gap: 1.5rem; color: var(--color-text-light); font-size: 0.9rem;">
                    <span style="display: flex; align-items: center; gap: 0.4rem;">
                        <i class="ph ph-check-circle" style="color: <?= $product['stock'] > 0 ? 'var(--color-success)' : 'var(--color-error)' ?>;"></i>
                        <?= $product['stock'] > 0 ? "En stock ({$product['stock']})" : 'Rupture' ?>
                    </span>
                    <span style="display: flex; align-items: center; gap: 0.4rem;"><i class="ph ph-truck"></i> Livraison 48h</span>
                    <span style="display: flex; align-items: center; gap: 0.4rem;"><i class="ph ph-shield-check"></i> Paiement sécurisé</span>
                </div>
            </div>
        </div>

        <!-- Section Avis -->
        <div id="avis" style="margin-top: var(--spacing-xl);">
            <h2 style="margin-bottom: 1.5rem;">Avis clients</h2>
            <?php
            $avis_stmt = $pdo->prepare("SELECT r.*, u.prenom, u.nom FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :id ORDER BY r.created_at DESC LIMIT 5");
            $avis_stmt->execute([':id' => $id]);
            $avis = $avis_stmt->fetchAll();
            ?>
            <?php if (empty($avis)): ?>
            <p style="color: var(--color-text-light);">Aucun avis pour le moment. Soyez le premier à partager votre expérience !</p>
            <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem; max-width: 700px;">
                <?php foreach ($avis as $a): ?>
                <div style="background: var(--color-white); padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <strong><?= htmlspecialchars($a['prenom'] . ' ' . substr($a['nom'], 0, 1) . '.') ?></strong>
                        <span style="color: #f39c12;"><?= str_repeat('★', $a['note']) . str_repeat('☆', 5 - $a['note']) ?></span>
                    </div>
                    <p style="color: var(--color-text-light);"><?= htmlspecialchars($a['commentaire']) ?></p>
                    <small style="color: var(--color-border);"><?= date('d/m/Y', strtotime($a['created_at'])) ?></small>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Produits similaires -->
        <?php if (!empty($similar_products)): ?>
        <div style="margin-top: var(--spacing-xl);">
            <h2 style="margin-bottom: 1.5rem;">Vous aimerez aussi</h2>
            <div class="product-grid">
                <?php foreach ($similar_products as $sp): ?>
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($sp['image_principale']) ?>" alt="<?= htmlspecialchars($sp['nom']) ?>"
                             onerror="this.src='https://placehold.co/400x400/F5F0E8/7A9E7E?text=<?= urlencode($sp['nom']) ?>'">
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= ucfirst($sp['espece']) ?></div>
                        <h3 class="product-title"><a href="produit.php?id=<?= $sp['id'] ?>"><?= htmlspecialchars($sp['nom']) ?></a></h3>
                        <div class="product-price"><?= number_format($sp['prix'], 2, ',', ' ') ?> €</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function updateQty(change) {
    const input = document.getElementById('qty');
    const max = parseInt(input.max) || 99;
    let val = parseInt(input.value) + change;
    input.value = Math.max(1, Math.min(max, val));
}

document.getElementById('addToCartBtn')?.addEventListener('click', function() {
    const qty = parseInt(document.getElementById('qty').value);
    const product = {
        id: this.dataset.id, name: this.dataset.name,
        price: parseFloat(this.dataset.price), image: this.dataset.img, quantity: qty
    };
    let cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    const idx = cart.findIndex(i => i.id === product.id);
    if (idx > -1) cart[idx].quantity += qty; else cart.push(product);
    localStorage.setItem('magicBrushCart', JSON.stringify(cart));
    updateCartCounter();
    this.innerHTML = '<i class="ph ph-check"></i> Ajouté !';
    this.style.backgroundColor = 'var(--color-success)';
    setTimeout(() => { this.innerHTML = '<i class="ph ph-shopping-cart"></i> Ajouter au panier'; this.style.backgroundColor = ''; }, 2500);
});
</script>

<?php require_once 'includes/footer.php'; ?>
