<?php


namespace Shopping\Shared\Domain\Provider;


use GuzzleHttp\Client;

class CurrencyRates
{
    protected $client;

    public function __construct()
    {
        $this->client = New Client();
    }

    public function getRate($currency){
        $exchangeString = $this->getExchangeCurrencyString($currency);
        $response = $this->client->request("GET",'https://free.currconv.com/api/v7/convert',
            ['query' =>
                [
                    'apiKey' => '28bcb8d395846f5f012a',
                    'q' => $exchangeString,
                    'compact' => 'ultra',
                ]
            ]
        );
        $decodedResponse = json_decode($response->getBody());
        return $decodedResponse->$exchangeString;
    }

    private function getExchangeCurrencyString($currency){
        return "EUR_".$currency;
    }
}