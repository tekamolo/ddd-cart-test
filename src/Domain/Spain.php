<?php


namespace Shopping\Domain;


class Spain implements Country
{

    public function getVatRate()
    {
        return 1; // Equal to 1 for the sake of the exercice
    }

    public function divisa(){
        return 1;
    }

    public function getShippingCost()
    {
        return 0; //no cost stipulated in the exercice
    }
}