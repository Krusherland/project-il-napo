<?php
require_once __DIR__ . '/db.class.php';
require_once __DIR__ . '/order.class.php';
require_once __DIR__ . '/orderItem.class.php';

/**
 * OrderManager Class - Handles database operations for orders
 * CRUD operations and business logic for order management
 */
class OrderManager
{
    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    /**
     * Create a new order with items
     */
    public function createOrder(Order $order, array $items): int
    {
        try {
            $this->db->beginTransaction();

            // Insert order
            $sql = "INSERT INTO orders (
                user_id, customer_name, customer_email, customer_phone,
                delivery_address, delivery_city, delivery_zipcode,
                subtotal, delivery_fee, discount, total,
                status, payment_method, payment_status, notes
            ) VALUES (
                :user_id, :customer_name, :customer_email, :customer_phone,
                :delivery_address, :delivery_city, :delivery_zipcode,
                :subtotal, :delivery_fee, :discount, :total,
                :status, :payment_method, :payment_status, :notes
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $order->getUserId(),
                ':customer_name' => $order->getCustomerName(),
                ':customer_email' => $order->getCustomerEmail(),
                ':customer_phone' => $order->getCustomerPhone(),
                ':delivery_address' => $order->getDeliveryAddress(),
                ':delivery_city' => $order->getDeliveryCity(),
                ':delivery_zipcode' => $order->getDeliveryZipCode(),
                ':subtotal' => $order->getSubtotal(),
                ':delivery_fee' => $order->getDeliveryFee(),
                ':discount' => $order->getDiscount(),
                ':total' => $order->getTotal(),
                ':status' => $order->getStatus(),
                ':payment_method' => $order->getPaymentMethod(),
                ':payment_status' => $order->getPaymentStatus(),
                ':notes' => $order->getNotes()
            ]);

            $orderId = (int)$this->db->lastInsertId();

            // Insert order items
            foreach ($items as $item) {
                $this->addOrderItem($orderId, $item);
            }

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw new RuntimeException("Error al crear la orden: " . $e->getMessage());
        }
    }

    /**
     * Add an item to an existing order
     */
    private function addOrderItem(int $orderId, OrderItem $item): void
    {
        $sql = "INSERT INTO order_items (
            order_id, product_id, product_name, unit_price, quantity, subtotal, notes
        ) VALUES (
            :order_id, :product_id, :product_name, :unit_price, :quantity, :subtotal, :notes
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $item->getProductId(),
            ':product_name' => $item->getProductName(),
            ':unit_price' => $item->getUnitPrice(),
            ':quantity' => $item->getQuantity(),
            ':subtotal' => $item->getSubtotal(),
            ':notes' => $item->getNotes()
        ]);
    }

    /**
     * Get order by ID with its items
     */
    public function getOrderById(int $orderId): ?array
    {
        $sql = "SELECT * FROM orders WHERE id_order = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $orderId]);
        
        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$orderData) {
            return null;
        }

        // Get order items
        $items = $this->getOrderItems($orderId);

        return [
            'order' => $orderData,
            'items' => $items
        ];
    }

    /**
     * Get all items for an order
     */
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id_item";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $sql = "UPDATE orders SET status = :status, updated_at = NOW() WHERE id_order = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':status' => $status,
            ':id' => $orderId
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        $sql = "UPDATE orders SET payment_status = :payment_status, updated_at = NOW() WHERE id_order = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':payment_status' => $paymentStatus,
            ':id' => $orderId
        ]);
    }

    /**
     * Get all orders (with optional filters)
     */
    public function getAllOrders(?string $status = null, ?int $limit = null): array
    {
        $sql = "SELECT o.*, COUNT(oi.id_item) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.id_order = oi.order_id";
        
        $params = [];
        
        if ($status !== null) {
            $sql .= " WHERE o.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " GROUP BY o.id_order ORDER BY o.created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get orders by customer email
     */
    public function getOrdersByEmail(string $email): array
    {
        $sql = "SELECT * FROM orders WHERE customer_email = :email ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(int $orderId): bool
    {
        return $this->updateOrderStatus($orderId, Order::STATUS_CANCELLED);
    }

    /**
     * Get order statistics
     */
    public function getOrderStats(): array
    {
        $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as total_revenue,
                AVG(total) as average_order_value
                FROM orders";
        
        $stmt = $this->db->run($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete order (admin only - cascades to items)
     */
    public function deleteOrder(int $orderId): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Delete order items first
            $sql = "DELETE FROM order_items WHERE order_id = :order_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
            
            // Delete order
            $sql = "DELETE FROM orders WHERE id_order = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $orderId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new RuntimeException("Error al eliminar la orden: " . $e->getMessage());
        }
    }
}
