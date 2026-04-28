<?php
// includes/footer.php
?>
</main> <!-- End of main content -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="brand-logo" style="color: var(--color-bg); margin-bottom: 1rem;">
                    <i class="ph-fill ph-paw-print"></i> Magic Brush
                </div>
                <p>Des brosses de toilettage premium pour choyer vos compagnons à quatre pattes. Qualité, douceur et efficacité.</p>
            </div>
            
            <div class="footer-col">
                <h3>Boutique</h3>
                <ul class="footer-links">
                    <li><a href="catalogue.php?espece=chien">Pour Chiens</a></li>
                    <li><a href="catalogue.php?espece=chat">Pour Chats</a></li>
                    <li><a href="catalogue.php">Tous les produits</a></li>
                    <li><a href="#">Promotions</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Informations</h3>
                <ul class="footer-links">
                    <li><a href="#">À propos de nous</a></li>
                    <li><a href="#">Conseils toilettage</a></li>
                    <li><a href="#">Livraison & Retours</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contact</h3>
                <ul class="footer-links">
                    <li><a href="mailto:hello@magicbrush.com">hello@magicbrush.com</a></li>
                    <li><a href="tel:+33123456789">+33 1 23 45 67 89</a></li>
                </ul>
                <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                    <a href="#" style="font-size: 1.5rem;"><i class="ph-fill ph-instagram-logo"></i></a>
                    <a href="#" style="font-size: 1.5rem;"><i class="ph-fill ph-facebook-logo"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Magic Brush. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- JS global -->
<script src="assets/js/main.js"></script>
<script src="assets/js/panier.js"></script>
</body>
</html>
