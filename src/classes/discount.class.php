

<?php

include_once("products.class.php");


class Discount extends Product
{
    private float $discountPercentage;

    public function __construct(
        int $id,
        string $description,
        float $price,
        string $image,
        int $stock,
        float $discountPercentage
    ) {
        parent::__construct($id, $description, $price, $image, $stock);
        $this->discountPercentage = $discountPercentage;
    }
    public function getDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }
}
