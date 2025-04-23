<?php

namespace App\Models\Spells;


namespace App\Models\Spells;

class DamageSpell extends Spell
{
    public function __construct(
        string $name,
        int $castTime,
        int $restTime,
        int $level,
        int $duration,
    ) {
        $this->setName($name);
        $this->setCastTime($castTime);
        $this->setRestTime($restTime);
        $this->setLevel($level);
        $this->setDuration($duration);
        $this->setType('damage');
    }
}
