// Checkout Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Load cart from localStorage
    const cart = JSON.parse(localStorage.getItem('pizzaCart') || '[]');
    
    // Constants
    const DELIVERY_FEE = 500;
    let discountAmount = 0;
    
    // Initialize checkout page
    initializeCheckout();
    
    function initializeCheckout() {
        if (cart.length === 0) {
            showEmptyCart();
            return;
        }
        
        displayOrderItems();
        calculateTotals();
        setupEventListeners();
    }
    
    function showEmptyCart() {
        const container = document.querySelector('.checkout-content');
        container.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Tu carrito está vacío</p>
                <a href="index.php" class="btn-back-home">
                    <i class="fas fa-arrow-left"></i> Volver al menú
                </a>
            </div>
        `;
    }
    
    function displayOrderItems() {
        const orderItemsContainer = document.getElementById('orderItems');
        
        if (!orderItemsContainer) return;
        
        orderItemsContainer.innerHTML = cart.map(item => `
            <div class="order-item" data-product-id="${item.id}">
                <div class="item-details">
                    <div class="item-name">${item.name}</div>
                    <div class="item-quantity">
                        Cantidad: 
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span class="qty">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    </div>
                </div>
                <div class="item-price">$${(item.price * item.quantity).toFixed(2)}</div>
            </div>
        `).join('');
    }
    
    function calculateTotals() {
        const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        const total = subtotal + DELIVERY_FEE - discountAmount;
        
        document.getElementById('summarySubtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('summaryDelivery').textContent = `$${DELIVERY_FEE.toFixed(2)}`;
        document.getElementById('summaryTotal').textContent = `$${total.toFixed(2)}`;
        
        if (discountAmount > 0) {
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('summaryDiscount').textContent = `-$${discountAmount.toFixed(2)}`;
        }
    }
    
    function setupEventListeners() {
        // Promo code
        const applyPromoBtn = document.getElementById('applyPromo');
        if (applyPromoBtn) {
            applyPromoBtn.addEventListener('click', applyPromoCode);
        }
        
        // Form submission
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', handleCheckoutSubmit);
        }
        
        // City selection - update delivery fee if needed
        const citySelect = document.getElementById('deliveryCity');
        if (citySelect) {
            citySelect.addEventListener('change', updateDeliveryFee);
        }
    }
    
    function applyPromoCode() {
        const promoInput = document.getElementById('promoCode');
        const code = promoInput.value.trim().toUpperCase();
        
        // Promo codes (you can expand this)
        const promoCodes = {
            'PIZZA10': { type: 'percent', value: 10 },
            'PRIMERAORDEN': { type: 'percent', value: 15 },
            'ENVIOGRATIS': { type: 'delivery', value: 0 },
            'DESCUENTO500': { type: 'fixed', value: 500 }
        };
        
        if (promoCodes[code]) {
            const promo = promoCodes[code];
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            
            if (promo.type === 'percent') {
                discountAmount = subtotal * (promo.value / 100);
                showAlert(`¡Código aplicado! ${promo.value}% de descuento`, 'success');
            } else if (promo.type === 'fixed') {
                discountAmount = promo.value;
                showAlert(`¡Código aplicado! $${promo.value} de descuento`, 'success');
            } else if (promo.type === 'delivery') {
                // Handle free delivery
                showAlert('¡Envío gratis aplicado!', 'success');
            }
            
            calculateTotals();
            promoInput.value = '';
        } else {
            showAlert('Código de descuento inválido', 'error');
        }
    }
    
    function updateDeliveryFee() {
        // You can implement zone-based delivery fees here
        calculateTotals();
    }
    
    function handleCheckoutSubmit(e) {
        e.preventDefault();
        
        if (cart.length === 0) {
            showAlert('Tu carrito está vacío', 'error');
            return;
        }
        
        // Validate form
        const form = e.target;
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Prepare order data
        const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        const total = subtotal + DELIVERY_FEE - discountAmount;
        
        // Create order data object
        const orderData = {
            items: cart,
            subtotal: subtotal,
            deliveryFee: DELIVERY_FEE,
            discount: discountAmount,
            total: total
        };
        
        // Set the hidden field value BEFORE creating FormData
        document.getElementById('orderData').value = JSON.stringify(orderData);
        
        // Now create FormData with the populated hidden field
        const formData = new FormData(form);
        
        // Show loading - button is outside form, so find it by class
        const submitBtn = document.querySelector('.btn-confirm-order');
        if (!submitBtn) {
            console.error('Submit button not found!');
            showAlert('Error: Botón de envío no encontrado', 'error');
            return;
        }
        
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        submitBtn.disabled = true;
        
        // Submit order
        fetch('../src/handlers/process_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Log the response for debugging
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Get the text first to see what we're receiving
            return response.text().then(text => {
                console.log('Raw response:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response text:', text);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            console.log('Parsed data:', data);
            
            if (data.success) {
                // Clear cart
                localStorage.removeItem('pizzaCart');
                
                // Redirect to success page
                console.log('Redirecting to order_success.php?order_id=' + data.orderId);
                window.location.href = `order_success.php?order_id=${data.orderId}`;
            } else {
                showAlert(data.message || 'Error al procesar el pedido', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error de conexión. Por favor intenta nuevamente.', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
    
    function showAlert(message, type) {
        // Use simple browser alert instead of modal
        if (type === 'success') {
            alert('✅ ' + message);
        } else if (type === 'error') {
            alert('⚠️ ' + message);
        } else {
            alert('ℹ️ ' + message);
        }
    }
    
    // Global function for quantity updates
    window.updateQuantity = function(productId, change) {
        const itemIndex = cart.findIndex(item => item.id === productId);
        
        if (itemIndex === -1) return;
        
        cart[itemIndex].quantity += change;
        
        if (cart[itemIndex].quantity <= 0) {
            cart.splice(itemIndex, 1);
        }
        
        // Update localStorage
        localStorage.setItem('pizzaCart', JSON.stringify(cart));
        
        // Refresh display
        if (cart.length === 0) {
            showEmptyCart();
        } else {
            displayOrderItems();
            calculateTotals();
        }
    };
});
