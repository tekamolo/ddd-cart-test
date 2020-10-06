<?php
namespace Shopping\Domain;

class Price
{
    private $price;

    private $discountedPrice;

    private $discountMinimumQuantity;

    private $country;

    public function __construct(string $productId, int $price, int $discountedPrice, int $discountMinimumQuantity, Country $country)
    {
        $this->price = $price;
        $this->country = $country;
        $this->discountedPrice = $discountedPrice;
        $this->discountMinimumQuantity = $discountMinimumQuantity;
    }

    public function getPriceVat(){
        return (float) $this->price * $this->country->getVatRate();
    }

    public function getDiscountedPriceVat(){
        return (float) $this->discountedPrice * $this->country->getVatRate();
    }

    public function getMinimumQuantityForDiscount(){
        return $this->discountMinimumQuantity;
    }
}