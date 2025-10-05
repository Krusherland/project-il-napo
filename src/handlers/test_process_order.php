<?php
// Simple test to check if process_order.php is working
echo "Testing process_order.php endpoint...\n\n";

// Test data
$testData = [
    'customerName' => 'Test User',
    'customerEmail' => 'test@example.com',
    'customerPhone' => '1234567890',
    'deliveryAddress' => 'Test Address 123',
    'deliveryCity' => 'San Martín',
    'deliveryZipCode' => '1650',
    'paymentMethod' => 'cash',
    'orderNotes' => 'Test order',
    'orderData' => json_encode([
        'items' => [
            [
                'id' => 1,
                'name' => 'Pizza Muzzarella',
                'price' => 1500.00,
                'quantity' => 1
            ]
        ],
        'subtotal' => 1500.00,
        'deliveryFee' => 500.00,
        'discount' => 0,
        'total' => 2000.00
    ])
];

// Initialize curl
$ch = curl_init('http://localhost/Project-Il-Napolitano/src/handlers/process_order.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Parse response
list($headers, $body) = explode("\r\n\r\n", $response, 2);

echo "HTTP Code: $httpCode\n";
echo "Headers:\n$headers\n\n";
echo "Body:\n$body\n\n";

// Try to decode JSON
$json = json_decode($body, true);
if ($json) {
    echo "Parsed JSON:\n";
    print_r($json);
} else {
    echo "Failed to parse JSON. Error: " . json_last_error_msg() . "\n";
}
