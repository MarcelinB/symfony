<?php

namespace App\Service;

use App\Entity\Beer;
use App\Service\CallApiService;
use Sabberworm\CSS\Value\Size;

/**
 * Eats CallApiService Array then filters name, description, date, into Beer objects
 * feeds BeerList with Beer objects
 */
class Bartender
{
    /**
     * @Route("/task/listing/viewApi", name="viewApi")
     * @input Array $packet : packet fetched from API
     * 
     */
    function filterBeers(array $packet)
    {
        $result = [];

        //Extract needed params for each object
        for ($i = 0; $i < count($packet); $i++) {
            $temp = new Beer;
            $temp->setName($packet[$i]['name'])
                ->setDescription($packet[$i]['description'])
                ->setFirstBrew($packet[$i]['first_brewed']);

            //Push the beers into $temp
            array_push($result, $temp);
        }
        return $result;
    }
}
