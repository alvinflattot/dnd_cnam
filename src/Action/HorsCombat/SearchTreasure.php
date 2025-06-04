<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;
use Exception;

class SearchTreasure implements ActionInterface
{
    /**
     * @throws Exception
     */
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $location      = $params['location'];
        $listOfAwards  = $params['listOfAwards'];
        $randomIndex   = array_rand($listOfAwards);
        $award         = $listOfAwards[$randomIndex];

        $resultDice = 10;

        if ($resultDice >= $award['minimumDice']) {
            $msg = "Le joueur fouille dans « $location », il obtient : « {$award['name']} »";
        }
        else {
            $msg = "Le joueur fouille dans « $location », mais ne trouve rien.";
        }


        return [
            'events' => [$msg],
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target->toArray(),
            ]
        ];
    }
}