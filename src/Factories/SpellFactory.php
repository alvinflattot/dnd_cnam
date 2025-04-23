<?php

namespace App\Factories;


namespace App\Factories;

use App\Models\Spells\Spell;
use App\Models\Spells\DamageSpell;
use App\Models\Spells\HealingSpell;
use App\Models\Spells\ControlSpell;
use App\Models\Spells\UtilitySpell;
use InvalidArgumentException;

class SpellFactory
{
    public static function createSpell(
        string $type,
        string $name,
        int    $castTime,
        int    $restTime,
        int    $level,
        int    $duration,
        array  $components,   // ['V', 'S', 'M']
        string $school       // Évocation, Illusion, etc.
    ): Spell
    {
        return match ($type) {
            'damage' => new DamageSpell($name, $castTime, $restTime, $level, $duration, $components, $school),
            'healing' => new HealingSpell($name, $castTime, $restTime, $level, $duration, $components, $school),
            'control' => new ControlSpell($name, $castTime, $restTime, $level, $duration, $components, $school),
            'utility' => new UtilitySpell($name, $castTime, $restTime, $level, $duration, $components, $school),
            default => throw new InvalidArgumentException("Type de sort inconnu : $type"),
        };
    }
}
