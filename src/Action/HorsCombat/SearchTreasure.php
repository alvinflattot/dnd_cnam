<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;
use App\Util\Dice;
use App\Util\TreasureGenerator;
use Exception;

class SearchTreasure implements ActionInterface
{
    /**
     * @throws Exception
     */
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $location = $params['location'];
        $awardDifficulty = $params['awardDifficulty'];
        $listOfAwards  = $params['listOfAwards'] ?? null;
        $randomTreasureValue  = rand(1, 100);
        $treasureRoll = $params['treasureRoll'];

        if (!isset($listOfAwards)) {
            $listOfAwards = TreasureGenerator::generateTreasure($randomTreasureValue);
        }

        if ($treasureRoll >= $awardDifficulty) {
            $msg = "Le joueur fouille dans « $location », il obtient : ";
            $rewards = [];

            foreach ($listOfAwards as $award) {
                $quantity = $award['quantity'] ?? null;
                $name = $award['name'];
                $rewards[] = "« {$quantity} {$name} »";

                $actor->ajouterObjetInventaire($name, $quantity);
            }

            $msg .= implode(', ', $rewards);
        } else {
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