<?php

namespace Shopping\Tests;

use Shopping\Application\CartService;
use Shopping\Domain\Country;
use Shopping\Domain\Exceptions\ProductException;
use Shopping\Domain\Exceptions\ProductNotInTheCartException;
use Shopping\Domain\Spain;
use Shopping\Infrastructure\CartRepository;
use Shopping\Shared\Domain\Provider\PriceList;

class CartServiceTest extends \PHPUnit\Framework\TestCase
{
    const MAXIMUM_DIFFERENT_PRODUCTS = 10;
    const MAXIMUM_ITEMS_PER_PRODUCT = 50;
    private $service;
    private $priceList;
    private $country;

    public function setUp()
    {
        $this->priceList = new PriceList();
        $this->country = new Spain();
        $this->service = new CartService(
            new CartRepository(),
            $this->priceList,
            $this->country
        );
    }

    public function testInventory(){
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","B",1);
        $this->service->addProduct("1","B",2);

        $inventory = $this->service->getCartInventory("1");

        $this->assertEquals(2,$inventory->findProduct("A")->getQuantity());
        $this->assertEquals(3,$inventory->findProduct("B")->getQuantity());
    }

    public function testRemovalOfProductException(){
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","A",1);
        $this->expectException(ProductNotInTheCartException::class);
        $this->expectExceptionMessage("The product you are trying to remove is not in the cart");
        $this->service->removeProduct("1","B");
    }

    public function testRemovalOfProductB(){
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","B",1);
        $this->service->removeProduct("1","B");

        $details = $this->service->getCartInventory("1");

        $this->assertEquals(2,$details->findProduct("A")->getQuantity());
        $this->assertEmpty($details->findProduct("B"));
    }

    public function testMaxDifferentProduct(){
        $productId = "A";

        $this->expectException(ProductException::class);
        $this->expectExceptionMessage("There are already 10 different products in your cart, the maximum is 10");
        for($i=0; $i<= self::MAXIMUM_DIFFERENT_PRODUCTS; $i++){
            $this->service->addProduct("1",$productId,1);
            $productId++;
        }
    }

    public function testMaxQuantityPerProduct(){
        $this->expectException(ProductException::class);
        $this->expectExceptionMessage("You are requesting more than 50 items for the product A, the maximum is 50");
        for($i=0; $i<= self::MAXIMUM_ITEMS_PER_PRODUCT; $i++){
            $this->service->addProduct("1","A",1);
        }
    }

    public function testTotal(){
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","C",1);
        $this->service->addProduct("1","B",1);

        $cartAccounting = $this->service->getCartAccounting("1");
        $total = $cartAccounting->getTotal();

        $this->assertEquals(30,$total);
    }

    public function testTotalTwo(){
        $this->service->addProduct("1","A",1);
        $this->service->addProduct("1","A",1);
        $totalA = $this->priceList->getPriceByProductId($this->country,"A")->getPriceVat() * 2;
        $this->service->addProduct("1","B",1);
        $totalB = $this->priceList->getPriceByProductId($this->country,"B")->getPriceVat() * 1;
        $this->service->addProduct("1","C",1);
        $this->service->addProduct("1","C",1);
        $this->service->addProduct("1","C",1);
        $totalC = $this->priceList->getPriceByProductId($this->country,"C")->getPriceVat() * 3;

        $cartAccounting = $this->service->getCartAccounting("1");
        $total = $cartAccounting->getTotal();

        $this->assertEquals($totalA + $totalB + $totalC,$total);
    }

    public function discountDataProvider(){
        return [
            '3 products A, discount' => [
                'type' => "A",
                'quantity' => 3,
                'expectedTotalWithoutDiscount' => 30,
                'expectedTotal' => 27
            ],
            '2 products A, NO discount' => [
                'type' => "A",
                'quantity' => 2,
                'expectedTotalWithoutDiscount' => 20,
                'expectedTotalWithDiscount' => 20
            ],
            '1 products A, NO discount' => [
                'type' => "A",
                'quantity' => 1,
                'expectedTotalWithoutDiscount' => 10,
                'expectedTotalWithDiscount' => 10
            ],
            '2 products B, discount' => [
                'type' => "B",
                'quantity' => 2,
                'expectedTotalWithoutDiscount' => 16,
                'expectedTotal' => 10
            ],
            '1 products B, NO discount' => [
                'type' => "B",
                'quantity' => 1,
                'expectedTotalWithoutDiscount' => 8,
                'expectedTotal' => 8
            ],
        ];
    }

    /**
     * @dataProvider discountDataProvider
     * @param $type
     * @param $quantity
     * @param $expectedTotalWithoutDiscount
     * @param $expectedTotal
     */
    public function testTotalWithDiscount($type,$quantity,
                                          $expectedTotalWithoutDiscount,$expectedTotal){
        for($i=0;$i<$quantity;$i++){
            $this->service->addProduct("1",$type,1);
        }

        $cartAccounting = $this->service->getCartAccounting("1");
        $this->assertEquals($expectedTotalWithoutDiscount,$cartAccounting->getTotalWithoutDiscount());
        $this->assertEquals($expectedTotal,$cartAccounting->getTotal());
    }
}