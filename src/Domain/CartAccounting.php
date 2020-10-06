<?php


namespace Shopping\Domain;


use Shopping\Shared\Domain\Collection;
use Shopping\Shared\Domain\Provider\PriceList;
use Symfony\Component\VarDumper\VarDumper;

class CartAccounting
{
    /**
     * @var Collection
     */
    private $products;

    /**
     * @var PriceList
     */
    private $priceList;
    private $country;

    private $totalWithoutDiscount = 0;

    private $total;


    /**
     * CartAccounting constructor.
     * @param Collection $products
     * @param PriceList $priceList
     * @param Country $country
     */
    public function __construct(Collection $products,PriceList $priceList,Country $country)
    {
        $this->products = $products;
        $this->priceList = $priceList;
        $this->country = $country;
        $this->compute();
    }

    public function compute(){
        /** @var Product $p */
        foreach ($this->products as $p){
            $this->totalWithoutDiscount += $p->calculatePriceLine($this->priceList->getPriceByProductId($this->country,$p->getId()));
        }

        foreach ($this->products as $p){
            $this->total += $p->calculateDiscountedLine($this->priceList->getPriceByProductId($this->country,$p->getId()));
        }
    }

    public function getTotalWithoutDiscount(){
        return $this->totalWithoutDiscount;
    }

    public function getTotal(){
        return $this->total;
    }
}