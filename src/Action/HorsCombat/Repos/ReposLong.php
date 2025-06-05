<?php

namespace App\Action\HorsCombat;

use App\Model\Character;

class ReposLongAction implements ReposActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $staminaGain = $actor->getStaminaMax();
        $hpGain = $actor->getHpMax();
        $events = [];

        $actor->setHp($hpGain);
        $actor->setStamina($staminaGain);

        // Réinitialisation des sorts
        $spells = $actor->getSpells();
        foreach ($spells as $name => &$spell) {
            if (isset($spell['maxUse'])) {
                $spell['remainingUse'] = $spell['maxUse'];
            }
        }
        $actor->setSpells($spells);

        $events[] = "{$actor->getName()} effectue un repos long, récupère $hpGain PV, toute sa stamina et tous ses sorts sont restaurés.";

        return [
            'events' => $events,
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target ? $target->toArray() : null,
            ],
        ];
    }
}