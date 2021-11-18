<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;


class CallApiService
{
    public $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getBeerTitle(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.punkapi.com/v2/beers'
        );

        $content = $response->toArray();


        return $content;
    }
}
