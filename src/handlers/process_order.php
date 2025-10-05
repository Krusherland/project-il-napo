<?php
// Prevent any output before JSON
ob_start();

session_start();
require_once '../config/database.php';
require_once '../classes/db.class.php';
require_once '../classes/order.class.php';
require_once '../classes/orderItem.class.php';
require_once '../classes/orderManager.class.php';

// Clear any accidental output from includes
ob_end_clean();

header('Content-Type: application/json');

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Get and validate form data
    $customerName = filter_input(INPUT_POST, 'customerName', FILTER_SANITIZE_STRING);
    $customerEmail = filter_input(INPUT_POST, 'customerEmail', FILTER_SANITIZE_EMAIL);
    $customerPhone = filter_input(INPUT_POST, 'customerPhone', FILTER_SANITIZE_STRING);
    $deliveryAddress = filter_input(INPUT_POST, 'deliveryAddress', FILTER_SANITIZE_STRING);
    $deliveryCity = filter_input(INPUT_POST, 'deliveryCity', FILTER_SANITIZE_STRING);
    $deliveryZipCode = filter_input(INPUT_POST, 'deliveryZipCode', FILTER_SANITIZE_STRING);
    $paymentMethod = filter_input(INPUT_POST, 'paymentMethod', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'orderNotes', FILTER_SANITIZE_STRING);
    $orderDataJson = $_POST['orderData'] ?? '';

    // Validate required fields
    if (empty($customerName) || empty($customerEmail) || empty($customerPhone) || 
        empty($deliveryAddress) || empty($deliveryCity)) {
        throw new Exception('Por favor complete todos los campos requeridos');
    }

    // Validate email
    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Parse order data
    $orderData = json_decode($orderDataJson, true);
    if (!$orderData || empty($orderData['items'])) {
        throw new Exception('Carrito vacío o datos inválidos');
    }

    $items = $orderData['items'];
    $subtotal = floatval($orderData['subtotal']);
    $deliveryFee = floatval($orderData['deliveryFee']);
    $discount = floatval($orderData['discount'] ?? 0);
    $total = floatval($orderData['total']);

    // Get user ID if logged in
    $userId = $_SESSION['user_id'] ?? 0;

    // Create Order object
    $order = new Order(
        null,
        $userId,
        $customerName,
        $customerEmail,
        $customerPhone,
        $deliveryAddress,
        $deliveryCity,
        $deliveryZipCode ?? '',
        $subtotal,
        $deliveryFee,
        $discount,
        $total,
        Order::STATUS_PENDING,
        $paymentMethod,
        Order::PAYMENT_PENDING,
        $notes
    );

    // Create OrderItem objects
    $orderItems = [];
    foreach ($items as $item) {
        $orderItem = new OrderItem(
            null,
            0, // Will be set when order is created
            intval($item['id']),
            htmlspecialchars($item['name']),
            floatval($item['price']),
            intval($item['quantity']),
            null
        );
        $orderItems[] = $orderItem;
    }

    // Save order to database
    $orderManager = new OrderManager();
    $orderId = $orderManager->createOrder($order, $orderItems);

    // Send confirmation email (optional - implement if needed)
    // sendOrderConfirmationEmail($customerEmail, $orderId);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Pedido creado exitosamente',
        'orderId' => $orderId
    ]);

} catch (Exception $e) {
    // Log error for debugging
    error_log('Order processing error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
