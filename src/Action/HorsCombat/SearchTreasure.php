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
        $awardDifficulty = $params['awardDifficulty'];
        $listOfAwards  = $params['listOfAwards'] ?? [];
        $randomTreasureValue  = rand(1, 100);
        $treasureRoll = $params['treasureRoll'];

        if (!isset($listOfAwards)) {
            $listOfAwards[] = match (true) {
                $randomTreasureValue <= 30 => ['name' => 'Une bourse de pièces d\'or'],
                $randomTreasureValue <= 60 => ['name' => "4d6 pièces d’argent (pa)"],
                $randomTreasureValue <= 70 => ['name' => "3d6 pièces d’or (po)"],
                $randomTreasureValue <= 75 => ['name' => "2d6 po + une gemme commune (valeur 10 po)"],
                $randomTreasureValue <= 80 => ['name' => "3d6 po + un objet d'art (valeur 25 po)"],
                $randomTreasureValue <= 85 => ['name' => "2d6 po + un parchemin de sort de niveau 1 (ex : Détection de la magie)"],
                $randomTreasureValue <= 90 => ['name' => "3d6 po + une potion de soin"],
                $randomTreasureValue <= 95 => ['name' => "1d6 po + 1 objet magique mineur (Table A*)"],
                $randomTreasureValue <= 99 => ['name' => "1d6 po + 1 objet magique inhabituel (Table B*)"],
                $randomTreasureValue <= 100 => ['name' => "Trésor spécial : 1d6 po + objet unique, mystérieux ou lié à une quête"],
                default => [],
            };
        }

        if ($treasureRoll >= $awardDifficulty) {
            $msg = "Le joueur fouille dans « $location », il obtient : « {$listOfAwards} »"; //TODO: boucler pour afficher chaque récompense
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