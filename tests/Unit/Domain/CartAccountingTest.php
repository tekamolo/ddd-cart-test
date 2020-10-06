<?php

namespace Shopping\Tests\Unit\Domain;

use Shopping\Domain\CartAccounting;
use Shopping\Domain\Country;
use Shopping\Domain\Price;
use Shopping\Domain\Product;
use Shopping\Shared\Domain\Collection;
use Shopping\Shared\Domain\Provider\CurrencyRates;
use Shopping\Shared\Domain\Provider\PriceList;

class CartAccountingTest extends \PHPUnit\Framework\TestCase
{
    private $collection;
    private $cartAccounting;
    private $priceList;
    private $currencyRates;

    public function setUp()
    {
        $this->collection = new Collection();
        $this->priceList = $this->createMock(PriceList::class);
        $this->currencyRates = $this->createMock(CurrencyRates::class);
        $this->cartAccounting = new CartAccounting(
            $this->collection,
            $this->priceList,
            $this->createMock(Country::class),
            $this->currencyRates
        );
    }

    public function computeDataProvider(){
        return [
            "One product, One item" => [
              "price" => 45,
              "discountedPrice" => 40,
              "discountedQuantityLimit" => 3,
              "quantityOrdered" => 1,
              "exchangeRate" => 2,
            ],
            "One product, 3 items" => [
                "price" => 45,
                "discountedPrice" => 40,
                "discountedQuantityLimit" => 3,
                "quantityOrdered" => 3,
                "exchangeRate" => 2,
            ],
        ];
    }

    /**
     * @dataProvider computeDataProvider
     * @param $price
     * @param $discountedPrice
     * @param $discountedQuantityLimit
     * @param $quantityOrdered
     * @param $exchangeRate
     */
    public function testCompute($price,$discountedPrice,$discountedQuantityLimit,$quantityOrdered,$exchangeRate){
        $this->collection->add("1",new Product("A",$quantityOrdered));
        $this->priceList->method("getPriceByProductId")->willReturn(
            new Price($price,$discountedPrice,$discountedQuantityLimit,new Country("Spain","EUR",1,0))
        );
        $this->cartAccounting->compute();
        if($quantityOrdered >= $discountedQuantityLimit){
            $priceApplied = $discountedPrice;
        }else{
            $priceApplied = $price;
        }

        $totalWithoutDiscount = $this->cartAccounting->getTotalWithoutDiscount();
        $this->assertEquals($price * $quantityOrdered,$totalWithoutDiscount);

        $total = $this->cartAccounting->getTotal();
        $this->assertEquals($priceApplied * $quantityOrdered,$total);
        $this->currencyRates->method("getRate")->willReturn($exchangeRate);
        $convertedTotal = $this->cartAccounting->getTotalConvertedToCurrency();

        $expectedAmount = $priceApplied * $exchangeRate * $quantityOrdered;

        $this->assertEquals($expectedAmount,$convertedTotal);
    }
}