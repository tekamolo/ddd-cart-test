<?php


namespace Shopping\Domain;


interface CountryInterface
{
    public function getVatRate();

    public function getShippingCost();

    public function getCurrency();
}