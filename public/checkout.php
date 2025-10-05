<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido - Il Napolitano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">
    <link rel="icon" type="image/svg+xml" href="../assets/images/pizza.svg">
</head>
<body>
    <header>
        <div id="header-container">
            <div id="logo">
                <a href="index.php"><img src="../assets/images/pizza.svg" alt="logo napolitano"></a>
                <a href="index.php"><img class="logo-text" src="../assets/images/text.svg" alt="nombre pizzeria"></a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="./pages/nosotros.html">NOSOTROS</a></li>
                    <li><a href="./pages/sucursales.html">SUCURSALES & DELIVERY</a></li>
                    <li><a href="./pages/contacto.html">CONTACTO</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="checkout-container">
        <h1><i class="fas fa-shopping-cart"></i> Finalizar Pedido</h1>
        
        <div class="checkout-content">
            <!-- Left Column: Order Form -->
            <div class="checkout-form-section">
                <form id="checkoutForm" method="POST" action="../src/handlers/process_order.php">
                    <!-- Customer Information -->
                    <div class="form-section">
                        <h2><i class="fas fa-user"></i> Información Personal</h2>
                        <div class="form-group">
                            <label for="customerName">Nombre Completo *</label>
                            <input type="text" id="customerName" name="customerName" required 
                                   placeholder="Juan Pérez">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerEmail">Email *</label>
                                <input type="email" id="customerEmail" name="customerEmail" required 
                                       placeholder="juan@ejemplo.com">
                            </div>
                            <div class="form-group">
                                <label for="customerPhone">Teléfono *</label>
                                <input type="tel" id="customerPhone" name="customerPhone" required 
                                       placeholder="11 1234-5678" pattern="[0-9\s\-\(\)]+">
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="form-section">
                        <h2><i class="fas fa-map-marker-alt"></i> Dirección de Entrega</h2>
                        <div class="form-group">
                            <label for="deliveryAddress">Dirección *</label>
                            <input type="text" id="deliveryAddress" name="deliveryAddress" required 
                                   placeholder="Calle Falsa 123, Piso 4, Depto B">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="deliveryCity">Ciudad *</label>
                                <select id="deliveryCity" name="deliveryCity" required>
                                    <option value="">Seleccione...</option>
                                    <option value="San Martín">San Martín</option>
                                    <option value="Villa Ballester">Villa Ballester</option>
                                    <option value="Villa Lynch">Villa Lynch</option>
                                    <option value="Villa Maipú">Villa Maipú</option>
                                    <option value="Caseros">Caseros</option>
                                    <option value="Tres de Febrero">Tres de Febrero</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="deliveryZipCode">Código Postal</label>
                                <input type="text" id="deliveryZipCode" name="deliveryZipCode" 
                                       placeholder="1650" pattern="[0-9]{4}">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h2><i class="fas fa-credit-card"></i> Método de Pago</h2>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="cash" checked>
                                <span class="payment-label">
                                    <i class="fas fa-money-bill-wave"></i> Efectivo
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="card">
                                <span class="payment-label">
                                    <i class="fas fa-credit-card"></i> Tarjeta (en el momento)
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="transfer">
                                <span class="payment-label">
                                    <i class="fas fa-university"></i> Transferencia
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="mercadopago">
                                <span class="payment-label">
                                    <i class="fas fa-wallet"></i> Mercado Pago
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="form-section">
                        <h2><i class="fas fa-comment"></i> Notas del Pedido (Opcional)</h2>
                        <div class="form-group">
                            <textarea id="orderNotes" name="orderNotes" rows="3" 
                                      placeholder="Ej: Sin cebolla, timbre roto, etc."></textarea>
                        </div>
                    </div>

                    <!-- Hidden field for cart data -->
                    <input type="hidden" id="orderData" name="orderData">
                </form>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="order-summary-section">
                <div class="order-summary-card">
                    <h2><i class="fas fa-receipt"></i> Resumen del Pedido</h2>
                    
                    <div id="orderItems" class="order-items">
                        <!-- Items will be populated by JavaScript -->
                    </div>

                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span id="summarySubtotal">$0.00</span>
                        </div>
                        <div class="total-row">
                            <span>Envío:</span>
                            <span id="summaryDelivery">$500.00</span>
                        </div>
                        <div class="total-row discount-row" id="discountRow" style="display: none;">
                            <span>Descuento:</span>
                            <span id="summaryDiscount">-$0.00</span>
                        </div>
                        <div class="total-row total-final">
                            <span>Total:</span>
                            <span id="summaryTotal">$0.00</span>
                        </div>
                    </div>

                    <div class="promo-code">
                        <input type="text" id="promoCode" placeholder="Código de descuento">
                        <button type="button" id="applyPromo" class="btn-promo">Aplicar</button>
                    </div>

                    <button type="submit" form="checkoutForm" class="btn-confirm-order">
                        <i class="fas fa-check-circle"></i> Confirmar Pedido
                    </button>

                    <div class="delivery-info">
                        <i class="fas fa-clock"></i>
                        <span>Tiempo estimado: 30-45 min</span>
                    </div>
                    
                    <div class="secure-payment">
                        <i class="fas fa-lock"></i>
                        <span>Pago seguro y protegido</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-copyright">
                <p>&copy; <script>document.write(new Date().getFullYear());</script> Pizzeria Il Napolitano</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/checkout.js?v=<?php echo time(); ?>"></script>
</body>
</html>
