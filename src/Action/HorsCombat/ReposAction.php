<?php

namespace App\Action\HorsCombat;

header('Content-Type: application/json');

use App\Action\ActionInterface;
use App\Model\Character;

class ReposAction implements ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $mode = $params['mode'] ?? 'court';
        $events = [];
        $result = [
            'actor' => $actor->toArray(),
            'target' => $target ? $target->toArray() : null,
        ];

        if ($mode === 'court') {
            $staminaGain = 5;
            $
            $actor->setStamina(min($actor->getStaminaMax(), $actor->getStamina() + $staminaGain));
            $events[] = "{$actor->getName()} effectue un repos court et récupère $staminaGain points de stamina.";
        } elseif ($mode === 'long') {
            $staminaGain = $actor->getStaminaMax();
            $hpGain = intval($actor->getHpMax() / 2);
            $actor->setStamina($staminaGain);
            $actor->setHp(min($actor->getHpMax(), $actor->getHp() + $hpGain));
            $events[] = "{$actor->getName()} effectue un repos long, récupère $hpGain PV et toute sa stamina.";
        } else {
            $events[] = "Mode de repos inconnu.";
        }

        $result['actor'] = $actor->toArray();

        return [
            'events' => $events,
            'result' => $result,
        ];
    }
}