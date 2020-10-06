<?php


namespace Shopping\Domain;


use Shopping\Shared\Domain\Collection;
use Shopping\Shared\Domain\Provider\PriceList;

class CartInventory
{
    private $products;

    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    public function getProductList(){
        return $this->products;
    }

    public function findProduct($productId){
        /** @var Product $p */
        foreach ($this->products as $p){
            if($productId == $p->getId()){
                return $p;
            }
        }
        return false;
    }
}