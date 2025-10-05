<?php
/**
 * OrderItem Class - Manages individual items within an order
 * Represents each product in the order with quantity and pricing
 */
class OrderItem
{
    private ?int $id;
    private int $orderId;
    private int $productId;
    private string $productName;
    private float $unitPrice;
    private int $quantity;
    private float $subtotal;
    private ?string $notes;

    public function __construct(
        ?int $id = null,
        int $orderId = 0,
        int $productId = 0,
        string $productName = '',
        float $unitPrice = 0.0,
        int $quantity = 1,
        ?string $notes = null
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
        $this->notes = $notes;
        $this->calculateSubtotal();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getOrderId(): int { return $this->orderId; }
    public function getProductId(): int { return $this->productId; }
    public function getProductName(): string { return $this->productName; }
    public function getUnitPrice(): float { return $this->unitPrice; }
    public function getQuantity(): int { return $this->quantity; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getNotes(): ?string { return $this->notes; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setOrderId(int $orderId): void { $this->orderId = $orderId; }
    
    public function setQuantity(int $quantity): void 
    { 
        if ($quantity < 1) {
            throw new InvalidArgumentException("La cantidad debe ser al menos 1");
        }
        $this->quantity = $quantity;
        $this->calculateSubtotal();
    }

    public function setNotes(?string $notes): void { $this->notes = $notes; }

    // Business logic
    private function calculateSubtotal(): void
    {
        $this->subtotal = $this->unitPrice * $this->quantity;
    }

    public function increaseQuantity(int $amount = 1): void
    {
        if ($amount < 1) {
            throw new InvalidArgumentException("El incremento debe ser positivo");
        }
        $this->quantity += $amount;
        $this->calculateSubtotal();
    }

    public function decreaseQuantity(int $amount = 1): void
    {
        if ($amount < 1) {
            throw new InvalidArgumentException("El decremento debe ser positivo");
        }
        
        if ($this->quantity - $amount < 1) {
            throw new RuntimeException("La cantidad no puede ser menor a 1");
        }
        
        $this->quantity -= $amount;
        $this->calculateSubtotal();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'unit_price' => $this->unitPrice,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'notes' => $this->notes
        ];
    }
}
