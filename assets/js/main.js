// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    // Menu mobile toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            
            // Changer l'icône
            const icon = mobileMenuBtn.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('ph-list');
                icon.classList.add('ph-x');
            } else {
                icon.classList.remove('ph-x');
                icon.classList.add('ph-list');
            }
        });
    }

    // Gestion de l'ajout au panier via localStorage (Version Frontend pure avant intégration PHP/API)
    const addToCartButtons = document.querySelectorAll('.js-add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const product = {
                id: button.dataset.id,
                name: button.dataset.name,
                price: parseFloat(button.dataset.price),
                image: button.dataset.img,
                quantity: 1
            };
            
            addToCart(product);
            
            // Feedback visuel
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="ph ph-check"></i> Ajouté';
            button.style.backgroundColor = 'var(--color-success)';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.backgroundColor = ''; // Restore CSS class style
            }, 2000);
        });
    });
});

// Fonction utilitaire pour gérer le panier en LocalStorage en attendant le Backend
function addToCart(product) {
    let cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    
    const existingItemIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingItemIndex > -1) {
        cart[existingItemIndex].quantity += 1;
    } else {
        cart.push(product);
    }
    
    localStorage.setItem('magicBrushCart', JSON.stringify(cart));
    updateCartCounter();
}

function updateCartCounter() {
    const cart = JSON.parse(localStorage.getItem('magicBrushCart')) || [];
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    
    const countElement = document.getElementById('cartCount');
    if (countElement) {
        countElement.textContent = count;
    }
}

// Initialiser le compteur au chargement
document.addEventListener('DOMContentLoaded', updateCartCounter);
