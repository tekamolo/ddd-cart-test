<?php

namespace Shopping\Infrastructure;

use Shopping\Domain\Cart;
use Simara\Cart\Domain\Cart\CartNotFoundException;

class CartRepository implements  \Shopping\Domain\CartRepository
{

    /**
     * @var array<string, Cart>
     */
    private $carts = [];

    public function add(Cart $cart): void
    {
        $this->carts[$cart->getId()] = $cart;
    }

    public function get(string $id): Cart
    {
        if (!isset($this->carts[$id])) {
            throw new ItemNotInMemoryException();
        }
        return $this->carts[$id];
    }

    public function remove(string $id): void
    {
        if (!isset($this->carts[$id])) {
            throw new ItemNotInMemoryException();
        }
        unset($this->carts[$id]);
    }
}