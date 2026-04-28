<?php
$page_title = 'Mon Panier — Magic Brush';
require_once 'includes/header.php';
?>

<div style="background-color: var(--color-bg); padding: var(--spacing-lg) 0;">
    <div class="container" style="text-align: center;">
        <h1>Votre Panier</h1>
    </div>
</div>

<section class="section" style="min-height: 50vh;">
    <div class="container">
        
        <!-- Ce conteneur sera rempli dynamiquement via JS -->
        <div id="cartContainer" style="display: flex; flex-wrap: wrap; gap: var(--spacing-lg);">
            <!-- Contenu généré par JS -->
            <div style="width: 100%; text-align: center; padding: 4rem 0;">
                <i class="ph ph-spinner-gap" style="font-size: 3rem; animation: spin 1s linear infinite; color: var(--color-primary);"></i>
                <p style="margin-top: 1rem;">Chargement du panier...</p>
            </div>
            <style>
                @keyframes spin { 100% { transform: rotate(360deg); } }
            </style>
        </div>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    renderCart();
});

function renderCart() {
    const cartContainer = document.getElementById('cartContainer');
    let cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    
    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div style="width: 100%; text-align: center; padding: 4rem 0; background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                <i class="ph-light ph-shopping-cart" style="font-size: 4rem; color: var(--color-border); margin-bottom: 1rem;"></i>
                <h2>Votre panier est vide</h2>
                <p style="color: var(--color-text-light); margin: 1rem 0 2rem;">Découvrez notre sélection de brosses premium pour votre compagnon.</p>
                <a href="catalogue.php" class="btn btn-primary">Voir le catalogue</a>
            </div>
        `;
        return;
    }
    
    let total = 0;
    let itemsHtml = cart.map((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        return `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-bottom: 1px solid var(--color-border);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="${item.image}" alt="${item.name}" style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg);">
                    <div>
                        <h4 style="margin-bottom: 0.25rem;">${item.name}</h4>
                        <div style="color: var(--color-primary); font-weight: 600;">${item.price.toFixed(2)} €</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 2rem;">
                    <div style="display: inline-flex; align-items: center; border: 1px solid var(--color-border); border-radius: var(--radius-full); padding: 0.25rem;">
                        <button onclick="updateQuantity(${index}, -1)" style="background: none; border: none; width: 30px; height: 30px; cursor: pointer;">-</button>
                        <span style="width: 30px; text-align: center; font-weight: 600;">${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)" style="background: none; border: none; width: 30px; height: 30px; cursor: pointer;">+</button>
                    </div>
                    <div style="font-weight: 700; width: 80px; text-align: right;">${itemTotal.toFixed(2)} €</div>
                    <button onclick="removeItem(${index})" style="background: none; border: none; color: var(--color-error); cursor: pointer; font-size: 1.2rem;"><i class="ph ph-trash"></i></button>
                </div>
            </div>
        `;
    }).join('');
    
    cartContainer.innerHTML = `
        <div style="flex: 2; min-width: 300px; background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden;">
            <div style="padding: 1.5rem; background: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                <h3 style="margin: 0;">Articles (${cart.length})</h3>
            </div>
            ${itemsHtml}
        </div>
        
        <div style="flex: 1; min-width: 300px;">
            <div style="background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 2rem; position: sticky; top: 100px;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">Récapitulatif</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--color-text-light);">
                    <span>Sous-total</span>
                    <span>${total.toFixed(2)} €</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--color-text-light);">
                    <span>Livraison estimée</span>
                    <span>4.90 €</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin: 1.5rem 0; padding-top: 1.5rem; border-top: 1px solid var(--color-border); font-size: 1.2rem; font-weight: 700;">
                    <span>Total TTC</span>
                    <span style="color: var(--color-primary);">${(total + 4.90).toFixed(2)} €</span>
                </div>
                
                <a href="commande.php" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Valider la commande</a>
                
                <div style="margin-top: 1.5rem; text-align: center; color: var(--color-text-light); font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="ph ph-lock-key"></i> Paiement 100% sécurisé
                </div>
            </div>
        </div>
    `;
}

function updateQuantity(index, change) {
    let cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    if (cart[index]) {
        cart[index].quantity += change;
        if (cart[index].quantity < 1) cart[index].quantity = 1;
        localStorage.setItem('magicBrushCart', JSON.stringify(cart));
        renderCart();
        updateCartCounter();
    }
}

function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('magicBrushCart', JSON.stringify(cart));
    renderCart();
    updateCartCounter();
}
</script>

<?php require_once 'includes/footer.php'; ?>
