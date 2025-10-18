// Shopping Cart System for Il Napolitano - v2.0 with hover tooltip
class ShoppingCart {
    constructor() {
        this.cart = this.loadCart();
        this.initializeButtons();
        this.updateCartBadge();
    }

    loadCart() {
        const saved = localStorage.getItem('pizzaCart');
        return saved ? JSON.parse(saved) : [];
    }

    saveCart() {
        localStorage.setItem('pizzaCart', JSON.stringify(this.cart));
    }

    initializeButtons() {
        const buttons = document.querySelectorAll('.button[data-price]');
        buttons.forEach(button => {
            button.addEventListener('click', (e) => this.addToCart(e));
            // Check if product is already in cart
            const productId = parseInt(button.value);
            if (this.isInCart(productId)) {
                this.markAsAdded(button);
            }
        });

        // Cart click handler
        const cartIcon = document.getElementById('cart');
        const cartBadge = document.getElementById('badge');
        if (cartIcon && cartBadge) {
            // Prevent default link behavior and handle click
            cartBadge.addEventListener('click', (e) => {
                e.preventDefault();
                this.goToCheckout();
            });
        }

        // Clear cart button handler
        const clearCartBtn = document.getElementById('clearCartBtn');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.clearCartWithConfirmation();
            });
        }
    }

    addToCart(event) {
        const button = event.currentTarget;
        const productId = parseInt(button.value);
        const productPrice = parseFloat(button.getAttribute('data-price'));
        
        // Get product name from the figure caption
        const productBox = button.closest('.box');
        const productName = productBox.querySelector('h3').textContent;

        // Check if already in cart
        if (this.isInCart(productId)) {
            this.showNotification('Este producto ya está en tu carrito', 'warning');
            return;
        }

        // Add to cart
        this.cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: 1
        });

        this.saveCart();
        this.updateCartBadge();
        this.markAsAdded(button);
        const subtotal = this.calculateSubtotal();
        this.showNotification(`Producto agregado al carrito<br><strong>Subtotal: $${subtotal.toFixed(2)}</strong>`, 'success');
    }

    isInCart(productId) {
        return this.cart.some(item => item.id === productId);
    }

    markAsAdded(button) {
        button.style.backgroundColor = '#27ae60';
        button.innerHTML = 'Agregado <i class="fa-solid fa-check"></i>';
        button.disabled = true;
    }

    updateCartBadge() {
        const badge = document.getElementById('badge');
        const tooltip = document.querySelector('.cart-tooltip');
        const subtotalElement = document.getElementById('cartSubtotal');
        
        if (badge) {
            const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
            const subtotal = this.calculateSubtotal();
            
            badge.setAttribute('value', totalItems);
            
            // Update subtotal in tooltip
            if (subtotalElement) {
                subtotalElement.textContent = `Subtotal: $${subtotal.toFixed(2)}`;
            }
            
            if (totalItems > 0) {
                badge.classList.add('has-items');
                if (tooltip) {
                    tooltip.style.display = 'block';
                }
            } else {
                badge.classList.remove('has-items');
                if (tooltip) {
                    tooltip.style.display = 'none';
                }
            }
        }
    }

    goToCheckout() {
        if (this.cart.length === 0) {
            this.showNotification('Tu carrito está vacío', 'warning');
            return;
        }
        window.location.href = 'checkout.php';
    }

    showNotification(message, type = 'info') {
        // Remove existing notification
        const existing = document.querySelector('.cart-notification');
        if (existing) {
            existing.remove();
        }

        // Create notification
        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => notification.classList.add('show'), 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    clearCart() {
        this.cart = [];
        this.saveCart();
        this.updateCartBadge();
        this.resetProductButtons();
    }

    clearCartWithConfirmation() {
        if (this.cart.length === 0) {
            this.showNotification('Tu carrito ya está vacío', 'info');
            return;
        }

        const subtotal = this.calculateSubtotal();
        this.showSimpleCartModal(subtotal);
    }

    showSimpleCartModal(subtotal) {
        // Remove existing modal if any
        const existingModal = document.getElementById('simpleCartModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Create modal HTML
        const modalHTML = `
            <div id="simpleCartModal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                font-family: 'Work Sans', sans-serif;
            ">
                <div style="
                    background: linear-gradient(135deg, #fff5e6 0%, #fff 50%, #fff5e6 100%);
                    border-radius: 12px;
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
                    max-width: 450px;
                    width: 90%;
                    overflow: hidden;
                    border: 3px solid #d4af37;
                ">
                    <!-- Header with Italian flag accent -->
                    <div style="
                        background: linear-gradient(90deg, #009246, #ce2b37);
                        height: 6px;
                    "></div>
                    <div style="
                        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                        color: white;
                        padding: 1rem 1.5rem;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-size: 1.5rem;">🍕</span>
                            <h3 style="margin: 0; font-family: 'Oswald', sans-serif; font-size: 1.2rem; text-transform: uppercase;">Il Napolitano</h3>
                        </div>
                        <button onclick="closeCartModal()" style="
                            background: none;
                            border: none;
                            color: white;
                            font-size: 1.8rem;
                            cursor: pointer;
                            padding: 0;
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                        ">&times;</button>
                    </div>
                    
                    <!-- Body -->
                    <div style="padding: 2rem 1.5rem; text-align: center;">
                        <div style="margin-bottom: 1rem;">
                            <i class="fa-solid fa-cart-arrow-down" style="font-size: 3rem; color: #ce2b37;"></i>
                        </div>
                        <h4 style="
                            font-family: 'Oswald', sans-serif;
                            font-size: 1.5rem;
                            color: #2c3e50;
                            margin: 0 0 0.75rem 0;
                            text-transform: uppercase;
                        ">¿Vaciar tu orden?</h4>
                        <p style="
                            font-size: 1rem;
                            color: #555;
                            margin-bottom: 1.5rem;
                            line-height: 1.4;
                        ">¿Estás seguro de que quieres vaciar tu carrito?</p>
                        
                        <div style="
                            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
                            border: 2px solid #d4af37;
                            border-radius: 6px;
                            padding: 1rem;
                            margin-bottom: 1rem;
                            display: flex;
                            justify-content: space-between;
                        ">
                            <span style="font-family: 'Oswald', sans-serif; font-weight: 600; color: #2c3e50; text-transform: uppercase;">Subtotal actual:</span>
                            <span style="font-family: 'Oswald', sans-serif; font-size: 1.25rem; font-weight: 700; color: #d4af37;">$${subtotal.toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div style="
                        padding: 1rem 1.5rem 1.5rem;
                        display: flex;
                        gap: 1rem;
                        justify-content: center;
                        background: linear-gradient(to bottom, transparent 0%, rgba(212, 175, 55, 0.05) 100%);
                    ">
                        <button onclick="closeCartModal()" style="
                            background: white;
                            color: #6c757d;
                            border: 2px solid #dee2e6;
                            border-radius: 6px;
                            padding: 0.75rem 1.5rem;
                            font-family: 'Oswald', sans-serif;
                            font-weight: 600;
                            cursor: pointer;
                            text-transform: uppercase;
                            font-size: 1rem;
                            flex: 1;
                            max-width: 180px;
                        ">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </button>
                        <button onclick="confirmClearCart()" style="
                            background: linear-gradient(135deg, #ce2b37 0%, #e74c3c 100%);
                            color: white;
                            border: 2px solid #ce2b37;
                            border-radius: 6px;
                            padding: 0.75rem 1.5rem;
                            font-family: 'Oswald', sans-serif;
                            font-weight: 600;
                            cursor: pointer;
                            text-transform: uppercase;
                            font-size: 1rem;
                            flex: 1;
                            max-width: 180px;
                        ">
                            <i class="fa-solid fa-trash-can"></i> Sí, vaciar carrito
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Add to page
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        document.body.style.overflow = 'hidden';
        
        // Store reference to this cart instance
        window.currentCartInstance = this;
    }

    calculateSubtotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    resetProductButtons() {
        const buttons = document.querySelectorAll('.button[data-price]');
        buttons.forEach(button => {
            button.style.backgroundColor = '';
            button.innerHTML = 'Añadir al carrito <i class="fa-solid fa-cart-shopping fa-lg"></i>';
            button.disabled = false;
        });
    }
}

// Global functions for cart modal
function closeCartModal() {
    const modal = document.getElementById('simpleCartModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

function confirmClearCart() {
    if (window.currentCartInstance) {
        window.currentCartInstance.clearCart();
        window.currentCartInstance.showNotification('Carrito vaciado exitosamente', 'success');
    }
    closeCartModal();
}

// Close cart modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCartModal();
    }
});

// Initialize cart when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const cart = new ShoppingCart();
    
    // Make cart globally accessible for debugging
    window.pizzaCart = cart;
});