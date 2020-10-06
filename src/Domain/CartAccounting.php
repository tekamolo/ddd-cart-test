<?php


namespace Shopping\Domain;


use Shopping\Shared\Domain\Collection;
use Shopping\Shared\Domain\Provider\CurrencyRates;
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
    private $currencyRates;

    private $totalWithoutDiscount = 0;

    private $total;

    private $totalConvertedToCurrency;


    /**
     * CartAccounting constructor.
     * @param Collection $products
     * @param PriceList $priceList
     * @param CountryInterface $country
     * @param CurrencyRates $currencyRates
     */
    public function __construct(Collection $products, PriceList $priceList, CountryInterface $country,CurrencyRates $currencyRates)
    {
        $this->products = $products;
        $this->priceList = $priceList;
        $this->country = $country;
        $this->currencyRates = $currencyRates;
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

    public function getTotalConvertedToCurrency(){
        $currency = $this->country->getCurrency();
        $exchangeRate = $this->currencyRates->getRate($currency);
        if(empty($this->total)) $this->compute();
        $this->totalConvertedToCurrency = $exchangeRate * $this->total;

        return $this->totalConvertedToCurrency;
    }
}