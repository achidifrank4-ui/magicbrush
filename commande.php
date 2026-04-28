<?php
$page_title = 'Paiement Sécurisé — Magic Brush';
require_once 'includes/header.php';
?>

<div style="background-color: var(--color-bg); padding: var(--spacing-sm) 0;">
    <div class="container">
        <!-- Tunnel de commande progress -->
        <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; max-width: 600px; margin: 0 auto;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-primary); font-weight: 600;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</div>
                Panier
            </div>
            <div style="height: 2px; width: 50px; background-color: var(--color-primary);"></div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-primary); font-weight: 600;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</div>
                Livraison
            </div>
            <div style="height: 2px; width: 50px; background-color: var(--color-border);"></div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-text-light);">
                <div style="width: 24px; height: 24px; border-radius: 50%; border: 2px solid var(--color-border); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">3</div>
                Paiement
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <h1 style="text-align: center; margin-bottom: var(--spacing-lg);">Finalisez votre commande</h1>
        
        <div style="display: flex; flex-wrap: wrap; gap: var(--spacing-xl);">
            <!-- Formulaire de livraison -->
            <div style="flex: 2; min-width: 300px;">
                <div style="background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 2rem;">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Adresse de Livraison</h2>
                    
                    <form id="checkoutForm" action="api/place-order.php" method="POST">
                        <input type="hidden" name="cart_data" id="cartDataInput">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Prénom *</label>
                                <input type="text" name="prenom" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nom *</label>
                                <input type="text" name="nom" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email *</label>
                            <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Adresse *</label>
                            <input type="text" name="adresse" required placeholder="Ex: 123 rue de la Paix" style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 2rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Code Postal *</label>
                                <input type="text" name="cp" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Ville *</label>
                                <input type="text" name="ville" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body);">
                            </div>
                        </div>

                        <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1.5rem;">Mode de Paiement</h2>
                        
                        <div style="border: 1px solid var(--color-primary); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; background-color: rgba(122, 158, 126, 0.05);">
                            <input type="radio" name="payment_method" id="pay_card" value="stripe" checked style="width: 1.2rem; height: 1.2rem; accent-color: var(--color-primary);">
                            <label for="pay_card" style="font-weight: 600; cursor: pointer; flex: 1;">Carte Bancaire (Stripe)</label>
                            <i class="ph-fill ph-credit-card" style="font-size: 1.5rem; color: var(--color-text-light);"></i>
                        </div>
                        
                        <div style="background-color: var(--color-bg); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 2rem; border: 1px dashed var(--color-border); text-align: center; color: var(--color-text-light);">
                            <i class="ph ph-lock-key" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                            <p>Le formulaire de paiement sécurisé Stripe s'affichera ici.</p>
                        </div>
                        
                        <div id="checkoutError" style="display: none; background: #fdecea; color: #721c24; padding: 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1rem;"></div>

                        <button type="submit" id="checkoutSubmit" class="btn btn-primary" style="width: 100%; padding: 1.2rem; font-size: 1.2rem; font-weight: 600;">
                            Payer <span id="checkoutTotal">--</span> €
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Récapitulatif de commande -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 2rem; position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">Votre commande</h3>
                    
                    <div id="checkoutItemsContainer" style="margin-bottom: 1.5rem; max-height: 300px; overflow-y: auto;">
                        <!-- Injecté par JS -->
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--color-text-light);">
                        <span>Sous-total</span>
                        <span id="checkoutSubtotal">-- €</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--color-text-light);">
                        <span>Livraison</span>
                        <span>4.90 €</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin: 1.5rem 0 0; padding-top: 1.5rem; border-top: 1px solid var(--color-border); font-size: 1.2rem; font-weight: 700;">
                        <span>Total TTC</span>
                        <span style="color: var(--color-primary);" id="checkoutTotalDisplay">-- €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    
    if (cart.length === 0) {
        window.location.href = 'panier.php';
        return;
    }
    
    document.getElementById('cartDataInput').value = JSON.stringify(cart);
    
    const container = document.getElementById('checkoutItemsContainer');
    let total = 0;
    
    let html = '';
    cart.forEach(item => {
        total += item.price * item.quantity;
        html += `
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="position: relative;">
                        <img src="${item.image}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--color-border);">
                        <span style="position: absolute; top: -5px; right: -5px; background: var(--color-secondary); color: white; width: 18px; height: 18px; border-radius: 50%; font-size: 0.7rem; display: flex; align-items: center; justify-content: center;">${item.quantity}</span>
                    </div>
                    <div style="font-size: 0.9rem; font-weight: 500; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</div>
                </div>
                <div style="font-weight: 600;">${(item.price * item.quantity).toFixed(2)} €</div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    const finalTotal = total + 4.90;
    document.getElementById('checkoutSubtotal').textContent = total.toFixed(2) + ' €';
    document.getElementById('checkoutTotalDisplay').textContent = finalTotal.toFixed(2) + ' €';
    document.getElementById('checkoutTotal').textContent = finalTotal.toFixed(2);

    document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('checkoutSubmit');
        const err = document.getElementById('checkoutError');
        err.style.display = 'none';
        btn.disabled = true;
        btn.innerHTML = '<i class="ph ph-spinner-gap" style="animation:spin 1s linear infinite;margin-right:.5rem;"></i> Traitement...';

        try {
            const res = await fetch('api/place-order.php', { method: 'POST', body: new FormData(e.target) });
            const data = await res.json();
            if (data.success) {
                localStorage.removeItem('magicBrushCart');
                window.location.href = data.redirect;
            } else {
                err.style.display = 'block';
                err.innerHTML = '<i class="ph ph-warning-circle"></i> ' + data.message;
                btn.disabled = false;
                btn.innerHTML = 'Payer ' + finalTotal.toFixed(2) + ' €';
            }
        } catch(e) {
            err.style.display = 'block';
            err.textContent = 'Erreur réseau. Veuillez réessayer.';
            btn.disabled = false;
            btn.innerHTML = 'Payer ' + finalTotal.toFixed(2) + ' €';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
