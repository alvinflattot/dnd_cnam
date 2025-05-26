<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;

class MoveAction implements ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $from = 0; //todo: faire système de position;
        $to = $from + $params['distance'];
        $msg = "{$actor->getName()} se déplace de $from à $to.";

        return [
            'events' => [$msg],
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target->toArray(),
            ]
        ];
    }
}
