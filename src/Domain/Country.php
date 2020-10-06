<?php


namespace Shopping\Domain;


interface Country
{
    public function getVatRate();

    public function getShippingCost();
}