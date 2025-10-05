<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/classes/db.class.php';
require_once '../src/classes/orderManager.class.php';

// Get order ID from URL
$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if (!$orderId) {
    header('Location: index.php');
    exit;
}

// Fetch order details
$orderManager = new OrderManager();
$orderData = $orderManager->getOrderById($orderId);

if (!$orderData) {
    header('Location: index.php');
    exit;
}

$order = $orderData['order'];
$items = $orderData['items'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Il Napolitano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/order-success.css">
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

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>¡Pedido Confirmado!</h1>
            <p class="success-message">
                Gracias por tu pedido. Hemos recibido tu solicitud y comenzaremos a preparar tu pedido en breve.
            </p>

            <div class="order-number">
                <span class="label">Número de Pedido:</span>
                <span class="number">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></span>
            </div>

            <div class="order-details-card">
                <h2><i class="fas fa-receipt"></i> Detalles del Pedido</h2>
                
                <div class="detail-section">
                    <h3><i class="fas fa-user"></i> Información del Cliente</h3>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                </div>

                <div class="detail-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Dirección de Entrega</h3>
                    <p><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <p><?php echo htmlspecialchars($order['delivery_city']); ?> 
                       <?php echo !empty($order['delivery_zipcode']) ? '(' . htmlspecialchars($order['delivery_zipcode']) . ')' : ''; ?>
                    </p>
                </div>

                <div class="detail-section">
                    <h3><i class="fas fa-pizza-slice"></i> Items del Pedido</h3>
                    <div class="order-items-list">
                        <?php foreach ($items as $item): ?>
                        <div class="order-item-row">
                            <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                            <span class="item-qty">x<?php echo $item['quantity']; ?></span>
                            <span class="item-price">$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="detail-section totals-section">
                    <div class="total-line">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                    </div>
                    <div class="total-line">
                        <span>Envío:</span>
                        <span>$<?php echo number_format($order['delivery_fee'], 2); ?></span>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                    <div class="total-line discount">
                        <span>Descuento:</span>
                        <span>-$<?php echo number_format($order['discount'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="total-line total-final">
                        <span>Total:</span>
                        <span>$<?php echo number_format($order['total'], 2); ?></span>
                    </div>
                </div>

                <div class="detail-section">
                    <h3><i class="fas fa-credit-card"></i> Método de Pago</h3>
                    <p class="payment-method">
                        <?php 
                        $paymentLabels = [
                            'cash' => 'Efectivo',
                            'card' => 'Tarjeta',
                            'transfer' => 'Transferencia',
                            'mercadopago' => 'Mercado Pago'
                        ];
                        echo $paymentLabels[$order['payment_method']] ?? 'No especificado';
                        ?>
                    </p>
                </div>

                <?php if (!empty($order['notes'])): ?>
                <div class="detail-section">
                    <h3><i class="fas fa-comment"></i> Notas</h3>
                    <p class="order-notes"><?php echo htmlspecialchars($order['notes']); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="estimated-time">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>Tiempo Estimado de Entrega</strong>
                    <p>30-45 minutos</p>
                </div>
            </div>

            <div class="action-buttons">
                <a href="track_order.php?order_id=<?php echo $orderId; ?>" class="btn btn-track">
                    <i class="fas fa-map-marked-alt"></i> Rastrear Pedido
                </a>
                <a href="index.php" class="btn btn-home">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
            </div>

            <div class="confirmation-email">
                <i class="fas fa-envelope"></i>
                <span>Hemos enviado un email de confirmación a <strong><?php echo htmlspecialchars($order['customer_email']); ?></strong></span>
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
</body>
</html>
