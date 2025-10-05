<?php
/**
 * Order Class - Manages customer orders
 * Handles order creation, status updates, and order details
 */
class Order
{
    private ?int $id;
    private int $userId;
    private string $customerName;
    private string $customerEmail;
    private string $customerPhone;
    private string $deliveryAddress;
    private string $deliveryCity;
    private string $deliveryZipCode;
    private float $subtotal;
    private float $deliveryFee;
    private float $discount;
    private float $total;
    private string $status;
    private string $paymentMethod;
    private string $paymentStatus;
    private ?string $notes;
    private string $createdAt;
    private ?string $updatedAt;

    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    // Payment methods
    const PAYMENT_CASH = 'cash';
    const PAYMENT_CARD = 'card';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_MERCADOPAGO = 'mercadopago';

    public function __construct(
        ?int $id = null,
        int $userId = 0,
        string $customerName = '',
        string $customerEmail = '',
        string $customerPhone = '',
        string $deliveryAddress = '',
        string $deliveryCity = '',
        string $deliveryZipCode = '',
        float $subtotal = 0.0,
        float $deliveryFee = 0.0,
        float $discount = 0.0,
        float $total = 0.0,
        string $status = self::STATUS_PENDING,
        string $paymentMethod = self::PAYMENT_CASH,
        string $paymentStatus = self::PAYMENT_PENDING,
        ?string $notes = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->customerPhone = $customerPhone;
        $this->deliveryAddress = $deliveryAddress;
        $this->deliveryCity = $deliveryCity;
        $this->deliveryZipCode = $deliveryZipCode;
        $this->subtotal = $subtotal;
        $this->deliveryFee = $deliveryFee;
        $this->discount = $discount;
        $this->total = $total;
        $this->status = $status;
        $this->paymentMethod = $paymentMethod;
        $this->paymentStatus = $paymentStatus;
        $this->notes = $notes;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = null;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getCustomerName(): string { return $this->customerName; }
    public function getCustomerEmail(): string { return $this->customerEmail; }
    public function getCustomerPhone(): string { return $this->customerPhone; }
    public function getDeliveryAddress(): string { return $this->deliveryAddress; }
    public function getDeliveryCity(): string { return $this->deliveryCity; }
    public function getDeliveryZipCode(): string { return $this->deliveryZipCode; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getDeliveryFee(): float { return $this->deliveryFee; }
    public function getDiscount(): float { return $this->discount; }
    public function getTotal(): float { return $this->total; }
    public function getStatus(): string { return $this->status; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getPaymentStatus(): string { return $this->paymentStatus; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setStatus(string $status): void { 
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
            self::STATUS_READY,
            self::STATUS_DELIVERING,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED
        ];
        
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Estado de orden inválido");
        }
        
        $this->status = $status;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function setPaymentStatus(string $paymentStatus): void { 
        $validStatuses = [
            self::PAYMENT_PENDING,
            self::PAYMENT_PAID,
            self::PAYMENT_FAILED,
            self::PAYMENT_REFUNDED
        ];
        
        if (!in_array($paymentStatus, $validStatuses)) {
            throw new InvalidArgumentException("Estado de pago inválido");
        }
        
        $this->paymentStatus = $paymentStatus;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function setNotes(?string $notes): void { $this->notes = $notes; }

    // Business logic methods
    public function calculateTotal(): void
    {
        $this->total = $this->subtotal + $this->deliveryFee - $this->discount;
        if ($this->total < 0) {
            $this->total = 0;
        }
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPaid(): bool
    {
        return $this->paymentStatus === self::PAYMENT_PAID;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        $classes = [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_CONFIRMED => 'badge-info',
            self::STATUS_PREPARING => 'badge-primary',
            self::STATUS_READY => 'badge-success',
            self::STATUS_DELIVERING => 'badge-info',
            self::STATUS_DELIVERED => 'badge-success',
            self::STATUS_CANCELLED => 'badge-danger'
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }

    public function getStatusLabel(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_PREPARING => 'En Preparación',
            self::STATUS_READY => 'Listo',
            self::STATUS_DELIVERING => 'En Camino',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado'
        ];

        return $labels[$this->status] ?? 'Desconocido';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'delivery_address' => $this->deliveryAddress,
            'delivery_city' => $this->deliveryCity,
            'delivery_zipcode' => $this->deliveryZipCode,
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->deliveryFee,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentStatus,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
