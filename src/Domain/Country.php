<?php


namespace Shopping\Domain;


use Shopping\Shared\Domain\Provider\CurrencyRates;

class Country implements CountryInterface
{
    private $name;
    private $currency;
    private $vatRate;
    private $shippingCost;

    public function __construct(string $name,string $currency,float $vatRate,float $shippingCost)
    {
        $this->name = $name;
        $this->currency = $currency;
        $this->vatRate = $vatRate;
        $this->shippingCost = $shippingCost;
    }

    public function getVatRate()
    {
        return $this->vatRate;
    }

    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}