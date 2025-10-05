<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/classes/db.class.php';
require_once '../src/classes/order.class.php';
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
    $error = "Pedido no encontrado";
} else {
    $order = $orderData['order'];
    $items = $orderData['items'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastrear Pedido - Il Napolitano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/track-order.css">
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

    <div class="track-container">
        <?php if (isset($error)): ?>
            <div class="error-card">
                <i class="fas fa-exclamation-circle"></i>
                <h2><?php echo $error; ?></h2>
                <a href="index.php" class="btn-home">Volver al Inicio</a>
            </div>
        <?php else: ?>
            <h1><i class="fas fa-map-marked-alt"></i> Rastrear Pedido</h1>
            
            <div class="order-header">
                <div class="order-id">
                    <span class="label">Pedido</span>
                    <span class="number">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="order-date">
                    <i class="far fa-calendar-alt"></i>
                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </div>
            </div>

            <!-- Order Status Timeline -->
            <div class="timeline-container">
                <div class="timeline">
                    <?php
                    $statuses = [
                        'pending' => ['label' => 'Recibido', 'icon' => 'fa-check'],
                        'confirmed' => ['label' => 'Confirmado', 'icon' => 'fa-thumbs-up'],
                        'preparing' => ['label' => 'Preparando', 'icon' => 'fa-fire'],
                        'ready' => ['label' => 'Listo', 'icon' => 'fa-box'],
                        'delivering' => ['label' => 'En Camino', 'icon' => 'fa-motorcycle'],
                        'delivered' => ['label' => 'Entregado', 'icon' => 'fa-home']
                    ];

                    $currentStatus = $order['status'];
                    $statusKeys = array_keys($statuses);
                    $currentIndex = array_search($currentStatus, $statusKeys);
                    $isCancelled = $currentStatus === 'cancelled';

                    if (!$isCancelled) {
                        foreach ($statuses as $key => $status) {
                            $keyIndex = array_search($key, $statusKeys);
                            $isActive = $keyIndex <= $currentIndex;
                            $isCurrent = $key === $currentStatus;
                            ?>
                            <div class="timeline-step <?php echo $isActive ? 'active' : ''; ?> <?php echo $isCurrent ? 'current' : ''; ?>">
                                <div class="timeline-icon">
                                    <i class="fas <?php echo $status['icon']; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h3><?php echo $status['label']; ?></h3>
                                    <?php if ($isActive): ?>
                                        <span class="time"><?php echo date('H:i', strtotime($order['updated_at'] ?? $order['created_at'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="cancelled-status">
                            <i class="fas fa-times-circle"></i>
                            <h2>Pedido Cancelado</h2>
                            <p>Este pedido ha sido cancelado</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- Order Details -->
            <div class="details-grid">
                <!-- Customer Info -->
                <div class="info-card">
                    <h3><i class="fas fa-user"></i> Cliente</h3>
                    <p><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                    <p><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <p><?php echo htmlspecialchars($order['customer_email']); ?></p>
                </div>

                <!-- Delivery Info -->
                <div class="info-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Dirección de Entrega</h3>
                    <p><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <p><?php echo htmlspecialchars($order['delivery_city']); ?> 
                       <?php echo !empty($order['delivery_zipcode']) ? htmlspecialchars($order['delivery_zipcode']) : ''; ?>
                    </p>
                </div>

                <!-- Payment Info -->
                <div class="info-card">
                    <h3><i class="fas fa-credit-card"></i> Pago</h3>
                    <?php
                    $paymentLabels = [
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'mercadopago' => 'Mercado Pago'
                    ];
                    ?>
                    <p><strong><?php echo $paymentLabels[$order['payment_method']] ?? 'No especificado'; ?></strong></p>
                    <p class="payment-status <?php echo $order['payment_status']; ?>">
                        <?php echo $order['payment_status'] === 'paid' ? 'Pagado' : 'Pendiente'; ?>
                    </p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="items-card">
                <h3><i class="fas fa-pizza-slice"></i> Items del Pedido</h3>
                <div class="items-list">
                    <?php foreach ($items as $item): ?>
                    <div class="item-row">
                        <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                        <span class="item-qty">x<?php echo $item['quantity']; ?></span>
                        <span class="item-price">$<?php echo number_format($item['subtotal'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-total">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Envío:</span>
                        <span>$<?php echo number_format($order['delivery_fee'], 2); ?></span>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                    <div class="total-row discount">
                        <span>Descuento:</span>
                        <span>-$<?php echo number_format($order['discount'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="total-row final">
                        <span>Total:</span>
                        <span>$<?php echo number_format($order['total'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="support-card">
                <i class="fas fa-headset"></i>
                <div>
                    <h4>¿Necesitas ayuda?</h4>
                    <p>Contactanos al: <strong>011 1234-5678</strong></p>
                </div>
            </div>

            <!-- Refresh Button -->
            <div class="action-buttons">
                <button onclick="location.reload()" class="btn-refresh">
                    <i class="fas fa-sync-alt"></i> Actualizar Estado
                </button>
                <a href="index.php" class="btn-home">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-copyright">
                <p>&copy; <script>document.write(new Date().getFullYear());</script> Pizzeria Il Napolitano</p>
            </div>
        </div>
    </footer>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
