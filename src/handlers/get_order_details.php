<?php
require_once '../config/database.php';
require_once '../classes/db.class.php';
require_once '../classes/orderManager.class.php';

header('Content-Type: application/json');

try {
    $orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
    
    if (!$orderId) {
        throw new Exception('ID de pedido inválido');
    }

    $orderManager = new OrderManager();
    $orderData = $orderManager->getOrderById($orderId);

    if (!$orderData) {
        throw new Exception('Pedido no encontrado');
    }

    echo json_encode([
        'success' => true,
        'order' => $orderData['order'],
        'items' => $orderData['items']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
