<?php
namespace Shopping\Domain;

use Shopping\Domain\Exceptions\PositiveNumberException;

class Voucher
{
    private $voucherId;

    public function __construct($productId)
    {
        $this->voucherId = $productId;
    }

    public function getId(){
        return $this->voucherId;
    }
}