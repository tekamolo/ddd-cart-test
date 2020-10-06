<?php
namespace Shopping\Domain;

use Shopping\Domain\Exceptions\PositiveNumberException;

class Product
{
    private $productId;

    private $quantity;

    public function __construct($productId, $quantity=1)
    {
        $this->checkNumberOf($quantity);
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    private function checkNumberOf(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new PositiveNumberException();
        }
    }

    public function updateNumberOf(int $quantity){
        $this->checkNumberOf($quantity);
        $this->quantity = $quantity;
    }

    public function getQuantity(){
        return $this->quantity;
    }

    public function getId(){
        return $this->productId;
    }

    public function calculatePriceLine(Price $price){
        return $this->quantity * $price->getPriceVat();
    }

    public function calculateDiscountedLine(Price $price){
        if($this->quantity >= $price->getMinimumQuantityForDiscount()){
            return $this->quantity * $price->getDiscountedPriceVat();
        }
        return $this->quantity * $price->getPriceVat();
    }
}