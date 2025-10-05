<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@ilnapolitano.com') {
    header('Location: ../public/login.html');
    exit;
}

require_once '../config/database.php';
require_once '../classes/db.class.php';
require_once '../classes/order.class.php';
require_once '../classes/orderManager.class.php';

$orderManager = new OrderManager();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    
    if ($_POST['action'] === 'update_status' && $orderId) {
        $newStatus = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $orderManager->updateOrderStatus($orderId, $newStatus);
        header('Location: manage_orders.php?success=1');
        exit;
    } elseif ($_POST['action'] === 'update_payment' && $orderId) {
        $paymentStatus = filter_input(INPUT_POST, 'payment_status', FILTER_SANITIZE_STRING);
        $orderManager->updatePaymentStatus($orderId, $paymentStatus);
        header('Location: manage_orders.php?success=1');
        exit;
    }
}

// Get filter parameters
$statusFilter = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$orders = $orderManager->getAllOrders($statusFilter);
$stats = $orderManager->getOrderStats();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Il Napolitano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/manage-orders.css">
    <link rel="icon" type="image/svg+xml" href="../../assets/images/pizza.svg">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1><i class="fas fa-receipt"></i> Gestión de Pedidos</h1>
            <div class="header-actions">
                <a href="welcome.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </header>

        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Acción realizada exitosamente
        </div>
        <?php endif; ?>

        <!-- Statistics Dashboard -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total Pedidos</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['pending_orders']; ?></h3>
                    <p>Pendientes</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['delivered_orders']; ?></h3>
                    <p>Entregados</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                    <p>Ingresos Totales</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <h3>Filtrar Pedidos:</h3>
            <div class="filter-buttons">
                <a href="manage_orders.php" class="filter-btn <?php echo !$statusFilter ? 'active' : ''; ?>">
                    Todos (<?php echo $stats['total_orders']; ?>)
                </a>
                <a href="?status=pending" class="filter-btn <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>">
                    Pendientes
                </a>
                <a href="?status=confirmed" class="filter-btn <?php echo $statusFilter === 'confirmed' ? 'active' : ''; ?>">
                    Confirmados
                </a>
                <a href="?status=preparing" class="filter-btn <?php echo $statusFilter === 'preparing' ? 'active' : ''; ?>">
                    En Preparación
                </a>
                <a href="?status=delivering" class="filter-btn <?php echo $statusFilter === 'delivering' ? 'active' : ''; ?>">
                    En Camino
                </a>
                <a href="?status=delivered" class="filter-btn <?php echo $statusFilter === 'delivered' ? 'active' : ''; ?>">
                    Entregados
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-container">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-inbox"></i>
                    <p>No hay pedidos para mostrar</p>
                </div>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Dirección</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($order['id_order'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                            <td>
                                <div class="customer-info">
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                                </div>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($order['delivery_address']); ?>,
                                <?php echo htmlspecialchars($order['delivery_city']); ?></small>
                            </td>
                            <td><span class="badge badge-info"><?php echo $order['item_count']; ?> items</span></td>
                            <td><strong>$<?php echo number_format($order['total'], 2); ?></strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'confirmed' => 'Confirmado',
                                        'preparing' => 'Preparando',
                                        'ready' => 'Listo',
                                        'delivering' => 'En Camino',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    echo $statusLabels[$order['status']] ?? 'Desconocido';
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="payment-badge payment-<?php echo $order['payment_status']; ?>">
                                    <?php echo $order['payment_status'] === 'paid' ? 'Pagado' : 'Pendiente'; ?>
                                </span>
                            </td>
                            <td><small><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="viewOrder(<?php echo $order['id_order']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-edit" onclick="editOrder(<?php echo $order['id_order']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- View/Edit Order Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <script src="../../assets/js/manage-orders.js"></script>
</body>
</html>
