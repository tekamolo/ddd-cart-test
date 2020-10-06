<?php

namespace Shopping\Shared\Domain\Provider;

use Shopping\Domain\CountryInterface;
use Shopping\Domain\Price;
use Shopping\Domain\PriceProvider;
use Shopping\Shared\Domain\Collection;

class PriceList implements PriceProvider
{
    private $prices = [
        "A" => ["price" => 10,"discountedPrice" => 9,"discountQuantity" => 3],
        "B" => ["price" => 8,"discountedPrice" => 5,"discountQuantity" => 2],
        "C" => ["price" => 12,"discountedPrice" => 10,"discountQuantity" => 5],
    ];

    private $pricesCollection;

    public function __construct()
    {
        $this->pricesCollection = new Collection();
    }

    private function priceLoader(CountryInterface $country){
        foreach ($this->prices as $key => $p){
            $this->pricesCollection->offsetSet($key,
                new Price($p['price'],$p['discountedPrice'],$p['discountQuantity'],$country)
            );
        }
    }

    public function getPriceByProductId(CountryInterface $country, $productId){
        if(empty($this->pricesCollection->count())){
            $this->priceLoader($country);
        }
        return $this->pricesCollection->offsetGet($productId);
    }
}