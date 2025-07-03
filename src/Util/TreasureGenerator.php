<?php

namespace App\Util;

// Prototype en attendant de l'implémentation de la fabrication des objets
class TreasureGenerator
{
    public static function generateTreasure(int $randomTreasureValue): array
    {
        $listOfAwards = match (true) {
            $randomTreasureValue <= 30 => [ // Trésor modeste
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 2))],
                ['name' => 'torch', 'quantity' => 1],
            ],
            $randomTreasureValue <= 60 => [ // Trésor intermédiaire
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 4))],
                ['name' => 'hpPotion', 'quantity' => 1],
            ],
            $randomTreasureValue <= 70 => [ // Trésor rare
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 6))],
                ['name' => 'gem', 'quantity' => rand(1, 2)],
            ],
            $randomTreasureValue <= 75 => [
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 3))],
                ['name' => 'scrollLvl1', 'quantity' => 1],
            ],
            $randomTreasureValue <= 80 => [
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 5))],
                ['name' => 'scrollLvl2', 'quantity' => 1],
            ],
            $randomTreasureValue <= 85 => [
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 6))],
                ['name' => 'strengthPotion', 'quantity' => 1],
            ],
            $randomTreasureValue <= 90 => [
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(6, 6))],
                ['name' => 'rareGem', 'quantity' => 1],
            ],
            $randomTreasureValue <= 95 => [
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(8, 6))],
                ['name' => 'magicCrystal', 'quantity' => 1],
            ],
            $randomTreasureValue <= 99 => [
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(10, 6))],
                ['name' => 'gem', 'quantity' => rand(4, 6)],
            ],
            $randomTreasureValue <= 100 => [ // Jackpot
                ['name' => 'gold', 'quantity' => Dice::totalRolls(Dice::roll(12, 6))],
                ['name' => 'legendaryArtefact', 'quantity' => 1],
                ['name' => 'epicGem', 'quantity' => rand(1, 3)],
            ],
            default => [],
        };

        return $listOfAwards;
    }
}
