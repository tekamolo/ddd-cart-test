<?php

namespace Shopping\Application;

use Shopping\Domain\Cart;
use Shopping\Domain\CartRepository;
use Shopping\Domain\Country;
use Shopping\Domain\Exceptions\ProductNotInTheCartException;
use Shopping\Domain\Product;
use Shopping\Infrastructure\ItemNotInMemoryException;
use Shopping\Shared\Domain\ItemNotInCollection;
use Shopping\Shared\Domain\Provider\PriceList;

class CartService
{
    private $cartRepository;

    private $pricesList;

    private $country;

    public function __construct(CartRepository $repository,PriceList $priceList,Country $country)
    {
        $this->cartRepository = $repository;
        $this->pricesList = $priceList;
        $this->country = $country;
    }

    public function addProduct($cartId, $productId, $quantity){
        $cart = $this->get($cartId);
        $cart->addProduct($productId,$quantity);
        $this->cartRepository->add($cart);
    }

    public function removeProduct($cartId,$productId){
        $cart = $this->get($cartId);
        try {
            $cart->removeProduct($productId);
        }
        catch (ItemNotInCollection $e) {
            throw new ProductNotInTheCartException("The product you are trying to remove is not in the cart");
        }
        $this->cartRepository->add($cart);
    }

    public function getCartInventory($cartId){
        $cart = $this->get($cartId);
        return $cart->getCartInventory();
    }

    public function getCartAccounting($cartId){
        $cart = $this->get($cartId);
        return $cart->getCartAccounting($this->pricesList,$this->country);
    }

    private function get(string $cartId): Cart
    {
        try {
            return $this->cartRepository->get($cartId);
        } catch (ItemNotInMemoryException $e) {
            return new Cart($cartId);
        }
    }
}