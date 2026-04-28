<?php
$page_title = 'Commande confirmée — Magic Brush';
require_once 'includes/header.php';
require_once 'includes/db.php';

$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
$order = null;
if ($order_id) {
    $stmt = $pdo->prepare("SELECT o.*, GROUP_CONCAT(p.nom SEPARATOR ', ') as produits FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id LEFT JOIN products p ON oi.product_id = p.id WHERE o.id = :id GROUP BY o.id");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch();
}
?>
<section class="section" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="text-align: center; max-width: 600px; margin: 0 auto;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: var(--shadow-lg);">
            <i class="ph-fill ph-check" style="font-size: 2.5rem; color: white;"></i>
        </div>
        <h1 style="color: var(--color-primary); margin-bottom: 1rem;">Merci pour votre commande !</h1>
        <?php if ($order): ?>
        <p style="color: var(--color-text-light); margin-bottom: 0.5rem;">
            Votre commande <strong>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong> a été enregistrée avec succès.
        </p>
        <p style="color: var(--color-text-light); margin-bottom: 2rem;">
            Un email de confirmation vous sera envoyé à l'adresse renseignée.
        </p>
        <div style="background: var(--color-bg); border-radius: var(--radius-md); padding: 1.5rem; text-align: left; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="color: var(--color-text-light);">Total payé</span>
                <strong><?= number_format($order['total'], 2, ',', ' ') ?> €</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--color-text-light);">Livraison à</span>
                <strong style="max-width: 60%; text-align: right;"><?= htmlspecialchars($order['adresse_livraison']) ?></strong>
            </div>
        </div>
        <?php else: ?>
        <p style="color: var(--color-text-light); margin-bottom: 2rem;">Votre commande a bien été enregistrée.</p>
        <?php endif; ?>
        <a href="catalogue.php" class="btn btn-primary" style="margin-right: 1rem;">Continuer les achats</a>
        <a href="compte.php" class="btn btn-outline">Mes commandes</a>
        <script>localStorage.removeItem('magicBrushCart'); updateCartCounter();</script>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
