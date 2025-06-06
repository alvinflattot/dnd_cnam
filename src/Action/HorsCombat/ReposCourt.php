<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;

class ReposCourt implements ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $staminaGain = 5;
        $events = [];
        $actor->setStamina(min($actor->getStaminaMax(), $actor->getStamina() + $staminaGain));
        $events[] = "{$actor->getName()} effectue un repos court et récupère $staminaGain points de stamina.";
        return [
            'events' => $events,
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target?->toArray(),
            ],
        ];
    }
}