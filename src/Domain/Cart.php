<?php
namespace Shopping\Domain;

use Shopping\Domain\Exceptions\ProductException;
use Shopping\Shared\Domain\Collection;
use Shopping\Shared\Domain\ItemNotInCollection;
use Shopping\Shared\Domain\Provider\CurrencyRates;
use Shopping\Shared\Domain\Provider\PriceList;
use Symfony\Component\VarDumper\VarDumper;

final class Cart
{
    const DIFFERENT_PRODUCT_LIMIT = 10;
    const LIMIT_ITEMS_PER_PRODUCT = 50;

    private $id;

    private $productCollection;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->productCollection = new Collection();
    }

    public function getId(){
        return $this->id;
    }

    public function addProduct($productId, $numberOf){
        /** @var Product $productLine */
        try {
            $productLine = $this->productCollection->get($productId);
            if( ($productLine->getQuantity() + $numberOf) > self::LIMIT_ITEMS_PER_PRODUCT){
                throw new ProductException("You are requesting more than ".self::LIMIT_ITEMS_PER_PRODUCT
                    ." items for the product ".$productId.", the maximum is ".self::LIMIT_ITEMS_PER_PRODUCT);
            }
            $productLine->updateNumberOf($productLine->getQuantity() + $numberOf);
        }catch (ItemNotInCollection $e){
            if($this->productCollection->count() == self::DIFFERENT_PRODUCT_LIMIT){
                throw new ProductException("There are already ".self::DIFFERENT_PRODUCT_LIMIT
                    ." different products in your cart, the maximum is ".self::DIFFERENT_PRODUCT_LIMIT);
            }
            $productLine = new Product($productId,$numberOf);
        }
        $this->productCollection->offsetSet($productId,$productLine);
    }

    public function removeProduct($productId){
        $this->productCollection->get($productId); //this will throw an error if the product not in the cart
        $this->productCollection->offsetUnset($productId);
    }

    public function getCartInventory(){
        return new CartInventory($this->productCollection);
    }

    public function getCartAccounting(PriceList $priceList, CountryInterface $country,CurrencyRates $currencyRates){
        return new CartAccounting(
            $this->productCollection,
            $priceList,
            $country,
            $currencyRates
        );
    }

    public function getQuantityByProductId(string $productId){
        return $this->productCollection->get($productId);
    }
}