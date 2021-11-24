<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use GuzzleHttp\Client;






class BeerConnectionManager
{
    public static function getPacket()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request(
            'GET',
            'https://api.punkapi.com/v2/beers',
            //NE FAITES PAS CA A LA MAISON !!
            ['verify' => false]
        );
        $body = $res->getBody();
        $rawPacket = json_decode($body);
        return $rawPacket;
    }
}
