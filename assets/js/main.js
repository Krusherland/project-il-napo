// Shopping Cart System for Il Napolitano
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
        if (cartIcon) {
            cartIcon.addEventListener('click', () => this.goToCheckout());
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
        this.showNotification('Producto agregado al carrito', 'success');
        this.updateSubtotal();
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
        if (badge) {
            const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
            badge.setAttribute('value', totalItems);
            
            if (totalItems > 0) {
                badge.classList.add('has-items');
            } else {
                badge.classList.remove('has-items');
            }
        }
    }

    updateSubtotal() {
        const subtotalDiv = document.getElementById('subtotal');
        const totalAmountSpan = document.getElementById('totalamount');
        
        if (subtotalDiv && totalAmountSpan) {
            const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            totalAmountSpan.textContent = `$${total.toFixed(2)}`;
            subtotalDiv.style.display = this.cart.length > 0 ? 'block' : 'none';
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
        this.updateSubtotal();
    }
}

// Initialize cart when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const cart = new ShoppingCart();
    
    // Make cart globally accessible for debugging
    window.pizzaCart = cart;
});