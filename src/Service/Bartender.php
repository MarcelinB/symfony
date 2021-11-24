<?php

namespace App\Service;

use App\Entity\Beer;
use Sabberworm\CSS\Value\Size;
use App\Service\CallApiService;
use App\Service\BeerConnectionManager;

/**
 * Gets array from BeerCOnnectionManager then filters name, description, date, into Beer objects
 * feeds BeerList with Beer objects
 * also, can make a beer list with names only
 */
class Bartender
{

    /**
     * @input Array $packet : packet fetched from API
     * @return array beer list (name+description) 
     */
    function filterPacket(): array
    {
        $beerListNameDescriptionDate = [];
        $packet = BeerConnectionManager::getPacket();

        //dd($packet[0]->name);

        //Extract needed params for each object
        for ($i = 0; $i < count($packet); $i++) {
            $temp = new Beer;
            $temp->setName($packet[$i]->name)
                ->setDescription($packet[$i]->description);

            //Push the beers into $temp
            array_push($beerListNameDescriptionDate, $temp);
        }
        //dd($beerListNameDescriptionDate);
        return $beerListNameDescriptionDate;
    }

    /**
     * @return array : beer list (name+name)
     */
    public function filterBeerList(): array
    {
        $FilteredBeerNameList = $this->filterPacket();
        $beerListNameName = [];
        //dd($beerListNameName);

        for ($i = 0; $i < count($FilteredBeerNameList); $i++) {

            array_push($beerListNameName, [$FilteredBeerNameList[$i]->getName() => $FilteredBeerNameList[$i]->getName()]);
        }

        //dd($beerListNameName);
        return $beerListNameName;
    }
}
