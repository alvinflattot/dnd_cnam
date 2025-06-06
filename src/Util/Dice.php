<?php

namespace App\Util;

class Dice
{
    public static function roll(int $faces, int $roll): array
    {
        $result = [];

        for ($i = 0; $i < $roll; $i++) {
            $result[] = rand(1, $faces);
        }

        return $result;
    }

    public static function totalRolls(array $rolls): int
    {
        $sum = 0;

        foreach ($rolls as $roll) {
            if (is_array($roll)) {
                $sum += self::totalRolls($roll);
            } else {
                $sum += $roll;
            }
        }

        return $sum;
    }
}