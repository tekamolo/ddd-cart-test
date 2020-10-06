<?php


namespace Shopping\Shared\Domain\Provider;


use Shopping\Domain\Country;

class CountryProvider
{
    private $countries = [
        "Spain" => ["name" => "Spain","currency"=>"EUR","vatRate"=>1,"shippingCost"=>0],
        "USA" => ["name" => "USA","currency"=>"USD","vatRate" => 1,"shippingCost"=>0],
    ];

    public function getCountry(string $country){
        foreach ($this->countries as $key => $c){
            if($key == $country){
                return new Country($c["name"],$c["currency"],$c["vatRate"],$c["shippingCost"]);
            }
        }
    }
}