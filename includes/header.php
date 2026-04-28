<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$page_title   = $page_title ?? 'Magic Brush — Brosses de toilettage premium';
$current_page = basename($_SERVER['PHP_SELF']);

// Compteur panier depuis localStorage (mis à jour par JS)
$cart_count = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="Découvrez notre sélection de brosses de toilettage premium pour chiens et chats. Offrez à votre animal un pelage soyeux et en pleine santé.">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="index.php" class="brand-logo">
                <?php if (file_exists(__DIR__ . '/../assets/img/logo-VosSC8x9.png')): ?>
                <img src="assets/img/logo-VosSC8x9.png" alt="Magic Brush" style="height: 40px; width: auto;">
                <?php else: ?>
                <i class="ph-fill ph-paw-print"></i> Magic Brush
                <?php endif; ?>
            </a>

            <ul class="nav-links" id="navLinks">
                <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Accueil</a></li>
                <li><a href="catalogue.php?espece=chien" class="<?= ($current_page == 'catalogue.php' && ($_GET['espece'] ?? '') == 'chien') ? 'active' : '' ?>">Chiens</a></li>
                <li><a href="catalogue.php?espece=chat" class="<?= ($current_page == 'catalogue.php' && ($_GET['espece'] ?? '') == 'chat') ? 'active' : '' ?>">Chats</a></li>
                <li><a href="catalogue.php" class="<?= ($current_page == 'catalogue.php' && empty($_GET['espece'])) ? 'active' : '' ?>">Tout le catalogue</a></li>
            </ul>

            <div class="nav-icons">
                <a href="compte.php" aria-label="Mon compte">
                    <i class="ph ph-user"></i>
                </a>
                <a href="panier.php" aria-label="Mon panier" class="cart-icon">
                    <i class="ph ph-shopping-bag"></i>
                    <span class="cart-count" id="cartCount"><?= $cart_count ?></span>
                </a>
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </nav>
    </div>
</header>
<main>
