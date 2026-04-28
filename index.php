<?php
$page_title = 'Magic Brush — L\'excellence du toilettage pour vos animaux';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Récupérer les 3 derniers produits ajoutés
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 3");
$featured_products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <!-- Pour un vrai site, on utiliserait une image d'un beau chien ou chat toiletté -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); z-index: 0;"></div>
    
    <div class="container hero-content">
        <h1>Des moments de complicité avec votre animal</h1>
        <p>Découvrez notre gamme exclusive de brosses de toilettage premium pour chiens et chats. Pensées pour leur bien-être, conçues pour votre confort.</p>
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="catalogue.php?espece=chien" class="btn btn-secondary">Pour les chiens</a>
            <a href="catalogue.php?espece=chat" class="btn btn-outline" style="color: white; border-color: white;">Pour les chats</a>
        </div>
    </div>
</section>

<!-- Catégories -->
<section class="section" style="background-color: var(--color-white);">
    <div class="container">
        <div style="text-align: center; margin-bottom: var(--spacing-lg);">
            <h2 style="color: var(--color-primary);">Trouvez la brosse idéale</h2>
            <p style="color: var(--color-text-light);">Nos outils s'adaptent à chaque type de pelage</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--spacing-md);">
            <!-- Card Chien -->
            <div style="border-radius: var(--radius-lg); overflow: hidden; position: relative; height: 300px; background-color: var(--color-bg); display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem;">
                <div style="position: relative; z-index: 2;">
                    <i class="ph-fill ph-dog" style="font-size: 4rem; color: var(--color-secondary); margin-bottom: 1rem;"></i>
                    <h3>Chiens</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--color-text-light);">Brosses pour poils courts, longs, ou épais.</p>
                    <a href="catalogue.php?espece=chien" class="btn btn-outline">Voir la sélection</a>
                </div>
            </div>
            
            <!-- Card Chat -->
            <div style="border-radius: var(--radius-lg); overflow: hidden; position: relative; height: 300px; background-color: var(--color-bg); display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem;">
                <div style="position: relative; z-index: 2;">
                    <i class="ph-fill ph-cat" style="font-size: 4rem; color: var(--color-primary); margin-bottom: 1rem;"></i>
                    <h3>Chats</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--color-text-light);">Gants de massage et brosses anti-poils morts.</p>
                    <a href="catalogue.php?espece=chat" class="btn btn-outline">Voir la sélection</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Produits Vedettes -->
<section class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: var(--spacing-lg);">
            <div>
                <h2>Nos Best-Sellers</h2>
                <p style="color: var(--color-text-light);">Les préférés de nos clients et de leurs compagnons.</p>
            </div>
            <a href="catalogue.php" style="color: var(--color-secondary); font-weight: 600; text-decoration: underline;">Tout voir</a>
        </div>
        
        <div class="product-grid">
            <?php foreach ($featured_products as $index => $product): ?>
            <div class="product-card">
                <?php if ($index === 0): ?>
                <span class="product-badge">Bestseller</span>
                <?php elseif ($index === 2): ?>
                <span class="product-badge" style="background-color: var(--color-primary);">Nouveau</span>
                <?php endif; ?>
                
                <div class="product-img-wrapper">
                    <img src="<?= htmlspecialchars($product['image_principale']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" onerror="this.src='https://placehold.co/400x400/F5F0E8/7A9E7E?text=Magic+Brush'">
                </div>
                <div class="product-info">
                    <div class="product-category"><?= htmlspecialchars(ucfirst($product['espece'])) ?></div>
                    <h3 class="product-title"><a href="produit.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['nom']) ?></a></h3>
                    <div style="display: flex; justify-content: center; gap: 0.2rem; color: #f39c12; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star-half"></i>
                    </div>
                    <div class="product-price"><?= number_format($product['prix'], 2, ',', ' ') ?> €</div>
                    <button class="btn btn-primary js-add-to-cart" style="width: 100%; margin-top: 1rem; border-radius: var(--radius-md);" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['nom']) ?>" data-price="<?= $product['prix'] ?>" data-img="<?= htmlspecialchars($product['image_principale']) ?>">
                        Ajouter au panier
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section Avantages -->
<section class="section" style="background-color: var(--color-bg);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-lg); text-align: center;">
            <div>
                <i class="ph ph-shield-check" style="font-size: 3rem; color: var(--color-secondary); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.2rem;">Qualité Premium</h3>
                <p style="color: var(--color-text-light); font-size: 0.9rem;">Matériaux durables et sûrs pour votre animal.</p>
            </div>
            <div>
                <i class="ph ph-truck" style="font-size: 3rem; color: var(--color-secondary); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.2rem;">Livraison Rapide</h3>
                <p style="color: var(--color-text-light); font-size: 0.9rem;">Expédition sous 24h, livraison en 2 à 3 jours.</p>
            </div>
            <div>
                <i class="ph ph-hand-heart" style="font-size: 3rem; color: var(--color-secondary); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.2rem;">Confort garanti</h3>
                <p style="color: var(--color-text-light); font-size: 0.9rem;">Des designs ergonomiques pour un toilettage en douceur.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
