<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;
use App\Util\Dice;
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
            $listOfAwards[] = match (true) {
                $randomTreasureValue <= 30 => ['name' => Dice::totalRolls(Dice::roll(6, 2)) . "pièces d’argent (pa)"],
                $randomTreasureValue <= 60 => ['name' => Dice::totalRolls(Dice::roll(6, 4)) . "pièces d’argent (pa)"],
                $randomTreasureValue <= 70 => ['name' => Dice::totalRolls(Dice::roll(6, 3)) . "pièces d’or (po)"],
                $randomTreasureValue <= 75 => ['name' => Dice::totalRolls(Dice::roll(6, 4)) . "po + une gemme commune (valeur 10 po)"],
                $randomTreasureValue <= 80 => ['name' => Dice::totalRolls(Dice::roll(6, 3)) . "po + un objet d'art (valeur 25 po)"],
                $randomTreasureValue <= 85 => ['name' => Dice::totalRolls(Dice::roll(6, 2)) . "po + un parchemin de sort de niveau 1 (ex : Détection de la magie)"],
                $randomTreasureValue <= 90 => ['name' => Dice::totalRolls(Dice::roll(6, 3)) . "po + une potion de soin"],
                $randomTreasureValue <= 95 => ['name' => Dice::totalRolls(Dice::roll(6, 1)) . "po + 1 objet magique mineur (Table A*)"],
                $randomTreasureValue <= 99 => ['name' => Dice::totalRolls(Dice::roll(6, 1)) . "po + 1 objet magique inhabituel (Table B*)"],
                $randomTreasureValue <= 100 => ['name' => "Trésor spécial : " . Dice::totalRolls(Dice::roll(6, 1)) . "po + objet unique, mystérieux ou lié à une quête"],
                default => [],
            };
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