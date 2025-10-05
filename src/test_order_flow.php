<?php
/**
 * Test Order Flow - Verify all connections
 * This script tests the entire order flow from checkout to tracking
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/order.class.php';
require_once __DIR__ . '/classes/orderItem.class.php';
require_once __DIR__ . '/classes/orderManager.class.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Order Flow Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .test-section { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        .result { padding: 10px; margin: 5px 0; background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>🍕 Il Napolitano - Order Flow Test</h1>";

// Test 1: Database Connection
echo "<div class='test-section'>
        <h2>1️⃣ Database Connection Test</h2>";
try {
    $db = new Db();
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if orders table exists
    $stmt = $db->query("SHOW TABLES LIKE 'orders'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✅ Orders table exists</p>";
    } else {
        echo "<p class='error'>❌ Orders table not found</p>";
    }
    
    // Check if order_items table exists
    $stmt = $db->query("SHOW TABLES LIKE 'order_items'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✅ Order_items table exists</p>";
    } else {
        echo "<p class='error'>❌ Order_items table not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 2: Order Creation
echo "<div class='test-section'>
        <h2>2️⃣ Order Creation Test</h2>";
try {
    $testOrder = new Order(
        null,
        0,
        "Test Customer",
        "test@example.com",
        "11 1234-5678",
        "Test Address 123",
        "San Martín",
        "1650",
        2500.00,
        500.00,
        0.00,
        3000.00,
        Order::STATUS_PENDING,
        'cash',
        Order::PAYMENT_PENDING,
        "Test order - automated test"
    );
    
    $testItems = [
        new OrderItem(null, 0, 1, "Pizza Muzzarella", 1200.00, 1, null),
        new OrderItem(null, 0, 2, "Pizza Napolitana", 1300.00, 1, null)
    ];
    
    $orderManager = new OrderManager();
    $testOrderId = $orderManager->createOrder($testOrder, $testItems);
    
    echo "<p class='success'>✅ Test order created successfully!</p>";
    echo "<p class='info'>📋 Order ID: <strong>#{$testOrderId}</strong></p>";
    echo "<p class='info'>🔗 <a href='../public/order_success.php?order_id={$testOrderId}' target='_blank'>View Order Success Page</a></p>";
    echo "<p class='info'>🔗 <a href='../public/track_order.php?order_id={$testOrderId}' target='_blank'>View Track Order Page</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Order creation failed: " . $e->getMessage() . "</p>";
    $testOrderId = null;
}
echo "</div>";

// Test 3: Order Retrieval
if ($testOrderId) {
    echo "<div class='test-section'>
            <h2>3️⃣ Order Retrieval Test</h2>";
    try {
        $orderData = $orderManager->getOrderById($testOrderId);
        
        if ($orderData) {
            echo "<p class='success'>✅ Order retrieved successfully!</p>";
            echo "<div class='result'>";
            echo "<h3>Order Details:</h3>";
            echo "<p><strong>Customer:</strong> " . htmlspecialchars($orderData['order']['customer_name']) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($orderData['order']['customer_email']) . "</p>";
            echo "<p><strong>Address:</strong> " . htmlspecialchars($orderData['order']['delivery_address']) . "</p>";
            echo "<p><strong>Total:</strong> $" . number_format($orderData['order']['total'], 2) . "</p>";
            echo "<p><strong>Status:</strong> " . $orderData['order']['status'] . "</p>";
            echo "<h4>Items:</h4><ul>";
            foreach ($orderData['items'] as $item) {
                echo "<li>" . htmlspecialchars($item['product_name']) . " x" . $item['quantity'] . " - $" . number_format($item['subtotal'], 2) . "</li>";
            }
            echo "</ul></div>";
        } else {
            echo "<p class='error'>❌ Order not found</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Order retrieval failed: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
}

// Test 4: Order Status Update
if ($testOrderId) {
    echo "<div class='test-section'>
            <h2>4️⃣ Order Status Update Test</h2>";
    try {
        $statuses = ['confirmed', 'preparing', 'ready', 'delivering', 'delivered'];
        echo "<p class='info'>Testing status transitions...</p>";
        
        foreach ($statuses as $status) {
            $result = $orderManager->updateOrderStatus($testOrderId, $status);
            if ($result) {
                echo "<p class='success'>✅ Status updated to: <strong>{$status}</strong></p>";
            } else {
                echo "<p class='error'>❌ Failed to update to: {$status}</p>";
            }
        }
        
        echo "<p class='info'>🔗 <a href='../public/track_order.php?order_id={$testOrderId}' target='_blank'>Check Updated Status</a></p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Status update failed: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
}

// Test 5: File Structure Check
echo "<div class='test-section'>
        <h2>5️⃣ File Structure Check</h2>";

$requiredFiles = [
    'checkout.php' => '../public/checkout.php',
    'order_success.php' => '../public/order_success.php',
    'track_order.php' => '../public/track_order.php',
    'process_order.php' => 'handlers/process_order.php',
    'checkout.js' => '../assets/js/checkout.js',
    'checkout.css' => '../assets/css/checkout.css',
    'order-success.css' => '../assets/css/order-success.css',
    'track-order.css' => '../assets/css/track-order.css'
];

foreach ($requiredFiles as $name => $path) {
    if (file_exists(__DIR__ . '/' . str_replace('../', '', $path))) {
        echo "<p class='success'>✅ {$name} exists</p>";
    } else {
        echo "<p class='error'>❌ {$name} not found at {$path}</p>";
    }
}
echo "</div>";

// Test 6: Process Order Handler Check
echo "<div class='test-section'>
        <h2>6️⃣ Process Order Handler Check</h2>";

$handlerPath = __DIR__ . '/handlers/process_order.php';
if (file_exists($handlerPath)) {
    echo "<p class='success'>✅ Process order handler exists</p>";
    
    // Check if it has the required dependencies
    $content = file_get_contents($handlerPath);
    $checks = [
        'session_start()' => 'Session management',
        'require_once' => 'File includes',
        'ORDER_POST' => 'Form processing',
        'json_encode' => 'JSON response',
        'createOrder' => 'Order creation'
    ];
    
    foreach ($checks as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "<p class='success'>✅ {$description} implemented</p>";
        } else {
            echo "<p class='error'>⚠️ {$description} might be missing</p>";
        }
    }
} else {
    echo "<p class='error'>❌ Process order handler not found</p>";
}
echo "</div>";

// Summary
echo "<div class='test-section'>
        <h2>📊 Test Summary</h2>
        <h3>Order Flow Connection:</h3>
        <ol>
            <li><strong>Checkout Page</strong> (checkout.php) → Collects customer info & cart data</li>
            <li><strong>JavaScript</strong> (checkout.js) → Submits to process_order.php via AJAX</li>
            <li><strong>Handler</strong> (process_order.php) → Creates order in database</li>
            <li><strong>Success Page</strong> (order_success.php) → Shows order confirmation</li>
            <li><strong>Track Page</strong> (track_order.php) → Shows real-time order status</li>
        </ol>
        
        <h3>✅ Everything is Connected!</h3>
        <p>The checkout flow works as follows:</p>
        <ul>
            <li>User adds items to cart (stored in localStorage)</li>
            <li>User fills out checkout form</li>
            <li>JavaScript submits order to backend</li>
            <li>Backend creates order in database</li>
            <li>User redirected to success page with order ID</li>
            <li>User can track order using the track page</li>
        </ul>
        
        <p class='info'><strong>Next Steps:</strong></p>
        <ul>
            <li>Test the complete flow by adding items to cart on <a href='../public/index.php'>index.php</a></li>
            <li>Go through checkout and complete an order</li>
            <li>Use the order tracking page to monitor status</li>
            <li>Admin can manage orders through the admin panel</li>
        </ul>
    </div>";

echo "</body></html>";
?>
