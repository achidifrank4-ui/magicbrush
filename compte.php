<?php
// compte.php — Espace client
require_once 'config/config.php';
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=compte.php'); exit;
}

// Action : déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php'); exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos du client
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$user_stmt->execute([':id' => $user_id]);
$user = $user_stmt->fetch();

// Récupérer ses commandes
$orders_stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as nb_articles FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = :id GROUP BY o.id ORDER BY o.created_at DESC");
$orders_stmt->execute([':id' => $user_id]);
$orders = $orders_stmt->fetchAll();

$page_title = 'Mon Compte — Magic Brush';
require_once 'includes/header.php';

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

<div style="background: var(--color-bg); padding: var(--spacing-lg) 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-family: var(--font-heading); font-weight: 700;">
                <?= strtoupper(mb_substr($user['prenom'], 0, 1)) ?>
            </div>
            <div>
                <h1 style="margin: 0; font-size: 1.8rem;">Bonjour, <?= htmlspecialchars($user['prenom']) ?> !</h1>
                <p style="color: var(--color-text-light); margin: 0;"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <a href="?action=logout" class="btn btn-outline" style="margin-left: auto; font-size: 0.9rem;">
                <i class="ph ph-sign-out"></i> Déconnexion
            </a>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: var(--spacing-lg);">

            <!-- Sidebar Navigation Compte -->
            <aside style="flex: 0 0 220px;">
                <div style="background: var(--color-white); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden; position: sticky; top: 100px;">
                    <a href="compte.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; color: var(--color-primary); font-weight: 600; border-left: 3px solid var(--color-primary); background: rgba(122,158,126,0.05);">
                        <i class="ph ph-shopping-cart-simple"></i> Mes commandes
                    </a>
                    <a href="compte.php#profil" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; color: var(--color-text-light); border-left: 3px solid transparent;">
                        <i class="ph ph-user"></i> Mon profil
                    </a>
                    <a href="catalogue.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; color: var(--color-text-light); border-left: 3px solid transparent;">
                        <i class="ph ph-storefront"></i> Retour boutique
                    </a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/index.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; color: var(--color-secondary); font-weight: 600; border-left: 3px solid transparent;">
                        <i class="ph ph-gear"></i> Administration
                    </a>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Contenu principal -->
            <div style="flex: 1; min-width: 300px;">

                <!-- Mes Commandes -->
                <div style="background: var(--color-white); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); padding: 2rem; margin-bottom: 2rem;">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.4rem;">Mes Commandes</h2>

                    <?php if (empty($orders)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--color-text-light);">
                        <i class="ph ph-shopping-cart-simple" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a href="catalogue.php" class="btn btn-primary" style="margin-top: 1rem;">Découvrir notre catalogue</a>
                    </div>
                    <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--color-border);">
                                    <th style="text-align: left; padding: 0.75rem 1rem; color: var(--color-text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Commande</th>
                                    <th style="text-align: left; padding: 0.75rem 1rem; color: var(--color-text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Articles</th>
                                    <th style="text-align: left; padding: 0.75rem 1rem; color: var(--color-text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total</th>
                                    <th style="text-align: left; padding: 0.75rem 1rem; color: var(--color-text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                                    <th style="text-align: left; padding: 0.75rem 1rem; color: var(--color-text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <?php $badge = statutBadge($order['statut']); ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: 1rem;"><strong>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                    <td style="padding: 1rem; color: var(--color-text-light);"><?= $order['nb_articles'] ?> article(s)</td>
                                    <td style="padding: 1rem;"><strong><?= number_format($order['total'], 2, ',', ' ') ?> €</strong></td>
                                    <td style="padding: 1rem;">
                                        <span style="background: <?= $badge['color'] ?>22; color: <?= $badge['color'] ?>; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                            <?= $badge['label'] ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-light); font-size: 0.9rem;"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Mon Profil -->
                <div id="profil" style="background: var(--color-white); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); padding: 2rem;">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.4rem;">Mon Profil</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Prénom</label>
                            <div style="padding: 0.75rem 1rem; background: var(--color-bg); border-radius: var(--radius-sm); font-weight: 500;"><?= htmlspecialchars($user['prenom']) ?></div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Nom</label>
                            <div style="padding: 0.75rem 1rem; background: var(--color-bg); border-radius: var(--radius-sm); font-weight: 500;"><?= htmlspecialchars($user['nom']) ?></div>
                        </div>
                        <div style="grid-column: 1/-1;">
                            <label style="display: block; font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</label>
                            <div style="padding: 0.75rem 1rem; background: var(--color-bg); border-radius: var(--radius-sm); font-weight: 500;"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Membre depuis</label>
                            <div style="padding: 0.75rem 1rem; background: var(--color-bg); border-radius: var(--radius-sm);"><?= date('F Y', strtotime($user['created_at'])) ?></div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Rôle</label>
                            <div style="padding: 0.75rem 1rem; background: var(--color-bg); border-radius: var(--radius-sm);"><?= $user['role'] === 'admin' ? '👑 Administrateur' : '👤 Client' ?></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
