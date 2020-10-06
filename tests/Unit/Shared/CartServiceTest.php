<?php

namespace Shopping\Tests\Unit\Shared;

use PHPUnit\Framework\TestCase;
use Shopping\Application\CartService;
use Shopping\Domain\Country;
use Shopping\Infrastructure\CartRepository;
use Shopping\Infrastructure\ItemNotInMemoryException;
use Shopping\Shared\Domain\Provider\CountryProvider;
use Shopping\Shared\Domain\Provider\CurrencyRates;
use Shopping\Shared\Domain\Provider\PriceList;

class CartServiceTest extends TestCase
{
    private $cartRepository;

    public function setUp()
    {
        $this->cartRepository = new CartRepository();
    }

    public function testGetException(){
        $this->expectException(ItemNotInMemoryException::class);
        $this->cartRepository->get("1");
    }

    public function testRemoveException(){
        $this->expectException(ItemNotInMemoryException::class);
        $this->cartRepository->remove("1");
    }
}